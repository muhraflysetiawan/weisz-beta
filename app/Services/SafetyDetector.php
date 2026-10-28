<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\RiskAssessment;

class SafetyDetector
{
    /**
     * Critical keywords and phrases that trigger safety protocol.
     *
     * @var list<string>
     */
    protected array $crisisKeywords = [
        'bunuh diri',
        'pengen mati',
        'ingin mati',
        'akhiri hidup',
        'mengakhiri hidup',
        'melukai diri',
        'menyakiti diri',
        'sayat tangan',
        'sayat pergelangan',
        'potong nadi',
        'self harm',
        'self-harm',
        'suicide',
        'mau mati',
        'tidak ingin hidup',
        'tak ingin hidup',
        'capek hidup pengen hilang',
        'pengen hilang selamanya',
        'pengen ngilang dari dunia',
        'lebih baik aku mati',
        'tidak ada alasan untuk hidup',
        'tak sanggup hidup lagi',
        'menyerah hidup',
        'minum racun',
        'gantung diri',
        'loncat dari gedung',
        'overdosis',
    ];

    /**
     * Evaluate if the message contains immediate critical risks.
     *
     * @return array{is_critical: bool, risk_level: string, trigger_text: ?string, safety_action: ?string, response_message: ?string}
     */
    public function analyze(string $text, ?ChatSession $session = null): array
    {
        $normalized = mb_strtolower(trim($text));

        foreach ($this->crisisKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                $safetyAction = 'Arahkan ke layanan krisis darurat (Layanan SEJIWA 119 ext 8 / Halo Kemenkes 1500-567) dan dorong hubungi orang terpercaya di sekitar saat ini.';

                if ($session) {
                    RiskAssessment::create([
                        'session_id' => $session->id,
                        'trigger_text' => $text,
                        'risk_level' => 'critical',
                        'safety_action' => $safetyAction,
                        'is_acknowledged' => false,
                        'triggered_at' => now(),
                    ]);
                }

                $responseMessage = "Aku sangat peduli dengan apa yang kamu rasakan saat ini, dan kamu tidak harus menanggung perasaan seberat ini sendirian.\n\nKarena ini situasi yang sangat penting bagi keselamatanmu, tolong segera hubungi layanan dukungan krisis atau orang terdekat yang kamu percaya:\n\nLayanan SEJIWA Kemenkes di 119 ekstensi 8 (bebas pulsa), Halo Kemenkes di 1500-567 (24 Jam), atau Yayasan Pulih di 0811-8436-633.\n\nJika ada orang terdekat di sekitarmu sekarang (keluarga, teman, atau kerabat), tolong beritahu mereka apa yang sedang kamu rasakan. Kamu berharga, dan bantuan profesional siap mendengarkanmu tanpa menghakimi.";

                return [
                    'is_critical' => true,
                    'risk_level' => 'critical',
                    'trigger_text' => $text,
                    'safety_action' => $safetyAction,
                    'response_message' => $responseMessage,
                ];
            }
        }

        return [
            'is_critical' => false,
            'risk_level' => 'normal',
            'trigger_text' => null,
            'safety_action' => null,
            'response_message' => null,
        ];
    }
}
