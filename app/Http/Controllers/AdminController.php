<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ExpertRule;
use App\Models\KnowledgeBase;
use App\Models\ProblemCategory;
use App\Models\ProblemSubcategory;
use App\Models\ProblemTopic;
use App\Models\Professional;
use App\Models\RiskAssessment;
use App\Models\RuleAction;
use App\Models\RuleCondition;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Admin Dashboard (Admin Page 15).
     */
    public function dashboard(): View
    {
        $totalSessions = ChatSession::count();
        $totalUsers = User::where('role', 'user')->count();
        $activeRulesCount = ExpertRule::where('is_active', true)->count();
        $professionalsCount = Professional::where('status', 'active')->count();
        $safetyAlertsCount = RiskAssessment::count();

        $recentSessions = ChatSession::with(['user', 'topic', 'category'])
            ->latest()
            ->take(6)
            ->get();

        $recentSafetyEvents = RiskAssessment::with(['session.user', 'session.topic'])
            ->latest()
            ->take(5)
            ->get();

        $topicStats = ProblemTopic::withCount('chatSessions')
            ->orderByDesc('chat_sessions_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'totalSessions' => $totalSessions,
            'totalUsers' => $totalUsers,
            'activeRulesCount' => $activeRulesCount,
            'professionalsCount' => $professionalsCount,
            'safetyAlertsCount' => $safetyAlertsCount,
            'recentSessions' => $recentSessions,
            'recentSafetyEvents' => $recentSafetyEvents,
            'topicStats' => $topicStats,
        ]);
    }

    /**
     * Category, Subcategory & Topic Management (Admin Page 16).
     */
    public function categories(): View
    {
        $categories = ProblemCategory::with(['subcategories.topics'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories', [
            'categories' => $categories,
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        ProblemCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'folder',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori utama berhasil ditambahkan.');
    }

    public function storeSubcategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:problem_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        ProblemSubcategory::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Subkategori berhasil ditambahkan.');
    }

    public function storeTopic(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subcategory_id' => ['required', 'exists:problem_subcategories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'prompt_context' => ['nullable', 'string'],
            'suggested_chips' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $chips = ! empty($validated['suggested_chips'])
            ? array_values(array_filter(array_map('trim', explode(',', $validated['suggested_chips']))))
            : [];

        ProblemTopic::create([
            'subcategory_id' => $validated['subcategory_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            'description' => $validated['description'] ?? null,
            'prompt_context' => $validated['prompt_context'] ?? null,
            'suggested_chips' => $chips,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Topik percakapan baru berhasil ditambahkan.');
    }

    public function deleteCategory(ProblemCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function deleteSubcategory(ProblemSubcategory $subcategory): RedirectResponse
    {
        $subcategory->delete();

        return back()->with('success', 'Subkategori berhasil dihapus.');
    }

    public function deleteTopic(ProblemTopic $topic): RedirectResponse
    {
        $topic->delete();

        return back()->with('success', 'Topik berhasil dihapus.');
    }

    /**
     * Knowledge Base Management (Admin Page 17).
     */
    public function knowledgeBase(): View
    {
        $knowledgeItems = KnowledgeBase::with(['category', 'subcategory', 'topic'])
            ->latest()
            ->paginate(10);

        $categories = ProblemCategory::where('is_active', true)->get();
        $topics = ProblemTopic::where('is_active', true)->get();

        return view('admin.knowledge', [
            'knowledgeItems' => $knowledgeItems,
            'categories' => $categories,
            'topics' => $topics,
        ]);
    }

    public function storeKnowledge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:problem_categories,id'],
            'topic_id' => ['nullable', 'exists:problem_topics,id'],
            'content' => ['required', 'string'],
            'reference_source' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published'],
        ]);

        KnowledgeBase::create($validated);

        return back()->with('success', 'Entri Knowledge Base berhasil disimpan.');
    }

    public function deleteKnowledge(KnowledgeBase $knowledge): RedirectResponse
    {
        $knowledge->delete();

        return back()->with('success', 'Entri Knowledge Base berhasil dihapus.');
    }

    /**
     * Expert Rule Management (Admin Page 18).
     */
    public function rules(): View
    {
        $rules = ExpertRule::with(['topic', 'conditions', 'actions'])
            ->orderByDesc('priority')
            ->get();

        $topics = ProblemTopic::where('is_active', true)->get();

        return view('admin.rules', [
            'rules' => $rules,
            'topics' => $topics,
        ]);
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'topic_id' => ['nullable', 'exists:problem_topics,id'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'integer'],
            'condition_fact_key' => ['required', 'string'],
            'condition_operator' => ['required', 'string'],
            'condition_fact_value' => ['required', 'string'],
            'action_type' => ['required', 'string'],
            'action_directive' => ['nullable', 'string'],
        ]);

        $rule = ExpertRule::create([
            'name' => $validated['name'],
            'topic_id' => $validated['topic_id'] ?: null,
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'is_active' => true,
        ]);

        RuleCondition::create([
            'expert_rule_id' => $rule->id,
            'fact_key' => $validated['condition_fact_key'],
            'operator' => $validated['condition_operator'],
            'fact_value' => $validated['condition_fact_value'],
        ]);

        RuleAction::create([
            'expert_rule_id' => $rule->id,
            'action_type' => $validated['action_type'],
            'action_payload' => [
                'directive' => $validated['action_directive'] ?? '',
            ],
        ]);

        return back()->with('success', 'Aturan Expert System berhasil dibuat.');
    }

    public function toggleRule(ExpertRule $rule): RedirectResponse
    {
        $rule->is_active = ! $rule->is_active;
        $rule->save();

        return back()->with('success', 'Status keaktifan aturan diperbarui.');
    }

    public function deleteRule(ExpertRule $rule): RedirectResponse
    {
        $rule->delete();

        return back()->with('success', 'Aturan Expert System berhasil dihapus.');
    }

    /**
     * Professional Data Management (Admin Page 19).
     */
    public function professionals(): View
    {
        $professionals = Professional::latest()->paginate(10);

        return view('admin.professionals', [
            'professionals' => $professionals,
        ]);
    }

    public function storeProfessional(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profession' => ['required', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Professional::create($validated);

        return back()->with('success', 'Data profesional berhasil didaftarkan.');
    }

    public function updateProfessional(Request $request, Professional $professional): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profession' => ['required', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $professional->update($validated);

        return back()->with('success', 'Data profesional berhasil diperbarui.');
    }

    public function deleteProfessional(Professional $professional): RedirectResponse
    {
        $professional->delete();

        return back()->with('success', 'Data profesional berhasil dihapus.');
    }

    /**
     * Conversation Monitoring (Admin Page 20).
     */
    public function monitoring(Request $request): View
    {
        $query = ChatSession::with(['user', 'topic', 'category', 'context', 'riskAssessments'])
            ->latest();

        if ($request->filled('filter') && $request->filter === 'safety') {
            $query->whereHas('riskAssessments');
        }

        $sessions = $query->paginate(15)->withQueryString();

        $safetyAssessments = RiskAssessment::with(['session.user', 'session.topic'])
            ->latest()
            ->paginate(10, ['*'], 'safety_page');

        return view('admin.monitoring', [
            'sessions' => $sessions,
            'safetyAssessments' => $safetyAssessments,
        ]);
    }
}
