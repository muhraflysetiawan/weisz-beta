<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ConversationSummary;

class SummaryGenerator
{
    /**
     * Generate or update structured conversation summary.
     */
    public function generate(ChatSession $session): ConversationSummary
    {
        $topicName = $session->topic->name ?? 'Kondisi Personal';
        $userMessages = $session->messages()->where('sender', 'user')->pluck('message')->toArray();
        $signals = $session->context->signals ?? [];

        $userSharedSummary = ! empty($userMessages)
            ? implode(' ', array_slice($userMessages, 0, 3))
            : 'Pengguna mendiskusikan tantangan yang dihadapi seputar '.$topicName;

        if (mb_strlen($userSharedSummary) > 280) {
            $userSharedSummary = mb_substr($userSharedSummary, 0, 277).'...';
        }

        // Possible factors based on signals & topic
        $factors = [];
        if (! empty($signals['academic_pressure'])) {
            $factors[] = 'Tekanan tenggat waktu dan ekspektasi studi yang tinggi.';
        }
        if (! empty($signals['sleep_problem'])) {
            $factors[] = 'Pola istirahat yang tidak konsisten dan overthinking di malam hari.';
        }
        if (! empty($signals['emotional_exhaustion'])) {
            $factors[] = 'Kelelahan kognitif dan minimnya jeda pemulihan diri.';
        }
        if (empty($factors)) {
            $factors[] = 'Dinamika adaptasi terhadap situasi dan tuntutan sehari-hari.';
        }

        // Suggested next steps
        $steps = [
            '1. Membagi beban pikiran menjadi langkah-langkah mikro yang terukur.',
            '2. Menetapkan batasan waktu istirahat yang jelas tanpa rasa bersalah.',
            '3. Melakukan journaling singkat untuk melepaskan beban kognitif sebelum tidur.',
        ];

        // Professional support
        $supportText = ! empty($signals['professional_support']) || $session->recommendations()->exists()
            ? 'Pertimbangkan untuk berdiskusi dengan psikolog klinis atau konselor profesional guna memperoleh strategi coping yang dipersonalisasi.'
            : 'Dukungan mandiri dan berbicara dengan orang terpercaya sudah merupakan langkah awal yang baik. Bantuan profesional selalu tersedia jika sewaktu-waktu dibutuhkan.';

        return ConversationSummary::updateOrCreate(
            ['session_id' => $session->id],
            [
                'main_topic' => $topicName,
                'user_shared' => $userSharedSummary,
                'possible_factors' => implode("\n", $factors),
                'suggested_steps' => implode("\n", $steps),
                'professional_support' => $supportText,
                'summary_text' => "Sesi refleksi untuk topik {$topicName} telah diselesaikan dengan penekanan pada pemahaman diri dan langkah praktis sehari-hari.",
            ]
        );
    }
}
