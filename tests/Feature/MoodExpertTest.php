<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ProblemTopic;
use App\Models\User;
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
            'mood_info' => [
                'emoji' => '😭',
                'label' => 'Hancur & Terpuruk',
            ],
        ]);

        $context = $session->fresh()->context;
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
}
