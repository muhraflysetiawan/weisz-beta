<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ExplorationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - WEISZ
|--------------------------------------------------------------------------
| Exactly 20 primary pages: 14 User + 6 Admin.
*/

// 01. Landing Page
Route::get('/', [HomeController::class, 'landing'])->name('landing');

// Language Switcher (Public for guests & authenticated users)
Route::post('/locale', [AuthController::class, 'updateLocale'])->name('locale.update');

// CSRF Token refresh endpoint
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    // 02. Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // 03. Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Redirect any legacy dashboard/home URLs to chat
    Route::redirect('/dashboard', '/chat');
    Route::redirect('/home', '/chat');

    // Mulai Curhat: Directs to guided Category selection
    Route::get('/curhat', function () {
        return redirect()->route('chat.index');
    })->name('chat.quick');

    // Jelajahi Semua Topik (Katalog & Search)
    Route::get('/topics', [ExplorationController::class, 'allTopics'])->name('topics.explore');

    // 05. Problem Explorer (Pilih Kategori)
    Route::get('/explore', [ExplorationController::class, 'categories'])->name('explore.categories');

    // 06. Subcategory
    Route::get('/explore/{category:slug}', [ExplorationController::class, 'subcategories'])->name('explore.subcategories');

    // 07. Topic Selection
    Route::get('/explore/{category:slug}/{subcategory:slug}', [ExplorationController::class, 'topics'])->name('explore.topics');

    // 08. Context Confirmation
    Route::get('/chat/confirm/{topic:slug}', [ExplorationController::class, 'confirm'])->name('chat.confirm');
    Route::match(['get', 'post'], '/chat/start/{topic:slug}', [ExplorationController::class, 'start'])->name('chat.start');

    // 09. AI Chatbot (Core Page)
    Route::get('/chat', [ChatbotController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatbotController::class, 'sendFallback'])->name('chat.send-fallback');
    Route::get('/chat/{session}', [ChatbotController::class, 'show'])->name('chat.show');
    Route::post('/chat/{session}/send', [ChatbotController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/{session}/update-mood', [ChatbotController::class, 'updateMood'])->name('chat.update-mood');
    Route::match(['get', 'post'], '/chat/{session}/leave', [ChatbotController::class, 'leave'])->name('chat.leave');

    // 10. Chatbot Suggestion
    Route::get('/chat/{session}/suggestions', [ChatbotController::class, 'getSuggestions'])->name('chat.suggestions');
    Route::post('/chat/{session}/idle-suggestion', [ChatbotController::class, 'sendIdleSuggestion'])->name('chat.idle-suggestion');
    Route::post('/chat/{session}/cancel-idle-suggestion', [ChatbotController::class, 'cancelIdleSuggestion'])->name('chat.cancel-idle-suggestion');

    // Complete session action
    Route::post('/chat/{session}/complete', [ChatbotController::class, 'complete'])->name('chat.complete');

    // 11. Conversation Summary
    Route::get('/chat/{session}/summary', [ChatbotController::class, 'summary'])->name('chat.summary');

    // 12. Professional Recommendation (Session & General)
    Route::get('/chat/{session}/professionals', [ChatbotController::class, 'sessionProfessionals'])->name('chat.professionals');
    Route::get('/professionals', [HomeController::class, 'professionals'])->name('professionals');

    // 13. Conversation History
    Route::get('/history', [HomeController::class, 'history'])->name('history');

    // 14. Profile & Settings
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // ==========================================
    // ADMIN ROUTES (Admin Pages 15 - 20)
    // ==========================================
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // 15. Admin Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // 16. Category Management
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::post('/subcategories', [AdminController::class, 'storeSubcategory'])->name('subcategories.store');
        Route::post('/topics', [AdminController::class, 'storeTopic'])->name('topics.store');
        Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');
        Route::delete('/subcategories/{subcategory}', [AdminController::class, 'deleteSubcategory'])->name('subcategories.destroy');
        Route::delete('/topics/{topic}', [AdminController::class, 'deleteTopic'])->name('topics.destroy');

        // 17. Knowledge Base
        Route::get('/knowledge-base', [AdminController::class, 'knowledgeBase'])->name('knowledge');
        Route::post('/knowledge-base', [AdminController::class, 'storeKnowledge'])->name('knowledge.store');
        Route::delete('/knowledge-base/{knowledge}', [AdminController::class, 'deleteKnowledge'])->name('knowledge.destroy');

        // 18. Expert Rule Management
        Route::get('/rules', [AdminController::class, 'rules'])->name('rules');
        Route::post('/rules', [AdminController::class, 'storeRule'])->name('rules.store');
        Route::post('/rules/{rule}/toggle', [AdminController::class, 'toggleRule'])->name('rules.toggle');
        Route::delete('/rules/{rule}', [AdminController::class, 'deleteRule'])->name('rules.destroy');

        // 19. Professional Management
        Route::get('/professionals', [AdminController::class, 'professionals'])->name('professionals');
        Route::post('/professionals', [AdminController::class, 'storeProfessional'])->name('professionals.store');
        Route::put('/professionals/{professional}', [AdminController::class, 'updateProfessional'])->name('professionals.update');
        Route::delete('/professionals/{professional}', [AdminController::class, 'deleteProfessional'])->name('professionals.destroy');

        // 20. Conversation Monitoring
        Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');
    });
});
