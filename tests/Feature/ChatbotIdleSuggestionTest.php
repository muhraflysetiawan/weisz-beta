<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ConversationContext;
use App\Models\ProblemCategory;
use App\Models\ProblemSubcategory;
use App\Models\ProblemTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotIdleSuggestionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected ChatSession $session;

    protected ProblemTopic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*chat/completions*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Santai aja ya, kamu nggak perlu buru-buru membalas. Bagaimana perasaanmu sekarang? [SUGGESTIONS: "Masih bingung", "Perlu rehat sejenak"]',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->user = User::factory()->create([
            'email' => 'student@weisz.test',
            'role' => 'user',
        ]);

        $category = ProblemCategory::create([
            'name' => 'Akademik & Perkuliahan',
            'slug' => 'akademik-dan-perkuliahan',
            'description' => 'Terkait tugas dan skripsi',
            'icon' => 'academic-cap',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $subcategory = ProblemSubcategory::create([
            'category_id' => $category->id,
            'name' => 'Beban Tugas & Ujian',
            'slug' => 'beban-tugas-dan-ujian',
            'description' => 'Deadline dan kecemasan nilai',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->topic = ProblemTopic::create([
            'subcategory_id' => $subcategory->id,
            'name' => 'Stres Ujian & Skripsi',
            'slug' => 'stres-ujian-dan-skripsi',
            'description' => 'Merasa kewalahan dengan jadwal ujian dan revisi skripsi.',
            'prompt_context' => 'Fokus pada beban kognitif dan ketakutan gagal dalam akademik.',
            'suggested_chips' => ['Banyak tugas numpuk', 'Susah tidur mikirin nilai', 'Bingung mau mulai dari mana'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->session = ChatSession::create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'topic_id' => $this->topic->id,
            'title' => 'Sesi: Stres Ujian & Skripsi',
            'status' => 'active',
            'started_at' => now(),
        ]);

        ConversationContext::create([
            'session_id' => $this->session->id,
            'current_state' => 'EXPLORING',
            'signals' => [],
            'context_data' => [
                'topic_name' => $this->topic->name,
                'locked' => true,
            ],
        ]);
    }

    public function test_unauthorized_user_cannot_request_idle_suggestion(): void
    {
        $otherUser = User::factory()->create([
            'email' => 'other@weisz.test',
            'role' => 'user',
        ]);

        $response = $this->actingAs($otherUser)->postJson(route('chat.idle-suggestion', $this->session->id));
        $response->assertStatus(403);
    }

    public function test_idle_suggestion_fails_if_session_inactive(): void
    {
        $this->session->update(['status' => 'completed']);

        $response = $this->actingAs($this->user)->postJson(route('chat.idle-suggestion', $this->session->id));
        $response->assertStatus(422);
        $response->assertJson(['success' => false, 'error' => 'session_inactive']);
    }

    public function test_idle_suggestion_fails_if_requested_too_soon(): void
    {
        // Assistant message created just now
        ChatMessage::create([
            'session_id' => $this->session->id,
            'sender' => 'assistant',
            'message' => 'Hai! Ada hal apa yang belakangan ini membebanimu?',
        ]);

        // Only 10 seconds elapsed
        $this->travel(10)->seconds();

        $response = $this->actingAs($this->user)->postJson(route('chat.idle-suggestion', $this->session->id));
        $response->assertStatus(422);
        $response->assertJson(['success' => false, 'error' => 'too_soon']);
    }

    public function test_idle_suggestion_succeeds_when_user_is_inactive_for_over_a_minute(): void
    {
        ChatMessage::create([
            'session_id' => $this->session->id,
            'sender' => 'assistant',
            'message' => 'Hai! Ada hal apa yang belakangan ini membebanimu?',
        ]);

        // Fast forward 65 seconds
        $this->travel(65)->seconds();

        $response = $this->actingAs($this->user)->postJson(route('chat.idle-suggestion', $this->session->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => $this->session->id,
            'sender' => 'assistant',
        ]);

        $latestAssistantMsg = ChatMessage::where('session_id', $this->session->id)->latest('id')->first();
        $this->assertNotNull($latestAssistantMsg);
        $this->assertTrue($latestAssistantMsg->metadata['idle_suggestion'] ?? false);
        $this->assertEquals('idle_suggestion', $latestAssistantMsg->metadata['type'] ?? '');
    }

    public function test_idle_suggestion_rejected_if_already_sent_in_current_turn(): void
    {
        // 1. Assistant initial greeting
        ChatMessage::create([
            'session_id' => $this->session->id,
            'sender' => 'assistant',
            'message' => 'Hai! Apa kabar?',
        ]);

        $this->travel(65)->seconds();

        // 2. First idle suggestion sent
        ChatMessage::create([
            'session_id' => $this->session->id,
            'sender' => 'assistant',
            'message' => 'Santai aja ya, kamu nggak perlu buru-buru membalas...',
            'metadata' => [
                'type' => 'idle_suggestion',
                'idle_suggestion' => true,
            ],
        ]);

        $this->travel(65)->seconds();

        // 3. User still hasn't replied, attempt to trigger another idle suggestion
        $response = $this->actingAs($this->user)->postJson(route('chat.idle-suggestion', $this->session->id));

        // Must be rejected so AI does not spam consecutive suggestions!
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => 'already_suggested',
        ]);
    }

    public function test_idle_suggestion_allowed_again_after_user_replies_and_goes_afk(): void
    {
        // 1. Initial greeting
        ChatMessage::create([
            'session_id' => $this->session->id,
            'sender' => 'assistant',
            'message' => 'Hai! Apa kabar?',
        ]);

        $this->travel(65)->seconds();

        // 2. First idle suggestion sent
        ChatMessage::create([
            'session_id' => $this->session->id,
            'sender' => 'assistant',
            'message' => 'Santai aja ya, pelan-pelan dipikirkan...',
            'metadata' => [
                'type' => 'idle_suggestion',
                'idle_suggestion' => true,
            ],
        ]);

        $this->travel(30)->seconds();

        // 3. User responds to the chat!
        $this->actingAs($this->user)->postJson(route('chat.send', $this->session->id), [
            'message' => 'Aku takut nilaiku anjlok kak.',
        ]);

        // 4. Verify that normal assistant response was created and does not have idle_suggestion set
        $latestAiMsg = ChatMessage::where('session_id', $this->session->id)->latest('id')->first();
        $this->assertEquals('assistant', $latestAiMsg->sender);
        $this->assertFalse($latestAiMsg->metadata['idle_suggestion'] ?? false);

        // 5. User is AFK again for > 1 minute
        $this->travel(65)->seconds();

        // 6. Request new idle suggestion
        $response = $this->actingAs($this->user)->postJson(route('chat.idle-suggestion', $this->session->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $newMsg = ChatMessage::where('session_id', $this->session->id)->latest('id')->first();
        $this->assertTrue($newMsg->metadata['idle_suggestion'] ?? false);
    }

    public function test_cancel_idle_suggestion_deletes_unrendered_idle_message(): void
    {
        $assistantMsg = ChatMessage::create([
            'session_id' => $this->session->id,
            'sender' => 'assistant',
            'message' => 'Santai aja ya, kamu nggak perlu buru-buru membalas...',
            'metadata' => [
                'type' => 'idle_suggestion',
                'idle_suggestion' => true,
            ],
        ]);

        $response = $this->actingAs($this->user)->postJson(route('chat.cancel-idle-suggestion', $this->session->id), [
            'message_id' => $assistantMsg->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('chat_messages', [
            'id' => $assistantMsg->id,
        ]);
    }

    public function test_unauthorized_user_cannot_cancel_idle_suggestion(): void
    {
        $otherUser = User::factory()->create([
            'email' => 'hacker@weisz.test',
            'role' => 'user',
        ]);

        $response = $this->actingAs($otherUser)->postJson(route('chat.cancel-idle-suggestion', $this->session->id));
        $response->assertStatus(403);
    }
}
