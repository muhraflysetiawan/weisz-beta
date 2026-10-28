<?php

namespace Tests\Feature;

use App\Models\ChatSession;
use App\Models\ProblemTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WeiszFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        Http::fake([
            '*chat/completions*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Aku mendengarkan ceritamu dengan seksama. Tenang ya, kamu tidak sendirian.',
                        ],
                    ],
                ],
            ], 200),
        ]);
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $response->assertSee('Weisz');
        $response->assertSee('Ruang Nyaman Mengurai');
    }

    public function test_user_can_register_and_reach_chat(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@weisz.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('chat.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'budi@weisz.test',
            'role' => 'user',
        ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'user@weisz.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('chat.index'));
        $this->assertAuthenticated();

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('landing'));
        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_landing_or_auth_pages_redirects_to_chat(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        // 1. Visiting landing page when logged in
        $landingResponse = $this->actingAs($user)->get(route('landing'));
        $landingResponse->assertRedirect(route('chat.index'));

        // 2. Visiting login page when logged in
        $loginResponse = $this->actingAs($user)->get(route('login'));
        $loginResponse->assertRedirect(route('chat.index'));

        // 3. Visiting register page when logged in
        $registerResponse = $this->actingAs($user)->get(route('register'));
        $registerResponse->assertRedirect(route('chat.index'));

        // 4. Visiting legacy /dashboard URL
        $dashboardResponse = $this->actingAs($user)->get('/dashboard');
        $dashboardResponse->assertRedirect('/chat');
    }

    public function test_user_can_explore_hierarchy_steps(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::with('subcategory.category')->first();
        $subcategory = $topic->subcategory;
        $category = $subcategory->category;

        // 05. Problem Explorer
        $response = $this->actingAs($user)->get(route('explore.categories'));
        $response->assertStatus(200);
        $response->assertSee($category->name);

        // 06. Subcategory
        $subResponse = $this->actingAs($user)->get(route('explore.subcategories', $category->slug));
        $subResponse->assertStatus(200);
        $subResponse->assertSee($subcategory->name);

        // 07. Topic Selection
        $topicResponse = $this->actingAs($user)->get(route('explore.topics', ['category' => $category->slug, 'subcategory' => $subcategory->slug]));
        $topicResponse->assertStatus(200);
        $topicResponse->assertSee($topic->name);

        // 08. Context Confirmation
        $confirmResponse = $this->actingAs($user)->get(route('chat.confirm', $topic->slug));
        $confirmResponse->assertStatus(200);
        $confirmResponse->assertSee('Mulai');
    }

    public function test_user_can_start_session_and_exchange_messages(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        // Start session
        $startResponse = $this->actingAs($user)->post(route('chat.start', $topic->slug));
        $session = ChatSession::where('user_id', $user->id)->latest('id')->first();

        $this->assertNotNull($session);
        $startResponse->assertRedirect(route('chat.show', $session->id));

        // View chat page (09)
        $chatPageResponse = $this->actingAs($user)->get(route('chat.show', $session->id));
        $chatPageResponse->assertStatus(200);
        $chatPageResponse->assertSee('Context Locked');
        $chatPageResponse->assertDontSee('WeisZ dapat membuat kesalahan. Periksa info penting.');
        $chatPageResponse->assertSee('id="openSidebarBtn"', false);
        $chatPageResponse->assertSee('id="voiceNoteBtn"', false);
        $chatPageResponse->assertSee('id="sendBtn"', false);
        $chatPageResponse->assertSee('id="messageInput"', false);

        // Send a message
        $sendResponse = $this->actingAs($user)->postJson(route('chat.send', $session->id), [
            'message' => 'Aku merasa banyak tugas dan tidak bisa tidur memikirkan nilai.',
        ]);

        $sendResponse->assertStatus(200);
        $sendResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => $session->id,
            'sender' => 'user',
        ]);
        $this->assertDatabaseHas('chat_messages', [
            'session_id' => $session->id,
            'sender' => 'assistant',
        ]);
    }

    public function test_unengaged_session_is_not_saved_in_history_when_leaving_before_typing(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        // 1. User starts session
        $this->actingAs($user)->post(route('chat.start', $topic->slug));
        $session = ChatSession::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($session);

        // 2. User leaves without sending any messages
        $leaveResponse = $this->actingAs($user)->get(route('chat.leave', $session->id));
        $leaveResponse->assertRedirect(route('chat.index'));

        // 3. Verify session was deleted and does not appear in database or history
        $this->assertDatabaseMissing('chat_sessions', [
            'id' => $session->id,
        ]);

        $historyResponse = $this->actingAs($user)->get(route('history'));
        $historyResponse->assertStatus(200);
        $historyResponse->assertDontSee('Sesi: '.$topic->name);
    }

    public function test_engaged_session_is_preserved_in_history_when_leaving(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        // 1. User starts session and sends a message
        $this->actingAs($user)->post(route('chat.start', $topic->slug));
        $session = ChatSession::where('user_id', $user->id)->latest('id')->first();

        $this->actingAs($user)->postJson(route('chat.send', $session->id), [
            'message' => 'Halo aku mau cerita tugas kuliahku.',
        ]);

        // 2. User clicks kembali ke beranda
        $leaveResponse = $this->actingAs($user)->get(route('chat.leave', $session->id));
        $leaveResponse->assertRedirect(route('chat.index'));

        // 3. Verify session is preserved
        $this->assertDatabaseHas('chat_sessions', [
            'id' => $session->id,
        ]);

        $historyResponse = $this->actingAs($user)->get(route('history'));
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee($session->title ?: $topic->name);
    }

    public function test_safety_detector_triggers_immediate_crisis_support(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        $session = ChatSession::create([
            'user_id' => $user->id,
            'category_id' => $topic->subcategory->category_id,
            'subcategory_id' => $topic->subcategory_id,
            'topic_id' => $topic->id,
            'title' => 'Safety Test',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->postJson(route('chat.send', $session->id), [
            'message' => 'Aku merasa capek banget dan ingin bunuh diri sekarang.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'safety_alert' => true,
        ]);

        $this->assertDatabaseHas('risk_assessments', [
            'session_id' => $session->id,
            'risk_level' => 'critical',
        ]);

        $latestAssistantMsg = $session->messages()->where('sender', 'assistant')->latest()->first();
        $this->assertStringContainsString('119', $latestAssistantMsg->message);
    }

    public function test_user_can_complete_session_and_access_summary_and_professionals(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        $session = ChatSession::create([
            'user_id' => $user->id,
            'category_id' => $topic->subcategory->category_id,
            'subcategory_id' => $topic->subcategory_id,
            'topic_id' => $topic->id,
            'title' => 'Completion Test',
            'status' => 'active',
        ]);

        // Complete session
        $completeResponse = $this->actingAs($user)->post(route('chat.complete', $session->id));
        $completeResponse->assertRedirect(route('chat.summary', $session->id));

        // Summary Page (11)
        $summaryResponse = $this->actingAs($user)->get(route('chat.summary', $session->id));
        $summaryResponse->assertStatus(200);
        $summaryResponse->assertSee('Rangkuman Refleksi Percakapan');
        $this->assertDatabaseHas('conversation_summaries', [
            'session_id' => $session->id,
        ]);

        // Professional recommendation page for session (12)
        $proResponse = $this->actingAs($user)->get(route('chat.professionals', $session->id));
        $proResponse->assertStatus(200);
        $proResponse->assertSee('Rekomendasi Tenaga Profesional');
    }

    public function test_chat_page_renders_with_plain_background_and_explore_room_section(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::with('subcategory.category')->first();

        // Start session
        $this->actingAs($user)->post(route('chat.start', $topic->slug), [
            'initial_mood' => 'calm',
        ]);
        $session = ChatSession::where('user_id', $user->id)->latest()->first();

        $response = $this->actingAs($user)->get(route('chat.show', $session->id));
        $response->assertStatus(200);

        // 1. Background must not contain the grid pattern (plain background with gradient orbs preserved)
        $response->assertDontSee('[background-size:32px_32px]');
        $response->assertSee('Ambient Glowing Orbs');

        // 2. Chat room must contain the Explore room section for "Obrolan Baru"
        $response->assertSee('id="exploreRoomSection"', false);
        $response->assertSee('id="roomExploreProgressBar"', false);
        $response->assertSee('sidebarNewChatBtn');
        $response->assertSee('openNewChatExplore');

        // 3. /chat direct route works and opens in explore mode
        $chatIndexResponse = $this->actingAs($user)->get(route('chat.index'));
        $chatIndexResponse->assertStatus(200);
        $chatIndexResponse->assertSee('id="exploreRoomSection"', false);
    }

    public function test_admin_access_control(): void
    {
        $regularUser = User::where('email', 'user@weisz.test')->first();
        $adminUser = User::where('email', 'admin@weisz.test')->first();

        // Regular user denied
        $deniedResponse = $this->actingAs($regularUser)->get(route('admin.dashboard'));
        $deniedResponse->assertStatus(403);

        // Admin user allowed
        $allowedResponse = $this->actingAs($adminUser)->get(route('admin.dashboard'));
        $allowedResponse->assertStatus(200);
        $allowedResponse->assertSee('Dashboard Administrator');

        // Admin categories (16)
        $catResponse = $this->actingAs($adminUser)->get(route('admin.categories'));
        $catResponse->assertStatus(200);

        // Admin knowledge base (17)
        $kbResponse = $this->actingAs($adminUser)->get(route('admin.knowledge'));
        $kbResponse->assertStatus(200);

        // Admin rules (18)
        $ruleResponse = $this->actingAs($adminUser)->get(route('admin.rules'));
        $ruleResponse->assertStatus(200);

        // Admin professionals (19)
        $proResponse = $this->actingAs($adminUser)->get(route('admin.professionals'));
        $proResponse->assertStatus(200);

        // Admin monitoring (20)
        $monResponse = $this->actingAs($adminUser)->get(route('admin.monitoring'));
        $monResponse->assertStatus(200);
    }

    public function test_user_can_send_voice_note_and_receive_ai_text_response(): void
    {
        Storage::fake('public');

        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::first();

        $session = ChatSession::create([
            'user_id' => $user->id,
            'category_id' => $topic->subcategory->category_id,
            'subcategory_id' => $topic->subcategory_id,
            'topic_id' => $topic->id,
            'title' => 'Voice Note Session',
            'status' => 'active',
        ]);

        $audioFile = UploadedFile::fake()->create('voicenote.webm', 100, 'audio/webm');

        $response = $this->actingAs($user)->post(route('chat.send', $session->id), [
            'audio' => $audioFile,
            'message' => 'Halo aku lagi merasa tertekan dan butuh teman bicara',
            'duration' => 6,
            'is_voice_note' => 1,
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert user message is stored as voice note
        $userMsg = $session->messages()->where('sender', 'user')->latest('id')->first();
        $this->assertNotNull($userMsg);
        $this->assertEquals('voice_note', $userMsg->metadata['type'] ?? null);
        $this->assertNotNull($userMsg->metadata['audio_url'] ?? null);
        $this->assertEquals(6, $userMsg->metadata['duration'] ?? null);
        $this->assertEquals('Halo aku lagi merasa tertekan dan butuh teman bicara', $userMsg->message);

        // Assert AI generated a text response answering the user
        $assistantMsg = $session->messages()->where('sender', 'assistant')->latest('id')->first();
        $this->assertNotNull($assistantMsg);
        $this->assertNotEmpty($assistantMsg->message);
        $this->assertStringContainsString('Aku mendengarkan ceritamu', $assistantMsg->message);

        // Assert chat view contains Voice Note button and dynamic composer elements
        $viewResponse = $this->actingAs($user)->get(route('chat.show', $session->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('id="voiceNoteBtn"', false);
        $viewResponse->assertSee('id="sendBtn"', false);
        $viewResponse->assertSee('id="voiceNoteRecordingBar"', false);
        $viewResponse->assertSee('id="pauseResumeVoiceNoteBtn"', false);
        $viewResponse->assertSee('id="cancelVoiceNoteBtn"', false);
        $viewResponse->assertSee('data-role="voice-player"', false);
    }

    public function test_mobile_chat_elements_and_hamburger_button_present_on_index_and_show(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        // 1. On /chat (chat.index flow / after login)
        $indexResponse = $this->actingAs($user)->get(route('chat.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('id="openSidebarBtn"', false);
        $indexResponse->assertSee('interactive-widget=resizes-content', false);
        $indexResponse->assertSee('chat-app-container', false);
        // Header controls must not be hidden
        $indexResponse->assertDontSee('id="chatHeaderControls" class="absolute top-0 inset-x-0 h-14 px-4 sm:px-6 md:px-10 flex items-center justify-between pointer-events-none z-30 hidden', false);
        $indexResponse->assertDontSee('Kembali ke Chat', false);
        $indexResponse->assertDontSee('id="exploreCloseControl"', false);

        // 2. On /chat/{session} (chat room)
        $topic = ProblemTopic::first();
        $this->actingAs($user)->post(route('chat.start', $topic->slug));
        $session = ChatSession::where('user_id', $user->id)->latest('id')->first();

        $showResponse = $this->actingAs($user)->get(route('chat.show', $session->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('id="openSidebarBtn"', false);
        $showResponse->assertDontSee('Kembali ke Chat', false);
        $showResponse->assertDontSee('id="exploreCloseControl"', false);
        $showResponse->assertSee('id="chatComposerArea"', false);
        $showResponse->assertSee('id="messageInput"', false);
        $showResponse->assertSee('interactive-widget=resizes-content', false);
        $showResponse->assertSee('h-[100dvh]', false);
    }

    public function test_profile_route_redirects_to_chat_with_profile_param(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertRedirect(route('chat.index', ['profile' => 1]));
    }

    public function test_profile_menu_in_room_chat_displays_proper_structure(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        $response = $this->actingAs($user)->get(route('chat.index', ['profile' => 1]));
        $response->assertStatus(200);

        // Sidebar should have profile button only
        $response->assertSee('id="sidebarProfileBtn"', false);
        $response->assertSee('onclick="openProfileSection()"', false);
        $response->assertDontSee('id="sidebarSettingBtn"', false);

        // Profile section in room chat
        $response->assertSee('id="profileRoomSection"', false);
        $response->assertSee('id="profileScrollContainer"', false);
        // Sizing matches explore & chat feed (max-w-5xl)
        $response->assertSee('max-w-5xl mx-auto w-full space-y-6 sm:space-y-8 pb-12', false);
        $response->assertSee('id="profileUpdateNameForm"', false);
        $response->assertSee('id="profileAccountNameInput"', false);
        $response->assertSee($user->name);
        $response->assertSee($user->email);

        // Merged Settings Section
        $response->assertSee('id="profileSettingsSection"', false);
        // Theme Options (Sistem, Light, Dark with 3D clay illustrations and no radio circles)
        $response->assertSee('id="themeCard_system"', false);
        $response->assertSee('id="themeCard_light"', false);
        $response->assertSee('id="themeCard_dark"', false);
        $response->assertSee('images/themes/system.jpg');
        $response->assertSee('images/themes/light.jpg');
        $response->assertSee('images/themes/dark.jpg');
        $response->assertDontSee('theme-radio-circle');

        // App Language (English & Bahasa Indonesia dropdown beside text)
        $response->assertSee('id="profileLanguageSelect"', false);
        $response->assertSee('value="id"', false);
        $response->assertSee('value="en"', false);

        // Password update and Privacy cards must be removed
        $response->assertDontSee('id="profilePasswordUpdateForm"', false);
        $response->assertDontSee('Privasi & Keamanan');
        $response->assertDontSee('Ganti Kata Sandi');

        // Log out button without icon
        $response->assertSee('Log Out');
        $response->assertDontSee('><span>Log Out</span><', false);

        // Must NOT have 'Kembali' button or 'Menu Profil' text
        $response->assertDontSee('Menu Profil');
        $response->assertDontSee('>Kembali<', false);

        // Must NOT have label for 'Nama Akun'
        $response->assertDontSee('Nama Akun</label>', false);

        // Must NOT see 'Peran:'
        $response->assertDontSee('Peran:');
        // Must NOT have email input
        $response->assertDontSee('name="email"');
    }

    public function test_topics_route_redirects_to_chat_with_topics_param(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        $response = $this->actingAs($user)->get(route('topics.explore'));
        $response->assertRedirect(route('chat.index', ['topics' => 1]));
    }

    public function test_topics_menu_in_room_chat_displays_proper_structure(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        $response = $this->actingAs($user)->get(route('chat.index', ['topics' => 1]));
        $response->assertStatus(200);

        // Sidebar should have Topics button triggering openTopicsSection
        $response->assertSee('id="sidebarTopicsBtn"', false);
        $response->assertSee('onclick="openTopicsSection()"', false);

        // Topics section in room chat
        $response->assertSee('id="topicsRoomSection"', false);
        $response->assertSee('id="topicsScrollContainer"', false);
        $response->assertSee('id="topicsSearchInput"', false);
        $response->assertSee('id="topicsGridContainer"', false);
        $response->assertSee('id="topicsEmptyState"', false);
        $response->assertSee('Semua Kategori');

        // Should not contain legacy breadcrumb header from standalone page
        $response->assertDontSee('Kembali ke Ruang Curhat');

        // Cards should be forms directly posting to chat.start
        $response->assertSee('action="'.url('/chat/start'), false);
        $response->assertSee('Mulai Percakapan');
    }

    public function test_clicking_topic_creates_chat_session_with_recorded_professional(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();
        $topic = ProblemTopic::where('is_active', true)->first();

        // 1. Post to chat.start directly from topic card
        $response = $this->actingAs($user)->post(route('chat.start', $topic->slug));

        // 2. Asserts redirect directly to chat.show (not explore wizard)
        $latestSession = ChatSession::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($latestSession);
        $this->assertEquals($topic->id, $latestSession->topic_id);
        $response->assertRedirect(route('chat.show', $latestSession->id));

        // 3. Asserts professional (pakar) is recorded in session recommendations
        $this->assertTrue($latestSession->recommendations()->exists());
        $rec = $latestSession->recommendations()->first();
        $this->assertNotNull($rec->professional_id);

        // 4. Asserts professional is recorded in conversation context data
        $context = $latestSession->context;
        $this->assertNotNull($context);
        $this->assertArrayHasKey('assigned_professional_id', $context->context_data);
        $this->assertEquals($rec->professional_id, $context->context_data['assigned_professional_id']);
    }

    public function test_user_can_switch_app_language_in_settings(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        // 1. Switch to Indonesian
        $responseId = $this->actingAs($user)->postJson(route('locale.update'), [
            'locale' => 'id',
        ]);
        $responseId->assertStatus(200);
        $responseId->assertJson([
            'success' => true,
            'locale' => 'id',
        ]);

        $this->assertEquals('id', session('locale'));

        // 2. Switch to English (Default)
        $responseEn = $this->actingAs($user)->postJson(route('locale.update'), [
            'locale' => 'en',
        ]);
        $responseEn->assertStatus(200);
        $responseEn->assertJson([
            'success' => true,
            'locale' => 'en',
        ]);

        $this->assertEquals('en', session('locale'));
    }

    public function test_user_can_update_account_name_without_changing_email(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        // 1. JSON / AJAX request
        $jsonResponse = $this->actingAs($user)->putJson(route('profile.update'), [
            'name' => 'Raden Weisz',
        ]);

        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJson([
            'success' => true,
            'user' => [
                'name' => 'Raden Weisz',
                'email' => $user->email,
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Raden Weisz',
            'email' => $user->email,
        ]);

        // 2. Standard form submission (fallback)
        $formResponse = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Raden Weisz Updated',
        ]);

        $formResponse->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Raden Weisz Updated',
            'email' => $user->email,
        ]);
    }

    public function test_authenticated_user_accessing_professionals_redirects_to_chat_with_professionals_param(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        $response = $this->actingAs($user)->get(route('professionals'));
        $response->assertRedirect(route('chat.index', ['professionals' => 1]));
    }

    public function test_chat_page_renders_professionals_room_section_and_cards(): void
    {
        $user = User::where('email', 'user@weisz.test')->first();

        $response = $this->actingAs($user)->get(route('chat.index', ['professionals' => 1]));

        $response->assertStatus(200);
        $response->assertSee('id="sidebarProfessionalsBtn"', false);
        $response->assertSee('id="professionalsRoomSection"', false);
        $response->assertSee('id="professionalsSearchInput"', false);
        $response->assertSee('id="professionalsGridContainer"', false);
        $response->assertSee('professional-room-card', false);
    }
}
