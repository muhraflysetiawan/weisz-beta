<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ConversationContext;
use App\Models\ExpertRule;
use App\Models\Professional;
use App\Models\SessionRecommendation;

class ExpertSystemEngine
{
    /**
     * Evaluate conversation and apply expert rules.
     *
     * @param  array<string, mixed>  $additionalFacts
     * @return array{
     *     signals: array<string, mixed>,
     *     actions: list<array<string, mixed>>,
     *     conversation_state: string,
     *     suggested_chips: list<string>,
     *     recommend_professional: bool,
     *     safety_alert: bool
     * }
     */
    public function evaluate(ChatSession $session, string $latestUserMessage, array $additionalFacts = []): array
    {
        $context = $session->context ?? ConversationContext::create([
            'session_id' => $session->id,
            'current_state' => 'EXPLORING',
            'signals' => [],
            'context_data' => ['topic_id' => $session->topic_id],
        ]);

        $existingSignals = $context->signals ?? [];

        // 1. Extract dynamic facts from message text
        $extractedFacts = $this->extractFactsFromText($latestUserMessage, $session);
        $mergedFacts = array_merge($existingSignals, $additionalFacts, $extractedFacts);

        // 2. Fetch active rules (topic-specific rules + global rules)
        $rules = ExpertRule::with(['conditions', 'actions'])
            ->where('is_active', true)
            ->where(function ($query) use ($session) {
                $query->where('topic_id', $session->topic_id)
                    ->orWhereNull('topic_id');
            })
            ->orderByDesc('priority')
            ->get();

        $triggeredActions = [];
        $recommendProfessional = false;
        $safetyAlert = false;
        $suggestedChips = [];

        // Check if professional recommendation was already given in this session
        $alreadyRecommended = ($existingSignals['professional_support'] ?? '') === 'suggested'
            || $session->recommendations()->exists()
            || $session->messages()->where('sender', 'assistant')->where('metadata->recommend_professional', true)->exists();

        $currentMessageRequestedPro = ! empty($extractedFacts['need_professional']) || ! empty($extractedFacts['psychiatrist_support_needed']);

        foreach ($rules as $rule) {
            if ($this->checkRuleConditions($rule, $mergedFacts)) {
                foreach ($rule->actions as $action) {
                    $triggeredActions[] = [
                        'rule_id' => $rule->id,
                        'rule_name' => $rule->name,
                        'type' => $action->action_type,
                        'payload' => $action->action_payload,
                    ];

                    if ($action->action_type === 'suggest_professional') {
                        if (! $alreadyRecommended || $currentMessageRequestedPro) {
                            $recommendProfessional = true;
                            $mergedFacts['professional_support'] = 'suggested';
                            $this->attachProfessionalRecommendation($session);
                        }
                    }

                    if ($action->action_type === 'provide_chips') {
                        $suggestedChips = $session->topic->suggested_chips ?? [
                            'Mau cerita lebih detail',
                            'Pengen tahu saran praktis',
                            'Bingung mau mulai dari mana',
                        ];
                    }

                    if ($action->action_type === 'trigger_safety') {
                        $safetyAlert = true;
                    }
                }
            }
        }

        // Automatic detection for psychiatric / professional recommendation
        if (! $alreadyRecommended) {
            if ($currentMessageRequestedPro || (! empty($mergedFacts['emotional_exhaustion']) && ! empty($mergedFacts['sleep_problem']))) {
                $recommendProfessional = true;
                $mergedFacts['professional_support'] = 'suggested';
                $this->attachProfessionalRecommendation($session);
            }
        } elseif ($currentMessageRequestedPro) {
            $recommendProfessional = true;
            $mergedFacts['professional_support'] = 'suggested';
            $this->attachProfessionalRecommendation($session);
        }

        // Determine conversation state transition
        $messageCount = $session->messages()->count();
        $currentState = $context->current_state;

        if ($safetyAlert) {
            $currentState = 'SAFETY_ALERT';
        } elseif ($recommendProfessional) {
            $currentState = 'RECOMMENDATION';
        } elseif (! empty($suggestedChips)) {
            $currentState = 'SUGGESTING';
        } elseif ($messageCount >= 6) {
            $currentState = 'SUMMARY_READY';
        } elseif ($messageCount >= 2) {
            $currentState = 'CLARIFYING';
        } else {
            $currentState = 'EXPLORING';
        }

        // If rules did not explicitly set chips, generate dynamic contextual chips
        if (empty($suggestedChips)) {
            $suggestedChips = $this->generateContextualChips($currentState, $mergedFacts, $session);
        }

        // Save updated context (strip per-message ephemeral triggers so they don't leak into future turns)
        $persistentSignals = $mergedFacts;
        unset(
            $persistentSignals['need_professional'],
            $persistentSignals['psychiatrist_support_needed'],
            $persistentSignals['topic_shift_requested'],
            $persistentSignals['is_off_topic']
        );

        if ($recommendProfessional || $alreadyRecommended) {
            $persistentSignals['professional_support'] = 'suggested';
        }

        $context->update([
            'current_state' => $currentState,
            'signals' => $persistentSignals,
        ]);

        return [
            'signals' => $mergedFacts,
            'actions' => $triggeredActions,
            'conversation_state' => $currentState,
            'suggested_chips' => $suggestedChips,
            'recommend_professional' => $recommendProfessional,
            'safety_alert' => $safetyAlert,
        ];
    }

    /**
     * Check if all conditions for an expert rule are met.
     *
     * @param  array<string, mixed>  $facts
     */
    protected function checkRuleConditions(ExpertRule $rule, array $facts): bool
    {
        if ($rule->conditions->isEmpty()) {
            return false;
        }

        foreach ($rule->conditions as $cond) {
            $key = $cond->fact_key;
            $val = $facts[$key] ?? null;

            if ($val === null) {
                return false;
            }

            $expected = $cond->fact_value;
            $operator = $cond->operator ?: '==';

            $matched = match ($operator) {
                '==' => (string) $val === (string) $expected,
                '!=' => (string) $val !== (string) $expected,
                '>=' => (float) $val >= (float) $expected,
                '<=' => (float) $val <= (float) $expected,
                'contains' => str_contains(mb_strtolower((string) $val), mb_strtolower((string) $expected)),
                default => (string) $val === (string) $expected,
            };

            if (! $matched) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extract key semantic facts from user message.
     *
     * @return array<string, mixed>
     */
    protected function extractFactsFromText(string $text, ChatSession $session): array
    {
        $normalized = mb_strtolower(trim($text));
        $facts = [];

        // Check if user is stuck or confused
        $stuckPhrases = [
            'bingung',
            'nggak tahu',
            'ngga tau',
            'tidak tahu',
            'gatau',
            'mau cerita apa',
            'blank',
            'pusing mau mulai',
        ];

        $isStuck = false;
        foreach ($stuckPhrases as $phrase) {
            if (str_contains($normalized, $phrase)) {
                $isStuck = true;
                break;
            }
        }

        // If message is very short (< 8 chars) after prior messages
        if (mb_strlen($normalized) <= 8 && in_array($normalized, ['ya', 'nggak', 'ga', 'iya', 'capek', 'entah', 'hmm'])) {
            $isStuck = true;
        }

        if ($isStuck) {
            $facts['stuck_detected'] = 'true';
        }

        // Academic pressure
        if (str_contains($normalized, 'tugas') || str_contains($normalized, 'skripsi') || str_contains($normalized, 'ujian') || str_contains($normalized, 'ipk') || str_contains($normalized, 'dosen')) {
            $facts['academic_pressure'] = 'high';
        }

        // Sleep problems
        if (str_contains($normalized, 'tidur') || str_contains($normalized, 'insomnia') || str_contains($normalized, 'begadang') || str_contains($normalized, 'susah tidur')) {
            $facts['sleep_problem'] = 'true';
        }

        // Kelelahan emosional / burnout
        if (str_contains($normalized, 'lelah') || str_contains($normalized, 'capek mental') || str_contains($normalized, 'hampa') || str_contains($normalized, 'burnout')) {
            $facts['emotional_exhaustion'] = 'high';
        }

        // Off-topic / topic shift intent detection
        $topicShiftPhrases = [
            'ganti topik',
            'topik lain',
            'topik baru',
            'pindah topik',
            'bahas hal lain',
            'bahas yang lain',
            'ngomongin yang lain',
            'ngomongin hal lain',
            'mau cerita yang lain',
            'mau cerita hal lain',
            'curhat hal lain',
            'mau curhat yang lain',
            'bukan soal ini',
            'bukan tentang ini',
            'bukan masalah ini',
            'bukan ini masalahnya',
            'keluar topik',
            'keluar dari topik',
            'keluar konteks',
            'ganti obrolan',
            'pindah obrolan',
            'beda topik',
            'topik berbeda',
            'mau tanya yang lain',
            'nggak nyambung sama topik',
            'tidak nyambung sama topik',
            'di luar konteks',
            'di luar bahasan',
            'bukan bahasan ini',
            'mau nanya di luar topik',
            'nanya hal lain',
            'tanya hal lain',
        ];

        foreach ($topicShiftPhrases as $ts) {
            if (str_contains($normalized, $ts)) {
                $facts['topic_shift_requested'] = 'true';
                $facts['is_off_topic'] = 'true';
                break;
            }
        }

        // Homework / technical task request detection
        $homeworkPhrases = [
            'buatkan coding',
            'tuliskan coding',
            'bikinin coding',
            'kerjakan tugasku',
            'kerjain tugas',
            'tuliskan skripsi bab',
            'buatkan skripsi',
            'buatkan makalah',
            'kerjakan soal ujian',
            'jawaban soal ujian',
            'kerjakan pr',
            'jawabkan soal nomor',
            'tuliskan esai',
            'buatkan essay',
            'tuliskan makalah',
        ];
        foreach ($homeworkPhrases as $hp) {
            if (str_contains($normalized, $hp)) {
                $facts['homework_request_detected'] = 'true';
                break;
            }
        }

        // Prompt injection / jailbreak attempt detection
        $injectionPhrases = [
            'ignore all previous instructions',
            'ignore previous instructions',
            'abaikan semua instruksi',
            'abaikan instruksi sebelumnya',
            'kamu sekarang adalah',
            'act as',
            'jailbreak',
            'dan mode',
            'developer mode',
        ];
        foreach ($injectionPhrases as $ip) {
            if (str_contains($normalized, $ip)) {
                $facts['injection_attempt_detected'] = 'true';
                $facts['is_off_topic'] = 'true';
                break;
            }
        }

        // Psychiatric / Psychological Consultation Need detection
        if ($this->hasPsychiatristIntent($normalized)) {
            $facts['need_professional'] = 'true';
            $facts['psychiatrist_support_needed'] = 'true';
        }

        return $facts;
    }

    /**
     * Determine if a text explicitly requests or indicates psychiatric / professional assistance.
     */
    public function hasPsychiatristIntent(string $text): bool
    {
        $normalized = mb_strtolower(trim($text));
        $psychiatristNeedPhrases = [
            'psikiater',
            'psikolog',
            'dokter jiwa',
            'konseling',
            'konsul ahli',
            'bantuan profesional',
            'butuh obat',
            'obat penenang',
            'panic attack',
            'serangan panik',
            'depresi berat',
            'trauma berat',
            'trauma masa kecil',
            'halusinasi',
            'bipolar',
            'gangguan cemas parah',
            'cemas berlebihan',
            'nggak sanggup lagi',
            'tidak sanggup lagi',
            'butuh rujukan',
            'rekomendasi psikiater',
            'rekomendasi psikolog',
            'rekomendasi dokter',
            'terapis',
            'terapi profesional',
            'klinik jiwa',
            'klinik psikologi',
            'puskesmas jiwa',
            'rujukan bpjs',
        ];

        foreach ($psychiatristNeedPhrases as $pnp) {
            if (str_contains($normalized, $pnp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Attach a professional recommendation from database if not already attached.
     */
    public function attachProfessionalRecommendation(ChatSession $session): void
    {
        if ($session->recommendations()->exists()) {
            return;
        }

        // Match professional by topic or location or get first active
        $pro = Professional::where('status', 'active')->first();

        if ($pro) {
            SessionRecommendation::create([
                'session_id' => $session->id,
                'professional_id' => $pro->id,
                'reason' => 'Sinyal beban pikiran dan stres berkelanjutan membutuhkan evaluasi lebih mendalam bersama tenaga profesional.',
                'notes' => 'Rekomendasi otomatis dari Expert System Weisz berdasarkan kondisi yang dibagikan.',
            ]);
        }
    }

    /**
     * Generate dynamic contextual chips based on active facts, conversation state, and topic.
     *
     * @param  array<string, mixed>  $facts
     * @return list<string>
     */
    protected function generateContextualChips(string $state, array $facts, ChatSession $session): array
    {
        if (! empty($facts['topic_shift_requested']) || ! empty($facts['is_off_topic'])) {
            return [
                'Mau lanjut topik ini aja',
                'Iya, aku mau cerita topik lain',
                'Bingung mau curhat apa lagi',
            ];
        }

        if (! empty($facts['stuck_detected'])) {
            return [
                'Boleh bantu kasih pertanyaan panduan?',
                'Rasanya campur aduk banget',
                'Pengen coba cerita pelan-pelan',
                'Butuh waktu sebentar buat mikir',
            ];
        }

        if (! empty($facts['academic_pressure'])) {
            return [
                'Deadlinenya mepet banget',
                'Takut ngecewain orang tua',
                'Susah banget buat mulai fokus',
                'Ada saran langkah kecil buat dicoba?',
            ];
        }

        if (! empty($facts['sleep_problem'])) {
            return [
                'Pikiran muter terus pas mau tidur',
                'Bangun tidur tetep ngerasa lelah',
                'Ada cara rileks sebelum tidur?',
                'Udah coba rebahan tapi nggak bisa tidur',
            ];
        }

        if (! empty($facts['emotional_exhaustion'])) {
            return [
                'Pengen istirahat tanpa rasa bersalah',
                'Semuanya terasa terlalu berat',
                'Udah lama nggak ngerasa tenang',
                'Bisa bantu kasih sudut pandang lain?',
            ];
        }

        if ($state === 'SUMMARY_READY') {
            return [
                'Boleh bantu rangkum obrolan kita?',
                'Langkah apa yang paling pas kuambil?',
                'Ada satu hal lagi mau kutambahin',
                'Terima kasih udah mendengarkan',
            ];
        }

        if ($state === 'CLARIFYING') {
            return [
                'Iya, persis seperti itu',
                'Sebenernya ada faktor lain juga',
                'Belum pernah cerita ini ke siapa-siapa',
                'Gimana saran terbaikmu?',
            ];
        }

        return $session->topic->suggested_chips ?? [
            'Mau cerita lebih detail',
            'Bisa bantu analisis situasiku?',
            'Apa langkah awal yang baik?',
            'Aku merasa sedikit lebih lega',
        ];
    }
}
