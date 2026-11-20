<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ExpertRule;
use App\Models\ProblemTopic;
use App\Models\RiskAssessment;
use App\Models\User;
use App\Services\ExpertSystemEngine;
use App\Services\MoodExpertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoodExpertTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_start_session_with_selected_mood(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        $response = $this->actingAs($user)->post(route('chat.start', $topic->slug), [
            'initial_mood' => 'anxious',
        ]);

        $session = ChatSession::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($session);
        $this->assertSame(2, $session->mood_score);
        $this->assertSame('anxious', $session->mood_label);
        $response->assertRedirect(route('chat.show', $session->id));

        $context = $session->context;
        $this->assertNotNull($context);
        $this->assertEquals('anxious', $context->current_mood);
        $this->assertEquals('anxious', $context->initial_mood);
        $this->assertEquals('anxious', $context->signals['user_mood']);

        // First message should be tailored to anxious mood
        $initialMessage = $session->messages()->first();
        $this->assertNotNull($initialMessage);
        $this->assertStringContainsString('cemas dan gelisah', $initialMessage->message);
    }

    public function test_user_can_update_mood_in_active_session(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        $this->actingAs($user)->post(route('chat.start', $topic->slug), [
            'initial_mood' => 'calm',
        ]);
        $session = ChatSession::where('user_id', $user->id)->latest('id')->first();

        $checkinMsg = ChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'assistant',
            'message' => 'Bagaimana kondisimu saat ini?',
            'metadata' => [
                'mood_checkin' => true,
            ],
        ]);

        $updateResponse = $this->actingAs($user)->postJson(route('chat.update-mood', $session->id), [
            'mood' => 'overwhelmed',
            'message_id' => $checkinMsg->id,
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'success' => true,
            'mood' => 'overwhelmed',
            'mood_score' => 1,
            'mood_info' => [
                'score' => 1,
                'emoji' => '😭',
                'label' => 'Hancur & Terpuruk',
            ],
        ]);

        $freshSession = $session->fresh();
        $this->assertSame(1, $freshSession->mood_score);
        $this->assertSame('overwhelmed', $freshSession->mood_label);

        $context = $freshSession->context;
        $this->assertEquals('overwhelmed', $context->current_mood);
        $this->assertCount(2, $context->mood_history);

        // Target message should now record selected_mood
        $this->assertEquals('overwhelmed', $checkinMsg->fresh()->metadata['selected_mood']);

        // Chat view should render this check-in bubble as locked (data-answered="true")
        $showResponse = $this->actingAs($user)->get(route('chat.show', $session->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('data-answered="true"', false);
        $showResponse->assertDontSee('Sistem Pakar Ekspresi: Cek Kondisi Hatimu');
        $showResponse->assertDontSee('Refleksi Dinamis');
        $showResponse->assertDontSee('Kondisi diperbarui! Weisz kini menyesuaikan jawaban berikutnya');
    }

    public function test_mood_expert_service_detects_keywords_and_checkin_interval(): void
    {
        // Keyword detection
        $this->assertEquals('anxious', MoodExpertService::detectMoodShift('jantungku berdebar kencang sekali panik dan takut'));
        $this->assertEquals('overwhelmed', MoodExpertService::detectMoodShift('aku benar-benar hancur putus asa dan tidak sanggup lagi'));
        $this->assertEquals('sad', MoodExpertService::detectMoodShift('aku merasa sangat sedih murung dan terluka'));
        $this->assertNull(MoodExpertService::detectMoodShift('saya hanya ingin mendiskusikan jadwal besok'));

        // Checkin interval rules (between 5 and 7 messages, or shift)
        $this->assertFalse(MoodExpertService::evaluateCheckinRule(1, null, null));
        $this->assertFalse(MoodExpertService::evaluateCheckinRule(2, null, null));
        $this->assertTrue(MoodExpertService::evaluateCheckinRule(5, null, null));
        $this->assertTrue(MoodExpertService::evaluateCheckinRule(6, null, null));
        $this->assertTrue(MoodExpertService::evaluateCheckinRule(2, null, 'anxious'));
    }

    public function test_authenticated_non_chat_pages_expose_global_crisis_access(): void
    {
        $guestResponse = $this->get(route('landing'));
        $guestResponse->assertStatus(200);
        $guestResponse->assertDontSee('id="globalCrisisBtn"', false);

        $user = User::where('email', 'user@weisz.test')->first();

        $exploreResponse = $this->actingAs($user)->get(route('explore.categories'));
        $exploreResponse->assertStatus(200);
        $exploreResponse->assertSee('id="globalCrisisBtn"', false);
        $exploreResponse->assertSee('id="globalCrisisModal"', false);
        $exploreResponse->assertSee('tel:119', false);
        $exploreResponse->assertSee('tel:1500567', false);
    }

    public function test_history_empty_states_and_chat_calm_loading_and_chip_cap(): void
    {
        $newUser = User::factory()->create();

        $emptyHistory = $this->actingAs($newUser)->get(route('history'));
        $emptyHistory->assertStatus(200);
        $emptyHistory->assertSee('Belum ada refleksi. Mulai dari topik yang terasa berat sekarang.');
        $emptyHistory->assertSee('Lihat Topik Refleksi');

        $filteredHistory = $this->actingAs($newUser)->get(route('history', ['search' => 'tidak-ada']));
        $filteredHistory->assertStatus(200);
        $filteredHistory->assertSee('Tidak ada sesi yang cocok dengan pencarianmu.');
        $filteredHistory->assertSee('Reset Pencarian');

        $topic = ProblemTopic::first();
        $topic->update(['suggested_chips' => ['Chip 1', 'Chip 2', 'Chip 3', 'Chip 4']]);

        $this->actingAs($newUser)->post(route('chat.start', $topic->slug), [
            'initial_mood' => 'anxious',
        ]);
        $session = ChatSession::where('user_id', $newUser->id)->latest('id')->first();

        $chatResponse = $this->actingAs($newUser)->get(route('chat.show', $session->id));
        $chatResponse->assertStatus(200);
        $chatResponse->assertSee('Weisz sedang merespons...');
        $chatResponse->assertSee('motion-safe:animate-pulse', false);
        $chatResponse->assertSee('data-text="Chip 1"', false);
        $chatResponse->assertSee('data-text="Chip 2"', false);
        $chatResponse->assertDontSee('data-text="Chip 3"', false);
        $chatResponse->assertDontSee('data-text="Chip 4"', false);
    }

    public function test_onboarding_consent_modal_and_soft_clay_cards(): void
    {
        $newUser = User::factory()->create(['onboarding_shown_at' => null]);

        $explore = $this->actingAs($newUser)->get(route('explore.categories'));
        $explore->assertStatus(200);
        $explore->assertSee('id="onboardingConsentModal"', false);
        $explore->assertSee('clay-card-interactive', false);
        $explore->assertSee('data-clay-tilt', false);

        $ack = $this->actingAs($newUser)->postJson(route('onboarding.acknowledge'));
        $ack->assertStatus(200)->assertJson(['success' => true]);
        $this->assertNotNull($newUser->fresh()->onboarding_shown_at);

        $exploreAfter = $this->actingAs($newUser->fresh())->get(route('explore.categories'));
        $exploreAfter->assertStatus(200);
        $exploreAfter->assertDontSee('id="onboardingConsentModal"', false);
    }

    public function test_admin_can_acknowledge_safety_assessment_on_monitoring(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        $session = ChatSession::create([
            'user_id' => $user->id,
            'category_id' => $topic->subcategory->category_id,
            'subcategory_id' => $topic->subcategory_id,
            'topic_id' => $topic->id,
            'title' => 'Safety Check',
            'status' => 'active',
            'started_at' => now(),
        ]);

        $assessment = RiskAssessment::create([
            'session_id' => $session->id,
            'trigger_text' => 'tidak sanggup lagi',
            'risk_level' => 'high',
            'safety_action' => 'Menampilkan hotline krisis',
            'is_acknowledged' => false,
            'triggered_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('admin.safety.acknowledge', $assessment))
            ->assertStatus(403);

        $monitoringBefore = $this->actingAs($admin)->get(route('admin.monitoring'));
        $monitoringBefore->assertStatus(200);
        $monitoringBefore->assertSee('Tandai Ditinjau');

        $this->actingAs($admin)
            ->post(route('admin.safety.acknowledge', $assessment))
            ->assertRedirect();

        $this->assertTrue($assessment->fresh()->is_acknowledged);

        $monitoringAfter = $this->actingAs($admin)->get(route('admin.monitoring'));
        $monitoringAfter->assertStatus(200);
        $monitoringAfter->assertSee('Sudah Ditinjau');
    }

    public function test_expert_rule_confidence_factor_scoring_and_admin_management(): void
    {
        $this->assertSame(0.0, ExpertSystemEngine::combineConfidence([]));
        $this->assertSame(0.8, ExpertSystemEngine::combineConfidence([0.6, 0.5]));
        $this->assertSame(1.0, ExpertSystemEngine::combineConfidence([1.0, 0.5]));

        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        $this->actingAs($admin)->post(route('admin.rules.store'), [
            'name' => 'Uji CF Tekanan Skripsi',
            'topic_id' => $topic->id,
            'priority' => 99,
            'confidence_factor' => 0.75,
            'condition_fact_key' => 'academic_pressure',
            'condition_operator' => '==',
            'condition_fact_value' => 'high',
            'action_type' => 'prompt_directive',
            'action_directive' => 'Validasi tekanan akademik secara tenang.',
        ])->assertRedirect();

        $createdRule = ExpertRule::where('name', 'Uji CF Tekanan Skripsi')->first();
        $this->assertNotNull($createdRule);
        $this->assertSame(0.75, $createdRule->confidence_factor);

        $rulesPage = $this->actingAs($admin)->get(route('admin.rules'));
        $rulesPage->assertStatus(200);
        $rulesPage->assertSee('CF 0.75');

        $session = ChatSession::create([
            'user_id' => $user->id,
            'category_id' => $topic->subcategory->category_id,
            'subcategory_id' => $topic->subcategory_id,
            'topic_id' => $topic->id,
            'title' => 'CF Evaluation',
            'status' => 'active',
            'started_at' => now(),
        ]);

        $result = app(ExpertSystemEngine::class)
            ->evaluate($session, 'Aku stres banget sama revisi skripsi dan ujian minggu ini');

        $this->assertGreaterThanOrEqual(0.75, $result['confidence_factor']);
        $this->assertSame($result['confidence_factor'], $session->fresh()->context->context_data['rule_confidence']);
    }
}
