<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ConversationContext;
use App\Models\ConversationSummary;
use App\Models\ExpertRule;
use App\Models\KnowledgeBase;
use App\Models\ProblemTopic;
use App\Models\Professional;
use App\Models\RuleAction;
use App\Models\RuleCondition;
use App\Models\SessionRecommendation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@weisz.test'],
            [
                'name' => 'Admin Weisz',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'user@weisz.test'],
            [
                'name' => 'Alex Pratama',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // 2. Seed Problem Categories, Subcategories, & Topics (147 Topics across 21 Subcategories)
        $this->call(CounselingTaxonomySeeder::class);

        // 3. Seed Knowledge Base Entries
        $academicStressTopic = ProblemTopic::where('slug', 'tekanan-pekan-ujian-utsuas-ketakutan-nilai-anjlok')->first();
        if ($academicStressTopic) {
            KnowledgeBase::updateOrCreate(
                ['title' => 'Pendekatan Mengurai Beban Akademik'],
                [
                    'category_id' => $academicStressTopic->subcategory->category_id,
                    'subcategory_id' => $academicStressTopic->subcategory_id,
                    'topic_id' => $academicStressTopic->id,
                    'content' => 'Beban akademik pada mahasiswa dan pelajar seringkali bersumber dari kombinasi deadline ganda, fear of failure, dan kurangnya jeda terencana. Langkah awal yang terbukti membantu adalah mendaftar semua tugas dalam matriks prioritas dan mengambil langkah mikro (5-minute rule).',
                    'reference_source' => 'American Psychological Association (APA) - Stress in America & Weisz Lab Studies',
                    'status' => 'published',
                ]
            );

            KnowledgeBase::updateOrCreate(
                ['title' => 'Sleep Hygiene & Penurunan Kecemasan Malam Hari'],
                [
                    'category_id' => $academicStressTopic->subcategory->category_id,
                    'subcategory_id' => $academicStressTopic->subcategory_id,
                    'topic_id' => $academicStressTopic->id,
                    'content' => 'Kualitas tidur berkorelasi langsung dengan regulasi emosi korteks prefrontal. Hindari layar biru 45 menit sebelum tidur dan gunakan teknik braindump (menuliskan kekhawatiran di kertas) untuk melepaskan beban kognitif sebelum tidur.',
                    'reference_source' => 'Sleep Foundation & Cognitive Behavioral Framework',
                    'status' => 'published',
                ]
            );
        }

        // 4. Seed Expert Rules
        $rule1 = ExpertRule::updateOrCreate(
            ['name' => 'Stres Akademik Akut + Gangguan Tidur Kronis'],
            [
                'topic_id' => $academicStressTopic?->id,
                'description' => 'Jika pengguna mengalami tekanan akademik berat disertai gangguan pola tidur berkelanjutan, sarankan mempertimbangkan dukungan konseling profesional.',
                'priority' => 10,
                'is_active' => true,
            ]
        );

        RuleCondition::firstOrCreate(
            ['expert_rule_id' => $rule1->id, 'fact_key' => 'academic_pressure'],
            ['operator' => '==', 'fact_value' => 'high']
        );
        RuleCondition::firstOrCreate(
            ['expert_rule_id' => $rule1->id, 'fact_key' => 'sleep_problem'],
            ['operator' => '==', 'fact_value' => 'true']
        );

        RuleAction::firstOrCreate(
            ['expert_rule_id' => $rule1->id, 'action_type' => 'suggest_professional'],
            [
                'action_payload' => [
                    'directive' => 'Jelaskan dengan hangat bahwa beban yang dialami cukup berat dan menguras tenaga. Tawarkan referensi psikolog profesional di database Weisz jika mereka ingin berbicara lebih dalam secara resmi.',
                    'signal_trigger' => 'professional_support',
                ],
            ]
        );

        // Rule 2: User Bingung / Stuck (Global)
        $rule2 = ExpertRule::updateOrCreate(
            ['name' => 'Deteksi User Bingung atau Stuck'],
            [
                'topic_id' => null, // Global
                'description' => 'Jika pengguna mengirim pesan pendek berulang kali atau menyatakan bingung, berikan pilihan chip kontekstual.',
                'priority' => 5,
                'is_active' => true,
            ]
        );

        RuleCondition::firstOrCreate(
            ['expert_rule_id' => $rule2->id, 'fact_key' => 'stuck_detected'],
            ['operator' => '==', 'fact_value' => 'true']
        );

        RuleAction::firstOrCreate(
            ['expert_rule_id' => $rule2->id, 'action_type' => 'provide_chips'],
            [
                'action_payload' => [
                    'directive' => 'Bantu pengguna memilih fokus obrolan dengan menyajikan 2-4 pilihan topik ringan yang relevan.',
                ],
            ]
        );

        // Rule 3: Safety Signal (Global)
        $rule3 = ExpertRule::updateOrCreate(
            ['name' => 'Deteksi Sinyal Darurat & Krisis'],
            [
                'topic_id' => null,
                'description' => 'Mengidentifikasi potensi bahaya terhadap diri sendiri atau situasi darurat dan mengaktifkan respons keselamatan seketika.',
                'priority' => 100,
                'is_active' => true,
            ]
        );

        RuleCondition::firstOrCreate(
            ['expert_rule_id' => $rule3->id, 'fact_key' => 'critical_safety'],
            ['operator' => '==', 'fact_value' => 'true']
        );

        RuleAction::firstOrCreate(
            ['expert_rule_id' => $rule3->id, 'action_type' => 'trigger_safety'],
            [
                'action_payload' => [
                    'risk_level' => 'critical',
                    'hotline' => '119 ext 8 (Layanan SEJIWA) atau Halo Kemenkes 1500-567',
                    'directive' => 'Prioritaskan keselamatan pengguna. Ajak mereka berbicara ke orang terdekat atau hubungi layanan bantuan darurat.',
                ],
            ]
        );

        // 5. Seed Professionals (Psikolog, Psikiater, & Konselor di Karawang, Bekasi, Purwakarta, dll)
        $this->call(ProfessionalSeeder::class);

        // 6. Seed Sample Completed Conversation for Demo User
        if ($academicStressTopic) {
            $sampleSession = ChatSession::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'topic_id' => $academicStressTopic->id,
                    'title' => 'Sesi Konsultasi: Beban Tugas & Skripsi',
                ],
                [
                    'category_id' => $academicStressTopic->subcategory->category_id,
                    'subcategory_id' => $academicStressTopic->subcategory_id,
                    'status' => 'completed',
                    'started_at' => now()->subDays(2),
                    'ended_at' => now()->subDays(2)->addMinutes(18),
                ]
            );

            ConversationContext::firstOrCreate(
                ['session_id' => $sampleSession->id],
                [
                    'current_state' => 'COMPLETED',
                    'signals' => [
                        'academic_pressure' => 'high',
                        'sleep_problem' => 'true',
                        'identified_stressor' => 'deadline_tugas_akhir',
                    ],
                    'context_data' => [
                        'topic_name' => 'Academic Stress',
                        'locked' => true,
                    ],
                ]
            );

            ChatMessage::firstOrCreate(
                ['session_id' => $sampleSession->id, 'sender' => 'assistant'],
                [
                    'message' => 'Hai Alex! Aku Weisz. Kita sudah mengonfirmasi topik obrolan kita tentang Academic Stress. Apa yang akhir-akhir ini paling bikin kamu ngerasa terbebani dalam perkuliahan?',
                    'metadata' => ['step' => 'greeting'],
                    'created_at' => now()->subDays(2),
                ]
            );

            ChatMessage::firstOrCreate(
                ['session_id' => $sampleSession->id, 'sender' => 'user'],
                [
                    'message' => 'Lagi pusing banget kak, tugas numpuk barengan sama revisi skripsi yang nggak kelar-kelar. Mau mulai ngerjain tapi rasanya overwhelmed, akhirnya tiap malem nggak bisa tidur mikirin deadline.',
                    'metadata' => ['sentiment' => 'stressed'],
                    'created_at' => now()->subDays(2)->addMinutes(2),
                ]
            );

            ChatMessage::firstOrCreate(
                ['session_id' => $sampleSession->id, 'sender' => 'assistant'],
                [
                    'message' => 'Wajar banget kalau kamu ngerasa overwhelmed saat tugas dan revisi datang bersamaan. Beban kognitif yang numpuk memang sering bikin tubuh kita tetap siaga di malam hari, jadi susah tidur. Kalau boleh tahu, dari dua hal itu (tugas atau revisi), mana yang paling bikin kamu cemas saat mau tidur?',
                    'metadata' => ['step' => 'clarifying'],
                    'created_at' => now()->subDays(2)->addMinutes(4),
                ]
            );

            ChatMessage::firstOrCreate(
                ['session_id' => $sampleSession->id, 'sender' => 'user'],
                [
                    'message' => 'Revisi skripsi sih, dosen pembimbing nuntut cepet tapi responnya sering bikin down.',
                    'created_at' => now()->subDays(2)->addMinutes(6),
                ]
            );

            ChatMessage::firstOrCreate(
                ['session_id' => $sampleSession->id, 'sender' => 'assistant'],
                [
                    'message' => 'Terima kasih sudah mau berbagi, Alex. Tekanan dari ekspektasi pembimbing memang bisa sangat menguras emosi. Mengingat hal ini sudah mempengaruhi pola tidurmu beberapa minggu, sangat wajar untuk mempertimbangkan berbicara dengan profesional jika bebannya terasa semakin berat. Kita bisa mulai dengan memecah revisi menjadi langkah mikro 15 menit terlebih dahulu.',
                    'metadata' => ['step' => 'suggesting'],
                    'created_at' => now()->subDays(2)->addMinutes(8),
                ]
            );

            ConversationSummary::firstOrCreate(
                ['session_id' => $sampleSession->id],
                [
                    'main_topic' => 'Academic Stress (Beban Kuliah & Revisi Skripsi)',
                    'user_shared' => 'Pengguna menceritakan rasa overwhelmed akibat penumpukan tugas kuliah dan revisi skripsi, ditambah tekanan dari respon dosen pembimbing yang memicu kecemasan dan gangguan tidur di malam hari.',
                    'possible_factors' => 'Kombinasi ekspektasi akademik tinggi, komunikasi menantang dengan dosen pembimbing, serta penumpukan beban kognitif sebelum waktu istirahat tidur.',
                    'suggested_steps' => '1. Menerapkan teknik langkah mikro (memecah revisi skripsi menjadi sub-tugas berdurasi 15-20 menit).\n2. Memisahkan area istirahat dari meja kerja atau laptop.\n3. Melakukan "braindump" catatan singkat sebelum tidur agar beban pikiran tidak terus berputar.',
                    'professional_support' => 'Direkomendasikan berkonsultasi dengan Psikolog Klinis jika gangguan pola tidur dan rasa cemas berlanjut lebih dari 2 minggu.',
                    'summary_text' => 'Percakapan berfokus pada manajemen stres akademik dan pola istirahat. Langkah praktis awal telah disusun untuk meredakan rasa kewalahan.',
                ]
            );

            $proRec = Professional::first();
            if ($proRec) {
                SessionRecommendation::firstOrCreate(
                    [
                        'session_id' => $sampleSession->id,
                        'professional_id' => $proRec->id,
                    ],
                    [
                        'reason' => 'Indikasi beban stres akademik yang mengganggu pola tidur dan membutuhkan strategi coping terarah.',
                        'notes' => 'Rekomendasi profesional klinis dari database resmi Weisz.',
                    ]
                );
            }
        }
    }
}
