<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ProblemCategory;
use App\Models\ProblemSubcategory;
use App\Models\ProblemTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiLanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_indonesian(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertSame('id', app()->getLocale());
        $response->assertSee('Ruang Nyaman Mengurai');
    }

    public function test_guest_can_switch_locale_to_english_via_post(): void
    {
        $response = $this->post(route('locale.update'), [
            'locale' => 'en',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
        $response->assertCookie('weisz_locale', 'en');

        // Subsequent request with session should render in English
        $landing = $this->withSession(['locale' => 'en'])->get('/');
        $landing->assertStatus(200);
        $this->assertSame('en', app()->getLocale());
        $landing->assertSee('Comfortable Space to Unpack');
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $response = $this->post(route('locale.update'), [
            'locale' => 'fr',
        ]);

        $response->assertSessionHasErrors(['locale']);
        $this->assertNotSame('fr', session('locale'));
    }

    public function test_authenticated_user_locale_update_persists_in_database(): void
    {
        $user = User::factory()->create([
            'locale' => 'id',
        ]);

        $response = $this->actingAs($user)->post(route('locale.update'), [
            'locale' => 'en',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_authenticated_user_locale_is_loaded_from_profile(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'locale' => 'en',
        ]);

        $response = $this->actingAs($user)->get('/history');

        $response->assertStatus(200);
        $this->assertSame('en', app()->getLocale());
        $response->assertSee('Conversation Session History');
    }

    public function test_chat_messages_and_bot_responses_remain_untouched_in_original_language(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'locale' => 'en',
        ]);

        $category = ProblemCategory::first();
        $subcategory = ProblemSubcategory::first();
        $topic = ProblemTopic::first();

        $session = ChatSession::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'topic_id' => $topic->id,
            'status' => 'active',
            'initial_mood' => 'anxious',
            'current_mood' => 'anxious',
        ]);

        $userText = 'Saya merasa sangat tertekan dengan tugas akhir kuliah saya.';
        $botText = 'Aku mengerti perasaanmu. Sangat wajar merasa terbebani saat tugas akhir menumpuk.';

        ChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'user',
            'message' => $userText,
        ]);

        ChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'assistant',
            'message' => $botText,
        ]);

        // When viewing chat page in English locale
        $response = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('chat.show', $session->id));

        $response->assertStatus(200);
        $this->assertSame('en', app()->getLocale());

        // UI elements are in English
        $response->assertSee('New Chat');

        // User and bot chat contents remain verbatim in their original Indonesian language
        $response->assertSee($userText);
        $response->assertSee($botText);
    }
}
