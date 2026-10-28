<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ConversationContext;
use App\Models\ProblemCategory;
use App\Models\ProblemTopic;
use App\Models\Professional;
use App\Services\ExpertSystemEngine;
use App\Services\MoodExpertService;
use App\Services\OpenRouterService;
use App\Services\SafetyDetector;
use App\Services\SummaryGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    /**
     * Start a brand new curhat session directly.
     */
    public function startQuickCurhat(): RedirectResponse
    {
        $user = Auth::user();

        $topic = ProblemTopic::where('is_active', true)
            ->with(['subcategory.category'])
            ->orderBy('sort_order')
            ->first();

        if (! $topic) {
            return redirect()->route('explore.categories');
        }

        // Clean up any stale sessions where user never sent any message
        ChatSession::where('user_id', $user->id)
            ->whereDoesntHave('messages', function ($q) {
                $q->where('sender', 'user');
            })
            ->delete();

        $session = ChatSession::create([
            'user_id' => $user->id,
            'category_id' => $topic->subcategory->category_id,
            'subcategory_id' => $topic->subcategory_id,
            'topic_id' => $topic->id,
            'title' => 'Sesi: '.$topic->name,
            'status' => 'active',
            'started_at' => now(),
        ]);

        ConversationContext::create([
            'session_id' => $session->id,
            'current_state' => 'START',
            'signals' => [],
            'context_data' => [
                'topic_name' => $topic->name,
                'category_name' => $topic->subcategory->category->name,
                'subcategory_name' => $topic->subcategory->name,
                'locked' => true,
            ],
        ]);

        $greeting = "Hai {$user->name}! Aku Weisz. Konteks obrolan kita sudah difokuskan pada topik {$topic->name}.\n\nAku di sini untuk mendengarkan tanpa menghakimi. Apa yang belakangan ini paling terasa membebani atau mengganjal pikiranmu tentang hal ini?";

        ChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'assistant',
            'message' => $greeting,
            'metadata' => [
                'type' => 'greeting',
                'suggested_chips' => $topic->suggested_chips,
            ],
        ]);

        return redirect()->route('chat.show', $session->id);
    }

    /**
     * Display AI Chatbot core interface in Explore / New Chat mode (User Page 09).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Clean up unengaged/empty sessions where user never typed or sent any message
        ChatSession::where('user_id', $user->id)
            ->whereDoesntHave('messages', function ($q) {
                $q->where('sender', 'user');
            })
            ->delete();

        $exploreCategories = ProblemCategory::where('is_active', true)
            ->with([
                'subcategories' => function ($q) {
                    $q->where('is_active', true)
                        ->withCount('topics')
                        ->with(['topics' => function ($tq) {
                            $tq->where('is_active', true)->orderBy('sort_order');
                        }])
                        ->orderBy('sort_order');
                },
            ])
            ->orderBy('sort_order')
            ->get();

        $allMoods = MoodExpertService::getMoods();
        $professionals = Professional::where('status', 'active')->get();

        $userSessions = ChatSession::where('user_id', $user->id)
            ->withUserMessages()
            ->with(['topic', 'category', 'subcategory'])
            ->latest()
            ->get();

        $latestSession = $userSessions->first();

        if (! $latestSession) {
            $topic = ProblemTopic::where('is_active', true)
                ->with(['subcategory.category'])
                ->orderBy('sort_order')
                ->first();

            if ($topic) {
                $latestSession = ChatSession::create([
                    'user_id' => $user->id,
                    'category_id' => $topic->subcategory->category_id,
                    'subcategory_id' => $topic->subcategory_id,
                    'topic_id' => $topic->id,
                    'title' => 'Sesi: '.$topic->name,
                    'status' => 'active',
                    'started_at' => now(),
                ]);

                ConversationContext::create([
                    'session_id' => $latestSession->id,
                    'current_state' => 'START',
                    'signals' => [],
                    'context_data' => [
                        'topic_name' => $topic->name,
                        'category_name' => $topic->subcategory->category->name,
                        'subcategory_name' => $topic->subcategory->name,
                        'locked' => true,
                    ],
                ]);

                $greeting = "Hai {$user->name}! Aku Weisz. Konteks obrolan kita sudah difokuskan pada topik {$topic->name}.\n\nAku di sini untuk mendengarkan tanpa menghakimi. Apa yang belakangan ini paling terasa membebani atau mengganjal pikiranmu tentang hal ini?";

                ChatMessage::create([
                    'session_id' => $latestSession->id,
                    'sender' => 'assistant',
                    'message' => $greeting,
                    'metadata' => [
                        'type' => 'greeting',
                        'suggested_chips' => $topic->suggested_chips,
                    ],
                ]);

                $userSessions = collect([$latestSession]);
            }
        }

        if ($latestSession) {
            $latestSession->load([
                'category',
                'subcategory',
                'topic',
                'context',
                'messages',
                'recommendations.professional',
            ]);

            $latestAssistantMsg = $latestSession->messages->where('sender', 'assistant')->last();
            $suggestedChips = $latestAssistantMsg->metadata['suggested_chips']
                ?? ($latestSession->topic->suggested_chips ?? [
                    'Banyak tugas numpuk',
                    'Sulit fokus belajar',
                    'Takut nilai jelek',
                    'Aku bingung mau cerita apa',
                ]);

            $currentMoodKey = $latestSession->context?->context_data['current_mood']
                ?? ($latestSession->context?->signals['user_mood'] ?? 'calm');
            $currentMoodInfo = MoodExpertService::getMood($currentMoodKey)
                ?? MoodExpertService::getMood('calm');

            $allTopics = ProblemTopic::where('is_active', true)
                ->with(['subcategory.category'])
                ->orderBy('sort_order')
                ->get();

            $startWithProfessionals = $request->boolean('professionals') || $request->boolean('pakar') || $request->query('view') === 'professionals' || $request->query('view') === 'pakar';
            $startWithTopics = ! $startWithProfessionals && ($request->boolean('topics') || $request->query('view') === 'topics');
            $startWithProfile = ! $startWithProfessionals && ! $startWithTopics && ($request->boolean('profile') || $request->query('view') === 'profile');
            $startWithExplore = ! $startWithProfessionals && ! $startWithTopics && ! $startWithProfile;

            return view('chat.show', [
                'session' => $latestSession,
                'messages' => $latestSession->messages,
                'context' => $latestSession->context,
                'topic' => $latestSession->topic,
                'suggestedChips' => $suggestedChips,
                'currentMood' => $currentMoodKey,
                'currentMoodInfo' => $currentMoodInfo,
                'userSessions' => $userSessions,
                'user' => $user,
                'exploreCategories' => $exploreCategories,
                'allTopics' => $allTopics,
                'allMoods' => $allMoods,
                'professionals' => $professionals,
                'startWithExplore' => $startWithExplore,
                'startWithProfile' => $startWithProfile,
                'startWithTopics' => $startWithTopics,
                'startWithProfessionals' => $startWithProfessionals,
            ]);
        }

        $allTopics = ProblemTopic::where('is_active', true)
            ->with(['subcategory.category'])
            ->orderBy('sort_order')
            ->get();

        $startWithProfessionals = $request->boolean('professionals') || $request->boolean('pakar') || $request->query('view') === 'professionals' || $request->query('view') === 'pakar';
        $startWithTopics = ! $startWithProfessionals && ($request->boolean('topics') || $request->query('view') === 'topics');
        $startWithProfile = ! $startWithProfessionals && ! $startWithTopics && ($request->boolean('profile') || $request->query('view') === 'profile');

        return view('chat.show', [
            'session' => null,
            'messages' => collect(),
            'context' => null,
            'topic' => null,
            'suggestedChips' => [],
            'currentMood' => 'calm',
            'currentMoodInfo' => MoodExpertService::getMood('calm'),
            'userSessions' => $userSessions,
            'user' => $user,
            'exploreCategories' => $exploreCategories,
            'allTopics' => $allTopics,
            'allMoods' => $allMoods,
            'professionals' => $professionals,
            'startWithExplore' => ! $startWithProfessionals && ! $startWithTopics && ! $startWithProfile,
            'startWithProfile' => $startWithProfile,
            'startWithTopics' => $startWithTopics,
            'startWithProfessionals' => $startWithProfessionals,
        ]);
    }

    /**
     * Fallback endpoint for POST /chat (handles submissions if session ID is omitted or posted to root chat).
     */
    public function sendFallback(
        Request $request,
        SafetyDetector $safetyDetector,
        ExpertSystemEngine $expertEngine,
        OpenRouterService $aiService
    ): JsonResponse|RedirectResponse {
        $user = Auth::user();
        $session = ChatSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (! $session) {
            $topic = ProblemTopic::where('is_active', true)
                ->with(['subcategory.category'])
                ->orderBy('sort_order')
                ->first();

            $session = ChatSession::create([
                'user_id' => $user->id,
                'category_id' => $topic?->subcategory?->category_id,
                'subcategory_id' => $topic?->subcategory_id,
                'topic_id' => $topic?->id,
                'title' => 'Sesi: '.($topic?->name ?? 'Curhat'),
                'status' => 'active',
                'started_at' => now(),
            ]);

            ConversationContext::create([
                'session_id' => $session->id,
                'current_state' => 'START',
                'signals' => [],
                'context_data' => [
                    'topic_name' => $topic?->name ?? 'Curhat',
                    'locked' => true,
                ],
            ]);
        }

        return $this->sendMessage($request, $session, $safetyDetector, $expertEngine, $aiService);
    }

    /**
     * Display AI Chatbot core interface (User Page 09).
     */
    public function show(Request $request, ChatSession $session): View
    {
        $this->authorizeSession($session);

        $session->load([
            'category',
            'subcategory',
            'topic',
            'context',
            'messages',
            'recommendations.professional',
        ]);

        $latestAssistantMsg = $session->messages->where('sender', 'assistant')->last();
        $suggestedChips = $latestAssistantMsg->metadata['suggested_chips']
            ?? ($session->topic->suggested_chips ?? [
                'Banyak tugas numpuk',
                'Sulit fokus belajar',
                'Takut nilai jelek',
                'Aku bingung mau cerita apa',
            ]);

        $currentMoodKey = $session->context?->context_data['current_mood']
            ?? ($session->context?->signals['user_mood'] ?? 'calm');
        $currentMoodInfo = MoodExpertService::getMood($currentMoodKey)
            ?? MoodExpertService::getMood('calm');
        $user = Auth::user();

        $userSessions = ChatSession::where('user_id', $user->id)
            ->where(function ($q) use ($session) {
                $q->withUserMessages()
                    ->orWhere('id', $session->id);
            })
            ->with(['topic', 'category', 'subcategory'])
            ->latest()
            ->get();

        $exploreCategories = ProblemCategory::where('is_active', true)
            ->with([
                'subcategories' => function ($q) {
                    $q->where('is_active', true)
                        ->withCount('topics')
                        ->with(['topics' => function ($tq) {
                            $tq->where('is_active', true)->orderBy('sort_order');
                        }])
                        ->orderBy('sort_order');
                },
            ])
            ->orderBy('sort_order')
            ->get();

        $allMoods = MoodExpertService::getMoods();
        $professionals = Professional::where('status', 'active')->get();

        $allTopics = ProblemTopic::where('is_active', true)
            ->with(['subcategory.category'])
            ->orderBy('sort_order')
            ->get();

        $startWithProfessionals = $request->boolean('professionals') || $request->boolean('pakar') || $request->query('view') === 'professionals' || $request->query('view') === 'pakar';
        $startWithTopics = ! $startWithProfessionals && ($request->boolean('topics') || $request->query('view') === 'topics');
        $startWithProfile = ! $startWithProfessionals && ! $startWithTopics && ($request->boolean('profile') || $request->query('view') === 'profile');
        $startWithExplore = ! $startWithProfessionals && ! $startWithTopics && ! $startWithProfile && ($request->boolean('new') || $request->boolean('explore'));

        return view('chat.show', [
            'session' => $session,
            'messages' => $session->messages,
            'context' => $session->context,
            'topic' => $session->topic,
            'suggestedChips' => $suggestedChips,
            'currentMood' => $currentMoodKey,
            'currentMoodInfo' => $currentMoodInfo,
            'userSessions' => $userSessions,
            'user' => $user,
            'exploreCategories' => $exploreCategories,
            'allTopics' => $allTopics,
            'allMoods' => $allMoods,
            'professionals' => $professionals,
            'startWithExplore' => $startWithExplore,
            'startWithProfile' => $startWithProfile,
            'startWithTopics' => $startWithTopics,
            'startWithProfessionals' => $startWithProfessionals,
        ]);
    }

    /**
     * Process message from user and generate guided AI response.
     */
    public function sendMessage(
        Request $request,
        ChatSession $session,
        SafetyDetector $safetyDetector,
        ExpertSystemEngine $expertEngine,
        OpenRouterService $aiService
    ): JsonResponse|RedirectResponse {
        $this->authorizeSession($session);

        if ($session->status === 'completed') {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi percakapan ini sudah selesai. Silakan buat percakapan baru di katalog topik.',
                ], 422);
            }

            return back()->with('error', 'Sesi percakapan ini sudah selesai.');
        }

        $isVoiceNote = $request->boolean('is_voice_note') || $request->hasFile('audio');

        $validated = $request->validate([
            'message' => [$isVoiceNote ? 'nullable' : 'required', 'string', 'max:3000'],
            'audio' => ['nullable', 'file', 'max:15360'],
            'duration' => ['nullable', 'numeric'],
            'is_voice_note' => ['nullable', 'boolean'],
        ]);

        $audioUrl = null;
        $duration = 0;

        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');
            $extension = $audioFile->getClientOriginalExtension() ?: ($audioFile->extension() ?: 'webm');
            $filename = 'vn_'.$session->id.'_'.time().'_'.uniqid().'.'.$extension;
            $audioPath = $audioFile->storeAs('voice_notes', $filename, 'public');
            $audioUrl = '/storage/'.$audioPath;
            $duration = (int) round((float) $request->input('duration', 0));
        }

        $userText = trim((string) ($validated['message'] ?? ''));

        if (! $isVoiceNote && $userText === '') {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesan tidak boleh kosong.',
                ], 422);
            }

            return back()->withErrors(['message' => 'Pesan tidak boleh kosong.']);
        }

        if ($userText === '' && $isVoiceNote) {
            $userText = 'Pesan suara';
        }

        $userMetadata = [];
        if ($isVoiceNote) {
            $userMetadata['type'] = 'voice_note';
            if ($audioUrl) {
                $userMetadata['audio_url'] = $audioUrl;
            }
            if ($duration > 0) {
                $userMetadata['duration'] = $duration;
            }
        }

        // 1. Save User Message
        $userMsg = ChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'user',
            'message' => $userText,
            'metadata' => ! empty($userMetadata) ? $userMetadata : null,
        ]);

        // 2. Evaluate Safety Detector (High Priority)
        $safetyResult = $safetyDetector->analyze($userText, $session);
        if ($safetyResult['is_critical']) {
            $assistantMsg = ChatMessage::create([
                'session_id' => $session->id,
                'sender' => 'assistant',
                'message' => $safetyResult['response_message'],
                'metadata' => [
                    'safety_alert' => true,
                    'risk_level' => 'critical',
                ],
            ]);

            $session->context?->update([
                'current_state' => 'SAFETY_ALERT',
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'user_message' => $userMsg,
                    'assistant_message' => $assistantMsg,
                    'safety_alert' => true,
                    'suggested_chips' => [],
                    'recommend_professional' => false,
                ]);
            }

            return back();
        }

        // 3. Evaluate Expert System Engine
        $expertContext = $expertEngine->evaluate($session, $userText);

        // 4. Generate AI Response via OpenRouter / Backend Fallback
        $aiResult = $aiService->generateResponse($session, $expertContext);
        $aiReply = is_array($aiResult) ? ($aiResult['content'] ?? '') : (string) $aiResult;
        $isOffTopic = is_array($aiResult) ? (! empty($aiResult['is_off_topic'])) : false;

        if (! empty($expertContext['signals']['topic_shift_requested']) || ! empty($expertContext['signals']['is_off_topic'])) {
            $isOffTopic = true;
        }

        $alreadyRecommended = $session->recommendations()->exists()
            || $session->messages()->where('sender', 'assistant')->where('metadata->recommend_professional', true)->exists();
        $userRequestedPro = $expertEngine->hasPsychiatristIntent($userText);

        $isRecommendProfessional = ! empty($expertContext['recommend_professional'])
            || (! empty($aiResult['recommend_professional']));

        if ($alreadyRecommended && ! $userRequestedPro) {
            $isRecommendProfessional = false;
        }

        if ($isRecommendProfessional) {
            $expertEngine->attachProfessionalRecommendation($session);
        }

        // Determine dynamic suggestion chips: prioritize AI-generated contextual chips, then expert system chips
        $updatedChips = ! empty($aiResult['suggested_chips'])
            ? $aiResult['suggested_chips']
            : (! empty($expertContext['suggested_chips']) ? $expertContext['suggested_chips'] : ($session->topic->suggested_chips ?? []));

        // 5. Evaluate In-Chat Mood Check-in Trigger
        $isMoodCheckin = MoodExpertService::shouldTriggerCheckin($session, $userText);

        // 6. Save Assistant Message
        $assistantMsg = ChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'assistant',
            'message' => $aiReply,
            'metadata' => [
                'state' => $expertContext['conversation_state'],
                'signals' => $expertContext['signals'],
                'recommend_professional' => $isRecommendProfessional,
                'suggested_chips' => $updatedChips,
                'is_off_topic' => $isOffTopic,
                'mood_checkin' => $isMoodCheckin,
            ],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user_message' => $userMsg,
                'assistant_message' => $assistantMsg,
                'safety_alert' => false,
                'state' => $expertContext['conversation_state'],
                'suggested_chips' => $updatedChips,
                'recommend_professional' => $isRecommendProfessional,
                'professionals' => $isRecommendProfessional ? Professional::where('status', 'active')->get() : [],
                'is_off_topic' => $isOffTopic,
                'mood_checkin' => $isMoodCheckin,
            ]);
        }

        return back();
    }

    /**
     * Update user emotional state dynamically from in-chat checkin or sidebar.
     */
    public function updateMood(Request $request, ChatSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        $validated = $request->validate([
            'mood' => ['required', 'string'],
            'message_id' => ['nullable', 'integer'],
        ]);

        $moodKey = $validated['mood'];
        if (! MoodExpertService::isValidMood($moodKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Kondisi emosi tidak valid.',
            ], 422);
        }

        // Persist selected_mood to the corresponding ChatMessage metadata
        $msgId = $validated['message_id'] ?? null;
        $targetMsg = $msgId
            ? ChatMessage::where('session_id', $session->id)->find($msgId)
            : ChatMessage::where('session_id', $session->id)
                ->where('sender', 'assistant')
                ->where('metadata->mood_checkin', true)
                ->latest('id')
                ->first();

        if ($targetMsg) {
            $meta = $targetMsg->metadata ?? [];
            $meta['selected_mood'] = $moodKey;
            $targetMsg->update(['metadata' => $meta]);
        }

        $context = $session->context;
        if ($context) {
            $contextData = $context->context_data ?? [];
            $contextData['current_mood'] = $moodKey;
            $history = $contextData['mood_history'] ?? [];
            $history[] = [
                'mood' => $moodKey,
                'time' => now()->toIso8601String(),
                'source' => 'in_chat_update',
            ];
            $contextData['mood_history'] = $history;

            $signals = $context->signals ?? [];
            $signals['user_mood'] = $moodKey;

            $context->update([
                'context_data' => $contextData,
                'signals' => $signals,
            ]);
        }

        $moodInfo = MoodExpertService::getMood($moodKey);

        return response()->json([
            'success' => true,
            'mood' => $moodKey,
            'mood_info' => $moodInfo,
            'message' => "Kondisi emosi berhasil diperbarui menjadi {$moodInfo['label']}.",
        ]);
    }

    /**
     * Contextual Chatbot Suggestions (User Page 10).
     */
    public function getSuggestions(ChatSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        $chips = $session->topic->suggested_chips ?? [
            'Sulit memulai pekerjaan',
            'Pikiran berputar terus',
            'Nggak tahu mau cerita apa',
            'Pengen saran praktis',
        ];

        return response()->json([
            'suggestions' => $chips,
        ]);
    }

    /**
     * Send proactive contextual idle suggestion if user has been AFK for > 1 minute.
     *
     * Validations:
     * 1. User is authorized and session is active.
     * 2. User has been inactive for at least 50 seconds since the last message (allowing slight network tolerance).
     * 3. The last message must be from the assistant.
     * 4. AI only sends 1 suggestion per turn without user response (cannot send consecutive idle suggestions).
     * 5. The suggestion connects directly to previous chat context.
     */
    public function sendIdleSuggestion(
        Request $request,
        ChatSession $session,
        ExpertSystemEngine $expertEngine,
        OpenRouterService $aiService
    ): JsonResponse {
        $this->authorizeSession($session);

        if ($session->status !== 'active') {
            return response()->json([
                'success' => false,
                'error' => 'session_inactive',
                'message' => 'Sesi percakapan tidak aktif.',
            ], 422);
        }

        $lastMsg = ChatMessage::where('session_id', $session->id)->latest('id')->first();

        if (! $lastMsg) {
            return response()->json([
                'success' => false,
                'error' => 'no_messages',
                'message' => 'Percakapan belum dimulai.',
            ], 422);
        }

        // Only allowed if the last message was from assistant
        if ($lastMsg->sender !== 'assistant') {
            return response()->json([
                'success' => false,
                'error' => 'awaiting_ai_response',
                'message' => 'Menunggu balasan AI sebelumnya.',
            ], 422);
        }

        // Rule 2: AI only sends 1 idle suggestion if user hasn't replied
        if (! empty($lastMsg->metadata['idle_suggestion']) || ($lastMsg->metadata['type'] ?? '') === 'idle_suggestion') {
            return response()->json([
                'success' => false,
                'error' => 'already_suggested',
                'message' => 'Saran otomatis sudah dikirimkan dan sedang menunggu balasan pengguna.',
            ], 422);
        }

        // Rule 1: Check inactivity duration (at least 50 seconds on server)
        if ($lastMsg->created_at && $lastMsg->created_at->diffInSeconds(now()) < 50) {
            return response()->json([
                'success' => false,
                'error' => 'too_soon',
                'message' => 'Belum mencapai batas waktu 1 menit tidak aktif.',
            ], 422);
        }

        // Rule 3: Generate suggestion contextually connected to previous discussion
        $latestUserMsg = ChatMessage::where('session_id', $session->id)
            ->where('sender', 'user')
            ->latest('id')
            ->first()?->message ?? '';
        $expertContext = $expertEngine->evaluate($session, $latestUserMsg);

        $suggestionResult = $aiService->generateIdleSuggestion($session, $expertContext);
        $suggestionText = $suggestionResult['content'] ?? '';
        $chips = $suggestionResult['suggested_chips'] ?? ($session->topic->suggested_chips ?? []);

        // Save Assistant Idle Suggestion Message
        $assistantMsg = ChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'assistant',
            'message' => $suggestionText,
            'metadata' => [
                'type' => 'idle_suggestion',
                'idle_suggestion' => true,
                'suggested_chips' => $chips,
            ],
        ]);

        return response()->json([
            'success' => true,
            'assistant_message' => $assistantMsg,
            'suggested_chips' => $chips,
        ]);
    }

    /**
     * Cancel an unrendered idle suggestion message if user started typing.
     */
    public function cancelIdleSuggestion(Request $request, ChatSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        $messageId = $request->input('message_id');
        if ($messageId) {
            $msg = ChatMessage::where('session_id', $session->id)->find($messageId);
            if ($msg && $msg->sender === 'assistant' && ! empty($msg->metadata['idle_suggestion'])) {
                $msg->delete();
            }
        } else {
            $lastMsg = ChatMessage::where('session_id', $session->id)->latest('id')->first();
            if ($lastMsg && $lastMsg->sender === 'assistant' && ! empty($lastMsg->metadata['idle_suggestion'])) {
                $lastMsg->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Saran idle dibatalkan karena pengguna sedang mengetik.',
        ]);
    }

    /**
     * Generate or view session summary without locking the session.
     */
    public function complete(ChatSession $session, SummaryGenerator $summaryGenerator): RedirectResponse
    {
        $this->authorizeSession($session);

        $summaryGenerator->generate($session);

        return redirect()->route('chat.summary', $session->id)->with('success', 'Rangkuman percakapan berhasil diperbarui. Kamu tetap bisa melanjutkan obrolan kapan saja.');
    }

    /**
     * Display Conversation Summary (User Page 11).
     */
    public function summary(ChatSession $session, SummaryGenerator $summaryGenerator): View
    {
        $this->authorizeSession($session);

        $summary = $session->summary ?? $summaryGenerator->generate($session);

        return view('chat.summary', [
            'session' => $session,
            'summary' => $summary,
            'recommendations' => $session->recommendations()->with('professional')->get(),
        ]);
    }

    /**
     * Display Professional Recommendations for this session (User Page 12).
     */
    public function sessionProfessionals(ChatSession $session): View
    {
        $this->authorizeSession($session);

        $sessionRecs = $session->recommendations()->with('professional')->get();

        // If no specific recommendations yet, load active database professionals
        $professionals = $sessionRecs->isNotEmpty()
            ? $sessionRecs->pluck('professional')
            : Professional::where('status', 'active')->take(6)->get();

        return view('chat.professionals', [
            'session' => $session,
            'professionals' => $professionals,
            'sessionRecommendations' => $sessionRecs,
        ]);
    }

    /**
     * Leave chat room. If user has not sent any message yet, delete session so it's not saved in history.
     */
    public function leave(Request $request, ChatSession $session): JsonResponse|RedirectResponse
    {
        $this->authorizeSession($session);

        $hasUserMessages = $session->hasUserMessages();

        if (! $hasUserMessages) {
            $session->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'deleted' => ! $hasUserMessages,
            ]);
        }

        return redirect()->route('chat.index');
    }

    /**
     * Ensure the authenticated user owns this session or is admin.
     */
    protected function authorizeSession(ChatSession $session): void
    {
        if (Auth::id() !== $session->user_id && ! Auth::user()?->isAdmin()) {
            abort(403, 'Kamu tidak memiliki akses ke percakapan ini.');
        }
    }
}
