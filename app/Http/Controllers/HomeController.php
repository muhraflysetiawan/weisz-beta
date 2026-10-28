<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ProblemCategory;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function landing(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('chat.index');
        }

        $categories = ProblemCategory::where('is_active', true)
            ->withCount('subcategories')
            ->orderBy('sort_order')
            ->get();

        $professionalsCount = Professional::where('status', 'active')->count();

        return view('user.landing', [
            'categories' => $categories,
            'professionalsCount' => $professionalsCount,
        ]);
    }

    public function history(Request $request): View
    {
        $user = Auth::user();

        // Clean up unengaged/empty sessions where user never typed or sent any message
        ChatSession::where('user_id', $user->id)
            ->whereDoesntHave('messages', function ($q) {
                $q->where('sender', 'user');
            })
            ->delete();

        $query = ChatSession::where('user_id', $user->id)
            ->withUserMessages()
            ->with(['category', 'subcategory', 'topic', 'summary', 'recommendations.professional'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('topic', function ($tq) use ($search) {
                        $tq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sessions = $query->paginate(10)->withQueryString();

        return view('user.history', [
            'sessions' => $sessions,
        ]);
    }

    public function professionals(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('chat.index', array_merge($request->query(), ['professionals' => 1]));
        }

        $query = Professional::where('status', 'active');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('profession', 'like', "%{$term}%")
                    ->orWhere('specialization', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhere('address', 'like', "%{$term}%");
            });
        }

        $professionals = $query->paginate(12)->withQueryString();

        return view('user.professionals', [
            'professionals' => $professionals,
        ]);
    }
}
