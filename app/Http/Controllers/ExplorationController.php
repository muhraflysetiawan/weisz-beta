<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ConversationContext;
use App\Models\ProblemCategory;
use App\Models\ProblemSubcategory;
use App\Models\ProblemTopic;
use App\Models\Professional;
use App\Models\SessionRecommendation;
use App\Services\MoodExpertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExplorationController extends Controller
{
    public function allTopics(Request $request): RedirectResponse
    {
        return redirect()->route('chat.index', array_merge($request->query(), ['topics' => 1]));
    }

    public function categories(): View
    {
        $categories = ProblemCategory::where('is_active', true)
            ->withCount('subcategories')
            ->with(['subcategories' => function ($q) {
                $q->where('is_active', true)->withCount('topics');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('user.explore-categories', [
            'categories' => $categories,
        ]);
    }

    public function subcategories(string $categorySlug): View
    {
        $category = ProblemCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $subcategories = ProblemSubcategory::where('category_id', $category->id)
            ->where('is_active', true)
            ->withCount('topics')
            ->orderBy('sort_order')
            ->get();

        return view('user.explore-subcategories', [
            'category' => $category,
            'subcategories' => $subcategories,
        ]);
    }

    public function topics(string $categorySlug, string $subcategorySlug): View
    {
        $category = ProblemCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $subcategory = ProblemSubcategory::where('category_id', $category->id)
            ->where('slug', $subcategorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $topics = ProblemTopic::where('subcategory_id', $subcategory->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('user.explore-topics', [
            'category' => $category,
            'subcategory' => $subcategory,
            'topics' => $topics,
        ]);
    }

    public function confirm(string $topicSlug): View
    {
        $topic = ProblemTopic::where('slug', $topicSlug)
            ->where('is_active', true)
            ->with(['subcategory.category'])
            ->firstOrFail();

        return view('user.context-confirm', [
            'topic' => $topic,
            'subcategory' => $topic->subcategory,
            'category' => $topic->subcategory->category,
        ]);
    }

    public function start(Request $request, string $topicSlug): RedirectResponse
    {
        $topic = ProblemTopic::where('slug', $topicSlug)
            ->where('is_active', true)
            ->with(['subcategory.category'])
            ->firstOrFail();

        $user = Auth::user();

        $rawMood = (string) $request->input('initial_mood', 'calm');
        $initialMood = MoodExpertService::isValidMood($rawMood) ? $rawMood : 'calm';

        // Clean up any stale sessions where user never sent any message
        ChatSession::where('user_id', $user->id)
            ->whereDoesntHave('messages', function ($q) {
                $q->where('sender', 'user');
            })
            ->delete();

        // Create new chat session with context lock
        $session = ChatSession::create([
            'user_id' => $user->id,
            'category_id' => $topic->subcategory->category_id,
            'subcategory_id' => $topic->subcategory_id,
            'topic_id' => $topic->id,
            'title' => 'Sesi: '.$topic->name,
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Match and record professional (pakar) for this topic
        $matchedPro = $this->matchProfessionalForTopic($topic);

        if ($matchedPro) {
            SessionRecommendation::firstOrCreate([
                'session_id' => $session->id,
                'professional_id' => $matchedPro->id,
            ], [
                'reason' => 'Rujukan tenaga profesional sesuai topik '.$topic->name,
                'notes' => 'Tercatat otomatis saat memulai sesi percakapan topik.',
            ]);
        }

        // Initialize Context with Mood & matched professional info
        ConversationContext::create([
            'session_id' => $session->id,
            'current_state' => 'START',
            'signals' => [
                'user_mood' => $initialMood,
            ],
            'context_data' => [
                'topic_name' => $topic->name,
                'category_name' => $topic->subcategory->category->name,
                'subcategory_name' => $topic->subcategory->name,
                'locked' => true,
                'initial_mood' => $initialMood,
                'current_mood' => $initialMood,
                'assigned_professional_id' => $matchedPro?->id,
                'assigned_professional_name' => $matchedPro?->name,
                'assigned_professional_title' => $matchedPro?->profession,
                'mood_history' => [
                    [
                        'mood' => $initialMood,
                        'time' => now()->toIso8601String(),
                        'source' => 'initial_confirmation',
                    ],
                ],
            ],
        ]);

        // Generate warm greeting tailored to user mood from Weisz
        $greeting = match ($initialMood) {
            'overwhelmed' => "Hai {$user->name}... Aku Weisz. Aku menangkap bahwa perasaanmu saat ini sedang sangat berat dan terpuruk. Konteks percakapan kita difokuskan pada topik {$topic->name}.\n\nKamu tidak sendirian di sini. Tarik napas pelan-pelan ya. Jika kamu sudah siap, apa hal yang paling membuatmu merasa hancur atau terbebani belakangan ini?",
            'anxious' => "Hai {$user->name}. Aku Weisz. Aku mengerti saat ini pikiranmu sedang diselimuti rasa cemas dan gelisah mengenai {$topic->name}.\n\nSantai aja ya, kita urai satu per satu bersama tanpa tekanan. Hal apa yang saat ini paling bikin pikiranmu berputar terus?",
            'sad' => "Hai {$user->name}. Aku Weisz. Aku merasakan kesedihan dan rasa lelah yang sedang kamu alami seputar {$topic->name}.\n\nAku di sini untuk mendengarkan tanpa menghakimi. Ceritakan apa saja yang membuat hatimu merasa down atau terluka belakangan ini.",
            'numb' => "Hai {$user->name}. Aku Weisz. Aku mengerti kalau saat ini kamu merasa hampa, kosong, atau bingung harus mulai dari mana terkait {$topic->name}.\n\nNggak apa-apa banget kok kalau merasa blank. Pelan-pelan aja ya, apa yang paling mengganjal di hatimu saat ini?",
            default => "Hai {$user->name}! Aku Weisz. Konteks obrolan kita sudah difokuskan pada topik {$topic->name}.\n\nAku di sini untuk mendengarkan tanpa menghakimi. Apa yang belakangan ini paling terasa membebani atau mengganjal pikiranmu tentang hal ini?",
        };

        ChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'assistant',
            'message' => $greeting,
            'metadata' => [
                'type' => 'greeting',
                'mood' => $initialMood,
                'suggested_chips' => $topic->suggested_chips,
                'assigned_professional_id' => $matchedPro?->id,
                'assigned_professional_name' => $matchedPro?->name,
            ],
        ]);

        return redirect()->route('chat.show', $session->id);
    }

    /**
     * Match the most suitable professional/pakar for a topic.
     */
    protected function matchProfessionalForTopic(ProblemTopic $topic): ?Professional
    {
        $catSlug = strtolower($topic->subcategory->category->slug ?? '');
        $catName = strtolower($topic->subcategory->category->name ?? '');
        $subcatName = strtolower($topic->subcategory->name ?? '');
        $topicName = strtolower($topic->name);
        $fullText = $catSlug.' '.$catName.' '.$subcatName.' '.$topicName.' '.strtolower((string) $topic->description);

        $matched = null;

        if (str_contains($fullText, 'relasi') || str_contains($fullText, 'interpersonal') || str_contains($fullText, 'sosial') || str_contains($fullText, 'keluarga') || str_contains($fullText, 'teman') || str_contains($fullText, 'sahabat') || str_contains($fullText, 'circle') || str_contains($fullText, 'pasangan') || str_contains($fullText, 'boundaries') || str_contains($fullText, 'pertengkaran')) {
            $matched = Professional::where('status', 'active')->where('name', 'like', '%Farida%')->first();
        } elseif (str_contains($fullText, 'gaya-hidup') || str_contains($fullText, 'kebiasaan') || str_contains($fullText, 'prokrastinasi') || str_contains($fullText, 'adhd') || str_contains($fullText, 'menunda') || str_contains($fullText, 'regulasi')) {
            $matched = Professional::where('status', 'active')->where('name', 'like', '%Dimas%')->first();
        } elseif (str_contains($fullText, 'kesehatan-mental') || str_contains($fullText, 'krisis') || str_contains($fullText, 'tidur') || str_contains($fullText, 'insomnia') || str_contains($fullText, 'depresi') || str_contains($fullText, 'panik') || str_contains($fullText, 'burnout') || str_contains($fullText, 'psikosomatis')) {
            $matched = Professional::where('status', 'active')->where('name', 'like', '%Reyhan%')->first();
        } elseif (str_contains($fullText, 'akademik') || str_contains($fullText, 'karier') || str_contains($fullText, 'karir') || str_contains($fullText, 'kuliah') || str_contains($fullText, 'ujian') || str_contains($fullText, 'skripsi') || str_contains($fullText, 'ipk')) {
            $matched = Professional::where('status', 'active')->where('name', 'like', '%Nadia%')->first();
        }

        return $matched ?: Professional::where('status', 'active')->first();
    }
}
