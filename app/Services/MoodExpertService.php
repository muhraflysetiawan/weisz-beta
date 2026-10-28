<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatSession;

class MoodExpertService
{
    /**
     * The 5 expert system mood / expression profiles.
     *
     * @return array<string, array{
     *     key: string,
     *     emoji: string,
     *     label: string,
     *     desc: string,
     *     tone_guidance: string,
     *     keywords: list<string>
     * }>
     */
    public static function getMoods(): array
    {
        return [
            'overwhelmed' => [
                'key' => 'overwhelmed',
                'emoji' => '😭',
                'label' => 'Hancur & Terpuruk',
                'desc' => 'Beban terasa sangat berat dan melelahkan',
                'tone_guidance' => 'Pengguna saat ini sedang berada dalam kondisi emosi yang sangat terpuruk, hancur, atau menangis. Respon dengan nada yang ekstra lembut, perlahan, penuh kehangatan, dan tidak terburu-buru memberikan nasihat teknis. Validasi beban perasaannya secara mendalam dan berikan rasa aman bahwa ia tidak sendirian.',
                'keywords' => ['hancur', 'terpuruk', 'nangis', 'menangis', 'gak kuat', 'nggak kuat', 'gak sanggup', 'drop parah', 'sesak'],
            ],
            'anxious' => [
                'key' => 'anxious',
                'emoji' => '😰',
                'label' => 'Cemas & Gelisah',
                'desc' => 'Pikiran berputar cepat dan overthinking',
                'tone_guidance' => 'Pengguna sedang mengalami kecemasan, gelisah, panik, atau overthinking yang intens. Gunakan gaya bicara yang menstabilkan ritme (grounding), bantu mengurai rasa takutnya satu per satu dengan tenang, dan ingatkan untuk bernapas perlahan tanpa memperkeruh kecemasannya.',
                'keywords' => ['cemas', 'gelisah', 'panik', 'overthinking', 'takut banget', 'gemetar', 'deg-degan', 'waswas', 'khawatir'],
            ],
            'sad' => [
                'key' => 'sad',
                'emoji' => '😞',
                'label' => 'Sedih & Murung',
                'desc' => 'Merasa down, kecewa, atau kehilangan energi',
                'tone_guidance' => 'Pengguna sedang merasa sedih, murung, kecewa, atau kehilangan semangat. Hadirlah sebagai pendengar yang berempati tinggi tanpa menggurui atau meremehkan perasaannya. Berikan dukungan moral yang hangat selayaknya sahabat dekat.',
                'keywords' => ['sedih', 'murung', 'down', 'kecewa', 'capek batin', 'lelah batin', 'sendiri', 'hampa hati'],
            ],
            'numb' => [
                'key' => 'numb',
                'emoji' => '😐',
                'label' => 'Hampa & Bingung',
                'desc' => 'Mati rasa, kosong, atau bingung arah',
                'tone_guidance' => 'Pengguna sedang merasa hampa, mati rasa (numb), bingung, atau datar. Ajak berefleksi secara perlahan dengan pertanyaan yang ringan, jangan memaksa mereka langsung mengekspresikan emosi besar, dan bantu mereka menemukan kembali titik awal yang ingin diurai.',
                'keywords' => ['hampa', 'bingung', 'mati rasa', 'numb', 'kosong', 'datar', 'nggak tahu mau apa', 'blank'],
            ],
            'calm' => [
                'key' => 'calm',
                'emoji' => '😌',
                'label' => 'Butuh Ruang Cerita',
                'desc' => 'Ingin refleksi terarah dengan tenang',
                'tone_guidance' => 'Pengguna dalam kondisi yang relatif tenang dan siap untuk merefleksikan masalahnya secara terarah. Ajak berdialog secara lebih mendalam, bantu mengeksplorasi sudut pandang objektif, dan diskusikan langkah-langkah solutif atau pemaknaan positif secara santai.',
                'keywords' => ['tenang', 'refleksi', 'cerita', 'santai', 'mau sharing', 'curhat', 'mencari solusi'],
            ],
        ];
    }

    /**
     * Get specific mood metadata by key.
     *
     * @return array{key: string, emoji: string, label: string, desc: string, tone_guidance: string, keywords: list<string>}|null
     */
    public static function getMood(?string $key): ?array
    {
        if (! $key) {
            return null;
        }

        return self::getMoods()[$key] ?? null;
    }

    /**
     * Check if mood key is valid.
     */
    public static function isValidMood(?string $key): bool
    {
        return $key !== null && isset(self::getMoods()[$key]);
    }

    /**
     * Get AI tone guidance prompt for a given mood.
     */
    public static function getToneGuidance(?string $key): string
    {
        $mood = self::getMood($key) ?? self::getMood('calm');

        return $mood['tone_guidance'];
    }

    /**
     * Detect if user text indicates an emotional shift.
     */
    public static function detectMoodShift(string $text, ?string $currentMood = null): ?string
    {
        $lower = mb_strtolower($text);

        // 1. Overwhelmed
        if (preg_match('/(nangis|menangis|hancur|terpuruk|gak kuat|nggak kuat|gak sanggup|tidak sanggup|drop parah|drop banget|putus asa|mau menyerah|sesak)/i', $lower)) {
            if ($currentMood !== 'overwhelmed') {
                return 'overwhelmed';
            }
        }

        // 2. Anxious
        if (preg_match('/(panik|takut|cemas|gelisah|overthinking|gemetar|berdebar|waswas|khawatir)/i', $lower)) {
            if ($currentMood !== 'anxious') {
                return 'anxious';
            }
        }

        // 3. Sad
        if (preg_match('/(sedih|murung|kecewa|terluka|capek batin|lelah batin|down|hampa hati)/i', $lower)) {
            if ($currentMood !== 'sad') {
                return 'sad';
            }
        }

        // 4. Numb
        if (preg_match('/(mati rasa|hampa|kosong|bingung mau apa|blank)/i', $lower)) {
            if ($currentMood !== 'numb') {
                return 'numb';
            }
        }

        // 5. Calm / Relieved
        if (preg_match('/(lega|tenang|merasa baikan|udah baikan|terbantu|plong|nyaman)/i', $lower)) {
            if ($currentMood !== 'calm') {
                return 'calm';
            }
        }

        return null;
    }

    /**
     * Check if the session should trigger an in-chat Mood Check-in bubble.
     * Rules:
     * - Trigger every 5 to 7 user messages sent.
     * - OR triggered when a dynamic mood shift is detected in user text.
     * - Do not trigger too frequently (at least 4 messages gap since last check-in).
     */
    public static function shouldTriggerCheckin(ChatSession $session, string $latestMessage): bool
    {
        $userMsgCount = ChatMessage::where('session_id', $session->id)
            ->where('sender', 'user')
            ->count();

        $lastCheckin = ChatMessage::where('session_id', $session->id)
            ->where('sender', 'assistant')
            ->where('metadata->mood_checkin', true)
            ->latest('id')
            ->first();

        $messagesSinceLastCheckin = null;
        if ($lastCheckin) {
            $messagesSinceLastCheckin = ChatMessage::where('session_id', $session->id)
                ->where('id', '>', $lastCheckin->id)
                ->where('sender', 'user')
                ->count();
        }

        $currentMood = $session->context?->context_data['current_mood']
            ?? ($session->context?->signals['user_mood'] ?? null);

        $shift = self::detectMoodShift($latestMessage, $currentMood);

        return self::evaluateCheckinRule($userMsgCount, $messagesSinceLastCheckin, $shift);
    }

    /**
     * Pure rule evaluator for mood check-in triggers.
     */
    public static function evaluateCheckinRule(int $userMsgCount, ?int $messagesSinceLastCheckin, ?string $detectedShift): bool
    {
        // Dynamic mood shift takes immediate priority if user has started talking
        if ($detectedShift !== null && $userMsgCount >= 1) {
            return true;
        }

        // Need at least 3 user messages to begin periodic check-ins
        if ($userMsgCount < 3) {
            return false;
        }

        if ($messagesSinceLastCheckin !== null) {
            // At least 4 user messages must pass before triggering another check-in
            if ($messagesSinceLastCheckin < 4) {
                return false;
            }

            // Interval rule: Every 5-7 user messages (e.g. 5 or more messages since last checkin)
            return $messagesSinceLastCheckin >= 5;
        }

        // First periodic checkin: trigger between 5-7 user messages
        return $userMsgCount >= 5;
    }
}
