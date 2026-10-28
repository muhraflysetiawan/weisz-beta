<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Scan Blade files in resources/views for localization keys.
     *
     * @return list<string>
     */
    public function scanBladeFiles(): array
    {
        $viewsPath = resource_path('views');
        if (! File::isDirectory($viewsPath)) {
            return [];
        }

        $files = File::allFiles($viewsPath);
        $keys = [];

        // Patterns to match: __('text'), @lang('text'), trans('text')
        $patterns = [
            "/__\(\s*'([^'\\\\]*(?:\\\\.[^'\\\\]*)*)'\s*[\),]/u",
            '/__\(\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"\s*[\),]/u',
            "/@lang\(\s*'([^'\\\\]*(?:\\\\.[^'\\\\]*)*)'\s*\)/u",
            '/@lang\(\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"\s*\)/u',
        ];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = $file->getContents();

            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches)) {
                    foreach ($matches[1] as $match) {
                        $key = trim(stripcslashes($match));
                        // Ignore dynamic expressions or empty strings
                        if (! empty($key) && ! str_starts_with($key, '$') && ! str_contains($key, '::')) {
                            $keys[$key] = true;
                        }
                    }
                }
            }
        }

        return array_keys($keys);
    }

    /**
     * Load existing JSON translations for a given locale.
     *
     * @return array<string, string>
     */
    public function loadLocaleJson(string $locale): array
    {
        $filePath = base_path("lang/{$locale}.json");
        if (File::exists($filePath)) {
            $data = json_decode(File::get($filePath), true);
            if (is_array($data)) {
                return $data;
            }
        }

        return [];
    }

    /**
     * Save translations into JSON localization file.
     *
     * @param  array<string, string>  $translations
     */
    public function saveLocaleJson(string $locale, array $translations): void
    {
        $langDir = base_path('lang');
        if (! File::isDirectory($langDir)) {
            File::makeDirectory($langDir, 0755, true);
        }

        ksort($translations, SORT_NATURAL | SORT_FLAG_CASE);

        File::put(
            base_path("lang/{$locale}.json"),
            json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Translate batch of Indonesian phrases to English via Translation/AI API.
     *
     * @param  list<string>  $phrases
     * @return array<string, string>
     */
    public function translateBatch(array $phrases): array
    {
        if (empty($phrases)) {
            return [];
        }

        $apiKey = trim((string) config('services.openrouter.api_key'));
        $model = (string) config('services.openrouter.model', 'gpt-5.6');
        $baseUrl = rtrim((string) config('services.openrouter.base_url', 'https://nevagate.anext.dev/v1'), '/');
        $verifySsl = (bool) config('services.openrouter.verify_ssl', false);
        $timeout = (int) config('services.openrouter.timeout', 15);

        // Fallback dictionary for common UI strings if API key is missing or offline
        $curatedFallback = $this->getCuratedFallbackDictionary();

        if (empty($apiKey)) {
            Log::info('TranslationService: No API key configured, using curated fallback.');
            $results = [];
            foreach ($phrases as $phrase) {
                $results[$phrase] = $curatedFallback[$phrase] ?? $phrase;
            }

            return $results;
        }

        // Quick probe to ensure translation API is reachable before starting batch requests
        $isApiReachable = true;
        try {
            $probe = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'HTTP-Referer' => config('app.url', 'http://localhost:8000'),
            ])->timeout(4)->withoutVerifying()->get("{$baseUrl}/models");

            if (! $probe->successful() && $probe->status() !== 404 && $probe->status() !== 401) {
                $isApiReachable = false;
            }
        } catch (\Throwable $e) {
            $isApiReachable = false;
            Log::info("Translation API endpoint ({$baseUrl}) is currently unreachable or timed out ({$e->getMessage()}). Using curated dictionary fallback.");
        }

        if (! $isApiReachable) {
            $results = [];
            foreach ($phrases as $phrase) {
                $results[$phrase] = $curatedFallback[$phrase] ?? $phrase;
            }

            return $results;
        }

        $chunks = array_chunk($phrases, 30);
        $translations = [];

        foreach ($chunks as $chunk) {
            $systemPrompt = "You are an expert software UI localization translator. Translate Indonesian user interface phrases to natural, clear, professional English for a modern mental health web application.\n"
                ."Strict Rules:\n"
                ."- Preserve technical placeholders like :name, :count if present.\n"
                ."- Keep brand name 'Weisz' unchanged.\n"
                ."- Respond ONLY with a valid JSON object mapping the exact Indonesian key to its English translation.\n"
                .'- Example: {"Ruang Curhat": "Chat Room", "Masuk": "Log In"}';

            $userPrompt = "Translate the following Indonesian UI strings to English:\n".json_encode($chunk, JSON_UNESCAPED_UNICODE);

            $candidateModels = [
                $model,
                'gpt-5.6',
                'deepseek-v4-flash',
            ];

            $success = false;

            foreach ($candidateModels as $candidateModel) {
                try {
                    $client = Http::withHeaders([
                        'Authorization' => "Bearer {$apiKey}",
                        'HTTP-Referer' => config('app.url', 'http://localhost:8000'),
                        'X-Title' => 'Weisz Localization Translator',
                        'Content-Type' => 'application/json',
                    ])->timeout($timeout);

                    if (! $verifySsl || app()->environment('local', 'testing')) {
                        $client = $client->withoutVerifying();
                    }

                    $response = $client->post("{$baseUrl}/chat/completions", [
                        'model' => $candidateModel,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userPrompt],
                        ],
                        'temperature' => 0.2,
                        'max_tokens' => 2000,
                    ]);

                    if ($response->successful()) {
                        $body = $response->json();
                        $content = trim((string) ($body['choices'][0]['message']['content'] ?? ''));

                        // Extract JSON block if wrapped in markdown
                        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $content, $m)) {
                            $content = $m[1];
                        } elseif (preg_match('/(\{.*\})/s', $content, $m)) {
                            $content = $m[1];
                        }

                        $decoded = json_decode($content, true);
                        if (is_array($decoded)) {
                            foreach ($chunk as $original) {
                                if (isset($decoded[$original]) && is_string($decoded[$original])) {
                                    $translations[$original] = trim($decoded[$original]);
                                } elseif (isset($curatedFallback[$original])) {
                                    $translations[$original] = $curatedFallback[$original];
                                } else {
                                    $translations[$original] = $original;
                                }
                            }
                            $success = true;
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Translation API attempt failed with model {$candidateModel}: ".$e->getMessage());
                }
            }

            if (! $success) {
                foreach ($chunk as $original) {
                    $translations[$original] = $curatedFallback[$original] ?? $original;
                }
            }
        }

        return $translations;
    }

    /**
     * Curated fallback dictionary covering all primary UI strings in the application.
     *
     * @return array<string, string>
     */
    public function getCuratedFallbackDictionary(): array
    {
        return [
            // Brand & Navigation
            'Weisz' => 'Weisz',
            'Teman Refleksi & Pemahaman Emosi' => 'Reflection & Emotional Clarity Companion',
            'Ruang Curhat' => 'Chat Room',
            'Mulai Curhat' => 'Start Reflection',
            'Jelajahi Topik' => 'Explore Topics',
            'Jelajahi Semua Topik' => 'Explore All Topics',
            'Riwayat' => 'History',
            'Riwayat Percakapan' => 'Conversation History',
            'Profesional' => 'Professionals',
            'Cari Bantuan Profesional' => 'Find Professional Help',
            'Bantuan Profesional' => 'Professional Help',
            'Pengaturan' => 'Settings',
            'Profil' => 'Profile',
            'Profil & Pengaturan' => 'Profile & Settings',
            'Keluar' => 'Log Out',
            'Log Out' => 'Log Out',
            'Masuk' => 'Log In',
            'Daftar' => 'Register',
            'Daftar Akun' => 'Create Account',
            'Kembali ke Ruang Obrolan' => 'Back to Chat Room',
            'Kembali ke Aplikasi User' => 'Back to User App',
            'Kembali' => 'Back',
            'Lanjutkan' => 'Continue',
            'Simpan' => 'Save',
            'Simpan Perubahan' => 'Save Changes',
            'Batal' => 'Cancel',
            'Hapus' => 'Delete',
            'Tutup' => 'Close',
            'Cari' => 'Search',
            'Filter' => 'Filter',
            'Semua' => 'All',
            'Detail' => 'Details',
            'Status' => 'Status',
            'Aksi' => 'Action',

            // Theme & Language
            'Ganti Tema' => 'Change Theme',
            'Pengaturan Tema' => 'Theme Settings',
            'Tema Antarmuka' => 'Interface Theme',
            'Pilih tampilan antarmuka yang paling nyaman untuk matamu.' => 'Choose the interface appearance most comfortable for your eyes.',
            'Sistem' => 'System',
            'Sistem (Dua Warna)' => 'System (Auto)',
            'Terang' => 'Light',
            'Light Mode' => 'Light Mode',
            'Gelap' => 'Dark',
            'Dark Mode' => 'Dark Mode',
            'Bahasa' => 'Language',
            'Bahasa Aplikasi' => 'Application Language',
            'Pilih bahasa tampilan antarmuka sistem. Pesan percakapan AI akan tetap merespons secara otomatis sesuai bahasa ceritamu.' => 'Select system interface language. AI chat responses will automatically match the language of your story.',
            'Bahasa Indonesia' => 'Bahasa Indonesia',
            'Bahasa Inggris' => 'English',
            'English' => 'English',
            'Default' => 'Default',
            'Lokal' => 'Local',
            'Default system interface language' => 'Default system interface language',
            'Bahasa tampilan bahasa Indonesia' => 'Indonesian interface language',
            'Bahasa tampilan utama sistem' => 'Main system interface language',
            'Alternatif' => 'Alternative',
            'Bahasa alternatif antarmuka' => 'Alternative interface language',
            'Switch to English' => 'Switch to English',
            'Ganti ke Indonesia' => 'Ganti ke Indonesia',

            // Auth Pages
            'Masuk ke Weisz' => 'Log in to Weisz',
            'Lanjutkan sesi refleksi dan kelola riwayat percakapanmu.' => 'Continue your reflection session and manage your conversation history.',
            'Alamat Email' => 'Email Address',
            'Kata Sandi' => 'Password',
            'Konfirmasi Kata Sandi' => 'Confirm Password',
            'Ingat saya di perangkat ini' => 'Remember me on this device',
            'Belum punya akun Weisz?' => "Don't have a Weisz account?",
            'Daftar sekarang' => 'Register now',
            'Sudah punya akun?' => 'Already have an account?',
            'Masuk di sini' => 'Log in here',
            'Buat Akun Weisz' => 'Create a Weisz Account',
            'Mulai ruang refleksi yang aman, tenang, dan tanpa penghakiman.' => 'Start a safe, calm, and judgment-free reflection space.',
            'Nama Lengkap' => 'Full Name',
            'Nama Panggilan' => 'Nickname',
            'Nama Panggilan atau Nama Lengkap' => 'Nickname or Full Name',
            'Contoh: Alex' => 'Example: Alex',
            'Ulangi kata sandi' => 'Repeat password',
            'Minimal 8 karakter' => 'At least 8 characters',
            'Email atau kata sandi yang kamu masukkan salah.' => 'The email or password you entered is incorrect.',
            'Selamat datang di Weisz! Akunmu berhasil dibuat.' => 'Welcome to Weisz! Your account has been created successfully.',
            'Mulai perjalanan memahami emosi dan mengurai stres harianmu.' => 'Begin the journey of understanding emotions and untangling daily stress.',

            // Profile & Settings
            'Ubah Nama Akun' => 'Change Account Name',
            'Perbarui nama akun atau nama panggilan tampilanmu.' => 'Update your account name or display nickname.',
            'Perbarui nama akun atau identitas tampilanmu pada sesi percakapan.' => 'Update your account name or display identity for chat sessions.',
            'Nama Akun' => 'Account Name',
            'Masukkan nama akun...' => 'Enter account name...',
            'Nama akun berhasil diperbarui.' => 'Account name updated successfully.',
            'Kata sandi berhasil diperbarui.' => 'Password updated successfully.',
            'Terjadi kesalahan saat memperbarui nama.' => 'An error occurred while updating the name.',
            'Kelola preferensi antarmuka tampilan dan bahasa antarmuka aplikasi.' => 'Manage interface display preferences and application language.',
            'Keluar dari sesi akun ini dengan aman.' => 'Safely log out of this account session.',
            'Log Out dari Akun' => 'Log Out from Account',
            'Buka Profil' => 'Open Profile',
            'Profile' => 'Profile',
            'Setting' => 'Settings',
            'Tema' => 'Theme',

            // Landing Page
            'Weisz — Teman Refleksi & Pemahaman Emosi' => 'Weisz — Emotional Clarity & Reflection Companion',
            'Teman Refleksi & Pemahaman Emosi Terpandu' => 'Guided Reflection & Emotional Understanding Companion',
            'Urai keresahan pikiranmu secara perlahan dalam ruang yang aman, tenang, dan terstruktur tanpa penghakiman.' => 'Gently unravel your mental distress in a safe, calm, and structured space free from judgment.',
            'Mulai Sesi Curhat' => 'Start Reflection Session',
            'Jelajahi Berbagai Masalah' => 'Explore Various Topics',
            'Pendekatan Berbasis Pakar' => 'Expert-Guided Approach',
            'Kerahasiaan Terjaga' => 'Confidential & Private',
            'Responsif & Empatik' => 'Empathetic & Responsive',
            'Tanpa Penghakiman' => 'Judgment-Free',
            'Bagaimana Weisz Membantumu?' => 'How Weisz Helps You',
            'Langkah Terpandu Menuju Ketenangan Pikiran' => 'Guided Steps Toward Peace of Mind',
            '1. Pilih Konteks & Topik' => '1. Choose Context & Topic',
            'Pilih situasi spesifik yang sedang mengganjal pikiranmu agar pendampingan terarah.' => 'Select the specific situation troubling your mind for targeted guidance.',
            '2. Bercerita dengan Leluasa' => '2. Express Yourself Freely',
            'Ceritakan apa yang kamu rasakan tanpa khawatir dinilai atau dihakimi.' => 'Share how you feel without fear of judgment or scrutiny.',
            '3. Dapatkan Refleksi & Pemahaman' => '3. Gain Reflection & Insights',
            'Dapatkan pandangan baru, validasi emosi, serta langkah penanganan mandiri terstruktur.' => 'Receive fresh perspectives, emotional validation, and structured self-care steps.',
            'Kategori Permasalahan Populer' => 'Popular Concern Categories',
            'Akademik & Studi' => 'Academic & Studies',
            'Karier & Finansial' => 'Career & Finances',
            'Relasi & Keluarga' => 'Relationships & Family',
            'Kecemasan & Overthinking' => 'Anxiety & Overthinking',
            'Dukungan Profesional Jika Dibutuhkan' => 'Professional Support When Needed',
            'Jika kondisimu membutuhkan penanganan lebih lanjut, Weisz siap menghubungkanmu dengan psikolog dan psikiater berlisensi.' => 'If your condition requires advanced care, Weisz connects you with licensed psychologists and psychiatrists.',
            'Lihat Rekomendasi Profesional' => 'View Professional Recommendations',
            'Ruang Nyaman Mengurai' => 'Comfortable Space to Unpack',
            'Rasa dan' => 'Emotions and',
            'Pikiran' => 'Mind',
            'Bersama Weisz' => 'With Weisz',
            'Temukan ketenangan saat kepala terasa penuh. Menggabungkan sistem pakar psikologis terstruktur dengan teman ngobrol AI penuh empati, siap mendengarkan tanpa menghakimi 24/7.' => 'Find calm when your mind feels overwhelmed. Combining structured psychological expert systems with an empathetic AI companion, ready to listen without judgment 24/7.',
            'Mulai Sekarang' => 'Get Started',
            'Explore' => 'Explore',
            'Jujur aku kewalahan banget sama skripsi dan ekspektasi keluarga belakangan ini. Pengin istirahat tapi rasa bersalah terus muncul...' => "Honestly I'm so overwhelmed with my thesis and family expectations lately. I want to rest but guilt keeps creeping in...",
            'Wajar sekali merasa lelah saat menanggung dua beban besar sekaligus. Tarik napas sebentar ya. Pikiranmu yang butuh jeda bukanlah sebuah kegagalan.' => 'It is completely natural to feel exhausted carrying two heavy burdens at once. Take a breath. Your mind needing a pause is not a failure.',
            'Apakah rasa sesak ini lebih terasa di pundak atau dadamu saat ini?' => 'Does this tightness feel more in your shoulders or your chest right now?',
            'Ceritakan apa yang dirasa...' => 'Share what you feel...',
            'Alur Terstruktur Weisz' => 'Weisz Structured Workflow',
            'Bukan sekadar chat biasa — setiap sesi terkunci pada konteks spesifikmu.' => 'Not just an ordinary chat — every session is locked to your specific context.',
            'Pilih Masalah' => 'Choose Problem',
            'Pilih kategori utama emosi atau relasi, lalu kerucutkan ke topik spesifik.' => 'Choose the main category of emotion or relationship, then narrow down to a specific topic.',
            'Kunci Konteks' => 'Lock Context',
            'Konfirmasi topikmu agar obrolan AI tetap fokus dan tidak melebar ke mana-mana.' => 'Confirm your topic so the AI chat stays focused and avoids straying.',
            'AI + Expert System' => 'AI + Expert System',
            'AI mendengarkan ceritamu, sementara aturan pakar mengevaluasi faktor pemicu secara objektif.' => 'AI listens to your story, while expert rules evaluate triggers objectively.',
            'Rangkuman & Rujukan' => 'Summary & Referrals',
            'Dapatkan rangkuman poin refleksi dan rekomendasi psikolog berlisensi jika diperlukan.' => 'Get summary reflection points and licensed psychologist referrals if needed.',
            'Kategori Masalah yang Sering Dihadapi' => 'Frequently Encountered Problem Categories',
            'Mulai dengan memilih situasi yang paling menggambarkan kondisimu.' => 'Start by choosing the situation that best describes your condition.',
            'Direktori Terpercaya' => 'Trusted Directory',
            'Tersedia :count Data Profesional Berlisensi' => 'Available :count Licensed Professional Profiles',
            'Weisz tidak menjual konsultasi atau memungut biaya. Kami menyediakan informasi kontak psikolog & psikiater terverifikasi yang dapat kamu hubungi secara mandiri jika sistem pakar mendeteksi kebutuhan dukungan profesional.' => 'Weisz does not sell consultations or charge fees. We provide verified psychologist & psychiatrist contacts you can reach independently if the expert system detects professional support needs.',
            'Rekomendasi Pakar' => 'Expert Recommendations',
            'About Weisz' => 'About Weisz',
            'Ruang Aman Tanpa Prasangka, Menemani Setiap Emosi dan Langkahmu' => 'A Safe Space Free from Prejudices, Accompanying Every Emotion and Step',
            'Weisz hadir sebagai sahabat refleksi diri yang menggabungkan kehangatan dialog empati berbasis kecerdasan buatan dengan ketegasan sistem pakar psikologis. Kami percaya bahwa setiap beban pikiran berhak didengar, diurai dengan tenang, dan dipahami tanpa rasa takut akan penghakiman.' => 'Weisz is here as a self-reflection companion combining warm AI-based empathetic dialogue with structured psychological expert systems. We believe every mental burden deserves to be heard, unpacked calmly, and understood without fear of judgment.',
            'Privasi Penuh' => 'Full Privacy',
            'Bebas bercerita tanpa syarat identitas KTP atau nomor HP.' => 'Freely share without ID card or phone number requirements.',
            'Sistem Pakar' => 'Expert System',
            'Evaluasi konteks terstruktur berlandaskan metode psikologis.' => 'Structured context evaluation grounded in psychological methods.',
            'Pendengar 24/7' => '24/7 Listener',
            'Selalu tersedia kapan pun kepalamu butuh ruang untuk jeda.' => 'Always available whenever your mind needs space for a pause.',
            'Direktori Resmi' => 'Official Directory',
            'Daftar Psikolog & Psikiater Terverifikasi' => 'Verified Psychologist & Psychiatrist Directory',
            'Data profesional terverifikasi yang dikelola oleh admin untuk membantu Gen Z saat membutuhkan evaluasi medis/klinis langsung.' => 'Verified professional data managed by admins to support Gen Z when requiring direct medical/clinical evaluation.',
            'Cari berdasarkan nama, spesialisasi, kota...' => 'Search by name, specialization, city...',
            'Transparansi Weisz:' => 'Weisz Transparency:',
            'Seluruh data profesional di bawah ini murni sebagai rujukan kontak dan tidak melibatkan komisi konsultasi atau pemesanan melalui aplikasi Weisz.' => 'All professional data below is purely for contact referral and does not involve consultation commissions or booking through Weisz.',
            'Telepon:' => 'Phone:',
            'Tidak ditemukan data profesional yang sesuai kriteria pencarian.' => 'No professional data found matching the search criteria.',

            // Exploration Pages (Categories, Subcategories, Topics)
            'Apa yang Sedang Mengganjal Pikiranmu?' => "What's on Your Mind?",
            'Pilih kategori situasi yang paling mendekati kondisimu saat ini. Setiap kategori dirancang dengan konteks psikologis spesifik untuk mengurai keresahanmu secara bertahap dan terarah.' => 'Choose the situation category closest to your current state. Each category is designed with psychological context to unravel your distress gradually and purposefully.',
            'Kerucutkan Subkategori' => 'Narrow Down Subcategory',
            'Pilih area yang paling relevan untuk membantumu memfokuskan cerita pada aspek yang paling mengganggu pikiranmu.' => 'Select the most relevant area to help focus your story on what troubles you most.',
            'Pilih Topik Percakapan' => 'Choose Conversation Topic',
            'Pilih topik yang paling mewakili situasi yang sedang kamu alami. Weisz akan mengunci konteks ini agar sesi berlangsung terarah dan tidak melebar.' => 'Select the topic best representing your situation. Weisz will lock this context so your session stays focused.',
            'Katalog Lengkap Permasalahan' => 'Complete Problem Catalog',
            'Cari topik atau keresahan spesifik yang ingin kamu bicarakan...' => 'Search specific topic or concern you want to talk about...',
            'Konfirmasi Konteks Percakapan' => 'Confirm Conversation Context',
            'Sebelum memulai obrolan, mari pastikan ruang dan konteks yang kamu pilih sudah tepat.' => 'Before starting the chat, let us ensure the selected room and context are right for you.',
            'Mulai Sesi Sekarang' => 'Start Session Now',
            'Pilih Topik Lain' => 'Choose Another Topic',
            'Topik ini telah dirancang untuk mendampingi:' => 'This topic is designed to guide:',
            'Langkah Penanganan Mandiri Awal' => 'Initial Self-Care Steps',
            'Catatan Keamanan' => 'Safety Notice',
            'Weisz bukan pengganti layanan gawat darurat medis atau psikiatri.' => 'Weisz is not a substitute for emergency medical or psychiatric care.',
            'Langkah 1: Kategori' => 'Step 1: Category',
            'Langkah 2: Subkategori' => 'Step 2: Subcategory',
            'Langkah 3: Topik' => 'Step 3: Topic',
            'Langkah 4: Konfirmasi' => 'Step 4: Confirmation',
            'Konfirmasi' => 'Confirmation',
            'Mulai Obrolan' => 'Start Chat',
            'Kembali ke Pilih Topik' => 'Back to Select Topic',
            'Ganti Topik Lain' => 'Switch to Another Topic',
            'Katalog Topik' => 'Topic Catalog',
            'Kembali ke Ruang Curhat' => 'Back to Chat Room',
            'Katalog Lengkap :count Topik' => 'Complete Catalog of :count Topics',
            'Pilih Topik yang Sedang Kamu Hadapi' => 'Choose the Topic You Are Facing',
            'Cari langsung situasi atau keresahan yang kamu rasakan berdasarkan kata kunci, atau saring berdasarkan kategori utama.' => 'Directly search your concerns by keyword, or filter by main category.',
            'Cari topik... (misal: overthinking, skripsi, insecure, patah hati)' => 'Search topic... (e.g., overthinking, thesis, insecure, heartbreak)',
            'Mulai Percakapan' => 'Start Conversation',
            'Tidak ada topik yang sesuai dengan pencarianmu' => 'No topics match your search',
            'Coba gunakan kata kunci yang lebih umum atau pilih kategori lain di atas.' => 'Try using more general keywords or select another category above.',
            'Reset Pencarian' => 'Reset Search',
            'Pilih Kategori Masalah' => 'Choose Problem Category',
            'Pilih Subkategori' => 'Choose Subcategory',
            'Kembali ke Kategori' => 'Back to Categories',
            'Kategori Aktif' => 'Active Category',
            'Pilih Topik' => 'Choose Topic',
            'Kembali ke Subkategori' => 'Back to Subcategories',
            'Mungkin ini yang sedang kamu rasakan:' => 'Perhaps this is what you are experiencing:',

            // Chat Feed & Messages
            'Ketik ceritamu di sini...' => 'Type your story here...',
            'Kirim pesan...' => 'Send message...',
            'Kirim' => 'Send',
            'Sedang mengetik...' => 'Typing...',
            'Memikirkan respons reflektif...' => 'Thinking reflective response...',
            'Pesan baru' => 'New message',
            'Sesi Baru' => 'New Session',
            'Mulai Obrolan Baru' => 'Start New Chat',
            'Obrolan Baru' => 'New Chat',
            'Topics' => 'Topics',
            'Pakar' => 'Experts',
            'Riwayat Obrolan' => 'Chat History',
            'Percakapan Weisz' => 'Weisz Conversation',
            'Sedang Dibuka' => 'Currently Open',
            'Belum ada riwayat obrolan lainnya' => 'No other chat history yet',
            'Sesi Aktif' => 'Active Session',
            'Selesai' => 'Completed',
            'Selesaikan Sesi' => 'End Session',
            'Keluar dari Sesi' => 'Leave Session',
            'Rangkuman Sesi' => 'Session Summary',
            'Lihat Rangkuman' => 'View Summary',
            'Apakah kamu masih di sana?' => 'Are you still there?',
            'Aku masih di sini mendengarkanmu. Jika kamu butuh waktu untuk bernapas atau berpikir sejenak, itu sangat wajar kok.' => 'I am still here listening. If you need a moment to breathe or reflect, that is completely natural.',
            'Lanjut Bercerita' => 'Continue Sharing',
            'Istirahat Sejenak' => 'Take a Break',
            'Evaluasi Emosi & Mood' => 'Emotion & Mood Evaluation',
            'Bagaimana kondisi atau perasaanmu saat ini?' => 'How is your mood or state right now?',
            'Bagaimana perasaanmu sekarang?' => 'How are you feeling right now?',
            'Pilih ekspresi yang paling menggambarkan suasana hatimu agar Weisz AI dapat memahami dan masuk ke suasana hatimu secara tepat.' => 'Select the expression best describing your mood so Weisz AI can accurately connect with your state.',
            'Seiring kita mengurai cerita, suasana perasaanmu bisa berkembang. Bagaimana kondisimu saat ini? Pilih salah satu untuk membantu Weisz menyesuaikan nada bicara dengan hatimu:' => 'As we unpack your story, your feelings may evolve. How are you feeling right now? Select one to help Weisz calibrate its tone with your heart:',
            'Sangat Cemas' => 'Very Anxious',
            'Cemas' => 'Anxious',
            'Netral' => 'Neutral',
            'Lebih Tenang' => 'Calmer',
            'Lega' => 'Relieved',
            'Perbarui Mood' => 'Update Mood',
            'Sesi ini telah selesai.' => 'This session is completed.',
            'Kamu bisa membaca kembali rangkuman atau memulai sesi baru kapan saja.' => 'You can review the summary or start a new session anytime.',
            'Sistem Weisz mendeteksi beban emosional yang cukup berat dan berkelanjutan. Ceritamu sangat berharga, dan kamu tidak harus menanggung semuanya sendirian. Berkonsultasi dengan psikiater atau psikolog klinis berlisensi dapat membantumu mendapatkan penanganan medis dan evaluasi mendalam yang tepat.' => 'The Weisz system detected quite heavy and persistent emotional distress. Your story matters, and you do not have to carry it all alone. Consulting with a licensed psychiatrist or clinical psychologist can provide proper medical evaluation and in-depth care.',
            'Ingin Membahas Topik Lain?' => 'Want to Discuss Another Topic?',
            'Sepertinya ceritamu mulai menyentuh hal di luar topik ini. Agar refleksimu tetap terarah dan efektif, kamu bisa membuat percakapan baru dengan topik yang lebih sesuai.' => 'It seems your story is touching upon areas outside this topic. To keep your reflection focused and effective, you can start a new chat with a more suitable topic.',
            'Buat Percakapan Baru' => 'Start New Conversation',
            'Lihat Katalog Topik' => 'View Topic Catalog',
            'Aku bingung mau cerita apa' => "I'm not sure what to share",
            'Ceritakan apapun...' => 'Share whatever comes to mind...',
            'Merekam...' => 'Recording...',
            'Batalkan Rekaman' => 'Cancel Recording',
            'Jeda Rekaman' => 'Pause Recording',
            'Kirim Voice Note' => 'Send Voice Note',
            'Kirim Pesan' => 'Send Message',

            // Conversation Summary & Recommendations
            'Rangkuman Percakapan' => 'Conversation Summary',
            'Rangkuman Refleksi Percakapan' => 'Conversation Reflection Summary',
            'Berikut adalah rangkuman dari apa yang telah kamu bagikan selama sesi ini. Kamu tetap bisa melanjutkan obrolan kapan saja.' => 'Here is a summary of what you have shared during this session. You can continue the chat anytime.',
            'Topik Utama' => 'Main Topic',
            'Konteks' => 'Context',
            'Apa yang Kamu Bagikan' => 'What You Shared',
            'Kemungkinan Faktor yang Memengaruhi' => 'Possible Contributing Factors',
            'Saran Langkah Kecil Berikutnya' => 'Suggested Next Small Steps',
            'Dukungan Profesional' => 'Professional Support',
            'Catatan Keamanan:' => 'Safety Notice:',
            'Rangkuman ini dibuat secara otomatis berdasarkan apa yang kamu ceritakan dan tidak dimaksudkan sebagai diagnosis klinis medis, psikiatri, atau evaluasi psikologis resmi.' => 'This summary is automatically generated based on what you shared and is not intended as a clinical medical diagnosis, psychiatry, or official psychological evaluation.',
            'Rangkuman percakapan berhasil diperbarui. Kamu tetap bisa melanjutkan obrolan kapan saja.' => 'Conversation summary updated successfully. You can continue the chat anytime.',
            'Inti Refleksi' => 'Core Reflection',
            'Kondisi Emosi Awal' => 'Initial Emotional State',
            'Kondisi Emosi Akhir' => 'Final Emotional State',
            'Wawasan Utama' => 'Key Insights',
            'Rekomendasi Tindakan Mandiri' => 'Self-Care Recommendations',
            'Rekomendasi Ahli Profesional' => 'Professional Recommendations',
            'Lanjutkan Obrolan' => 'Continue Conversation',
            'Kembali ke Riwayat' => 'Back to History',

            // Professional Directory
            'Daftar Tenaga Ahli Profesional' => 'Professional Directory',
            'Temukan psikolog dan psikiater berlisensi untuk pendampingan lebih lanjut.' => 'Find licensed psychologists and psychiatrists for advanced consultation.',
            'Dukungan Eksternal Terpercaya' => 'Trusted External Support',
            'Rekomendasi Tenaga Profesional' => 'Professional Directory Recommendations',
            'Berdasarkan evaluasi sesi' => 'Based on session evaluation',
            'berikut adalah psikolog atau psikiater berlisensi yang dapat kamu hubungi secara mandiri.' => 'here are licensed psychologists or psychiatrists you can contact independently.',
            'Kembali ke Rangkuman' => 'Back to Summary',
            'Informasi Penting:' => 'Important Information:',
            'Weisz tidak menyediakan sistem booking langsung, obrolan privat dengan profesional di aplikasi, maupun pembayaran. Data di bawah ini disediakan resmi sebagai rujukan kontak untuk kamu hubungi secara langsung di luar platform Weisz.' => 'Weisz does not provide direct booking systems, private in-app chats with professionals, or payments. The data below is officially provided as referral contact details for you to reach out directly outside the Weisz platform.',
            'Terverifikasi' => 'Verified',
            'Alamat / Praktik:' => 'Address / Practice:',
            'Telepon / WhatsApp:' => 'Phone / WhatsApp:',
            'Email:' => 'Email:',
            'Belum ada data profesional aktif di database saat ini.' => 'No active professional data in the database currently.',
            'Spesialisasi' => 'Specialization',
            'Pengalaman' => 'Experience',
            'Lokasi' => 'Location',
            'Jadwal Konsultasi' => 'Consultation Schedule',
            'Kontak / Reservasi' => 'Contact / Booking',
            'Lihat Profil' => 'View Profile',
            'Hubungi Sekarang' => 'Contact Now',
            'Tahun' => 'Years',

            // History Page
            'Semua Sesi Percakapan' => 'All Conversation Sessions',
            'Riwayat Sesi Percakapan' => 'Conversation Session History',
            'Daftar seluruh sesi percakapanmu. Semua riwayat obrolan dapat diakses dan dilanjutkan kembali kapan saja.' => 'List of all your conversation sessions. All chat history can be accessed and resumed anytime.',
            'Cari topik atau judul sesi...' => 'Search topic or session title...',
            'Reset' => 'Reset',
            'Rangkuman' => 'Summary',
            'pesan' => 'messages',
            'Belum ada riwayat percakapan yang cocok' => 'No matching conversation history yet',
            'Mulai sesi baru untuk membicarakan kondisi pikiranmu hari ini.' => 'Start a new session to share your state of mind today.',
            'Belum ada riwayat percakapan.' => 'No conversation history yet.',
            'Mulai sesi curhat pertamamu untuk melacak perjalanan emosimu.' => 'Start your first reflection session to track your emotional journey.',
            'Terakhir aktif' => 'Last active',
            'Jumlah pesan' => 'Message count',
            'Kategori' => 'Category',
            'Topik' => 'Topic',
            'Buka Sesi' => 'Open Session',

            // Admin Panel - Dashboard
            'Weisz Admin' => 'Weisz Admin',
            'Admin Dashboard' => 'Admin Dashboard',
            'Dashboard Administrator' => 'Administrator Dashboard',
            'Panel Kontrol Sistem Pakar' => 'Expert System Control Panel',
            'Ringkasan metrik sistem Weisz, aktivitas percakapan, dan status aturan pakar.' => 'Summary of Weisz system metrics, conversation activity, and expert rule status.',
            'Dashboard' => 'Dashboard',
            'Kategori & Topik' => 'Categories & Topics',
            'Knowledge Base' => 'Knowledge Base',
            'Expert Rules' => 'Expert Rules',
            'Data Profesional' => 'Professional Data',
            'Monitoring Sesi' => 'Session Monitoring',
            'Total Pengguna' => 'Total Users',
            'Total Sesi Chat' => 'Total Chat Sessions',
            'Total Topik' => 'Total Topics',
            'Aturan Aktif' => 'Active Rules',
            'Buka Aplikasi User' => 'Open User App',
            'Semua percakapan' => 'All conversations',
            'Pengguna Terdaftar' => 'Registered Users',
            'Akun pengguna reguler' => 'Regular user accounts',
            'Aturan Pakar Aktif' => 'Active Expert Rules',
            'Rule expert system' => 'Expert system rules',
            'Psikolog & Psikiater' => 'Psychologists & Psychiatrists',
            'Sinyal Krisis / Darurat' => 'Crisis / Emergency Signals',
            'Pemicu safety detector' => 'Safety detector triggers',
            'Topik Paling Banyak Dibicarakan' => 'Most Discussed Topics',
            'Kelola' => 'Manage',
            'Jumlah Sesi' => 'Session Count',
            'Belum ada statistik topik.' => 'No topic statistics yet.',
            'Pemicu Keselamatan (Safety Events)' => 'Safety Triggers (Safety Events)',
            'Pantau Semua' => 'Monitor All',
            'Tidak ada peristiwa pemicu keselamatan baru-baru ini. Semua obrolan berjalan normal.' => 'No recent safety trigger events. All chats are proceeding normally.',
            'Tingkat' => 'Level',
            'User' => 'User',
            'Anonim' => 'Anonymous',
            'Sesi Percakapan Terbaru' => 'Recent Conversation Sessions',
            'Lihat Semua di Monitoring' => 'View All in Monitoring',
            'Mulai' => 'Started',
            'Buka Chat' => 'Open Chat',

            // Admin Panel - Categories
            '16. Manajemen Kategori & Topik' => '16. Category & Topic Management',
            'Manajemen Hirarki Masalah' => 'Problem Hierarchy Management',
            'Kelola Kategori Utama &rarr; Subkategori &rarr; Topik Percakapan yang menjadi sumber konteks AI Weisz.' => 'Manage Main Categories &rarr; Subcategories &rarr; Conversation Topics as AI Weisz context sources.',
            'Tambah Kategori Utama' => 'Add Main Category',
            'Nama Kategori' => 'Category Name',
            'Contoh: Finansial & Karier' => 'Example: Finances & Career',
            'Deskripsi Singkat' => 'Short Description',
            'Penjelasan masalah yang dicakup...' => 'Explanation of concerns covered...',
            'Simpan Kategori' => 'Save Category',
            'Pilih Kategori Induk' => 'Select Parent Category',
            'Nama Subkategori' => 'Subcategory Name',
            'Contoh: Ekspektasi Karier' => 'Example: Career Expectations',
            'Penjelasan subkategori...' => 'Subcategory explanation...',
            'Simpan Subkategori' => 'Save Subcategory',
            'Tambah Topik Percakapan' => 'Add Conversation Topic',
            'Subkategori Induk' => 'Parent Subcategory',
            'Nama Topik' => 'Topic Name',
            'Contoh: Career Anxiety' => 'Example: Career Anxiety',
            'Konteks AI (Prompt Directive)' => 'AI Context (Prompt Directive)',
            'Panduan bagi AI saat topik ini terkunci...' => 'Guidance for AI when this topic is locked...',
            'Pilihan Chip Saat Bingung (Pisahkan Koma)' => 'Choice Chips When Confused (Comma Separated)',
            'Contoh: Takut nganggur, Bingung passion' => 'Example: Fear of unemployment, Confused about passion',
            'Simpan Topik' => 'Save Topic',
            'Struktur Data Hirarki Saat Ini' => 'Current Hierarchy Data Structure',
            'Hapus kategori ini beserta seluruh subkategori & topiknya?' => 'Delete this category along with all its subcategories & topics?',
            'Hapus Kategori' => 'Delete Category',
            'Subkategori' => 'Subcategory',
            'Hapus subkategori ini?' => 'Delete this subcategory?',
            'Hapus topik ini?' => 'Delete this topic?',
            'Belum ada topik di subkategori ini.' => 'No topics in this subcategory yet.',
            'Belum ada subkategori.' => 'No subcategories yet.',

            // Admin Panel - Knowledge Base
            '17. Knowledge Base — Weisz Admin' => '17. Knowledge Base — Weisz Admin',
            'Knowledge Base Pakar' => 'Expert Knowledge Base',
            'Kelola basis pengetahuan, bukti referensi psikologi, dan materi coping terstruktur yang digunakan sistem.' => 'Manage the knowledge base, psychological reference evidence, and structured coping materials used by the system.',
            'Tambah Entri Knowledge Base Baru' => 'Add New Knowledge Base Entry',
            'Judul Materi / Konsep' => 'Material / Concept Title',
            'Contoh: Intervensi Langkah Mikro untuk Prokrastinasi' => 'Example: Micro-step Intervention for Procrastination',
            'Status Publikasi' => 'Publication Status',
            'Published (Aktif Digunakan)' => 'Published (Actively Used)',
            'Draft (Disimpan Sementara)' => 'Draft (Temporarily Saved)',
            'Kategori Terkait (Opsional)' => 'Related Category (Optional)',
            '-- Semua Kategori / Umum --' => '-- All Categories / General --',
            'Topik Terkait (Opsional)' => 'Related Topic (Optional)',
            '-- Semua Topik / Umum --' => '-- All Topics / General --',
            'Konten Pengetahuan / Panduan Pakar' => 'Knowledge Content / Expert Guidance',
            'Tuliskan materi pemahaman, mekanisme koping, atau batasan refleksi...' => 'Write understanding material, coping mechanisms, or reflection boundaries...',
            'Sumber Referensi Ilmiah / Buku / Organisasi' => 'Scientific Reference Source / Book / Organization',
            'Contoh: APA Guidelines on Adolescent Stress & Weisz Lab (2017)' => 'Example: APA Guidelines on Adolescent Stress & Weisz Lab (2017)',
            'Simpan ke Knowledge Base' => 'Save to Knowledge Base',
            'Daftar Entri Knowledge Base' => 'Knowledge Base Entry List',
            'Judul & Referensi' => 'Title & Reference',
            'Kategori / Topik' => 'Category / Topic',
            'Semua Kategori' => 'All Categories',
            'Semua Topik' => 'All Topics',
            'Hapus entri knowledge base ini?' => 'Delete this knowledge base entry?',
            'Belum ada entri knowledge base yang tercatat.' => 'No recorded knowledge base entries yet.',

            // Admin Panel - Monitoring
            '20. Monitoring Percakapan & Keselamatan' => '20. Conversation & Safety Monitoring',
            'Monitoring Percakapan & Keselamatan' => 'Conversation & Safety Monitoring',
            'Pengawasan sesi aktif, sinyal yang terdeteksi oleh expert system, dan log penanganan sinyal krisis.' => 'Supervision of active sessions, signals detected by the expert system, and crisis signal response logs.',
            'Semua Sesi' => 'All Sessions',
            'Hanya Sinyal Krisis' => 'Crisis Signals Only',
            'Log Protokol Keselamatan (Crisis Alerts)' => 'Safety Protocol Logs (Crisis Alerts)',
            'Waktu' => 'Time',
            'User & Sesi' => 'User & Session',
            'Teks Pemicu' => 'Trigger Text',
            'Tindakan Sistem' => 'System Action',
            'Tingkat Risiko' => 'Risk Level',
            'Log Aktivitas Percakapan' => 'Conversation Activity Log',
            'ID Sesi' => 'Session ID',
            'Topik Terkunci' => 'Locked Topic',
            'Fase Percakapan' => 'Conversation Phase',
            'Sinyal Expert System' => 'Expert System Signals',
            'Buka' => 'Open',
            'Belum ada percakapan yang sesuai kriteria filter.' => 'No conversations match the filter criteria.',

            // Admin Panel - Professionals
            '19. Manajemen Data Profesional' => '19. Professional Data Management',
            'Manajemen Data Tenaga Profesional' => 'Healthcare Professional Data Management',
            'Kelola data rujukan psikolog dan psikiater berlisensi.' => 'Manage referral data for licensed psychologists and psychiatrists.',
            'Catatan: Halaman ini bukan akun login untuk profesional.' => 'Note: This page is not a login account for professionals.',
            'Tambah Data Profesional Baru' => 'Add New Professional Data',
            'Nama Lengkap & Gelar' => 'Full Name & Degree',
            'Contoh: dr. Amanda Pratama, Sp.KJ' => 'Example: Dr. Amanda Pratama, Sp.KJ',
            'Profesi' => 'Profession',
            'Contoh: Psikolog Klinis Dewasa / Psikiater' => 'Example: Adult Clinical Psychologist / Psychiatrist',
            'Fokus Spesialisasi' => 'Specialization Focus',
            'Contoh: Gangguan Cemas, Stres Akademik' => 'Example: Anxiety Disorders, Academic Stress',
            'Wilayah / Kota Layanan' => 'Service Region / City',
            'Contoh: Jakarta Selatan & Online' => 'Example: South Jakarta & Online',
            'Active (Tampil di Rekomendasi)' => 'Active (Shown in Recommendations)',
            'Inactive (Disembunyikan)' => 'Inactive (Hidden)',
            'Nomor Telepon / WhatsApp Konsultasi' => 'Consultation Phone / WhatsApp',
            'Alamat Email Resmi' => 'Official Email Address',
            'Alamat Lengkap / Lokasi Praktik Fisik' => 'Full Address / Physical Practice Location',
            'Nama klinik, jalan, gedung, nomor...' => 'Clinic name, street, building, number...',
            'Deskripsi Profil Singkat' => 'Short Profile Description',
            'Pengalaman, pendekatan terapi yang digunakan...' => 'Experience, therapeutic approaches used...',
            'Simpan Data Profesional' => 'Save Professional Data',
            'Data Profesional Terdaftar' => 'Registered Professional Data',
            'Nama & Profesi' => 'Name & Profession',
            'Spesialisasi & Lokasi' => 'Specialization & Location',
            'Kontak' => 'Contact',
            'Hapus data profesional ini?' => 'Delete this professional data?',
            'Belum ada data profesional.' => 'No professional data yet.',

            // Admin Panel - Expert Rules
            '18. Manajemen Aturan Expert System' => '18. Expert System Rule Management',
            'Manajemen Aturan Expert System' => 'Expert System Rule Management',
            'Kelola logika IF-THEN, kondisi sinyal emosi, dan tindakan otomatis (rekomendasi profesional, opsi stuck, intervensi keselamatan).' => 'Manage IF-THEN logic, emotional signal conditions, and automated actions (professional recommendation, stuck options, safety intervention).',
            'Tambah Aturan Expert System Baru' => 'Add New Expert System Rule',
            'Nama Aturan' => 'Rule Name',
            'Contoh: Tekanan Skripsi Kronis + Insomnia' => 'Example: Chronic Thesis Pressure + Insomnia',
            'Prioritas Evaluasi (0 - 100)' => 'Evaluation Priority (0 - 100)',
            'Topik Terkait' => 'Related Topic',
            '-- Aturan Global (Semua Topik) --' => '-- Global Rule (All Topics) --',
            'Tindakan Sistem (Action Type)' => 'System Action (Action Type)',
            'suggest_professional (Rekomendasikan Psikolog)' => 'suggest_professional (Recommend Psychologist)',
            'provide_chips (Tampilkan Chip Bantuan Saat Stuck)' => 'provide_chips (Display Help Chips When Stuck)',
            'trigger_safety (Aktivasi Protokol Krisis)' => 'trigger_safety (Activate Crisis Protocol)',
            'prompt_directive (Instruksi Tambahan AI)' => 'prompt_directive (AI Additional Instruction)',
            'Kondisi Logika IF (Single Condition)' => 'Logic IF Condition (Single Condition)',
            'Fact Key (Sinyal)' => 'Fact Key (Signal)',
            'Operator' => 'Operator',
            'Sama dengan (==)' => 'Equal to (==)',
            'Tidak sama dengan (!=)' => 'Not equal to (!=)',
            'Lebih besar sama dengan (>=)' => 'Greater than or equal to (>=)',
            'Mengandung kata (contains)' => 'Contains keyword (contains)',
            'Nilai Sinyal (Fact Value)' => 'Signal Value (Fact Value)',
            'Petunjuk Tambahan AI / Directive Payload' => 'Additional AI Instruction / Directive Payload',
            'Instruksi khusus bagi AI saat aturan ini terpenuhi...' => 'Special instructions for AI when this rule is met...',
            'Simpan Aturan Pakar' => 'Save Expert Rule',
            'Daftar Aturan Expert System Terdaftar' => 'Registered Expert System Rule List',
            'Cakupan' => 'Scope',
            'Global (Semua Sesi)' => 'Global (All Sessions)',
            'Aktif' => 'Active',
            'Non-aktif' => 'Inactive',
            'Hapus aturan expert system ini?' => 'Delete this expert system rule?',
            'Kondisi (IF)' => 'Condition (IF)',
            'Aksi Sistem (THEN)' => 'System Action (THEN)',

            // Admin Flash Messages & Actions
            'Tambah Kategori' => 'Add Category',
            'Tambah Subkategori' => 'Add Subcategory',
            'Tambah Topik' => 'Add Topic',
            'Tambah Pengetahuan' => 'Add Knowledge',
            'Tambah Aturan' => 'Add Rule',
            'Tambah Profesional' => 'Add Professional',
            'Edit' => 'Edit',
            'Simpan Data' => 'Save Data',
            'Aktifkan' => 'Enable',
            'Nonaktifkan' => 'Disable',
            'Konfirmasi Hapus' => 'Confirm Deletion',
            'Apakah kamu yakin ingin menghapus data ini?' => 'Are you sure you want to delete this data?',
            'Kategori utama berhasil ditambahkan.' => 'Main category added successfully.',
            'Subkategori berhasil ditambahkan.' => 'Subcategory added successfully.',
            'Topik percakapan baru berhasil ditambahkan.' => 'New conversation topic added successfully.',
            'Kategori berhasil dihapus.' => 'Category deleted successfully.',
            'Subkategori berhasil dihapus.' => 'Subcategory deleted successfully.',
            'Topik berhasil dihapus.' => 'Topic deleted successfully.',
            'Entri Knowledge Base berhasil disimpan.' => 'Knowledge Base entry saved successfully.',
            'Entri Knowledge Base berhasil dihapus.' => 'Knowledge Base entry deleted successfully.',
            'Aturan Expert System berhasil dibuat.' => 'Expert System rule created successfully.',
            'Status keaktifan aturan diperbarui.' => 'Rule active status updated successfully.',
            'Aturan Expert System berhasil dihapus.' => 'Expert System rule deleted successfully.',
            'Data profesional berhasil didaftarkan.' => 'Professional data registered successfully.',
            'Data profesional berhasil diperbarui.' => 'Professional data updated successfully.',
            'Data profesional berhasil dihapus.' => 'Professional data deleted successfully.',

            // Footer & Crisis Notices
            'Punya pertanyaan, kendala, atau ingin memberikan masukan? Hubungi kami melalui surat elektronik:' => 'Have questions, issues, or feedback? Reach out to us via email:',
            'Layanan Bantuan Krisis Emosional 24 Jam' => '24-Hour Emotional Crisis Helpline',
            'Jika Anda atau orang terdekat sedang berada dalam krisis emosional darurat atau dorongan menyakiti diri, mohon segera hubungi' => 'If you or someone close is facing an emergency emotional crisis or urge to self-harm, please immediately contact',
            'Hotline Kesehatan Jiwa Kemenkes 119 (ext. 8)' => 'Ministry of Health Mental Health Hotline 119 (ext. 8)',
            'atau' => 'or',
            'Halo Kemenkes 1500-567' => 'Halo Kemenkes 1500-567',
            'Weisz adalah pendamping refleksi dan evaluasi pakar terpandu, bukan pengganti intervensi medis darurat.' => 'Weisz is a guided reflection and expert evaluation companion, not a substitute for emergency medical intervention.',
            'Dirancang secara tenang, aman, dan tanpa penghakiman.' => 'Designed calmly, safely, and free from judgment.',
        ];
    }
}
