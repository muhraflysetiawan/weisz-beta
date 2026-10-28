@extends('layouts.app')

@section('title', 'AI Chatbot — ' . ($topic->name ?? 'Obrolan Baru'))
@section('body_class', 'h-screen h-[100dvh] overflow-hidden')

@section('full_content')
<style>
    .chat-app-container {
        height: 100vh;
        height: 100dvh;
        max-height: 100vh;
        max-height: 100dvh;
    }
    #chatComposerArea {
        padding-bottom: max(0.75rem, env(safe-area-inset-bottom, 0.75rem));
    }
    @media (max-width: 767px) {
        #messagesContainer {
            min-height: 0 !important;
        }
        #chatComposerArea {
            position: relative;
            z-index: 25;
        }
    }
    #chatSidebar {
        transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #chatSidebar.sidebar-collapsed {
        width: 72px !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-text {
        display: none !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-logo {
        display: none !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-header-box {
        padding: 0.875rem 0.5rem !important;
        justify-content: center !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-actions-box {
        padding: 0.625rem 0.5rem !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-nav-btn {
        width: 40px !important;
        height: 40px !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 0.75rem !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-collapsed-tile {
        display: flex !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-history-box {
        padding: 0.625rem 0.375rem !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-history-list {
        display: none !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-minimized-history {
        display: flex !important;
    }
    #chatSidebar.sidebar-collapsed .sidebar-footer-box {
        padding: 0.625rem 0.5rem !important;
        display: flex !important;
        justify-content: center !important;
    }
    #chatSidebar.sidebar-collapsed #accountTriggerBtn {
        width: 40px !important;
        height: 40px !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 0.75rem !important;
    }
    #chatSidebar.sidebar-collapsed #accountPopover {
        left: 0.75rem !important;
        right: auto !important;
        width: 230px !important;
    }
    .pro-slider-track {
        scrollbar-width: thin;
        scrollbar-color: var(--border-color) transparent;
        -webkit-overflow-scrolling: touch;
    }
    .pro-slider-track::-webkit-scrollbar {
        height: 5px;
    }
    .pro-slider-track::-webkit-scrollbar-track {
        background: transparent;
    }
    .pro-slider-track::-webkit-scrollbar-thumb {
        background-color: var(--border-color);
        border-radius: 9999px;
    }
    .pro-slider-track::-webkit-scrollbar-thumb:hover {
        background-color: var(--txt-tertiary);
    }
</style>

@php
    $registeredPros = $professionals ?? \App\Models\Professional::where('status', 'active')->get();
@endphp

<div class="flex h-screen h-[100dvh] w-full overflow-hidden relative bg-[var(--bg-page)] transition-colors chat-app-container">
    <!-- Ambient Glowing Orbs (Gradasi warna tetap ada, background polos tanpa ornamen grid/garis) -->
    <div class="absolute -top-36 -left-24 w-[520px] h-[520px] rounded-full bg-gradient-to-br from-amber-200/40 via-rose-200/25 to-transparent blur-3xl pointer-events-none z-0" aria-hidden="true"></div>
    <div class="absolute -bottom-36 -right-24 w-[580px] h-[580px] rounded-full bg-gradient-to-tl from-sky-200/40 via-blue-200/25 to-transparent blur-3xl pointer-events-none z-0" aria-hidden="true"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] rounded-full bg-gradient-to-tr from-purple-200/15 via-rose-100/10 to-transparent blur-3xl pointer-events-none z-0" aria-hidden="true"></div>

    <!-- ======================================================== -->
    <!-- 1. LEFT SIDEBAR                                          -->
    <!-- Logo "WeisZ", Button "Obrolan Baru", Topics & Pakar      -->
    <!-- Riwayat Chat, Footer Akun                                -->
    <!-- ======================================================== -->
    <aside id="chatSidebar" class="w-72 lg:w-80 h-full h-screen h-[100dvh] bg-white/95 dark:bg-[#1f1f1f]/95 backdrop-blur-md border-r border-[var(--border-color)] flex flex-col shrink-0 transition-all duration-300 z-50 fixed inset-y-0 left-0 md:static -translate-x-full md:translate-x-0 shadow-xl md:shadow-none select-none">
        
        <!-- a. Bagian atas: Logo "WeisZ" + Collapse Hamburger + Mobile Close -->
        <div id="sidebarHeaderBox" class="sidebar-header-box p-3.5 sm:p-4 border-b border-[var(--border-color)] flex items-center justify-between shrink-0">
            <!-- Logo Website (Sembunyi saat minimize) -->
            <a href="{{ route('chat.index') }}" id="sidebarLogo" class="sidebar-logo flex items-center gap-2.5 sm:gap-3 group min-w-0" title="WeisZ">
                <div class="w-10 h-10 rounded-xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center font-bold text-lg sm:text-xl shadow-2xs group-hover:scale-105 transition-transform shrink-0">
                    W
                </div>
                <span class="sidebar-text font-bold text-xl sm:text-2xl tracking-tight text-[var(--txt-primary)] truncate">WeisZ</span>
            </a>

            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Desktop Collapse Button (Hamburger icon, saat minimize menggantikan posisi logo) -->
                <button type="button" id="collapseSidebarBtn" aria-label="Kecilkan Sidebar" title="Kecilkan Sidebar" class="hidden md:flex w-10 h-10 rounded-xl border border-[var(--border-color)] items-center justify-center text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] hover:bg-[var(--card-hover)] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Mobile Close Button (Hanya tampil di mobile drawer) -->
                <button type="button" id="closeSidebarBtn" aria-label="Tutup Menu" class="md:hidden w-10 h-10 rounded-xl border border-[var(--border-color)] flex items-center justify-center text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- b. Dibawahnya: Button "Obrolan Baru", "Topics", & "Pakar" (Kebawah, Tanpa Icon, Style Mirip List History) -->
        <div class="sidebar-actions-box p-3 sm:p-4 border-b border-[var(--border-color)]/70 shrink-0 flex flex-col gap-1.5">
            <!-- 1. Obrolan Baru -->
            <button type="button" id="sidebarNewChatBtn" class="sidebar-nav-btn group block w-full px-3.5 py-2.5 rounded-xl text-left transition  text-[14.5px] sm:text-[15px] text-[var(--txt-primary)] hover:bg-[var(--card-hover)]/70 border border-transparent hover:border-[var(--border-color)]/60 cursor-pointer" title="{{ __('Obrolan Baru') }}">
                <span class="sidebar-text truncate block">{{ __('Obrolan Baru') }}</span>
                <span class="sidebar-collapsed-tile hidden items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--txt-primary)]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </span>
            </button>

            <!-- 2. Topics -->
            <button type="button" id="sidebarTopicsBtn" onclick="openTopicsSection()" class="sidebar-nav-btn group block w-full px-3.5 py-2.5 rounded-xl text-left transition font-medium text-[14.5px] sm:text-[15px] text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] hover:bg-[var(--card-hover)]/70 border border-transparent hover:border-[var(--border-color)]/60 cursor-pointer" title="{{ __('Topics') }}">
                <span class="sidebar-text truncate block">{{ __('Topics') }}</span>
                <span class="sidebar-collapsed-tile hidden items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--txt-secondary)] group-hover:text-[var(--txt-primary)]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </span>
            </button>

            <!-- 3. Pakar -->
            <button type="button" id="sidebarProfessionalsBtn" onclick="openProfessionalsSection()" class="sidebar-nav-btn group block w-full px-3.5 py-2.5 rounded-xl text-left transition font-medium text-[14.5px] sm:text-[15px] text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] hover:bg-[var(--card-hover)]/70 border border-transparent hover:border-[var(--border-color)]/60 cursor-pointer" title="{{ __('Pakar') }}">
                <span class="sidebar-text truncate block">{{ __('Pakar') }}</span>
                <span class="sidebar-collapsed-tile hidden items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--txt-secondary)] group-hover:text-[var(--txt-primary)]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </span>
            </button>
        </div>

        <!-- c. Dibawahnya: Riwayat Chat User (Saat minimize hanya ada icon history) -->
        <div class="sidebar-history-box flex-1 overflow-y-auto p-3 space-y-1.5 no-scrollbar chat-room-scroll">
            <div class="sidebar-text px-2 py-1 text-xs sm:text-sm font-bold text-[var(--txt-tertiary)] uppercase tracking-wider flex items-center justify-between">
                <span>{{ __('Riwayat Obrolan') }}</span>
            </div>

            <!-- Icon History saat minimize (Hanya tampil saat minimize, list history disembunyikan) -->
            <div class="sidebar-minimized-history hidden items-center justify-center py-1">
                <button type="button" onclick="toggleSidebarCollapse()" class="w-10 h-10 rounded-xl flex items-center justify-center text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] hover:bg-[var(--card-hover)] border border-transparent hover:border-[var(--border-color)]/60 transition cursor-pointer" title="{{ __('Riwayat Obrolan') }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </button>
            </div>

            <!-- List History Obrolan (Hanya tampil saat sidebar terbuka) -->
            <div class="sidebar-history-list space-y-1.5">
                @forelse($userSessions as $s)
                    @php $isActive = $session && ($s->id === $session->id); @endphp
                    <a href="{{ route('chat.show', $s->id) }}"
                       class="sidebar-history-item group block px-3 py-2.5 rounded-xl transition text-left relative {{ $isActive ? 'bg-[var(--card-hover)] font-semibold text-[var(--txt-primary)] border border-[var(--border-color)] shadow-2xs' : 'text-[var(--txt-secondary)] hover:bg-[var(--card-hover)]/70 hover:text-[var(--txt-primary)] border border-transparent' }}"
                       title="{{ $s->topic->name ?? $s->title }}">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-[14.5px] sm:text-[15.5px] {{ $isActive ? 'font-bold text-[var(--txt-primary)]' : 'font-medium' }}">
                                {{ $s->topic->name ?? $s->title ?? __('Percakapan Weisz') }}
                            </p>
                            @if($isActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" title="{{ __('Sedang Dibuka') }}"></span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-[13px] text-[var(--txt-tertiary)] truncate mt-0.5">
                            {{ $s->created_at->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <div class="sidebar-history-empty px-3 py-6 text-center text-sm sm:text-[15px] text-[var(--txt-tertiary)]">
                        {{ __('Belum ada riwayat obrolan lainnya') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- d. Dibagian footer: Informasi account (profile dan nama), klik muncul "Profile" dan "Setting" -->
        <div class="sidebar-footer-box p-3 border-t border-[var(--border-color)] relative shrink-0">
            <!-- Popover Menu: Profile -->
            <div id="accountPopover" class="hidden absolute bottom-full left-3 right-3 mb-2 rounded-2xl bg-[var(--card-surface)] border border-[var(--border-color)] shadow-xl p-2 z-50 space-y-1 transition-all">
                <div onclick="openProfileSection()" class="px-3 py-2 border-b border-[var(--border-color)]/70 hover:bg-[var(--card-hover)] rounded-xl cursor-pointer transition group" title="{{ __('Buka Profil') }}">
                    <p class="text-sm sm:text-base font-bold text-[var(--txt-primary)] group-hover:text-[var(--brand-main)] transition truncate">{{ $user->name }}</p>
                    <p class="text-xs sm:text-[13px] text-[var(--txt-tertiary)] truncate">{{ $user->email }}</p>
                </div>

                <!-- Option: Profile -->
                <button type="button" id="sidebarProfileBtn" onclick="openProfileSection()" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm sm:text-[15px] font-medium text-[var(--txt-primary)] hover:bg-[var(--card-hover)] transition text-left cursor-pointer">
                    <svg class="w-4.5 h-4.5 text-[var(--txt-secondary)]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    <span>{{ __('Profile') }}</span>
                </button>
            </div>

            <!-- Trigger Button -->
            <button type="button" id="accountTriggerBtn" class="w-full flex items-center gap-3 p-2 rounded-2xl hover:bg-[var(--card-hover)] border border-transparent hover:border-[var(--border-color)] transition-all text-left group" title="{{ $user->name }}">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-bold text-sm sm:text-base flex items-center justify-center shrink-0 shadow-2xs">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="sidebar-text flex-1 min-w-0">
                    <p class="text-sm sm:text-base font-bold text-[var(--txt-primary)] truncate leading-tight">{{ $user->name }}</p>
                    <p class="text-xs sm:text-[13px] text-[var(--txt-tertiary)] truncate leading-tight mt-0.5">{{ $user->email }}</p>
                </div>
                <div class="sidebar-text text-[var(--txt-tertiary)] group-hover:text-[var(--txt-primary)] transition">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </div>
            </button>
        </div>
    </aside>

    <!-- Mobile Backdrop Overlay -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-40 hidden md:hidden"></div>

    <!-- ======================================================== -->
    <!-- 2. MAIN CHAT ROOM AREA                                   -->
    <!-- Navbar chat dihapus, background section transparan,      -->
    <!-- scrolling bubble chat membentang penuh hingga atas       -->
    <!-- ======================================================== -->
    <main class="flex-1 h-full min-h-0 flex flex-col overflow-hidden relative bg-transparent z-10 min-w-0 transition-all duration-300">
        
        <!-- Floating Controls: Mobile Hamburger (Melayang di atas tanpa memotong area scrolling) -->
        <div id="chatHeaderControls" class="absolute top-0 inset-x-0 h-14 px-4 sm:px-6 md:px-10 flex items-center justify-between pointer-events-none z-30">
            <div class="flex items-center gap-2 pointer-events-auto">
                <!-- Toggle Sidebar Button (Mobile drawer only) -->
                <button type="button" id="openSidebarBtn" onclick="handleOpenSidebar()" aria-label="Buka Menu" class="md:hidden w-12 h-12 rounded-2xl border border-[var(--border-color)] bg-white/95 dark:bg-[#1f1f1f]/95 backdrop-blur-md flex items-center justify-center text-[var(--txt-primary)] hover:text-[var(--brand-main)] transition shadow-xs shrink-0 cursor-pointer pointer-events-auto" title="Buka Sidebar">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <span class="sr-only">Context Locked</span>
            </div>
        </div>

        <!-- Chat Feed: Full width scrolling membentang penuh hingga batas paling atas layar -->
        <div id="messagesContainer" class="flex-1 min-h-0 overflow-y-auto w-full px-4 sm:px-6 md:px-10 lg:px-12 pt-16 sm:pt-20 pb-4 sm:pb-6 scroll-smooth bg-transparent {{ (!empty($startWithExplore) || !empty($startWithProfile) || !empty($startWithTopics) || !empty($startWithProfessionals) || empty($session)) ? 'hidden' : '' }}">
            <div id="chatFeedList" class="w-full max-w-4xl lg:max-w-5xl mx-auto space-y-6">
                @php
                    $latestCheckinMsg = $messages->filter(fn($m) => !empty($m->metadata['mood_checkin']))->last();
                    $latestCheckinId = $latestCheckinMsg?->id;
                    $registeredPros = $professionals ?? \App\Models\Professional::where('status', 'active')->get();
                @endphp

                <!-- Messages List -->
                @foreach($messages as $msg)
                    @if($msg->sender === 'user')
                    <!-- User Bubble -->
                    <div class="flex justify-end w-full">
                        @if(!empty($msg->metadata['type']) && $msg->metadata['type'] === 'voice_note' && !empty($msg->metadata['audio_url']))
                        @php
                            $audioSrc = $msg->metadata['audio_url'];
                            if (str_starts_with($audioSrc, 'http')) {
                                $parsedPath = parse_url($audioSrc, PHP_URL_PATH);
                                if ($parsedPath) {
                                    $audioSrc = $parsedPath;
                                }
                            }
                            $durationSec = (int)($msg->metadata['duration'] ?? 0);
                        @endphp
                        <!-- Voice Note Bubble -->
                        <div data-role="voice-player" data-duration="{{ $durationSec }}" class="max-w-[85%] sm:max-w-[75%] rounded-2xl rounded-tr-sm bg-[var(--brand-btn)] text-white p-3 sm:p-4 shadow-sm space-y-2 select-text">
                            <div class="flex items-center gap-2.5 sm:gap-3">
                                <button type="button" class="vn-play-btn w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-[#252525] dark:bg-white dark:text-[#252525] flex items-center justify-center shrink-0 hover:scale-105 active:scale-95 transition shadow-xs cursor-pointer" data-audio-src="{{ $audioSrc }}" data-duration="{{ $durationSec }}" title="Putar Pesan Suara">
                                    <svg class="vn-icon-play w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                    <svg class="vn-icon-pause w-4 h-4 hidden" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                    </svg>
                                </button>
                                <div class="flex-1 min-w-0">
                                    <div class="vn-progress-track relative w-full h-1.5 sm:h-2 bg-white/25 rounded-full overflow-hidden cursor-pointer" title="Lompat ke durasi">
                                        <div class="vn-progress-bar h-full bg-white rounded-full transition-all duration-75" style="width: 0%;"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-[10.5px] sm:text-[11.5px] text-white/70 mt-1 font-mono">
                                        <span class="vn-time-current">0:00</span>
                                        <span class="vn-time-total">{{ gmdate('i:s', $msg->metadata['duration'] ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($msg->message))
                            <div class="vn-transcript-section pt-1.5 border-t border-white/15">
                                <button type="button" class="vn-transcript-toggle w-full flex items-center justify-between gap-1 text-[11px] text-white/70 hover:text-white transition-colors cursor-pointer select-none py-0.5" title="Buka Transkrip">
                                    <span class="font-medium tracking-wide">Transkrip</span>
                                    <svg class="vn-toggle-arrow w-3.5 h-3.5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div class="vn-transcript-collapse grid grid-rows-[0fr] transition-all duration-300 ease-in-out">
                                    <div class="overflow-hidden">
                                        <div class="pt-1.5 text-xs sm:text-[13px] text-white/90 leading-relaxed italic mt-1">
                                            "{!! nl2br(e($msg->message)) !!}"
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @else
                        <!-- Regular Text Bubble -->
                        <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl rounded-tr-sm bg-[var(--brand-btn)] text-white px-5 sm:px-6 py-3.5 sm:py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm">
                            {!! nl2br(e($msg->message)) !!}
                        </div>
                        @endif
                    </div>
                    @elseif($msg->sender === 'assistant')
                    <!-- Assistant Bubble -->
                    <div class="max-w-[95%] sm:max-w-[85%]">
                        <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-5 sm:px-6 py-3.5 sm:py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm">
                            {!! nl2br(e($msg->message)) !!}
                        </div>
                    </div>

                    <!-- Beda Bubble Chat: Sistem Pakar Ekspresi & Cek Kondisi Mood (Style Bubble Biasa) -->
                    @if(!empty($msg->metadata['mood_checkin']))
                    @php
                        $checkinAnswered = !empty($msg->metadata['selected_mood']);
                        $selectedMoodKey = $msg->metadata['selected_mood'] ?? null;
                        // Sekali pilih: bubble yang sudah dijawab atau sebelum check-in terbaru dikunci permanen
                        $isLocked = $checkinAnswered || (!empty($latestCheckinId) && $msg->id < $latestCheckinId);
                    @endphp
                    <div class="max-w-[95%] sm:max-w-[85%] mood-checkin-wrapper" data-msg-id="{{ $msg->id }}" data-answered="{{ $isLocked ? 'true' : 'false' }}">
                        <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-5 sm:px-6 py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm space-y-3">
                            <p class="text-[15px] sm:text-base leading-relaxed text-[var(--txt-secondary)]">
                                {{ __('Seiring kita mengurai cerita, suasana perasaanmu bisa berkembang. Bagaimana kondisimu saat ini? Pilih salah satu untuk membantu Weisz menyesuaikan nada bicara dengan hatimu:') }}
                            </p>
                            <div class="grid grid-cols-5 gap-2 sm:gap-3 pt-1">
                                @foreach(\App\Services\MoodExpertService::getMoods() as $mKey => $mVal)
                                    @php
                                        $isThisChosen = ($selectedMoodKey === $mKey);
                                    @endphp
                                    @if($isLocked)
                                        @if($isThisChosen)
                                        <button type="button" disabled class="mood-checkin-btn relative aspect-square p-1 rounded-xl sm:rounded-2xl border-2 border-[#252525] dark:border-white ring-4 ring-[#252525]/20 dark:ring-white/20 bg-[var(--card-secondary)] scale-[1.03] shadow-md transition-all flex items-center justify-center cursor-default pointer-events-none overflow-hidden" data-mood="{{ $mKey }}" title="{{ $mVal['label'] }}">
                                            <img src="{{ asset('images/moods/' . $mKey . '.jpg') }}" alt="{{ $mVal['label'] }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl pointer-events-none">
                                            <div class="absolute top-1.5 right-1.5 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center shadow-xs">
                                                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        </button>
                                        @else
                                        <button type="button" disabled class="mood-checkin-btn relative aspect-square p-1 rounded-xl sm:rounded-2xl border border-[var(--border-color)] bg-[var(--card-secondary)] opacity-35 grayscale-[40%] transition flex items-center justify-center cursor-not-allowed pointer-events-none overflow-hidden" data-mood="{{ $mKey }}" title="{{ $mVal['label'] }}">
                                            <img src="{{ asset('images/moods/' . $mKey . '.jpg') }}" alt="{{ $mVal['label'] }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl pointer-events-none">
                                        </button>
                                        @endif
                                    @else
                                        <button type="button" class="mood-checkin-btn relative aspect-square p-1 rounded-xl sm:rounded-2xl border-2 border-[var(--border-color)] hover:border-[var(--brand-main)] bg-[var(--card-secondary)] transition-all duration-200 flex items-center justify-center group cursor-pointer hover:shadow-md overflow-hidden" data-mood="{{ $mKey }}" title="{{ $mVal['label'] }}">
                                            <img src="{{ asset('images/moods/' . $mKey . '.jpg') }}" alt="{{ $mVal['label'] }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl group-hover:scale-105 transition-transform duration-300 pointer-events-none">
                                            <div class="room-check-indicator absolute top-1.5 right-1.5 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center shadow-xs opacity-0 transition-opacity pointer-events-none">
                                                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Beda Bubble Chat: Rekomendasi Tenaga Profesional (Slider Card) -->
                    @if(!empty($msg->metadata['recommend_professional']))
                    <div class="max-w-[95%] sm:max-w-[85%]">
                        <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-5 sm:px-6 py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm space-y-4 pro-slider-container">
                            <p class="text-base sm:text-[16.5px] leading-relaxed text-[var(--txt-primary)]">
                                {{ __('Sistem Weisz mendeteksi beban emosional yang cukup berat dan berkelanjutan. Ceritamu sangat berharga, dan kamu tidak harus menanggung semuanya sendirian. Berkonsultasi dengan psikiater atau psikolog klinis berlisensi dapat membantumu mendapatkan penanganan medis dan evaluasi mendalam yang tepat.') }}
                            </p>

                            <!-- Slider Cards Track (Bisa digeser dan discroll secara mulus) -->
                            <div class="pro-slider-track flex items-stretch gap-3 overflow-x-auto scroll-smooth snap-x snap-mandatory py-1 px-0.5 cursor-grab active:cursor-grabbing select-none">
                                @foreach($registeredPros as $pro)
                                <div class="w-[220px] sm:w-[240px] shrink-0 snap-start bg-white dark:bg-[#1c1c1c] text-[var(--txt-primary)] rounded-2xl border border-[var(--border-color)] p-3 sm:p-3.5 flex flex-col justify-between shadow-xs hover:shadow-md hover:border-emerald-500/40 dark:hover:border-emerald-500/40 transition-all select-text">
                                    <div>
                                        <!-- Top: Avatar & Identitas -->
                                        <div class="flex items-start gap-2.5 sm:gap-3">
                                            <img src="{{ $pro->avatar_url }}" alt="{{ $pro->display_name }}" class="w-12 h-14 sm:w-14 sm:h-16 rounded-xl object-cover shrink-0 border border-[var(--border-color)] shadow-2xs">
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-bold text-[var(--txt-primary)] text-sm sm:text-[15px] leading-tight tracking-tight truncate" title="{{ $pro->display_name }}">{{ $pro->display_name }}</h4>
                                                @if($pro->degree)
                                                <p class="text-[11.5px] text-[var(--txt-secondary)] font-normal mt-0.5 truncate" title="{{ $pro->degree }}">{{ $pro->degree }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Middle: Keahlian (tanpa judul) -->
                                        <div class="mt-2.5 pt-2 border-t border-[var(--border-color)]/70 flex flex-wrap gap-1">
                                            @forelse($pro->specialization_tags as $tag)
                                            <span class="px-2 py-0.5 rounded-md bg-neutral-100 dark:bg-neutral-800 text-[var(--txt-secondary)] border border-[var(--border-color)] text-[10px] sm:text-[10.5px] font-medium leading-tight">{{ $tag }}</span>
                                            @empty
                                            <span class="text-[10px] text-[var(--txt-tertiary)]">-</span>
                                            @endforelse
                                        </div>

                                        <!-- Bottom: Alamat Praktik (tanpa judul) -->
                                        <div class="mt-2.5 pt-2 border-t border-[var(--border-color)]/70 flex items-start gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-[var(--txt-tertiary)] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                            </svg>
                                            <p class="text-[11px] sm:text-xs text-[var(--txt-secondary)] leading-relaxed font-normal line-clamp-2" title="{{ $pro->address ?: ($pro->location ?: '-') }}">
                                                {{ $pro->address ?: ($pro->location ?: '-') }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($pro->phone)
                                    <div class="mt-3 pt-2.5 border-t border-[var(--border-color)]/70">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pro->phone) }}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-xs font-semibold transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.16 12.05 20.16C10.66 20.16 9.3 19.81 8.08 19.14L7.75 18.96L4.54 19.8L5.4 16.67L5.2 16.35C4.46 15.08 4.07 13.52 4.07 11.92C4.07 7.38 7.76 3.67 12.05 3.67M9.06 6.88C8.88 6.88 8.58 6.94 8.33 7.21C8.07 7.49 7.37 8.15 7.37 9.53C7.37 10.91 8.38 12.24 8.52 12.43C8.66 12.61 10.5 15.44 13.33 16.66C14 16.95 14.52 17.12 14.93 17.25C15.6 17.47 16.22 17.43 16.7 17.36C17.24 17.28 18.36 16.68 18.59 16.03C18.82 15.38 18.82 14.82 18.75 14.71C18.68 14.59 18.5 14.53 18.23 14.39C17.96 14.26 16.62 13.6 16.37 13.51C16.12 13.42 15.94 13.37 15.76 13.64C15.58 13.92 15.06 14.53 14.9 14.71C14.74 14.89 14.58 14.92 14.31 14.78C14.04 14.65 13.17 14.36 12.13 13.44C11.32 12.72 10.78 11.83 10.62 11.56C10.46 11.28 10.6 11.13 10.74 11C10.86 10.87 11.01 10.67 11.15 10.5C11.29 10.33 11.33 10.21 11.42 10.03C11.51 9.85 11.47 9.69 11.4 9.56C11.33 9.42 10.79 8.1 10.56 7.56C10.34 7.03 10.11 7.1 9.95 7.1L9.5 7.09C9.28 7.09 9.06 6.88 9.06 6.88Z" />
                                            </svg>
                                            <span>WhatsApp</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Beda Bubble Chat: Tombol Buat Percakapan Baru jika Keluar dari Topik -->
                    @if(!empty($msg->metadata['is_off_topic']))
                    <div class="max-w-[95%] sm:max-w-[85%]">
                        <div class="p-4 sm:p-5 rounded-2xl rounded-tl-sm bg-neutral-50 dark:bg-neutral-900/50 border border-[var(--border-color)] text-[15px] sm:text-base text-[var(--txt-secondary)] space-y-3 shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[var(--brand-main)] animate-pulse"></span>
                                <p class="font-bold text-[var(--brand-main)] text-base sm:text-lg">{{ __('Ingin Membahas Topik Lain?') }}</p>
                            </div>
                            <p class="text-[15px] sm:text-base leading-relaxed text-[var(--txt-primary)]">
                                {{ __('Sepertinya ceritamu mulai menyentuh hal di luar topik ini. Agar refleksimu tetap terarah dan efektif, kamu bisa membuat percakapan baru dengan topik yang lebih sesuai.') }}
                            </p>
                            <div class="pt-1 flex items-center gap-2 flex-wrap">
                                <button type="button" onclick="openNewChatExplore()" class="weisz-btn-primary text-sm sm:text-[15px] py-2 px-4 shadow-sm inline-flex items-center gap-1.5 font-semibold cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    {{ __('Buat Percakapan Baru') }}
                                </button>
                                <a href="{{ route('topics.explore') }}" class="weisz-btn-secondary text-sm sm:text-[15px] py-2 px-4">
                                    {{ __('Lihat Katalog Topik') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif
                @endforeach

                <!-- Dynamic AI Typing Indicator -->
                <div id="typingIndicator" class="hidden max-w-[95%] sm:max-w-[85%]">
                    <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] px-4 sm:px-5 py-3 sm:py-3.5 inline-flex items-center gap-2.5 sm:gap-3 shadow-xs">
                        <span class="relative flex h-2.5 w-2.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[var(--brand-main)] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[var(--brand-main)]"></span>
                        </span>
                        <span id="typingIndicatorText" class="text-xs sm:text-[13.5px] font-medium text-[var(--txt-secondary)] transition-all duration-150 ease-out select-none">
                            {{ __('Memikirkan jawaban...') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pinned Bottom Area: Suggestion Chips & Chat Composer -->
        <!-- Background Polos/Transparan: Tanpa background putih dan tanpa garis section -->
        <div id="chatComposerArea" class="shrink-0 bg-transparent border-t-0 px-3 sm:px-6 pt-1 pb-3 sm:pb-4 z-20 {{ (!empty($startWithExplore) || !empty($startWithProfile) || !empty($startWithTopics) || !empty($startWithProfessionals) || empty($session)) ? 'hidden' : '' }}" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom, 0.75rem));">
            <div class="w-full max-w-2xl lg:max-w-3xl mx-auto space-y-2">
                <!-- Contextual Suggestion Chips (Zero scrollbars, mouse wheel & swipe scrollable) -->
                <div id="suggestionChipsContainer" class="flex items-center gap-1.5 overflow-x-auto overflow-y-hidden no-scrollbar chat-room-scroll py-0.5 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden select-none cursor-grab active:cursor-grabbing" style="-webkit-overflow-scrolling: touch; overscroll-behavior-x: contain;">
                    <button type="button" id="stuckHelpBtn" class="weisz-chip text-xs sm:text-[13.5px] py-1 px-3 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 shrink-0 font-medium">
                        {{ __('Aku bingung mau cerita apa') }}
                    </button>

                    @foreach($suggestedChips as $chip)
                    <button type="button" class="suggestion-chip weisz-chip text-xs sm:text-[13.5px] py-1 px-3 shrink-0 whitespace-nowrap font-medium" data-text="{{ $chip }}">
                        {{ $chip }}
                    </button>
                    @endforeach
                </div>

                <!-- Composer Form with Clean & Compact Input Box -->
                <form id="chatForm" method="POST" action="{{ $session ? ('/chat/' . $session->id . '/send') : '/chat' }}" data-session-id="{{ $session?->id ?? '' }}" class="w-full">
                    @csrf
                    <div id="composerInputPill" class="flex items-center gap-2 p-1.5 sm:p-2 rounded-2xl bg-[var(--card-secondary)]/95 backdrop-blur-md border border-[var(--border-color)] focus-within:border-[var(--brand-main)] focus-within:ring-2 focus-within:ring-[var(--brand-main)]/20 transition-all shadow-xs relative">
                        <textarea id="messageInput" name="message" rows="1"
                            placeholder="{{ __('Ceritakan apapun...') }}"
                            class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none px-3.5 py-2.5 text-sm sm:text-[15px] text-[var(--txt-primary)] placeholder-[var(--txt-tertiary)] resize-none overflow-y-hidden leading-6 break-words whitespace-pre-wrap m-0 block"
                            style="min-height: 44px; height: 44px; max-height: 112px;"></textarea>

                        <!-- Voice Note Recording Bar (Ditampilkan saat sedang merekam) -->
                        <div id="voiceNoteRecordingBar" class="hidden flex-1 flex items-center justify-between gap-1.5 sm:gap-2 px-2.5 py-1 min-h-[44px] h-[44px] overflow-hidden">
                            <div class="flex items-center gap-2 min-w-0">
                                <div id="voiceNoteStatusDot" class="relative flex items-center justify-center shrink-0">
                                    <span id="voiceNotePing" class="w-2.5 h-2.5 rounded-full bg-neutral-900 dark:bg-white opacity-40 animate-ping absolute"></span>
                                    <span id="voiceNoteDot" class="w-2 h-2 rounded-full bg-neutral-900 dark:bg-white relative"></span>
                                </div>
                                <span id="voiceNoteTimer" class="font-mono text-xs sm:text-sm font-bold text-[var(--txt-primary)] shrink-0">0:00</span>
                                <div id="voiceNoteWaveform" class="flex items-center gap-0.5 h-3.5 shrink-0 px-1">
                                    <span class="vn-wave-bar w-0.5 h-2 bg-[#252525] dark:bg-white rounded-full transition-all duration-75"></span>
                                    <span class="vn-wave-bar w-0.5 h-3.5 bg-[#252525] dark:bg-white rounded-full transition-all duration-75"></span>
                                    <span class="vn-wave-bar w-0.5 h-1.5 bg-[#252525] dark:bg-white rounded-full transition-all duration-75"></span>
                                    <span class="vn-wave-bar w-0.5 h-3 bg-[#252525] dark:bg-white rounded-full transition-all duration-75"></span>
                                    <span class="vn-wave-bar w-0.5 h-2 bg-[#252525] dark:bg-white rounded-full transition-all duration-75"></span>
                                </div>
                                <span id="voiceNoteLiveText" class="text-xs text-[var(--txt-secondary)] truncate italic min-w-0">{{ __('Merekam...') }}</span>
                            </div>
                            <button type="button" id="cancelVoiceNoteBtn" class="text-xs font-medium text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] transition-colors px-1.5 py-0.5 rounded cursor-pointer shrink-0 select-none" title="{{ __('Batalkan Rekaman') }}">
                                {{ __('Batal') }}
                            </button>
                        </div>

                        <!-- Tombol Jeda / Lanjut (Bentuk sama persis dengan button VN/kirim, hanya icon, di samping button VN/kirim) -->
                        <button type="button" id="pauseResumeVoiceNoteBtn" class="hidden w-12 h-12 sm:w-11 sm:h-11 rounded-2xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center hover:opacity-90 active:scale-95 transition shadow-xs shrink-0 cursor-pointer" title="{{ __('Jeda Rekaman') }}">
                            <svg id="pauseIcon" class="w-5 h-5 sm:w-4.5 sm:h-4.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                            </svg>
                            <svg id="resumeIcon" class="w-5 h-5 sm:w-4.5 sm:h-4.5 hidden ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                        
                        <!-- Voice Note Button (Muncul jika input kosong) -->
                        <button type="button" id="voiceNoteBtn" class="w-12 h-12 sm:w-11 sm:h-11 rounded-2xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center hover:opacity-90 active:scale-95 transition shadow-xs shrink-0 cursor-pointer" title="{{ __('Kirim Voice Note') }}">
                            <svg class="w-6 h-6 sm:w-5.5 sm:h-5.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z" />
                            </svg>
                        </button>

                        <!-- Send Button (Muncul jika user memasukkan karakter selain spasi) -->
                        <button type="submit" id="sendBtn" class="hidden w-12 h-12 sm:w-11 sm:h-11 rounded-2xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center hover:opacity-90 active:scale-95 transition shadow-xs shrink-0 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" title="{{ __('Kirim Pesan') }}">
                            <svg class="w-6 h-6 sm:w-5.5 sm:h-5.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 3. EXPLORE WIZARD IN CHAT ROOM                           -->
        <!-- Menu & Stepper Progress dari page Explore ditampilkan    -->
        <!-- langsung di section room chat tanpa pindah halaman       -->
        <!-- ======================================================== -->
        <div id="exploreRoomSection" class="flex-1 h-full min-h-0 flex flex-col overflow-hidden relative bg-transparent z-10 {{ (empty($startWithProfile) && empty($startWithTopics) && empty($startWithProfessionals) && (!empty($startWithExplore) || empty($session))) ? '' : 'hidden' }}">
            
            <!-- Scrollable Explore Area -->
            <div id="exploreScrollContainer" class="flex-1 min-h-0 overflow-y-auto px-4 sm:px-6 md:px-8 lg:px-12 pt-16 sm:pt-8 pb-6 sm:pb-8 space-y-6 sm:space-y-8 no-scrollbar chat-room-scroll">
                <div class="max-w-5xl mx-auto w-full space-y-6 sm:space-y-8 pb-12">
                    
                    <!-- Dynamic Header Card + 4-Step Stepper Progress Bar (Sama persis seperti page Explore) -->
                    <div class="rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-5 sm:p-8 space-y-5 shadow-xs">
                        <!-- Step Header Text -->
                        <div class="space-y-1.5">
                            <h1 id="exploreStepTitle" class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-[var(--txt-primary)]">
                                {{ __('Apa yang Sedang Mengganjal Pikiranmu?') }}
                            </h1>
                            <p id="exploreStepDescription" class="text-xs sm:text-sm md:text-base text-[var(--txt-secondary)] leading-relaxed">
                                {{ __('Pilih kategori situasi yang paling mendekati kondisimu saat ini. Setiap kategori dirancang dengan konteks psikologis spesifik untuk mengurai keresahanmu secara bertahap dan terarah.') }}
                            </p>
                        </div>

                        <!-- Stepper Progress Bar 4 Step -->
                        <div class="relative w-full pt-2">
                            <!-- Track & Animated Progress Bar -->
                            <div class="relative w-full">
                                <div class="absolute inset-x-[12.5%] top-1/2 -translate-y-1/2 h-2 sm:h-2.5 z-0 pointer-events-none" aria-hidden="true">
                                    <div class="w-full h-full bg-neutral-200/80 dark:bg-neutral-800 rounded-full overflow-hidden">
                                        <div id="roomExploreProgressBar" class="h-full bg-[#252525] dark:bg-white rounded-full transition-all duration-500 shadow-xs" style="width: 0%;"></div>
                                    </div>
                                </div>

                                <!-- 4 Step Circles Grid -->
                                <div class="grid grid-cols-4 relative z-10 text-center">
                                    <!-- Step 1 Circle -->
                                    <div class="flex justify-center">
                                        <button type="button" id="stepCircle1" onclick="goToExploreStep(1)" class="w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-xs sm:text-sm flex items-center justify-center shadow-lg shadow-black/10 dark:shadow-black/40 ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] scale-105 transition-all cursor-pointer" title="{{ __('Langkah 1: Kategori') }}">
                                            1
                                        </button>
                                    </div>
                                    <!-- Step 2 Circle -->
                                    <div class="flex justify-center">
                                        <button type="button" id="stepCircle2" onclick="goToExploreStep(2)" class="w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-[var(--card-secondary)] text-[var(--txt-tertiary)] border-2 border-[var(--border-color)] font-extrabold text-xs sm:text-sm flex items-center justify-center ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] transition-all cursor-not-allowed" disabled title="{{ __('Langkah 2: Subkategori') }}">
                                            2
                                        </button>
                                    </div>
                                    <!-- Step 3 Circle -->
                                    <div class="flex justify-center">
                                        <button type="button" id="stepCircle3" onclick="goToExploreStep(3)" class="w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-[var(--card-secondary)] text-[var(--txt-tertiary)] border-2 border-[var(--border-color)] font-extrabold text-xs sm:text-sm flex items-center justify-center ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] transition-all cursor-not-allowed" disabled title="{{ __('Langkah 3: Topik') }}">
                                            3
                                        </button>
                                    </div>
                                    <!-- Step 4 Circle -->
                                    <div class="flex justify-center">
                                        <button type="button" id="stepCircle4" onclick="goToExploreStep(4)" class="w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-[var(--card-secondary)] text-[var(--txt-tertiary)] border-2 border-[var(--border-color)] font-extrabold text-xs sm:text-sm flex items-center justify-center ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] transition-all cursor-not-allowed" disabled title="{{ __('Langkah 4: Konfirmasi') }}">
                                            4
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step Labels Grid -->
                            <div class="grid grid-cols-4 text-center mt-2.5">
                                <button type="button" id="stepLabel1" onclick="goToExploreStep(1)" class="text-xs sm:text-sm font-bold text-[var(--txt-primary)] transition cursor-pointer">
                                    {{ __('Kategori') }}
                                </button>
                                <button type="button" id="stepLabel2" onclick="goToExploreStep(2)" class="text-xs sm:text-sm font-medium text-[var(--txt-tertiary)] transition cursor-not-allowed" disabled>
                                    {{ __('Subkategori') }}
                                </button>
                                <button type="button" id="stepLabel3" onclick="goToExploreStep(3)" class="text-xs sm:text-sm font-medium text-[var(--txt-tertiary)] transition cursor-not-allowed" disabled>
                                    {{ __('Topik') }}
                                </button>
                                <button type="button" id="stepLabel4" onclick="goToExploreStep(4)" class="text-xs sm:text-sm font-medium text-[var(--txt-tertiary)] transition cursor-not-allowed" disabled>
                                    {{ __('Konfirmasi') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 1 CONTAINER: Categories Grid -->
                    <div id="exploreStep1Container" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 items-stretch">
                            @foreach($exploreCategories as $cat)
                            <div onclick="selectCategory('{{ $cat->id }}')" class="group relative rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/40 hover:-translate-y-1 hover:border-[#252525] dark:hover:border-white/50 overflow-hidden cursor-pointer shadow-xs">
                                <div class="absolute -right-16 -top-16 w-36 h-36 rounded-full bg-gradient-to-br from-amber-400/15 via-rose-300/10 to-transparent blur-2xl group-hover:scale-150 transition-transform duration-500 pointer-events-none" aria-hidden="true"></div>
                                <div class="space-y-4 flex-1 flex flex-col justify-between">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)]/80 flex items-center justify-center text-[var(--txt-primary)] group-hover:border-[var(--brand-main)]/50 group-hover:bg-[var(--card-surface)] transition">
                                                <svg class="w-5 h-5 text-[var(--brand-main)]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                                </svg>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[var(--card-secondary)] text-[var(--txt-secondary)] border border-[var(--border-color)]/70">
                                                {{ $cat->subcategories->count() }} {{ __('Subkategori') }}
                                            </span>
                                        </div>
                                        <div class="space-y-1.5 pt-1">
                                            <h2 class="text-base sm:text-lg font-bold text-[var(--txt-primary)] group-hover:text-[var(--brand-main)] transition line-clamp-2">
                                                {{ $cat->name }}
                                            </h2>
                                            <p class="text-xs sm:text-sm text-[var(--txt-secondary)] leading-relaxed line-clamp-3">
                                                {{ $cat->description }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="pt-3 border-t border-[var(--border-color)]/60 flex items-center justify-between text-xs font-semibold text-[var(--txt-secondary)] group-hover:text-[var(--txt-primary)] transition">
                                        <span>{{ __('Pilih Kategori') }}</span>
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1 text-[var(--brand-main)]" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- STEP 2 CONTAINER: Subcategories Grid -->
                    <div id="exploreStep2Container" class="hidden space-y-5">
                        <div id="subcategoriesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 items-stretch">
                            <!-- Populated dynamically by JS -->
                        </div>
                    </div>

                    <!-- STEP 3 CONTAINER: Topics Grid -->
                    <div id="exploreStep3Container" class="hidden space-y-5">
                        <div id="topicsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 items-stretch">
                            <!-- Populated dynamically by JS -->
                        </div>
                    </div>

                    <!-- STEP 4 CONTAINER: Mood Confirmation & Start Form -->
                    <div id="exploreStep4Container" class="hidden space-y-5">
                        <!-- Mood Expressions Card (5 Pilihan Ilustrasi Ekspresi Suasana Hati) -->
                        <div class="rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-5 sm:p-8 space-y-6 shadow-xs">
                            <div class="grid grid-cols-5 gap-2.5 sm:gap-4 md:gap-5">
                                @foreach($allMoods as $key => $mood)
                                <button type="button" 
                                    class="room-mood-card relative aspect-square p-1.5 sm:p-2.5 rounded-2xl sm:rounded-3xl border-2 border-[var(--border-color)] hover:border-[var(--brand-main)]/70 bg-[var(--card-secondary)] transition-all duration-200 flex items-center justify-center group cursor-pointer hover:shadow-md overflow-hidden" 
                                    data-mood="{{ $key }}"
                                    onclick="selectRoomMood('{{ $key }}')"
                                    title="{{ $mood['label'] }}">
                                    <img src="{{ asset('images/moods/' . $key . '.jpg') }}" 
                                         alt="{{ $mood['label'] }}" 
                                         class="w-full h-full object-cover rounded-xl sm:rounded-2xl group-hover:scale-105 transition-transform duration-300 pointer-events-none">
                                    
                                    <div class="room-check-indicator absolute top-2 right-2 sm:top-3 sm:right-3 w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center shadow-md opacity-0 transition-opacity pointer-events-none">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </div>
                                </button>
                                @endforeach
                            </div>

                            <!-- Action Form: Tombol Aksi Mulai Obrolan -->
                            <form id="roomStartSessionForm" method="POST" action="" class="pt-2">
                                @csrf
                                <input type="hidden" name="initial_mood" id="roomInitialMoodInput" value="">

                                <button type="submit" id="roomStartSessionBtn" disabled 
                                    class="weisz-btn-primary w-full py-3 sm:py-3.5 text-xs sm:text-sm font-semibold justify-center opacity-40 cursor-not-allowed transition-all duration-200">
                                    {{ __('Mulai Obrolan') }}
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- 4. PROFILE MENU IN CHAT ROOM                             -->
        <!-- Ditampilkan langsung di section room chat                -->
        <!-- sama halnya seperti pemilihan kategori                   -->
        <!-- ======================================================== -->
        <div id="profileRoomSection" class="flex-1 h-full min-h-0 flex flex-col overflow-hidden relative bg-transparent z-10 {{ !empty($startWithProfile) ? '' : 'hidden' }}">
            
            <!-- Scrollable Profile Area -->
            <div id="profileScrollContainer" class="flex-1 min-h-0 overflow-y-auto px-4 sm:px-6 md:px-8 lg:px-12 pt-16 sm:pt-8 pb-6 sm:pb-8 space-y-6 sm:space-y-8 no-scrollbar chat-room-scroll">
                <div class="max-w-5xl mx-auto w-full space-y-6 sm:space-y-8 pb-12">
                    
                    <!-- Main Profile Card: Informasi Akun, Ubah Nama, Settings, Log Out -->
                    <div class="rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-6 sm:p-8 space-y-6 sm:space-y-8 shadow-xs relative overflow-hidden">
                        <!-- Ambient gradient matching explore cards -->
                        <div class="absolute -right-20 -top-20 w-44 h-44 rounded-full bg-gradient-to-br from-amber-400/10 via-rose-300/10 to-transparent blur-3xl pointer-events-none" aria-hidden="true"></div>

                        <!-- 2.a Informasi Akun: Avatar, Nama, Email -->
                        <div class="flex items-center gap-4 sm:gap-5 pb-6 border-b border-[var(--border-color)]/80 relative z-10">
                            <div id="profileAvatarBadge" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center font-extrabold text-2xl sm:text-3xl shadow-md ring-4 ring-[var(--card-secondary)] shrink-0 transition-transform">
                                <span id="profileAvatarLetter">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h1 id="profileDisplayName" class="text-xl sm:text-2xl font-extrabold tracking-tight text-[var(--txt-primary)] truncate">
                                    {{ $user->name }}
                                </h1>
                                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] truncate mt-0.5">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>

                        <!-- 2.b Ubah Nama Akun -->
                        <div class="space-y-3 relative z-10">
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-[var(--txt-primary)]">
                                    {{ __('Ubah Nama Akun') }}
                                </h2>
                                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] mt-0.5 leading-relaxed">
                                    {{ __('Perbarui nama akun atau identitas tampilanmu pada sesi percakapan.') }}
                                </p>
                            </div>

                            <form id="profileUpdateNameForm" method="POST" action="{{ route('profile.update') }}" class="space-y-3">
                                @csrf
                                @method('PUT')

                                <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
                                    <input id="profileAccountNameInput" type="text" name="name" value="{{ old('name', $user->name) }}" required
                                        placeholder="{{ __('Masukkan nama akun...') }}" aria-label="{{ __('Nama Akun') }}"
                                        class="flex-1 h-11 sm:h-12 px-4 rounded-xl sm:rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)] text-sm sm:text-base text-[var(--txt-primary)] placeholder-[var(--txt-tertiary)] focus:outline-none focus:border-[#252525] dark:focus:border-white focus:ring-2 focus:ring-[#252525]/10 dark:focus:ring-white/10 transition leading-normal">

                                    <button type="submit" id="saveProfileNameBtn" class="shrink-0 h-11 sm:h-12 px-5 rounded-xl sm:rounded-2xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] hover:opacity-90 active:scale-95 text-xs sm:text-sm font-bold transition shadow-xs cursor-pointer inline-flex items-center justify-center gap-2">
                                        <svg id="saveProfileSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <span>{{ __('Simpan Perubahan') }}</span>
                                    </button>
                                </div>

                                <!-- Status Feedback Alerts -->
                                <div id="profileUpdateSuccessAlert" class="hidden p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs sm:text-sm text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span id="profileSuccessMsgText">{{ __('Nama akun berhasil diperbarui.') }}</span>
                                </div>

                                <div id="profileUpdateErrorAlert" class="hidden p-3 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-xs sm:text-sm text-red-800 dark:text-red-300 flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                    <span id="profileErrorMsgText">{{ __('Terjadi kesalahan saat memperbarui nama.') }}</span>
                                </div>
                            </form>
                        </div>

                        <!-- 2.c Log Out (Dipindahkan tepat setelah Ubah Nama Akun) -->
                        <div class="pt-6 border-t border-[var(--border-color)]/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-[var(--txt-primary)]">{{ __('Log Out') }}</h3>
                                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] mt-0.5">
                                    {{ __('Keluar dari sesi akun ini dengan aman.') }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-950/70 border border-red-200 dark:border-red-900/40 transition flex items-center justify-center cursor-pointer shadow-2xs" title="{{ __('Log Out dari Akun') }}">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>

                        <!-- 2.d Settings (Tema Antarmuka & Bahasa Aplikasi tanpa header Settings) -->
                        <div id="profileSettingsSection" class="pt-6 border-t border-[var(--border-color)]/80 space-y-6 relative z-10">
                            <!-- Setting 1: Tema Antarmuka (Posisi di samping teks, 3 button persegi [] [] []) -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-6">
                                <div class="space-y-0.5">
                                    <h3 class="text-sm sm:text-base font-bold text-[var(--txt-primary)]">
                                        {{ __('Tema Antarmuka') }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[var(--txt-secondary)]">
                                        {{ __('Pilih tampilan antarmuka yang paling nyaman untuk matamu.') }}
                                    </p>
                                </div>

                                <!-- 3 Button Persegi/Kotak Menyamping [] [] [] -->
                                <div class="flex items-center gap-2 sm:gap-2.5 shrink-0 select-none" role="radiogroup" aria-label="{{ __('Tema Antarmuka') }}">
                                    <!-- 1. Sistem -->
                                    <button type="button" onclick="setAppTheme('system')" id="themeCard_system" role="radio" aria-checked="false" title="{{ __('Sistem') }}"
                                        class="theme-card-option w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl border border-[var(--border-color)] bg-[var(--card-surface)] hover:border-neutral-400 dark:hover:border-neutral-600 transition-all cursor-pointer p-0.5 overflow-hidden flex items-center justify-center focus:outline-none shrink-0 group shadow-2xs">
                                        <img src="{{ asset('images/themes/system.jpg') }}" alt="{{ __('Sistem') }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl pointer-events-none group-hover:scale-105 transition-transform duration-200">
                                    </button>

                                    <!-- 2. Light Mode -->
                                    <button type="button" onclick="setAppTheme('light')" id="themeCard_light" role="radio" aria-checked="false" title="{{ __('Light Mode') }}"
                                        class="theme-card-option w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl border border-[var(--border-color)] bg-[var(--card-surface)] hover:border-neutral-400 dark:hover:border-neutral-600 transition-all cursor-pointer p-0.5 overflow-hidden flex items-center justify-center focus:outline-none shrink-0 group shadow-2xs">
                                        <img src="{{ asset('images/themes/light.jpg') }}" alt="{{ __('Light Mode') }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl pointer-events-none group-hover:scale-105 transition-transform duration-200">
                                    </button>

                                    <!-- 3. Dark Mode -->
                                    <button type="button" onclick="setAppTheme('dark')" id="themeCard_dark" role="radio" aria-checked="false" title="{{ __('Dark Mode') }}"
                                        class="theme-card-option w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl border border-[var(--border-color)] bg-[var(--card-surface)] hover:border-neutral-400 dark:hover:border-neutral-600 transition-all cursor-pointer p-0.5 overflow-hidden flex items-center justify-center focus:outline-none shrink-0 group shadow-2xs">
                                        <img src="{{ asset('images/themes/dark.jpg') }}" alt="{{ __('Dark Mode') }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl pointer-events-none group-hover:scale-105 transition-transform duration-200">
                                    </button>
                                </div>
                            </div>

                            <!-- Setting 2: Bahasa Aplikasi (App Language) Dropdown tepat disamping text -->
                            <div class="pt-6 border-t border-[var(--border-color)]/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-6">
                                <div class="space-y-0.5">
                                    <h3 class="text-sm sm:text-base font-bold text-[var(--txt-primary)]">
                                        {{ __('Bahasa Aplikasi') }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[var(--txt-secondary)]">
                                        {{ __('Pilih bahasa tampilan antarmuka sistem. Pesan percakapan AI akan tetap merespons secara otomatis sesuai bahasa ceritamu.') }}
                                    </p>
                                </div>

                                <!-- Dropdown tepat di samping text -->
                                <div class="shrink-0 select-none">
                                    <div class="relative min-w-[200px]">
                                        <select id="profileLanguageSelect" onchange="setAppLanguage(this.value)" class="w-full appearance-none pl-4 sm:pl-5 pr-11 sm:pr-12 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)] text-xs sm:text-sm font-semibold text-[var(--txt-primary)] focus:outline-none focus:border-[#252525] dark:focus:border-white focus:ring-2 focus:ring-[#252525]/10 dark:focus:ring-white/10 transition cursor-pointer shadow-2xs">
                                            <option value="id" {{ app()->getLocale() === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                            <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 sm:pr-5 text-[var(--txt-secondary)]">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- 5. TOPICS MENU IN CHAT ROOM                              -->
        <!-- Ditampilkan langsung di section room chat                -->
        <!-- sama halnya seperti Obrolan Baru dan Profile             -->
        <!-- ======================================================== -->
        <div id="topicsRoomSection" class="flex-1 h-full min-h-0 flex flex-col overflow-hidden relative bg-transparent z-10 {{ !empty($startWithTopics) ? '' : 'hidden' }}">
            
            <!-- Scrollable Topics Area -->
            <div id="topicsScrollContainer" class="flex-1 min-h-0 overflow-y-auto px-4 sm:px-6 md:px-8 lg:px-12 pt-16 sm:pt-8 pb-6 sm:pb-8 space-y-6 sm:space-y-8 no-scrollbar chat-room-scroll">
                <div class="max-w-5xl mx-auto w-full space-y-5 sm:space-y-6 pb-12">

                    <!-- Searchbar & Filter Kategori (Hanya searchbar & filter kategori di bagian atas) -->
                    <div class="rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-4 sm:p-5 space-y-3.5 shadow-xs">
                        <!-- Searchbar -->
                        <div class="relative w-full">
                            <input type="text" id="topicsSearchInput" oninput="filterTopics()"
                                placeholder="{{ __('Cari topik... (misal: overthinking, skripsi, insecure, patah hati)') }}"
                                class="w-full text-xs sm:text-sm py-3 pl-11 pr-10 rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)] text-[var(--txt-primary)] placeholder-[var(--txt-tertiary)] focus:outline-none focus:border-[#252525] dark:focus:border-white focus:ring-2 focus:ring-[#252525]/10 dark:focus:ring-white/10 transition">
                            <svg class="w-4.5 h-4.5 text-[var(--txt-tertiary)] absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <button type="button" id="topicsClearSearchBtn" onclick="clearTopicsSearch()" class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-[var(--txt-tertiary)] hover:text-[var(--txt-primary)] p-1 rounded-full transition cursor-pointer" title="{{ __('Hapus pencarian') }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Filter Kategori -->
                        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 text-xs">
                            <button type="button" onclick="setTopicsCategoryFilter('')" data-category-filter=""
                                class="topics-category-pill px-3.5 py-2 rounded-xl border shrink-0 transition font-medium bg-[#252525] text-white dark:bg-white dark:text-[#252525] border-transparent font-semibold shadow-xs cursor-pointer">
                                {{ __('Semua Kategori') }}
                            </button>
                            @foreach($exploreCategories as $cat)
                            <button type="button" onclick="setTopicsCategoryFilter('{{ $cat->id }}')" data-category-filter="{{ $cat->id }}"
                                class="topics-category-pill px-3.5 py-2 rounded-xl border shrink-0 transition font-medium bg-[var(--card-secondary)] border-[var(--border-color)] text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)] cursor-pointer">
                                {{ $cat->name }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- List Card Topik -->
                    <div id="topicsGridContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 items-stretch">
                        @foreach($allTopics as $topic)
                        <form method="POST" action="{{ route('chat.start', $topic->slug) }}"
                            data-category-id="{{ $topic->subcategory->category_id ?? '' }}"
                            data-topic-name="{{ strtolower($topic->name) }}"
                            data-topic-desc="{{ strtolower($topic->description) }}"
                            data-subcat-name="{{ strtolower($topic->subcategory->name ?? '') }}"
                            data-cat-name="{{ strtolower($topic->subcategory->category->name ?? '') }}"
                            class="topic-list-card group relative rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/40 hover:-translate-y-1 hover:border-[#252525] dark:hover:border-white/50 overflow-hidden shadow-xs cursor-pointer">
                            @csrf
                            
                            <!-- Ambient hover glow -->
                            <div class="absolute -right-16 -top-16 w-36 h-36 rounded-full bg-gradient-to-br from-indigo-400/15 via-purple-300/10 to-transparent blur-2xl group-hover:scale-150 transition-transform duration-500 pointer-events-none" aria-hidden="true"></div>

                            <button type="submit" class="w-full h-full text-left flex flex-col justify-between flex-1 cursor-pointer focus:outline-none bg-transparent border-0 p-0 m-0">
                                <div class="space-y-2 flex-1 flex flex-col justify-start">
                                    <h3 class="text-base sm:text-lg font-bold text-[var(--txt-primary)] group-hover:text-[var(--brand-main)] transition line-clamp-2">
                                        {{ $topic->name }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[var(--txt-secondary)] leading-relaxed line-clamp-3">
                                        {{ $topic->description }}
                                    </p>
                                </div>

                                <div class="pt-4 mt-5 border-t border-[var(--border-color)]/70 w-full flex items-center justify-between text-xs sm:text-[13px] font-semibold text-[var(--txt-tertiary)] group-hover:text-[var(--txt-primary)] transition">
                                    <span>{{ __('Mulai Percakapan') }}</span>
                                    <div class="w-8 h-8 rounded-xl bg-[var(--card-secondary)] group-hover:bg-[#252525] group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-[#252525] flex items-center justify-center transition-all duration-200 group-hover:translate-x-0.5 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </form>
                        @endforeach
                    </div>

                    <!-- Empty State saat pencarian / filter tidak ditemukan -->
                    <div id="topicsEmptyState" class="hidden rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-10 sm:p-14 text-center space-y-4 shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)] flex items-center justify-center mx-auto text-[var(--txt-tertiary)]">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <p class="text-base font-bold text-[var(--txt-primary)]">{{ __('Tidak ada topik yang sesuai dengan pencarianmu') }}</p>
                            <p class="text-xs sm:text-sm text-[var(--txt-secondary)]">{{ __('Coba gunakan kata kunci yang lebih umum atau pilih kategori lain di atas.') }}</p>
                        </div>
                        <div>
                            <button type="button" onclick="resetTopicsFilter()" class="px-5 py-2.5 rounded-xl bg-[var(--card-secondary)] hover:bg-[var(--card-hover)] border border-[var(--border-color)] text-xs sm:text-sm font-semibold text-[var(--txt-primary)] transition shadow-2xs inline-flex items-center gap-2 cursor-pointer">
                                {{ __('Reset Pencarian') }}
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- 6. PAKAR MENU IN CHAT ROOM                               -->
        <!-- Ditampilkan langsung di section room chat                -->
        <!-- sama halnya seperti Obrolan Baru, Topics, dan Profile    -->
        <!-- ======================================================== -->
        <div id="professionalsRoomSection" class="flex-1 h-full min-h-0 flex flex-col overflow-hidden relative bg-transparent z-10 {{ !empty($startWithProfessionals) ? '' : 'hidden' }}">
            
            <!-- Scrollable Professionals Area -->
            <div id="professionalsScrollContainer" class="flex-1 min-h-0 overflow-y-auto px-4 sm:px-6 md:px-8 lg:px-12 pt-16 sm:pt-8 pb-6 sm:pb-8 space-y-6 sm:space-y-8 no-scrollbar chat-room-scroll">
                <div class="max-w-5xl mx-auto w-full space-y-5 sm:space-y-6 pb-12">

                    <!-- Searchbar Saja (Hanya searchbar di bagian atas, tanpa banner atau filter) -->
                    <div class="rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-4 sm:p-5 shadow-xs">
                        <div class="relative w-full">
                            <input type="text" id="professionalsSearchInput" oninput="filterProfessionals()"
                                placeholder="{{ __('Cari pakar... (misal: nama, psikiater, psikolog, kecemasan, trauma, depresi, kota)') }}"
                                class="w-full text-xs sm:text-sm py-3 pl-11 pr-10 rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)] text-[var(--txt-primary)] placeholder-[var(--txt-tertiary)] focus:outline-none focus:border-[#252525] dark:focus:border-white focus:ring-2 focus:ring-[#252525]/10 dark:focus:ring-white/10 transition">
                            <svg class="w-4.5 h-4.5 text-[var(--txt-tertiary)] absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <button type="button" id="professionalsClearSearchBtn" onclick="clearProfessionalsSearch()" class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-[var(--txt-tertiary)] hover:text-[var(--txt-primary)] p-1 rounded-full transition cursor-pointer" title="{{ __('Hapus pencarian') }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- List Card Pakar (Sama persis seperti card pada bubble chat) -->
                    <div id="professionalsGridContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 items-stretch">
                        @foreach($registeredPros as $pro)
                        <div class="professional-room-card bg-white dark:bg-[#1c1c1c] text-[var(--txt-primary)] rounded-2xl border border-[var(--border-color)] p-3 sm:p-3.5 flex flex-col justify-between shadow-xs hover:shadow-md hover:border-emerald-500/40 dark:hover:border-emerald-500/40 transition-all select-text"
                            data-pro-name="{{ strtolower($pro->name) }}"
                            data-pro-display="{{ strtolower($pro->display_name) }}"
                            data-pro-degree="{{ strtolower($pro->degree ?? '') }}"
                            data-pro-specialization="{{ strtolower(implode(' ', $pro->specialization_tags ?? [])) }}"
                            data-pro-location="{{ strtolower($pro->location ?? '') }}"
                            data-pro-address="{{ strtolower($pro->address ?? '') }}">
                            <div>
                                <!-- Top: Avatar & Identitas -->
                                <div class="flex items-start gap-2.5 sm:gap-3">
                                    <img src="{{ $pro->avatar_url }}" alt="{{ $pro->display_name }}" class="w-12 h-14 sm:w-14 sm:h-16 rounded-xl object-cover shrink-0 border border-[var(--border-color)] shadow-2xs">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-[var(--txt-primary)] text-sm sm:text-[15px] leading-tight tracking-tight truncate" title="{{ $pro->display_name }}">{{ $pro->display_name }}</h4>
                                        @if($pro->degree)
                                        <p class="text-[11.5px] text-[var(--txt-secondary)] font-normal mt-0.5 truncate" title="{{ $pro->degree }}">{{ $pro->degree }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Middle: Keahlian (tanpa judul) -->
                                <div class="mt-2.5 pt-2 border-t border-[var(--border-color)]/70 flex flex-wrap gap-1">
                                    @forelse($pro->specialization_tags as $tag)
                                    <span class="px-2 py-0.5 rounded-md bg-neutral-100 dark:bg-neutral-800 text-[var(--txt-secondary)] border border-[var(--border-color)] text-[10px] sm:text-[10.5px] font-medium leading-tight">{{ $tag }}</span>
                                    @empty
                                    <span class="text-[10px] text-[var(--txt-tertiary)]">-</span>
                                    @endforelse
                                </div>

                                <!-- Bottom: Alamat Praktik (tanpa judul) -->
                                <div class="mt-2.5 pt-2 border-t border-[var(--border-color)]/70 flex items-start gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[var(--txt-tertiary)] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    <p class="text-[11px] sm:text-xs text-[var(--txt-secondary)] leading-relaxed font-normal line-clamp-2" title="{{ $pro->address ?: ($pro->location ?: '-') }}">
                                        {{ $pro->address ?: ($pro->location ?: '-') }}
                                    </p>
                                </div>
                            </div>

                            @if($pro->phone)
                            <div class="mt-3 pt-2.5 border-t border-[var(--border-color)]/70">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pro->phone) }}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-xs font-semibold transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.16 12.05 20.16C10.66 20.16 9.3 19.81 8.08 19.14L7.75 18.96L4.54 19.8L5.4 16.67L5.2 16.35C4.46 15.08 4.07 13.52 4.07 11.92C4.07 7.38 7.76 3.67 12.05 3.67M9.06 6.88C8.88 6.88 8.58 6.94 8.33 7.21C8.07 7.49 7.37 8.15 7.37 9.53C7.37 10.91 8.38 12.24 8.52 12.43C8.66 12.61 10.5 15.44 13.33 16.66C14 16.95 14.52 17.12 14.93 17.25C15.6 17.47 16.22 17.43 16.7 17.36C17.24 17.28 18.36 16.68 18.59 16.03C18.82 15.38 18.82 14.82 18.75 14.71C18.68 14.59 18.5 14.53 18.23 14.39C17.96 14.26 16.62 13.6 16.37 13.51C16.12 13.42 15.94 13.37 15.76 13.64C15.58 13.92 15.06 14.53 14.9 14.71C14.74 14.89 14.58 14.92 14.31 14.78C14.04 14.65 13.17 14.36 12.13 13.44C11.32 12.72 10.78 11.83 10.62 11.56C10.46 11.28 10.6 11.13 10.74 11C10.86 10.87 11.01 10.67 11.15 10.5C11.29 10.33 11.33 10.21 11.42 10.03C11.51 9.85 11.47 9.69 11.4 9.56C11.33 9.42 10.79 8.1 10.56 7.56C10.34 7.03 10.11 7.1 9.95 7.1L9.5 7.09C9.28 7.09 9.06 6.88 9.06 6.88Z" />
                                    </svg>
                                    <span>WhatsApp</span>
                                </a>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Empty State saat pencarian pakar tidak ditemukan -->
                    <div id="professionalsEmptyState" class="hidden rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-10 sm:p-14 text-center space-y-4 shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)] flex items-center justify-center mx-auto text-[var(--txt-tertiary)]">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <p class="text-base font-bold text-[var(--txt-primary)]">{{ __('Tidak ada pakar yang sesuai dengan pencarianmu') }}</p>
                            <p class="text-xs sm:text-sm text-[var(--txt-secondary)]">{{ __('Coba gunakan kata kunci lain seperti nama, keahlian, atau kota.') }}</p>
                        </div>
                        <div>
                            <button type="button" onclick="resetProfessionalsFilter()" class="px-5 py-2.5 rounded-xl bg-[var(--card-secondary)] hover:bg-[var(--card-hover)] border border-[var(--border-color)] text-xs sm:text-sm font-semibold text-[var(--txt-primary)] transition shadow-2xs inline-flex items-center gap-2 cursor-pointer">
                                {{ __('Reset Pencarian') }}
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    const messagesContainer = document.getElementById('messagesContainer');
    const chatFeedList = document.getElementById('chatFeedList');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const typingIndicator = document.getElementById('typingIndicator');
    const sendBtn = document.getElementById('sendBtn');
    const voiceNoteBtn = document.getElementById('voiceNoteBtn');
    const pauseResumeVoiceNoteBtn = document.getElementById('pauseResumeVoiceNoteBtn');
    const voiceNoteRecordingBar = document.getElementById('voiceNoteRecordingBar');
    const voiceNoteTimer = document.getElementById('voiceNoteTimer');
    const voiceNoteLiveText = document.getElementById('voiceNoteLiveText');
    const cancelVoiceNoteBtn = document.getElementById('cancelVoiceNoteBtn');
    const pauseIcon = document.getElementById('pauseIcon');
    const resumeIcon = document.getElementById('resumeIcon');
    const pauseResumeText = document.getElementById('pauseResumeText');
    const voiceNotePing = document.getElementById('voiceNotePing');
    const voiceNoteDot = document.getElementById('voiceNoteDot');
    const voiceNoteWaveform = document.getElementById('voiceNoteWaveform');
    const stuckHelpBtn = document.getElementById('stuckHelpBtn');
    let isRecording = false;
    let isPaused = false;
    let userHasSentMessage = {{ ($session && $session->hasUserMessages()) ? 'true' : 'false' }};

    // Tahapan logis indikator loading AI secara berurut:
    // 1. Memikirkan -> 2. Menyiapkan -> 3. Memberikan -> 4. Sudah siap
    let typingIndicatorTimer = null;
    let typingIndicatorStepTimeout = null;
    const indicatorStages = [
        [
            "Memikirkan jawaban...",
            "Memikirkan jawaban terbaik...",
            "Meresapi ceritamu...",
            "Merenungkan respon yang tepat...",
            "Memikirkan sudut pandang baru...",
            "Memikirkan langkah awal yang nyaman..."
        ],
        [
            "Menyiapkan solusi...",
            "Menyiapkan solusi perlahan...",
            "Merangkai kata yang menenangkan...",
            "Menyiapkan respon hangat untukmu...",
            "Menyusun solusi dan dukungan...",
            "Menyiapkan sudut pandang nyaman..."
        ],
        [
            "Memberikan jawaban...",
            "Memberikan jawaban untukmu...",
            "Menyusun pesan balasan...",
            "Menyampaikan respon hangat...",
            "Memberikan sudut pandang hangat...",
            "Menyampaikan saran terbaik..."
        ],
        [
            "Sudah siap...",
            "Jawaban sudah siap...",
            "Balasan sudah siap...",
            "Sedikit lagi siap..."
        ]
    ];

    function showTypingIndicator() {
        if (!typingIndicator) return;

        if (typingIndicatorTimer) {
            clearInterval(typingIndicatorTimer);
            typingIndicatorTimer = null;
        }
        if (typingIndicatorStepTimeout) {
            clearTimeout(typingIndicatorStepTimeout);
            typingIndicatorStepTimeout = null;
        }

        // Ambil 1 frasa acak per tahapan untuk variasi dinamis namun tetap berurutan logis
        const currentSequence = indicatorStages.map(stageGroup => {
            return stageGroup[Math.floor(Math.random() * stageGroup.length)];
        });

        let currentStageIndex = 0;
        const textEl = document.getElementById('typingIndicatorText');

        if (textEl) {
            textEl.textContent = currentSequence[0];
            textEl.style.opacity = '1';
            textEl.style.transform = 'translateY(0)';
        }

        typingIndicator.classList.remove('hidden');
        scrollToBottom();

        // Transisi berurut antar tahapan setiap ~750ms
        typingIndicatorTimer = setInterval(() => {
            if (currentStageIndex < currentSequence.length - 1) {
                currentStageIndex++;
                if (textEl) {
                    textEl.style.opacity = '0';
                    textEl.style.transform = 'translateY(-2px)';

                    typingIndicatorStepTimeout = setTimeout(() => {
                        textEl.textContent = currentSequence[currentStageIndex];
                        textEl.style.opacity = '1';
                        textEl.style.transform = 'translateY(0)';
                    }, 120);
                }
            } else {
                clearInterval(typingIndicatorTimer);
                typingIndicatorTimer = null;
            }
        }, 750);
    }

    function hideTypingIndicator() {
        if (typingIndicatorTimer) {
            clearInterval(typingIndicatorTimer);
            typingIndicatorTimer = null;
        }
        if (typingIndicatorStepTimeout) {
            clearTimeout(typingIndicatorStepTimeout);
            typingIndicatorStepTimeout = null;
        }
        if (typingIndicator) {
            typingIndicator.classList.add('hidden');
        }
    }

    const moodPresets = @json(\App\Services\MoodExpertService::getMoods());
    const exploreCategoriesData = @json($exploreCategories ?? []);
    const exploreMoodsData = @json($allMoods ?? []);
    const registeredProfessionalsData = @json($registeredPros ?? ($professionals ?? \App\Models\Professional::where('status', 'active')->get()));
    const professionalsDirectoryUrl = "{{ route('professionals') }}";

    let exploreCurrentStep = 1;
    let selectedCategory = null;
    let selectedSubcategory = null;
    let selectedTopic = null;
    let selectedMood = null;

    const exploreRoomSection = document.getElementById('exploreRoomSection');
    const profileRoomSection = document.getElementById('profileRoomSection');
    const profileScrollContainer = document.getElementById('profileScrollContainer');
    const topicsRoomSection = document.getElementById('topicsRoomSection');
    const topicsScrollContainer = document.getElementById('topicsScrollContainer');
    const professionalsRoomSection = document.getElementById('professionalsRoomSection');
    const professionalsScrollContainer = document.getElementById('professionalsScrollContainer');
    const chatComposerArea = document.getElementById('chatComposerArea');
    const chatHeaderControls = document.getElementById('chatHeaderControls');

    const exploreStepTitle = document.getElementById('exploreStepTitle');
    const exploreStepDescription = document.getElementById('exploreStepDescription');
    const roomExploreProgressBar = document.getElementById('roomExploreProgressBar');

    const exploreStep1Container = document.getElementById('exploreStep1Container');
    const exploreStep2Container = document.getElementById('exploreStep2Container');
    const exploreStep3Container = document.getElementById('exploreStep3Container');
    const exploreStep4Container = document.getElementById('exploreStep4Container');
    const exploreScrollContainer = document.getElementById('exploreScrollContainer');

    function openNewChatExplore() {
        stopIdleTimer();
        hideTypingIndicator();
        closeSidebar();

        selectedCategory = null;
        selectedSubcategory = null;
        selectedTopic = null;
        selectedMood = null;

        if (messagesContainer) messagesContainer.classList.add('hidden');
        if (chatComposerArea) chatComposerArea.classList.add('hidden');
        if (profileRoomSection) {
            profileRoomSection.classList.add('hidden');
            profileRoomSection.classList.remove('flex');
        }
        if (topicsRoomSection) {
            topicsRoomSection.classList.add('hidden');
            topicsRoomSection.classList.remove('flex');
        }
        if (professionalsRoomSection) {
            professionalsRoomSection.classList.add('hidden');
            professionalsRoomSection.classList.remove('flex');
        }
        if (exploreRoomSection) {
            exploreRoomSection.classList.remove('hidden');
            exploreRoomSection.classList.add('flex');
        }

        goToExploreStep(1);
    }

    function closeNewChatExplore() {
        @if($session)
        if (exploreRoomSection) {
            exploreRoomSection.classList.add('hidden');
            exploreRoomSection.classList.remove('flex');
        }
        if (messagesContainer) messagesContainer.classList.remove('hidden');
        if (chatComposerArea) chatComposerArea.classList.remove('hidden');

        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        startIdleTimer();
        @endif
    }

    function openProfileSection(target = null) {
        stopIdleTimer();
        hideTypingIndicator();
        closeSidebar();

        const accountPopover = document.getElementById('accountPopover');
        if (accountPopover) accountPopover.classList.add('hidden');

        if (messagesContainer) messagesContainer.classList.add('hidden');
        if (chatComposerArea) chatComposerArea.classList.add('hidden');
        if (exploreRoomSection) {
            exploreRoomSection.classList.add('hidden');
            exploreRoomSection.classList.remove('flex');
        }
        if (topicsRoomSection) {
            topicsRoomSection.classList.add('hidden');
            topicsRoomSection.classList.remove('flex');
        }
        if (professionalsRoomSection) {
            professionalsRoomSection.classList.add('hidden');
            professionalsRoomSection.classList.remove('flex');
        }
        if (profileRoomSection) {
            profileRoomSection.classList.remove('hidden');
            profileRoomSection.classList.add('flex');
        }

        if (target === 'settings') {
            setTimeout(() => {
                const settingsEl = document.getElementById('profileSettingsSection');
                if (settingsEl) {
                    settingsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 80);
        } else if (profileScrollContainer) {
            profileScrollContainer.scrollTop = 0;
        }
    }

    function closeProfileSection() {
        if (profileRoomSection) {
            profileRoomSection.classList.add('hidden');
            profileRoomSection.classList.remove('flex');
        }

        @if($session)
        if (messagesContainer) messagesContainer.classList.remove('hidden');
        if (chatComposerArea) chatComposerArea.classList.remove('hidden');

        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        startIdleTimer();
        @else
        if (exploreRoomSection) {
            exploreRoomSection.classList.remove('hidden');
            exploreRoomSection.classList.add('flex');
        }
        @endif
    }

    function openTopicsSection() {
        stopIdleTimer();
        hideTypingIndicator();
        closeSidebar();

        const accountPopover = document.getElementById('accountPopover');
        if (accountPopover) accountPopover.classList.add('hidden');

        if (messagesContainer) messagesContainer.classList.add('hidden');
        if (chatComposerArea) chatComposerArea.classList.add('hidden');
        if (exploreRoomSection) {
            exploreRoomSection.classList.add('hidden');
            exploreRoomSection.classList.remove('flex');
        }
        if (profileRoomSection) {
            profileRoomSection.classList.add('hidden');
            profileRoomSection.classList.remove('flex');
        }
        if (professionalsRoomSection) {
            professionalsRoomSection.classList.add('hidden');
            professionalsRoomSection.classList.remove('flex');
        }
        if (topicsRoomSection) {
            topicsRoomSection.classList.remove('hidden');
            topicsRoomSection.classList.add('flex');
        }

        if (topicsScrollContainer) {
            topicsScrollContainer.scrollTop = 0;
        }
    }

    function closeTopicsSection() {
        if (topicsRoomSection) {
            topicsRoomSection.classList.add('hidden');
            topicsRoomSection.classList.remove('flex');
        }

        @if($session)
        if (messagesContainer) messagesContainer.classList.remove('hidden');
        if (chatComposerArea) chatComposerArea.classList.remove('hidden');

        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        startIdleTimer();
        @else
        if (exploreRoomSection) {
            exploreRoomSection.classList.remove('hidden');
            exploreRoomSection.classList.add('flex');
        }
        @endif
    }

    function openProfessionalsSection() {
        stopIdleTimer();
        hideTypingIndicator();
        closeSidebar();

        const accountPopover = document.getElementById('accountPopover');
        if (accountPopover) accountPopover.classList.add('hidden');

        if (messagesContainer) messagesContainer.classList.add('hidden');
        if (chatComposerArea) chatComposerArea.classList.add('hidden');
        if (exploreRoomSection) {
            exploreRoomSection.classList.add('hidden');
            exploreRoomSection.classList.remove('flex');
        }
        if (profileRoomSection) {
            profileRoomSection.classList.add('hidden');
            profileRoomSection.classList.remove('flex');
        }
        if (topicsRoomSection) {
            topicsRoomSection.classList.add('hidden');
            topicsRoomSection.classList.remove('flex');
        }
        if (professionalsRoomSection) {
            professionalsRoomSection.classList.remove('hidden');
            professionalsRoomSection.classList.add('flex');
        }

        if (professionalsScrollContainer) {
            professionalsScrollContainer.scrollTop = 0;
        }
    }

    function closeProfessionalsSection() {
        if (professionalsRoomSection) {
            professionalsRoomSection.classList.add('hidden');
            professionalsRoomSection.classList.remove('flex');
        }

        @if($session)
        if (messagesContainer) messagesContainer.classList.remove('hidden');
        if (chatComposerArea) chatComposerArea.classList.remove('hidden');

        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        startIdleTimer();
        @else
        if (exploreRoomSection) {
            exploreRoomSection.classList.remove('hidden');
            exploreRoomSection.classList.add('flex');
        }
        @endif
    }

    function filterProfessionals() {
        const searchInput = document.getElementById('professionalsSearchInput');
        const clearBtn = document.getElementById('professionalsClearSearchBtn');
        const query = (searchInput?.value || '').toLowerCase().trim();

        if (clearBtn) {
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        const cards = document.querySelectorAll('.professional-room-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.proName || '';
            const display = card.dataset.proDisplay || '';
            const degree = card.dataset.proDegree || '';
            const specialization = card.dataset.proSpecialization || '';
            const location = card.dataset.proLocation || '';
            const address = card.dataset.proAddress || '';

            const matchesQuery = !query ||
                name.includes(query) ||
                display.includes(query) ||
                degree.includes(query) ||
                specialization.includes(query) ||
                location.includes(query) ||
                address.includes(query);

            if (matchesQuery) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const emptyState = document.getElementById('professionalsEmptyState');
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    function clearProfessionalsSearch() {
        const searchInput = document.getElementById('professionalsSearchInput');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        filterProfessionals();
    }

    function resetProfessionalsFilter() {
        clearProfessionalsSearch();
    }

    function selectTopicForRoom(topicSlug) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/chat/start/${topicSlug}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }

    let activeTopicsCategory = '';

    function filterTopics() {
        const searchInput = document.getElementById('topicsSearchInput');
        const clearBtn = document.getElementById('topicsClearSearchBtn');
        const query = (searchInput?.value || '').toLowerCase().trim();

        if (clearBtn) {
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        const cards = document.querySelectorAll('.topic-list-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const catId = card.dataset.categoryId || '';
            const name = card.dataset.topicName || '';
            const desc = card.dataset.topicDesc || '';
            const subcat = card.dataset.subcatName || '';
            const cat = card.dataset.catName || '';

            const matchesCategory = !activeTopicsCategory || (catId === activeTopicsCategory);
            const matchesQuery = !query || name.includes(query) || desc.includes(query) || subcat.includes(query) || cat.includes(query);

            if (matchesCategory && matchesQuery) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const emptyState = document.getElementById('topicsEmptyState');
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    function setTopicsCategoryFilter(catId) {
        activeTopicsCategory = catId ? String(catId) : '';

        document.querySelectorAll('.topics-category-pill').forEach(pill => {
            const pillCat = pill.dataset.categoryFilter || '';
            if (pillCat === activeTopicsCategory) {
                pill.classList.remove('bg-[var(--card-secondary)]', 'border-[var(--border-color)]', 'text-[var(--txt-secondary)]');
                pill.classList.add('bg-[#252525]', 'text-white', 'dark:bg-white', 'dark:text-[#252525]', 'border-transparent', 'font-semibold', 'shadow-xs');
            } else {
                pill.classList.add('bg-[var(--card-secondary)]', 'border-[var(--border-color)]', 'text-[var(--txt-secondary)]');
                pill.classList.remove('bg-[#252525]', 'text-white', 'dark:bg-white', 'dark:text-[#252525]', 'border-transparent', 'font-semibold', 'shadow-xs');
            }
        });

        filterTopics();
    }

    function clearTopicsSearch() {
        const searchInput = document.getElementById('topicsSearchInput');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        filterTopics();
    }

    function resetTopicsFilter() {
        const searchInput = document.getElementById('topicsSearchInput');
        if (searchInput) searchInput.value = '';
        setTopicsCategoryFilter('');
    }

    window.openNewChatExplore = openNewChatExplore;
    window.closeNewChatExplore = closeNewChatExplore;
    window.openProfileSection = openProfileSection;
    window.closeProfileSection = closeProfileSection;
    window.openTopicsSection = openTopicsSection;
    window.closeTopicsSection = closeTopicsSection;
    window.openProfessionalsSection = openProfessionalsSection;
    window.closeProfessionalsSection = closeProfessionalsSection;
    window.filterProfessionals = filterProfessionals;
    window.clearProfessionalsSearch = clearProfessionalsSearch;
    window.resetProfessionalsFilter = resetProfessionalsFilter;
    window.selectTopicForRoom = selectTopicForRoom;
    window.filterTopics = filterTopics;
    window.setTopicsCategoryFilter = setTopicsCategoryFilter;
    window.clearTopicsSearch = clearTopicsSearch;
    window.resetTopicsFilter = resetTopicsFilter;

    // AJAX Profile Name Update Form Handler
    const profileUpdateNameForm = document.getElementById('profileUpdateNameForm');
    const profileAccountNameInput = document.getElementById('profileAccountNameInput');
    const profileDisplayName = document.getElementById('profileDisplayName');
    const profileAvatarLetter = document.getElementById('profileAvatarLetter');
    const profileUpdateSuccessAlert = document.getElementById('profileUpdateSuccessAlert');
    const profileUpdateErrorAlert = document.getElementById('profileUpdateErrorAlert');
    const profileSuccessMsgText = document.getElementById('profileSuccessMsgText');
    const profileErrorMsgText = document.getElementById('profileErrorMsgText');
    const saveProfileSpinner = document.getElementById('saveProfileSpinner');
    const saveProfileNameBtn = document.getElementById('saveProfileNameBtn');

    if (profileUpdateNameForm) {
        profileUpdateNameForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const newName = profileAccountNameInput ? profileAccountNameInput.value.trim() : '';
            if (!newName) return;

            if (profileUpdateSuccessAlert) profileUpdateSuccessAlert.classList.add('hidden');
            if (profileUpdateErrorAlert) profileUpdateErrorAlert.classList.add('hidden');
            if (saveProfileSpinner) saveProfileSpinner.classList.remove('hidden');
            if (saveProfileNameBtn) saveProfileNameBtn.disabled = true;

            try {
                const res = await fetch(profileUpdateNameForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        name: newName
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const updatedName = data.user.name;
                    const updatedInitial = data.user.initial || updatedName.charAt(0).toUpperCase();

                    // 1. Update in profile section
                    if (profileDisplayName) profileDisplayName.textContent = updatedName;
                    if (profileAvatarLetter) profileAvatarLetter.textContent = updatedInitial;

                    // 2. Update in sidebar footer trigger
                    const accountTriggerName = document.querySelector('#accountTriggerBtn .sidebar-text p:first-child');
                    const accountTriggerInitial = document.querySelector('#accountTriggerBtn > div:first-child');
                    if (accountTriggerName) accountTriggerName.textContent = updatedName;
                    if (accountTriggerInitial) accountTriggerInitial.textContent = updatedInitial;

                    // 3. Update in account popover
                    const popoverName = document.querySelector('#accountPopover .border-b p:first-child');
                    if (popoverName) popoverName.textContent = updatedName;

                    if (profileSuccessMsgText && data.message) {
                        profileSuccessMsgText.textContent = data.message;
                    }
                    if (profileUpdateSuccessAlert) {
                        profileUpdateSuccessAlert.classList.remove('hidden');
                        setTimeout(() => {
                            if (profileUpdateSuccessAlert) profileUpdateSuccessAlert.classList.add('hidden');
                        }, 4000);
                    }
                } else {
                    const errorMsg = (data.errors && data.errors.name) ? data.errors.name[0] : (data.message || 'Gagal memperbarui nama akun.');
                    if (profileErrorMsgText) profileErrorMsgText.textContent = errorMsg;
                    if (profileUpdateErrorAlert) profileUpdateErrorAlert.classList.remove('hidden');
                }
            } catch (err) {
                console.error('Update name error', err);
                if (profileErrorMsgText) profileErrorMsgText.textContent = 'Terjadi kesalahan saat menyimpan perubahan.';
                if (profileUpdateErrorAlert) profileUpdateErrorAlert.classList.remove('hidden');
            } finally {
                if (saveProfileSpinner) saveProfileSpinner.classList.add('hidden');
                if (saveProfileNameBtn) saveProfileNameBtn.disabled = false;
            }
        });
    }

    // ==========================================
    // THEME & APPLICATION LANGUAGE SETTINGS
    // ==========================================
    const appI18n = {
        en: {
            profile_edit_name_title: "Change Account Name",
            profile_edit_name_desc: "Update your account name or display identity for chat sessions.",
            profile_name_placeholder: "Enter account name...",
            btn_save_changes: "Save Changes",
            profile_update_success: "Account name updated successfully.",
            profile_update_error: "An error occurred while updating name.",

            settings_title: "Settings",
            settings_desc: "Manage interface appearance and application language preferences.",
            theme_title: "Interface Theme",
            theme_desc: "Choose the interface appearance that feels most comfortable for your eyes.",
            theme_system: "System",
            theme_system_desc: "Dual Tone (OS)",
            theme_light: "Light Mode",
            theme_light_desc: "Light Theme",
            theme_dark: "Dark Mode",
            theme_dark_desc: "Dark Theme",

            lang_title: "App Language",
            lang_desc: "Choose the system interface language. AI chat will automatically reply in your conversation language.",
            lang_default_badge: "Default",
            lang_id_badge: "Local",
            lang_en_desc: "Default system interface language",
            lang_id_desc: "Indonesian interface language",

            logout_title: "Log Out",
            logout_desc: "Sign out of this account securely.",
            btn_logout: "Log Out",

            sidebar_new_chat: "New Chat",
            sidebar_history_title: "Recent Chats",
            sidebar_history_empty: "No other chat history yet",
            sidebar_profile: "Profile",
            sidebar_settings: "Settings",
            sidebar_theme: "Change Theme",
            sidebar_logout: "Sign Out",

            chat_input_placeholder: "Share anything...",
            chat_stuck_chip: "I'm not sure what to talk about",
            chat_send_tooltip: "Send Message",
            chat_vn_tooltip: "Record Voice Note",

            offtopic_title: "Want to Discuss Another Topic?",
            offtopic_desc: "It seems your story is branching into another topic. To keep your reflection focused and effective, you can start a new conversation with a more fitting topic.",
            offtopic_new_btn: "Start New Conversation",
            offtopic_catalog_btn: "View Topic Catalog",

            explore_step_1: "Category",
            explore_step_2: "Subcategory",
            explore_step_3: "Topic",
            explore_step_4: "Confirmation",
        },
        id: {
            profile_edit_name_title: "Ubah Nama Akun",
            profile_edit_name_desc: "Perbarui nama akun atau identitas tampilanmu pada sesi percakapan.",
            profile_name_placeholder: "Masukkan nama akun...",
            btn_save_changes: "Simpan Perubahan",
            profile_update_success: "Nama akun berhasil diperbarui.",
            profile_update_error: "Terjadi kesalahan saat memperbarui nama.",

            settings_title: "Pengaturan",
            settings_desc: "Kelola preferensi antarmuka tampilan dan bahasa antarmuka aplikasi.",
            theme_title: "Tema Antarmuka",
            theme_desc: "Pilih tampilan antarmuka yang paling nyaman untuk matamu.",
            theme_system: "Sistem",
            theme_system_desc: "Dua Warna (OS)",
            theme_light: "Light Mode",
            theme_light_desc: "Mode Terang",
            theme_dark: "Dark Mode",
            theme_dark_desc: "Mode Gelap",

            lang_title: "Bahasa Aplikasi",
            lang_desc: "Pilih bahasa tampilan antarmuka sistem. Pesan percakapan AI akan tetap merespons secara otomatis sesuai bahasa ceritamu.",
            lang_default_badge: "Default",
            lang_id_badge: "Lokal",
            lang_en_desc: "Bahasa tampilan sistem bawaan",
            lang_id_desc: "Bahasa tampilan bahasa Indonesia",

            logout_title: "Log Out",
            logout_desc: "Keluar dari sesi akun ini dengan aman.",
            btn_logout: "Log Out",

            sidebar_new_chat: "Obrolan Baru",
            sidebar_history_title: "Riwayat Obrolan",
            sidebar_history_empty: "Belum ada riwayat obrolan lainnya",
            sidebar_profile: "Profile",
            sidebar_settings: "Setting",
            sidebar_theme: "Ganti Tema",
            sidebar_logout: "Keluar",

            chat_input_placeholder: "Ceritakan apapun...",
            chat_stuck_chip: "Aku bingung mau cerita apa",
            chat_send_tooltip: "Kirim Pesan",
            chat_vn_tooltip: "Rekam Pesan Suara",

            offtopic_title: "Ingin Membahas Topik Lain?",
            offtopic_desc: "Sepertinya ceritamu mulai menyentuh hal di luar topik ini. Agar refleksimu tetap terarah dan efektif, kamu bisa membuat percakapan baru dengan topik yang lebih sesuai.",
            offtopic_new_btn: "Buat Percakapan Baru",
            offtopic_catalog_btn: "Lihat Katalog Topik",

            explore_step_1: "Kategori",
            explore_step_2: "Subkategori",
            explore_step_3: "Topik",
            explore_step_4: "Konfirmasi",
        }
    };

    function getSavedTheme() {
        return localStorage.getItem('weisz_theme') || 'system';
    }

    function applyTheme(theme) {
        if (theme === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', prefersDark);
        } else if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateThemeRadioUI(theme);
    }

    function setAppTheme(theme) {
        localStorage.setItem('weisz_theme', theme);
        applyTheme(theme);
        window.dispatchEvent(new CustomEvent('weisz-theme-changed', { detail: { theme: theme } }));
    }

    function updateThemeRadioUI(theme) {
        ['system', 'light', 'dark'].forEach(t => {
            const card = document.getElementById('themeCard_' + t);
            if (!card) return;
            const isSelected = (t === theme);

            if (isSelected) {
                card.classList.add('border-blue-500', 'dark:border-blue-400', 'shadow-xs');
                card.classList.remove('border-[var(--border-color)]');
                card.setAttribute('aria-checked', 'true');
            } else {
                card.classList.remove('border-blue-500', 'dark:border-blue-400', 'shadow-xs');
                card.classList.add('border-[var(--border-color)]');
                card.setAttribute('aria-checked', 'false');
            }
        });
    }

    window.addEventListener('weisz-theme-changed', (e) => {
        const theme = e.detail?.theme || getSavedTheme();
        updateThemeRadioUI(theme);
    });

    function getSavedLanguage() {
        return '{{ app()->getLocale() }}';
    }

    function applyAppLanguage(lang) {
        const validLang = (lang === 'id') ? 'id' : 'en';
        document.documentElement.lang = validLang;
        updateLanguageRadioUI(validLang);
    }

    function setAppLanguage(lang) {
        const validLang = (lang === 'id') ? 'id' : 'en';
        localStorage.setItem('weisz_lang', validLang);
        applyAppLanguage(validLang);

        // Sync with backend session and reload to apply server localization
        fetch('{{ route("locale.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ locale: validLang })
        }).finally(() => {
            window.location.reload();
        });
    }

    function updateLanguageRadioUI(lang) {
        const profileLangSelect = document.getElementById('profileLanguageSelect');
        if (profileLangSelect) {
            profileLangSelect.value = lang;
        }
        ['en', 'id'].forEach(l => {
            const card = document.getElementById('langCard_' + l);
            if (!card) return;
            const isSelected = (l === lang);
            const dot = card.querySelector('.lang-radio-dot');
            const circle = card.querySelector('.lang-radio-circle');

            if (isSelected) {
                card.classList.add('border-blue-500', 'ring-2', 'ring-blue-500/25', 'bg-blue-50/20', 'dark:bg-blue-950/20');
                card.classList.remove('border-[var(--border-color)]', 'bg-[var(--card-secondary)]/40');
                card.setAttribute('aria-checked', 'true');
                if (dot) dot.classList.remove('hidden');
                if (circle) circle.classList.add('border-blue-500');
            } else {
                card.classList.remove('border-blue-500', 'ring-2', 'ring-blue-500/25', 'bg-blue-50/20', 'dark:bg-blue-950/20');
                card.classList.add('border-[var(--border-color)]', 'bg-[var(--card-secondary)]/40');
                card.setAttribute('aria-checked', 'false');
                if (dot) dot.classList.add('hidden');
                if (circle) circle.classList.remove('border-blue-500');
            }
        });
    }

    window.setAppTheme = setAppTheme;
    window.setAppLanguage = setAppLanguage;

    // Initialize Theme and Language on load
    document.addEventListener('DOMContentLoaded', () => {
        applyTheme(getSavedTheme());
        applyAppLanguage(getSavedLanguage());
    });

    const sidebarNewChatBtn = document.getElementById('sidebarNewChatBtn');
    if (sidebarNewChatBtn) {
        sidebarNewChatBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openNewChatExplore();
        });
    }

    const sidebarProfessionalsBtn = document.getElementById('sidebarProfessionalsBtn');
    if (sidebarProfessionalsBtn) {
        sidebarProfessionalsBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openProfessionalsSection();
        });
    }

    const stepMetadata = {
        1: {
            title: @json(__('Apa yang Sedang Mengganjal Pikiranmu?')),
            desc: @json(__('Pilih kategori situasi yang paling mendekati kondisimu saat ini. Setiap kategori dirancang dengan konteks psikologis spesifik untuk mengurai keresahanmu secara bertahap dan terarah.')),
            progress: "0%"
        },
        2: {
            title: @json(__('Kerucutkan Subkategori')),
            desc: @json(__('Pilih area yang paling relevan untuk membantumu memfokuskan cerita pada aspek yang paling mengganggu pikiranmu.')),
            progress: "33.33%"
        },
        3: {
            title: @json(__('Pilih Topik Percakapan')),
            desc: @json(__('Pilih topik yang paling mewakili situasi yang sedang kamu alami. Weisz akan mengunci konteks ini agar sesi berlangsung terarah dan tidak melebar.')),
            progress: "66.66%"
        },
        4: {
            title: @json(__('Bagaimana kondisi atau perasaanmu saat ini?')),
            desc: @json(__('Pilih ekspresi yang paling menggambarkan suasana hatimu agar Weisz AI dapat memahami dan masuk ke suasana hatimu secara tepat.')),
            progress: "100%"
        }
    };

    function updateStepperUI(step) {
        if (roomExploreProgressBar) {
            roomExploreProgressBar.style.width = stepMetadata[step].progress;
        }
        if (exploreStepTitle) {
            exploreStepTitle.textContent = stepMetadata[step].title;
        }
        if (exploreStepDescription) {
            exploreStepDescription.textContent = stepMetadata[step].desc;
        }

        for (let i = 1; i <= 4; i++) {
            const circle = document.getElementById('stepCircle' + i);
            const label = document.getElementById('stepLabel' + i);
            if (!circle || !label) continue;

            circle.className = 'w-11 h-11 sm:w-13 sm:h-13 rounded-full font-extrabold text-xs sm:text-sm flex items-center justify-center transition-all';
            label.className = 'text-xs sm:text-sm transition';

            if (i === step) {
                circle.classList.add('bg-[#252525]', 'text-white', 'dark:bg-white', 'dark:text-[#252525]', 'shadow-lg', 'shadow-black/10', 'dark:shadow-black/40', 'ring-4', 'sm:ring-6', 'ring-[var(--card-surface)]', 'dark:ring-[#1f1f1f]', 'scale-105', 'cursor-pointer');
                circle.disabled = false;
                label.classList.add('font-bold', 'text-[var(--txt-primary)]', 'cursor-pointer');
                label.disabled = false;
            } else if (i < step) {
                circle.classList.add('bg-[#252525]', 'text-white', 'dark:bg-white', 'dark:text-[#252525]', 'shadow-md', 'ring-4', 'sm:ring-6', 'ring-[var(--card-surface)]', 'dark:ring-[#1f1f1f]', 'hover:scale-110', 'cursor-pointer');
                circle.disabled = false;
                label.classList.add('font-semibold', 'text-[var(--txt-secondary)]', 'hover:text-[var(--txt-primary)]', 'cursor-pointer');
                label.disabled = false;
            } else {
                circle.classList.add('bg-[var(--card-secondary)]', 'text-[var(--txt-tertiary)]', 'border-2', 'border-[var(--border-color)]', 'ring-4', 'sm:ring-6', 'ring-[var(--card-surface)]', 'dark:ring-[#1f1f1f]', 'cursor-not-allowed');
                circle.disabled = true;
                label.classList.add('font-medium', 'text-[var(--txt-tertiary)]', 'cursor-not-allowed');
                label.disabled = true;
            }
        }
    }

    function goToExploreStep(step) {
        if (step === 2 && !selectedCategory) return;
        if (step === 3 && (!selectedCategory || !selectedSubcategory)) return;
        if (step === 4 && (!selectedCategory || !selectedSubcategory || !selectedTopic)) return;

        exploreCurrentStep = step;
        updateStepperUI(step);

        if (exploreStep1Container) exploreStep1Container.classList.toggle('hidden', step !== 1);
        if (exploreStep2Container) exploreStep2Container.classList.toggle('hidden', step !== 2);
        if (exploreStep3Container) exploreStep3Container.classList.toggle('hidden', step !== 3);
        if (exploreStep4Container) exploreStep4Container.classList.toggle('hidden', step !== 4);

        if (exploreScrollContainer) {
            exploreScrollContainer.scrollTop = 0;
        }
    }

    function selectCategory(catId) {
        selectedCategory = exploreCategoriesData.find(c => c.id == catId);
        if (!selectedCategory) return;

        const subGrid = document.getElementById('subcategoriesGrid');

        if (subGrid) {
            const subHtml = (selectedCategory.subcategories || []).map(sub => `
                <div onclick="selectSubcategory('${sub.id}')" class="group relative rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/40 hover:-translate-y-1 hover:border-[#252525] dark:hover:border-white/50 overflow-hidden cursor-pointer shadow-xs">
                    <div class="absolute -right-16 -top-16 w-36 h-36 rounded-full bg-gradient-to-br from-emerald-400/15 via-teal-300/10 to-transparent blur-2xl group-hover:scale-150 transition-transform duration-500 pointer-events-none" aria-hidden="true"></div>
                    <div class="space-y-4 flex-1 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)]/80 flex items-center justify-center text-[var(--txt-primary)] group-hover:border-[var(--brand-main)]/50 group-hover:bg-[var(--card-surface)] transition">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[var(--card-secondary)] text-[var(--txt-secondary)] border border-[var(--border-color)]/70">
                                    ${(sub.topics || []).length} Topik
                                </span>
                            </div>
                            <div class="space-y-1.5 pt-1">
                                <h2 class="text-base sm:text-lg font-bold text-[var(--txt-primary)] group-hover:text-[var(--brand-main)] transition line-clamp-2">
                                    ${sub.name}
                                </h2>
                                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] leading-relaxed line-clamp-3">
                                    ${sub.description || ''}
                                </p>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-[var(--border-color)]/60 flex items-center justify-between text-xs font-semibold text-[var(--txt-secondary)] group-hover:text-[var(--txt-primary)] transition">
                            <span>Pilih Subkategori</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1 text-[var(--brand-main)]" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </div>
                    </div>
                </div>
            `).join('');

            subGrid.innerHTML = subHtml;
        }

        goToExploreStep(2);
    }

    function selectSubcategory(subId) {
        if (!selectedCategory) return;
        selectedSubcategory = (selectedCategory.subcategories || []).find(s => s.id == subId);
        if (!selectedSubcategory) return;

        const topicsGrid = document.getElementById('topicsGrid');

        if (topicsGrid) {
            const topicsHtml = (selectedSubcategory.topics || []).map(topic => {
                const chips = topic.suggested_chips || [];
                let chipsHtml = '';
                if (Array.isArray(chips) && chips.length > 0) {
                    chipsHtml = `
                        <div class="pt-3 mt-2 border-t border-[var(--border-color)]/70 space-y-2">
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-[var(--txt-secondary)]">
                                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.322 1.488-3.923 3.518-3.923 4.869.34-.143.766-.213 1.25-.213 1.932 0 3.5 1.568 3.5 3.5s-1.568 3.5-3.5 3.5c-.975 0-1.874-.397-2.667-1.036zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.322 1.488-3.923 3.518-3.923 4.869.34-.143.766-.213 1.25-.213 1.932 0 3.5 1.568 3.5 3.5s-1.568 3.5-3.5 3.5c-.975 0-1.874-.397-2.667-1.036z"/>
                                </svg>
                                <span class="tracking-normal font-medium">Mungkin ini yang kamu rasakan:</span>
                            </div>
                            <div class="grid grid-cols-1 gap-1.5">
                                ${chips.slice(0, 3).map(chip => `
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[var(--card-secondary)]/80 border border-[var(--border-color)]/70 text-xs text-[var(--txt-secondary)]">
                                        <span class="text-amber-500 font-bold select-none text-xs">&ldquo;</span>
                                        <span class="truncate italic font-normal">${chip}</span>
                                        <span class="text-amber-500 font-bold select-none text-xs">&rdquo;</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                return `
                    <div onclick="selectTopic('${topic.id}')" class="group relative rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/40 hover:-translate-y-1 hover:border-[#252525] dark:hover:border-white/50 overflow-hidden cursor-pointer shadow-xs">
                        <div class="absolute -right-16 -top-16 w-36 h-36 rounded-full bg-gradient-to-br from-sky-400/15 via-blue-300/10 to-transparent blur-2xl group-hover:scale-150 transition-transform duration-500 pointer-events-none" aria-hidden="true"></div>
                        <div class="space-y-3 flex-1 flex flex-col justify-start">
                            <div class="space-y-1.5">
                                <h2 class="text-base sm:text-lg font-bold text-[var(--txt-primary)] group-hover:text-[var(--brand-main)] transition line-clamp-2">
                                    ${topic.name}
                                </h2>
                                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] leading-relaxed">
                                    ${topic.description || ''}
                                </p>
                            </div>
                            ${chipsHtml}
                        </div>
                    </div>
                `;
            }).join('');

            topicsGrid.innerHTML = topicsHtml;
        }

        goToExploreStep(3);
    }

    function selectTopic(topicId) {
        if (!selectedSubcategory) return;
        selectedTopic = (selectedSubcategory.topics || []).find(t => t.id == topicId);
        if (!selectedTopic) return;

        const form = document.getElementById('roomStartSessionForm');
        if (form) {
            form.action = `/chat/start/${selectedTopic.slug}`;
        }

        // Reset mood selection
        selectedMood = null;
        const moodInput = document.getElementById('roomInitialMoodInput');
        if (moodInput) moodInput.value = '';

        const startBtn = document.getElementById('roomStartSessionBtn');
        if (startBtn) {
            startBtn.disabled = true;
            startBtn.classList.add('opacity-40', 'cursor-not-allowed');
            startBtn.classList.remove('cursor-pointer');
        }

        document.querySelectorAll('.room-mood-card').forEach(c => {
            c.classList.remove('border-[#252525]', 'dark:border-white', 'ring-4', 'ring-[#252525]/20', 'dark:ring-white/20', 'scale-[1.03]', 'shadow-lg');
            c.classList.add('border-[var(--border-color)]');
            const ind = c.querySelector('.room-check-indicator');
            if (ind) ind.classList.add('opacity-0');
        });

        goToExploreStep(4);
    }

    function selectRoomMood(moodKey) {
        selectedMood = moodKey;
        const moodInput = document.getElementById('roomInitialMoodInput');
        if (moodInput) moodInput.value = moodKey;

        document.querySelectorAll('.room-mood-card').forEach(card => {
            const ind = card.querySelector('.room-check-indicator');
            if (card.dataset.mood === moodKey) {
                card.classList.remove('border-[var(--border-color)]');
                card.classList.add('border-[#252525]', 'dark:border-white', 'ring-4', 'ring-[#252525]/20', 'dark:ring-white/20', 'scale-[1.03]', 'shadow-lg');
                if (ind) ind.classList.remove('opacity-0');
            } else {
                card.classList.remove('border-[#252525]', 'dark:border-white', 'ring-4', 'ring-[#252525]/20', 'dark:ring-white/20', 'scale-[1.03]', 'shadow-lg');
                card.classList.add('border-[var(--border-color)]');
                if (ind) ind.classList.add('opacity-0');
            }
        });

        const startBtn = document.getElementById('roomStartSessionBtn');
        if (startBtn) {
            startBtn.disabled = false;
            startBtn.classList.remove('opacity-40', 'cursor-not-allowed');
            startBtn.classList.add('cursor-pointer');
        }
    }

    window.openNewChatExplore = openNewChatExplore;
    window.closeNewChatExplore = closeNewChatExplore;
    window.goToExploreStep = goToExploreStep;
    window.selectCategory = selectCategory;
    window.selectSubcategory = selectSubcategory;
    window.selectTopic = selectTopic;
    window.selectRoomMood = selectRoomMood;

    @if(!empty($startWithTopics))
    document.addEventListener('DOMContentLoaded', () => {
        openTopicsSection();
    });
    @elseif(!empty($startWithProfile))
    document.addEventListener('DOMContentLoaded', () => {
        openProfileSection();
    });
    @elseif(!empty($startWithExplore) || empty($session))
    document.addEventListener('DOMContentLoaded', () => {
        openNewChatExplore();
    });
    @endif

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (window.location.hash === '#topics' || urlParams.get('topics') === '1' || urlParams.get('view') === 'topics') {
            openTopicsSection();
            if (urlParams.get('search')) {
                const searchInput = document.getElementById('topicsSearchInput');
                if (searchInput) {
                    searchInput.value = urlParams.get('search');
                    filterTopics();
                }
            }
            if (urlParams.get('category')) {
                setTopicsCategoryFilter(urlParams.get('category'));
            }
        } else if (window.location.hash === '#profile' || urlParams.get('profile') === '1' || urlParams.get('view') === 'profile') {
            openProfileSection();
        }
    });

    // Sidebar Collapse (Desktop) & Drawer (Mobile) Controls
    const chatSidebar = document.getElementById('chatSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const openSidebarBtn = document.getElementById('openSidebarBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const collapseSidebarBtn = document.getElementById('collapseSidebarBtn');
    const collapseSidebarIcon = document.getElementById('collapseSidebarIcon');

    function toggleSidebarCollapse() {
        if (!chatSidebar) return;
        const isCollapsed = chatSidebar.classList.toggle('sidebar-collapsed');
        document.body.classList.toggle('sidebar-is-collapsed', isCollapsed);
        try {
            localStorage.setItem('weisz_chat_sidebar_collapsed', isCollapsed ? '1' : '0');
        } catch (e) {}
        updateSidebarCollapseUI(isCollapsed);
    }

    function updateSidebarCollapseUI(isCollapsed) {
        if (!chatSidebar) return;
        if (isCollapsed) {
            chatSidebar.classList.remove('w-72', 'lg:w-80');
            chatSidebar.classList.add('w-[72px]');
            if (collapseSidebarBtn) collapseSidebarBtn.title = 'Perluas Sidebar';
        } else {
            chatSidebar.classList.remove('w-[72px]');
            chatSidebar.classList.add('w-72', 'lg:w-80');
            if (collapseSidebarBtn) collapseSidebarBtn.title = 'Kecilkan Sidebar';
        }
    }

    window.toggleSidebarCollapse = toggleSidebarCollapse;

    if (collapseSidebarBtn) {
        collapseSidebarBtn.addEventListener('click', toggleSidebarCollapse);
    }

    // Restore saved collapse state
    try {
        const savedCollapsed = localStorage.getItem('weisz_chat_sidebar_collapsed') === '1';
        if (savedCollapsed && window.innerWidth >= 768 && chatSidebar) {
            chatSidebar.classList.add('sidebar-collapsed');
            document.body.classList.add('sidebar-is-collapsed');
            updateSidebarCollapseUI(true);
        }
    } catch (e) {}

    function openSidebar() {
        if (chatSidebar && sidebarBackdrop) {
            chatSidebar.classList.remove('-translate-x-full');
            sidebarBackdrop.classList.remove('hidden');
        }
    }

    function closeSidebar() {
        if (chatSidebar && sidebarBackdrop) {
            chatSidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
        }
    }

    function handleOpenSidebar() {
        if (window.innerWidth < 768) {
            openSidebar();
        } else {
            if (chatSidebar && chatSidebar.classList.contains('sidebar-collapsed')) {
                toggleSidebarCollapse();
            } else {
                openSidebar();
            }
        }
    }

    if (openSidebarBtn) openSidebarBtn.addEventListener('click', handleOpenSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
    if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);
    window.handleOpenSidebar = handleOpenSidebar;
    window.openSidebar = openSidebar;
    window.closeSidebar = closeSidebar;

    // Account Popover Toggle (Profile & Setting)
    const accountTriggerBtn = document.getElementById('accountTriggerBtn');
    const accountPopover = document.getElementById('accountPopover');

    if (accountTriggerBtn && accountPopover) {
        accountTriggerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            accountPopover.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!accountPopover.contains(e.target) && e.target !== accountTriggerBtn) {
                accountPopover.classList.add('hidden');
            }
        });
    }

    // In-Chat Mood Check-in: Global delegated click handler (Hanya bisa sekali pilih per bubble)
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.mood-checkin-btn');
        if (!btn) return;

        const wrapper = btn.closest('.mood-checkin-wrapper');
        if (!wrapper) return;

        // Jika bubble sudah dijawab atau tombol sudah nonaktif, abaikan
        if (wrapper.dataset.answered === 'true' || btn.disabled) return;

        const mood = btn.dataset.mood;
        const msgId = wrapper.dataset.msgId;

        // Langsung kunci agar user hanya bisa sekali pilih di bubble ini
        wrapper.dataset.answered = 'true';
        wrapper.querySelectorAll('.mood-checkin-btn').forEach(b => {
            b.disabled = true;
            b.style.pointerEvents = 'none';
            const ind = b.querySelector('.room-check-indicator');
            if (b === btn) {
                b.classList.remove('opacity-35', 'grayscale-[40%]', 'border-[var(--border-color)]', 'hover:border-[var(--brand-main)]', 'group', 'cursor-pointer');
                b.classList.add('border-[#252525]', 'dark:border-white', 'ring-4', 'ring-[#252525]/20', 'dark:ring-white/20', 'scale-[1.03]', 'shadow-md', 'cursor-default');
                if (ind) ind.classList.remove('opacity-0');
            } else {
                b.classList.remove('border-[#252525]', 'dark:border-white', 'ring-4', 'ring-[#252525]/20', 'dark:ring-white/20', 'scale-[1.03]', 'shadow-md', 'hover:border-[var(--brand-main)]', 'group', 'cursor-pointer');
                b.classList.add('opacity-35', 'grayscale-[40%]', 'cursor-not-allowed');
                if (ind) ind.classList.add('opacity-0');
            }
        });

        const updateMoodUrl = "{{ $session ? route('chat.update-mood', $session->id) : '' }}";
        if (!updateMoodUrl) return;

        try {
            const response = await fetch(updateMoodUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    mood: mood, 
                    message_id: msgId ? parseInt(msgId, 10) : null 
                })
            });

            const resData = await response.json();
            if (resData.success) {
                // Update sidebar mood display
                const sidebarEmoji = document.getElementById('sidebarMoodEmoji');
                const sidebarLabel = document.getElementById('sidebarMoodLabel');
                if (sidebarEmoji && resData.mood_info) sidebarEmoji.textContent = resData.mood_info.emoji;
                if (sidebarLabel && resData.mood_info) sidebarLabel.textContent = resData.mood_info.label;
            }
        } catch (e) {
            console.error('Failed to update mood', e);
        }
    });

    function renderMoodCheckinButtonsHtml() {
        return Object.entries(moodPresets).map(([key, item]) => `
            <button type="button" class="mood-checkin-btn relative aspect-square p-1 rounded-xl sm:rounded-2xl border-2 border-[var(--border-color)] hover:border-[var(--brand-main)] bg-[var(--card-secondary)] transition-all duration-200 flex items-center justify-center group cursor-pointer hover:shadow-md overflow-hidden" data-mood="${key}" title="${item.label}">
                <img src="/images/moods/${key}.jpg" alt="${item.label}" class="w-full h-full object-cover rounded-lg sm:rounded-xl group-hover:scale-105 transition-transform duration-300 pointer-events-none">
                <div class="room-check-indicator absolute top-1.5 right-1.5 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center shadow-xs opacity-0 transition-opacity pointer-events-none">
                    <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
            </button>
        `).join('');
    }

    // Proactive Idle Suggestion: 1 minute without changes or actions
    const IDLE_TIMEOUT_MS = 60000;
    const lastMsgSender = '{{ $messages->last()?->sender ?? "" }}';
    const lastMsgIsIdle = {{ (!empty($messages->last()?->metadata['idle_suggestion'])) ? 'true' : 'false' }};
    let hasSentIdleSuggestionInTurn = (lastMsgSender === 'assistant' && lastMsgIsIdle);
    let isAiGenerating = false;
    let isIdleSuggestionLoading = false;
    let idleAbortController = null;
    let idleTimer = null;
    let lastInputTime = Date.now();
    let lastInputValue = (messageInput ? messageInput.value : '');

    function startIdleTimer(delay = IDLE_TIMEOUT_MS) {
        stopIdleTimer();

        if (!isAiGenerating && !isIdleSuggestionLoading && !hasSentIdleSuggestionInTurn && !document.hidden) {
            idleTimer = setTimeout(triggerIdleSuggestion, delay);
        }
    }

    function stopIdleTimer() {
        if (idleTimer) {
            clearTimeout(idleTimer);
            idleTimer = null;
        }
    }

    // Dipanggil setiap kali user mengetik atau mengubah input (tambah/kurang huruf atau kata)
    function handleUserInputActivity() {
        const currentVal = messageInput ? messageInput.value : '';
        lastInputTime = Date.now();
        lastInputValue = currentVal;

        // Validasi: Jika AI sedang loading / mengirim pesan otomatis (idle suggestion)
        // maka walaupun sudah 1 menit sebelumnya, pesan otomatis AI BATAL!
        if (isIdleSuggestionLoading) {
            cancelInFlightIdleSuggestion();
        }

        // Reset timer 1 menit: jika tidak ada tindakan/perubahan lagi dalam 1 menit, AI bisa mengirim lagi
        stopIdleTimer();
        if (!hasSentIdleSuggestionInTurn && !document.hidden && !isAiGenerating) {
            idleTimer = setTimeout(triggerIdleSuggestion, IDLE_TIMEOUT_MS);
        }
    }

    // Batalkan pesan otomatis AI yang sedang dalam proses loading / kirim
    function cancelInFlightIdleSuggestion() {
        if (!isIdleSuggestionLoading) return;

        isIdleSuggestionLoading = false;
        isAiGenerating = false;

        if (idleAbortController) {
            idleAbortController.abort();
            idleAbortController = null;
        }

        hideTypingIndicator();

        // Hapus pesan idle di database jika sempat tersimpan sebelum selesai render
        const cancelIdleUrl = "{{ $session ? route('chat.cancel-idle-suggestion', $session->id) : '' }}";
        if (cancelIdleUrl) {
            fetch(cancelIdleUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            }).catch(() => {});
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopIdleTimer();
            if (isIdleSuggestionLoading) {
                cancelInFlightIdleSuggestion();
            }
        } else {
            // Ketika kembali ke tab, periksa apakah sudah 1 menit sejak aktivitas input terakhir
            const elapsed = Date.now() - lastInputTime;
            if (elapsed >= IDLE_TIMEOUT_MS) {
                startIdleTimer(1000);
            } else {
                startIdleTimer(IDLE_TIMEOUT_MS - elapsed);
            }
        }
    });

    async function triggerIdleSuggestion() {
        if (isAiGenerating || isIdleSuggestionLoading || hasSentIdleSuggestionInTurn || document.hidden) {
            return;
        }

        // Validasi: pastikan benar-benar sudah 1 menit penuh tanpa perubahan pada input
        const elapsedSinceInput = Date.now() - lastInputTime;
        if (elapsedSinceInput < IDLE_TIMEOUT_MS) {
            const remaining = IDLE_TIMEOUT_MS - elapsedSinceInput;
            startIdleTimer(Math.max(1000, remaining));
            return;
        }

        const idleSuggestionUrl = "{{ $session ? route('chat.idle-suggestion', $session->id) : '' }}";
        if (!idleSuggestionUrl) {
            return;
        }

        isIdleSuggestionLoading = true;
        isAiGenerating = true;
        stopIdleTimer();

        showTypingIndicator();

        const currentAbortController = new AbortController();
        idleAbortController = currentAbortController;

        try {
            const response = await fetch(idleSuggestionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                signal: currentAbortController.signal
            });

            if (!response.ok) {
                hideTypingIndicator();
                try {
                    const errData = await response.json();
                    if (errData && errData.error === 'already_suggested') {
                        hasSentIdleSuggestionInTurn = true;
                    }
                } catch (_) {}
                return;
            }

            const data = await response.json();

            // Validasi: jika saat request sedang berjalan user mulai mengetik, atau fetch dibatalkan
            if (!isIdleSuggestionLoading || currentAbortController.signal.aborted) {
                if (data && data.assistant_message && data.assistant_message.id) {
                    const cancelUrl = "{{ $session ? route('chat.cancel-idle-suggestion', $session->id) : '' }}";
                    if (cancelUrl) {
                        fetch(cancelUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ message_id: data.assistant_message.id })
                        }).catch(() => {});
                    }
                }
                hideTypingIndicator();
                return;
            }

            if (data.success && data.assistant_message) {
                hasSentIdleSuggestionInTurn = true;

                const aiBubble = document.createElement('div');
                aiBubble.className = 'max-w-[95%] sm:max-w-[85%]';

                const cleanRawMsg = sanitizeForDisplay(data.assistant_message.message || '');
                const safeMsg = cleanRawMsg.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, '<br>');

                aiBubble.innerHTML = `
                    <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-5 sm:px-6 py-3.5 sm:py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm">
                        <div>${safeMsg}</div>
                    </div>
                `;

                if (typingIndicator && typingIndicator.parentNode === chatFeedList) {
                    chatFeedList.insertBefore(aiBubble, typingIndicator);
                } else if (chatFeedList) {
                    chatFeedList.appendChild(aiBubble);
                }

                hideTypingIndicator();

                if (data.suggested_chips && Array.isArray(data.suggested_chips) && data.suggested_chips.length > 0) {
                    renderSuggestionChips(data.suggested_chips);
                }

                scrollToBottom();
            } else {
                hideTypingIndicator();
                if (data && data.error === 'already_suggested') {
                    hasSentIdleSuggestionInTurn = true;
                }
            }
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('Idle suggestion error:', err);
            }
            hideTypingIndicator();
        } finally {
            if (idleAbortController === currentAbortController) {
                isIdleSuggestionLoading = false;
                isAiGenerating = false;
                idleAbortController = null;
            }
        }
    }

    // Clean up empty session if user exits before typing any message
    window.addEventListener('pagehide', function() {
        const leaveUrl = "{{ $session ? route('chat.leave', $session->id) : '' }}";
        if (!userHasSentMessage && navigator.sendBeacon && leaveUrl) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            navigator.sendBeacon(leaveUrl, formData);
        }
    });

    function scrollToBottom() {
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    scrollToBottom();

    // Suggestion chips handler
    function autoResizeTextarea() {
        if (!messageInput) return;
        messageInput.style.height = 'auto';
        const minHeight = 44;
        const maxHeight = 112;
        const scrollH = messageInput.scrollHeight;
        const newH = Math.min(Math.max(scrollH, minHeight), maxHeight);
        messageInput.style.height = newH + 'px';
        messageInput.style.overflowY = scrollH > maxHeight ? 'auto' : 'hidden';

        const composerPill = document.getElementById('composerInputPill') || messageInput.parentElement;
        if (composerPill) {
            if (newH > minHeight + 4) {
                composerPill.classList.remove('items-center');
                composerPill.classList.add('items-end');
            } else {
                composerPill.classList.remove('items-end');
                composerPill.classList.add('items-center');
            }
        }
    }

    function updateComposerButtons() {
        if (!messageInput || !voiceNoteBtn || !sendBtn) return;
        const hasText = messageInput.value.trim().length > 0;
        if (hasText) {
            voiceNoteBtn.classList.add('hidden');
            sendBtn.classList.remove('hidden');
            if (pauseResumeVoiceNoteBtn) pauseResumeVoiceNoteBtn.classList.add('hidden');
        } else {
            if (!isRecording) {
                voiceNoteBtn.classList.remove('hidden');
                sendBtn.classList.add('hidden');
                if (pauseResumeVoiceNoteBtn) pauseResumeVoiceNoteBtn.classList.add('hidden');
            }
        }
    }

    if (messageInput) {
        messageInput.addEventListener('input', () => {
            autoResizeTextarea();
            handleUserInputActivity();
            updateComposerButtons();
        });

        messageInput.addEventListener('keyup', updateComposerButtons);
        messageInput.addEventListener('change', updateComposerButtons);

        // Enter sends message, Shift+Enter creates new line
        messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (messageInput.value.trim() !== '') {
                    chatForm.dispatchEvent(new Event('submit'));
                }
            } else {
                handleUserInputActivity();
            }
        });

        messageInput.addEventListener('compositionupdate', () => {
            handleUserInputActivity();
            updateComposerButtons();
        });

        messageInput.addEventListener('compositionend', () => {
            handleUserInputActivity();
            updateComposerButtons();
        });

        messageInput.addEventListener('paste', () => {
            setTimeout(() => {
                autoResizeTextarea();
                handleUserInputActivity();
                updateComposerButtons();
            }, 0);
        });

        autoResizeTextarea();
        updateComposerButtons();
    }

    const suggestionChipsContainer = document.getElementById('suggestionChipsContainer');

    // Horizontal wheel scrolling with mouse wheel (without scrollbars)
    if (suggestionChipsContainer) {
        suggestionChipsContainer.addEventListener('wheel', (e) => {
            if (e.deltaY !== 0) {
                e.preventDefault();
                suggestionChipsContainer.scrollLeft += e.deltaY;
            }
        }, { passive: false });

        // Touch & Mouse drag-to-scroll ("scrolling di usap")
        let isDown = false;
        let startX = 0;
        let scrollLeftPos = 0;
        let hasDragged = false;

        suggestionChipsContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            hasDragged = false;
            startX = e.pageX - suggestionChipsContainer.offsetLeft;
            scrollLeftPos = suggestionChipsContainer.scrollLeft;
        });

        window.addEventListener('mouseup', () => {
            if (isDown) {
                isDown = false;
                setTimeout(() => { hasDragged = false; }, 50);
            }
        });

        suggestionChipsContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - suggestionChipsContainer.offsetLeft;
            const walk = (x - startX) * 1.5;
            if (Math.abs(walk) > 5) {
                hasDragged = true;
            }
            suggestionChipsContainer.scrollLeft = scrollLeftPos - walk;
        });

        // Prevent accidental chip click if user was swiping/dragging
        suggestionChipsContainer.addEventListener('click', (e) => {
            if (hasDragged) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
    }

    function renderSuggestionChips(chips) {
        if (!suggestionChipsContainer || !Array.isArray(chips) || chips.length === 0) return;

        suggestionChipsContainer.innerHTML = '';

        // Stuck help button
        const stuckBtn = document.createElement('button');
        stuckBtn.type = 'button';
        stuckBtn.id = 'stuckHelpBtn';
        stuckBtn.className = 'weisz-chip text-xs sm:text-[13.5px] py-1 px-3 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 shrink-0 font-medium';
        stuckBtn.innerHTML = 'Aku bingung mau cerita apa';
        stuckBtn.addEventListener('click', () => {
            messageInput.value = "Aku lagi bingung banget mau cerita apa...";
            autoResizeTextarea();
            chatForm.dispatchEvent(new Event('submit'));
        });
        suggestionChipsContainer.appendChild(stuckBtn);

        // Dynamically append new chips
        chips.forEach(chipText => {
            const cleanText = sanitizeForDisplay(chipText);
            if (!cleanText) return;
            const chipBtn = document.createElement('button');
            chipBtn.type = 'button';
            chipBtn.className = 'suggestion-chip weisz-chip text-xs sm:text-[13.5px] py-1 px-3 shrink-0 whitespace-nowrap transition-all duration-150 hover:border-[var(--brand-main)] font-medium';
            chipBtn.dataset.text = cleanText;
            chipBtn.textContent = cleanText;
            chipBtn.addEventListener('click', () => {
                messageInput.value = cleanText;
                autoResizeTextarea();
                messageInput.focus();
                handleUserInputActivity();
                updateComposerButtons();
            });
            suggestionChipsContainer.appendChild(chipBtn);
        });

        suggestionChipsContainer.scrollTo({ left: 0, behavior: 'smooth' });
    }

    document.querySelectorAll('.suggestion-chip').forEach(btn => {
        btn.addEventListener('click', () => {
            messageInput.value = btn.dataset.text;
            autoResizeTextarea();
            messageInput.focus();
            handleUserInputActivity();
            updateComposerButtons();
        });
    });

    if (stuckHelpBtn) {
        stuckHelpBtn.addEventListener('click', () => {
            messageInput.value = "Aku lagi bingung banget mau cerita apa...";
            autoResizeTextarea();
            chatForm.dispatchEvent(new Event('submit'));
        });
    }

    function sanitizeForDisplay(text) {
        if (!text) return '';
        return text
            .replace(/\*(?:tersenyum|senyum|mengangguk|angguk|menghela napas|menghela nafas|tarik napas|peluk|memeluk|menatap|terdiam|smiles|nods|sighs)[^*]*\*/gi, '')
            .replace(/\((?:tersenyum|senyum|mengangguk|angguk|menghela napas|menghela nafas|tarik napas|peluk|memeluk|menatap|terdiam|smiles|nods|sighs)[^)]*\)/gi, '')
            .replace(/\*\*(.*?)\*\*/g, '$1')
            .replace(/__(.*?)__/g, '$1')
            .replace(/(^|[^\w])\*(.*?)\*([^\w]|$)/g, '$1$2$3')
            .replace(/(^|[^\w])_(.*?)_([^\w]|$)/g, '$1$2$3')
            .replace(/\*/g, '')
            .replace(/([,.;!?])\s*[—–―-]+\s*/g, '$1 ')
            .replace(/\s*[—–―]+\s*/g, ', ')
            .replace(/\s*--+\s*/g, ', ')
            .replace(/^#{1,6}\s+/gm, '')
            .replace(/^[ \t]*[•▪▫◦\-\+]\s+/gm, '')
            .replace(/[`~]/g, '')
            .trim();
    }

    // ==========================================
    // VOICE NOTE RECORDING & PLAYBACK LOGIC
    // ==========================================
    let mediaStream = null;
    let mediaRecorder = null;
    let recordedChunks = [];
    let recordStartTime = null;
    let accumulatedTime = 0;
    let recordTimerInterval = null;
    let recognition = null;
    let speechTranscript = '';
    let speechInterim = '';
    let selectedMimeType = '';
    let audioContext = null;
    let analyserNode = null;
    let animFrameId = null;

    let activeAudio = null;
    let activePlayBtn = null;

    function formatDuration(seconds) {
        const sec = Math.max(0, Math.floor(seconds || 0));
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return `${m}:${s < 10 ? '0' : ''}${s}`;
    }

    function startWaveformVisualization() {
        if (!analyserNode) return;
        const bars = voiceNoteWaveform ? voiceNoteWaveform.querySelectorAll('.vn-wave-bar') : [];
        if (!bars || bars.length === 0) return;

        const timeData = new Uint8Array(analyserNode.fftSize);
        const freqData = new Uint8Array(analyserNode.frequencyBinCount);

        function updateWave() {
            if (!isRecording || !analyserNode) return;
            if (isPaused) {
                bars.forEach(bar => {
                    bar.style.height = '3px';
                    bar.classList.add('opacity-40');
                });
                animFrameId = requestAnimationFrame(updateWave);
                return;
            }

            analyserNode.getByteTimeDomainData(timeData);
            analyserNode.getByteFrequencyData(freqData);

            let totalDeviation = 0;
            for (let i = 0; i < timeData.length; i++) {
                totalDeviation += Math.abs(timeData[i] - 128);
            }
            const avgDev = totalDeviation / (timeData.length || 1);

            bars.forEach((bar, idx) => {
                bar.classList.remove('opacity-40');
                const binIdx = Math.min(freqData.length - 1, (idx + 1) * 2);
                const freqVal = freqData[binIdx] || 0;
                const voiceEnergy = Math.max(avgDev * 2.8, freqVal * 1.2);

                let barHeight;
                if (voiceEnergy > 3) {
                    const jitter = (Math.sin(Date.now() * 0.035 + idx * 2.1) * 3) + ((Math.random() - 0.5) * 2.5);
                    barHeight = Math.max(4, Math.min(18, Math.round((voiceEnergy / 100) * 11) + 4 + jitter));
                } else {
                    const idleHeight = [3, 4, 5, 4, 3][idx % 5];
                    barHeight = idleHeight;
                }

                bar.style.height = `${barHeight}px`;
            });

            animFrameId = requestAnimationFrame(updateWave);
        }

        animFrameId = requestAnimationFrame(updateWave);
    }

    function stopWaveformVisualization() {
        if (animFrameId) {
            cancelAnimationFrame(animFrameId);
            animFrameId = null;
        }
        if (audioContext && audioContext.state !== 'closed') {
            try { audioContext.close(); } catch(e) {}
            audioContext = null;
            analyserNode = null;
        }
        const bars = voiceNoteWaveform ? voiceNoteWaveform.querySelectorAll('.vn-wave-bar') : [];
        bars.forEach((bar, idx) => {
            bar.style.height = '';
            bar.classList.remove('opacity-40');
        });
    }

    function startSpeechRecognition(appendMode = false) {
        if (!appendMode) {
            speechTranscript = '';
        }
        speechInterim = '';
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) return null;

        try {
            const r = new SpeechRecognition();
            r.continuous = true;
            r.interimResults = true;
            r.lang = 'id-ID';

            r.onresult = (event) => {
                let interim = '';
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        speechTranscript += event.results[i][0].transcript + ' ';
                    } else {
                        interim += event.results[i][0].transcript;
                    }
                }
                speechInterim = interim;
                const fullText = (speechTranscript + ' ' + speechInterim).trim();
                if (voiceNoteLiveText && fullText) {
                    voiceNoteLiveText.textContent = fullText;
                    voiceNoteLiveText.title = fullText;
                }
            };

            r.onerror = (event) => {
                console.warn('Speech recognition status:', event.error);
            };

            r.onend = () => {
                // Restart if still active and not paused to support long recordings
                if (isRecording && !isPaused) {
                    try { r.start(); } catch(e) {}
                }
            };

            r.start();
            return r;
        } catch (err) {
            console.warn('Speech recognition init error:', err);
            return null;
        }
    }

    function setPauseButtonUi(paused) {
        if (pauseIcon) pauseIcon.classList.toggle('hidden', paused);
        if (resumeIcon) resumeIcon.classList.toggle('hidden', !paused);
        if (pauseResumeVoiceNoteBtn) pauseResumeVoiceNoteBtn.title = paused ? 'Lanjutkan Rekaman' : 'Jeda Rekaman';

        if (voiceNotePing) voiceNotePing.classList.toggle('hidden', paused);
        if (voiceNoteDot) {
            voiceNoteDot.className = paused
                ? 'w-2 h-2 rounded-full bg-neutral-400 dark:bg-neutral-500 relative'
                : 'w-2 h-2 rounded-full bg-neutral-900 dark:bg-white relative';
        }
        if (voiceNoteTimer) {
            voiceNoteTimer.className = paused
                ? 'font-mono text-xs sm:text-sm font-bold text-[var(--txt-secondary)] shrink-0'
                : 'font-mono text-xs sm:text-sm font-bold text-[var(--txt-primary)] shrink-0';
        }
    }

    function togglePauseVoiceRecording() {
        if (!isRecording || !mediaRecorder) return;

        if (!isPaused) {
            // PAUSE
            try {
                if (mediaRecorder.state === 'recording') {
                    mediaRecorder.pause();
                }
            } catch(e) {
                console.warn('MediaRecorder pause warning:', e);
            }

            isPaused = true;
            accumulatedTime += Math.floor((Date.now() - recordStartTime) / 1000);

            if (recognition) {
                try { recognition.stop(); } catch(e) {}
                recognition = null;
            }

            setPauseButtonUi(true);

            if (voiceNoteLiveText) {
                const currentTxt = (speechTranscript + ' ' + speechInterim).trim();
                voiceNoteLiveText.textContent = currentTxt ? `Dijeda: "${currentTxt}"` : 'Dijeda...';
            }
        } else {
            // RESUME
            try {
                if (mediaRecorder.state === 'paused') {
                    mediaRecorder.resume();
                }
            } catch(e) {
                console.warn('MediaRecorder resume warning:', e);
            }

            isPaused = false;
            recordStartTime = Date.now();

            recognition = startSpeechRecognition(true);

            setPauseButtonUi(false);

            if (voiceNoteLiveText) {
                const currentTxt = (speechTranscript + ' ' + speechInterim).trim();
                voiceNoteLiveText.textContent = currentTxt || 'Merekam...';
            }

            startWaveformVisualization();
        }
    }

    async function startVoiceRecording() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || !window.MediaRecorder) {
            if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                alert('Akses mikrofon memerlukan konteks aman (HTTPS). Karena diakses melalui IP jaringan (' + location.hostname + '), silakan gunakan HTTPS atau aktifkan "Insecure origins treated as secure" pada browser.');
            } else {
                alert('Browser kamu belum mendukung rekaman audio langsung. Silakan gunakan Google Chrome, Microsoft Edge, atau browser modern lainnya.');
            }
            return;
        }

        try {
            recordedChunks = [];
            accumulatedTime = 0;
            isPaused = false;

            mediaStream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true
                }
            });

            mediaStream.getAudioTracks().forEach(t => { t.enabled = true; });

            // Connect AudioContext for volume visualizer
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) {
                    audioContext = new AudioCtx();
                    if (audioContext.state === 'suspended') {
                        await audioContext.resume();
                    }
                    const source = audioContext.createMediaStreamSource(mediaStream);
                    analyserNode = audioContext.createAnalyser();
                    analyserNode.fftSize = 64;
                    analyserNode.smoothingTimeConstant = 0.5;
                    source.connect(analyserNode);
                    startWaveformVisualization();
                }
            } catch (err) {
                console.warn('AudioContext visualization failed:', err);
            }

            const mimeTypes = [
                'audio/webm;codecs=opus',
                'audio/webm',
                'audio/ogg;codecs=opus',
                'audio/ogg',
                'audio/mp4',
                'audio/aac'
            ];
            selectedMimeType = '';
            for (const mime of mimeTypes) {
                if (MediaRecorder.isTypeSupported(mime)) {
                    selectedMimeType = mime;
                    break;
                }
            }

            const options = selectedMimeType ? { mimeType: selectedMimeType } : {};
            mediaRecorder = new MediaRecorder(mediaStream, options);

            mediaRecorder.ondataavailable = (e) => {
                if (e.data && e.data.size > 0) {
                    recordedChunks.push(e.data);
                }
            };

            mediaRecorder.start(250);
            recognition = startSpeechRecognition(false);

            isRecording = true;
            recordStartTime = Date.now();
            if (voiceNoteTimer) {
                voiceNoteTimer.textContent = '0:00';
                voiceNoteTimer.className = 'font-mono text-xs sm:text-sm font-bold text-[var(--txt-primary)] shrink-0';
            }
            if (voiceNoteLiveText) {
                voiceNoteLiveText.textContent = 'Mendengarkan...';
                voiceNoteLiveText.title = 'Mendengarkan...';
            }

            setPauseButtonUi(false);

            recordTimerInterval = setInterval(() => {
                if (!isPaused) {
                    const currentElapsed = accumulatedTime + Math.floor((Date.now() - recordStartTime) / 1000);
                    if (voiceNoteTimer) voiceNoteTimer.textContent = formatDuration(currentElapsed);
                }
            }, 500);

            // UI state transition
            if (messageInput) messageInput.classList.add('hidden');
            if (voiceNoteRecordingBar) voiceNoteRecordingBar.classList.remove('hidden');
            if (pauseResumeVoiceNoteBtn) pauseResumeVoiceNoteBtn.classList.remove('hidden');

            voiceNoteBtn.title = 'Kirim Pesan Suara';
            voiceNoteBtn.innerHTML = `
                <svg class="w-6 h-6 sm:w-5.5 sm:h-5.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            `;

        } catch (err) {
            console.error('Microphone access error:', err);
            alert('Akses mikrofon tidak diizinkan. Silakan aktifkan izin mikrofon di browser kamu untuk menggunakan Voice Note.');
            stopAndResetVoiceRecording();
        }
    }

    function stopAndResetVoiceRecording() {
        isRecording = false;
        isPaused = false;
        clearInterval(recordTimerInterval);
        recordTimerInterval = null;

        stopWaveformVisualization();

        if (mediaStream) {
            mediaStream.getTracks().forEach(t => t.stop());
            mediaStream = null;
        }

        if (recognition) {
            try { recognition.stop(); } catch(e) {}
            recognition = null;
        }

        setPauseButtonUi(false);

        if (messageInput) {
            messageInput.classList.remove('hidden');
        }
        if (voiceNoteRecordingBar) {
            voiceNoteRecordingBar.classList.add('hidden');
        }
        if (pauseResumeVoiceNoteBtn) {
            pauseResumeVoiceNoteBtn.classList.add('hidden');
        }

        if (voiceNoteBtn) {
            voiceNoteBtn.title = 'Kirim Voice Note';
            voiceNoteBtn.innerHTML = `
                <svg class="w-6 h-6 sm:w-5.5 sm:h-5.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z" />
                </svg>
            `;
        }

        updateComposerButtons();
    }

    function cancelVoiceRecording() {
        if (!isRecording) return;
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.onstop = null;
            mediaRecorder.stop();
        }
        recordedChunks = [];
        stopAndResetVoiceRecording();
    }

    function stopAndSendVoiceRecording() {
        if (!isRecording || !mediaRecorder) return;

        const totalElapsedSeconds = Math.max(1, accumulatedTime + (isPaused ? 0 : Math.floor((Date.now() - recordStartTime) / 1000)));

        clearInterval(recordTimerInterval);
        recordTimerInterval = null;

        if (recognition) {
            try { recognition.stop(); } catch(e) {}
            recognition = null;
        }

        stopWaveformVisualization();

        mediaRecorder.onstop = async () => {
            const mimeType = mediaRecorder.mimeType || selectedMimeType || 'audio/webm';
            const audioBlob = new Blob(recordedChunks, { type: mimeType });
            const ext = mimeType.includes('ogg') ? 'ogg' : (mimeType.includes('mp4') || mimeType.includes('aac') ? 'mp4' : 'webm');
            const audioFile = new File([audioBlob], `voice_note_${Date.now()}.${ext}`, { type: mimeType });

            const localBlobUrl = URL.createObjectURL(audioBlob);
            const finalTranscriptText = (speechTranscript + ' ' + speechInterim).trim() || 'Pesan suara';

            stopAndResetVoiceRecording();

            await sendVoiceNoteMessage(audioFile, localBlobUrl, finalTranscriptText, totalElapsedSeconds);
        };

        if (mediaRecorder.state === 'recording') {
            try { mediaRecorder.requestData(); } catch(e) {}
        }

        if (mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
    }

    if (voiceNoteBtn) {
        voiceNoteBtn.addEventListener('click', () => {
            if (!isRecording) {
                startVoiceRecording();
            } else {
                stopAndSendVoiceRecording();
            }
        });
    }

    if (pauseResumeVoiceNoteBtn) {
        pauseResumeVoiceNoteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            togglePauseVoiceRecording();
        });
    }

    if (cancelVoiceNoteBtn) {
        cancelVoiceNoteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            cancelVoiceRecording();
        });
    }

    function createUserVoiceNoteBubbleHtml(audioUrl, duration, transcriptText) {
        const durSec = Math.max(1, Math.round(duration || 0));
        const formattedDuration = formatDuration(durSec);
        const escapedTranscript = transcriptText
            ? transcriptText.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;").replace(/\n/g, '<br>')
            : '';
        const safeTranscript = escapedTranscript
            ? `
                <div class="vn-transcript-section pt-1.5 border-t border-white/15">
                    <button type="button" class="vn-transcript-toggle w-full flex items-center justify-between gap-1 text-[11px] text-white/70 hover:text-white transition-colors cursor-pointer select-none py-0.5" title="Buka Transkrip">
                        <span class="font-medium tracking-wide">Transkrip</span>
                        <svg class="vn-toggle-arrow w-3.5 h-3.5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div class="vn-transcript-collapse grid grid-rows-[0fr] transition-all duration-300 ease-in-out">
                        <div class="overflow-hidden">
                            <div class="pt-1.5 text-xs sm:text-[13px] text-white/90 leading-relaxed italic border-t border-white/10 mt-1">
                                "${escapedTranscript}"
                            </div>
                        </div>
                    </div>
                </div>
            `
            : '';

        return `
            <div data-role="voice-player" data-duration="${durSec}" class="max-w-[85%] sm:max-w-[75%] rounded-2xl rounded-tr-sm bg-[var(--brand-btn)] text-white p-3 sm:p-4 shadow-sm space-y-2 select-text">
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <button type="button" class="vn-play-btn w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-[#252525] dark:bg-white dark:text-[#252525] flex items-center justify-center shrink-0 hover:scale-105 active:scale-95 transition shadow-xs cursor-pointer" data-audio-src="${audioUrl}" data-duration="${durSec}" title="Putar Pesan Suara">
                        <svg class="vn-icon-play w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <svg class="vn-icon-pause w-4 h-4 hidden" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                        </svg>
                    </button>
                    <div class="flex-1 min-w-0">
                        <div class="vn-progress-track relative w-full h-1.5 sm:h-2 bg-white/25 rounded-full overflow-hidden cursor-pointer" title="Lompat ke durasi">
                            <div class="vn-progress-bar h-full bg-white rounded-full transition-all duration-75" style="width: 0%;"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10.5px] sm:text-[11.5px] text-white/70 mt-1 font-mono">
                            <span class="vn-time-current">0:00</span>
                            <span class="vn-time-total">${formattedDuration}</span>
                        </div>
                    </div>
                </div>
                ${safeTranscript}
            </div>
        `;
    }

    function setupVoiceNotePlayers(scope = document) {
        const playButtons = scope.querySelectorAll ? scope.querySelectorAll('.vn-play-btn') : [];
        playButtons.forEach(btn => {
            if (btn.dataset.playerInitialized === 'true') return;
            btn.dataset.playerInitialized = 'true';

            let audioSrc = btn.dataset.audioSrc;
            if (!audioSrc) return;

            // Normalize full URL to relative path to ensure cross-origin/LAN compatibility
            if (audioSrc.startsWith('http://') || audioSrc.startsWith('https://')) {
                try {
                    const u = new URL(audioSrc);
                    audioSrc = u.pathname + u.search;
                } catch(e) {}
            }

            const card = btn.closest('[data-role="voice-player"]') || btn.closest('.space-y-2') || btn.parentElement;
            const progressBar = card.querySelector('.vn-progress-bar');
            const progressTrack = card.querySelector('.vn-progress-track');
            const timeCurrent = card.querySelector('.vn-time-current');
            const timeTotal = card.querySelector('.vn-time-total');
            const iconPlay = btn.querySelector('.vn-icon-play');
            const iconPause = btn.querySelector('.vn-icon-pause');

            const knownDuration = parseFloat(btn.dataset.duration || card.dataset.duration || 0);

            const audio = new Audio();
            audio.preload = 'metadata';
            audio.src = audioSrc;

            function getEffectiveDuration() {
                if (audio.duration && isFinite(audio.duration) && audio.duration > 0) {
                    return audio.duration;
                }
                return knownDuration > 0 ? knownDuration : 0;
            }

            if (knownDuration > 0 && timeTotal) {
                timeTotal.textContent = formatDuration(knownDuration);
            }

            audio.addEventListener('loadedmetadata', () => {
                const dur = getEffectiveDuration();
                if (dur > 0 && timeTotal) {
                    timeTotal.textContent = formatDuration(dur);
                }
            });

            audio.addEventListener('timeupdate', () => {
                const dur = getEffectiveDuration();
                const cur = audio.currentTime || 0;
                if (dur > 0) {
                    const percent = Math.min(100, Math.max(0, (cur / dur) * 100));
                    if (progressBar) progressBar.style.width = percent + '%';
                }
                if (timeCurrent) {
                    timeCurrent.textContent = formatDuration(cur);
                }
            });

            audio.addEventListener('ended', () => {
                audio.currentTime = 0;
                if (progressBar) progressBar.style.width = '0%';
                if (timeCurrent) timeCurrent.textContent = '0:00';
                if (iconPlay) iconPlay.classList.remove('hidden');
                if (iconPause) iconPause.classList.add('hidden');
                if (activeAudio === audio) {
                    activeAudio = null;
                    activePlayBtn = null;
                }
            });

            audio.addEventListener('pause', () => {
                if (iconPlay) iconPlay.classList.remove('hidden');
                if (iconPause) iconPause.classList.add('hidden');
            });

            audio.addEventListener('play', () => {
                if (iconPlay) iconPlay.classList.add('hidden');
                if (iconPause) iconPause.classList.remove('hidden');
            });

            audio.addEventListener('error', (err) => {
                console.warn('Audio playback error on source:', audioSrc, err);
                if (iconPlay) iconPlay.classList.remove('hidden');
                if (iconPause) iconPause.classList.add('hidden');
                if (activeAudio === audio) {
                    activeAudio = null;
                    activePlayBtn = null;
                }
            });

            if (progressTrack) {
                progressTrack.addEventListener('click', (e) => {
                    const rect = progressTrack.getBoundingClientRect();
                    const clickPos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                    const dur = getEffectiveDuration();
                    if (dur > 0) {
                        audio.currentTime = clickPos * dur;
                        if (progressBar) progressBar.style.width = (clickPos * 100) + '%';
                        if (timeCurrent) timeCurrent.textContent = formatDuration(audio.currentTime);
                    }
                });
            }

            btn.addEventListener('click', () => {
                // Pause other audio if playing
                if (activeAudio && activeAudio !== audio) {
                    activeAudio.pause();
                    if (activePlayBtn) {
                        const prevPlay = activePlayBtn.querySelector('.vn-icon-play');
                        const prevPause = activePlayBtn.querySelector('.vn-icon-pause');
                        if (prevPlay) prevPlay.classList.remove('hidden');
                        if (prevPause) prevPause.classList.add('hidden');
                    }
                }

                if (audio.paused) {
                    const dur = getEffectiveDuration();
                    if (dur > 0 && audio.currentTime >= (dur - 0.3)) {
                        audio.currentTime = 0;
                    }

                    audio.play().then(() => {
                        activeAudio = audio;
                        activePlayBtn = btn;
                        if (iconPlay) iconPlay.classList.add('hidden');
                        if (iconPause) iconPause.classList.remove('hidden');
                    }).catch(err => {
                        console.error('Audio play error:', err);
                        if (iconPlay) iconPlay.classList.remove('hidden');
                        if (iconPause) iconPause.classList.add('hidden');
                    });
                } else {
                    audio.pause();
                    if (iconPlay) iconPlay.classList.remove('hidden');
                    if (iconPause) iconPause.classList.add('hidden');
                    activeAudio = null;
                    activePlayBtn = null;
                }
            });
        });
    }

    async function sendVoiceNoteMessage(audioFile, localBlobUrl, transcriptText, duration) {
        userHasSentMessage = true;
        stopIdleTimer();
        if (isIdleSuggestionLoading) {
            cancelInFlightIdleSuggestion();
        }
        isAiGenerating = true;
        hasSentIdleSuggestionInTurn = false;

        // Render user Voice Note bubble immediately
        const userBubble = document.createElement('div');
        userBubble.className = 'flex justify-end w-full';
        userBubble.innerHTML = createUserVoiceNoteBubbleHtml(localBlobUrl, duration, transcriptText);
        if (typingIndicator && typingIndicator.parentNode === chatFeedList) {
            chatFeedList.insertBefore(userBubble, typingIndicator);
        } else if (chatFeedList) {
            chatFeedList.appendChild(userBubble);
        }
        setupVoiceNotePlayers(userBubble);

        showTypingIndicator();
        if (sendBtn) sendBtn.disabled = true;
        if (voiceNoteBtn) voiceNoteBtn.disabled = true;

        const sessionId = chatForm.dataset.sessionId || '{{ $session?->id ?? "" }}';
        const formAction = chatForm.getAttribute('action') || '';
        let sendUrl = (formAction && formAction !== '#') ? formAction : '';
        if (!sendUrl && sessionId) {
            sendUrl = `/chat/${sessionId}/send`;
        } else if (!sendUrl) {
            sendUrl = '/chat';
        }

        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        let data = null;

        try {
            const formData = new FormData();
            formData.append('audio', audioFile);
            formData.append('message', transcriptText);
            formData.append('duration', duration);
            formData.append('is_voice_note', '1');

            let response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (response.status === 419) {
                csrfToken = await getFreshCsrfToken();
                response = await fetch(sendUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
            }

            if (response.ok) {
                data = await response.json();
            } else {
                try {
                    data = await response.json();
                } catch (_) {
                    data = { success: false, message: 'Gagal memproses rekaman suara di server.' };
                }
            }
        } catch (err) {
            console.error('Error sending voice note:', err);
            data = { success: false, message: 'Koneksi jaringan terputus saat mengirim suara.' };
        } finally {
            if (sendBtn) sendBtn.disabled = false;
            if (voiceNoteBtn) voiceNoteBtn.disabled = false;
            isAiGenerating = false;
            lastInputTime = Date.now();
            lastInputValue = messageInput ? messageInput.value : '';
            startIdleTimer();
        }

        if (data && data.success && data.assistant_message) {
            handleAiAssistantResponse(data);
            hideTypingIndicator();
        } else {
            hideTypingIndicator();
            const errMsg = (data && data.message) ? data.message : 'Koneksi server sedang sangat padat sehingga respon suara belum dapat diproses. Boleh coba kirim ulang ya.';
            renderErrorBubble(errMsg, transcriptText || 'Pesan suara');
        }
    }

    // Helper to get or refresh CSRF token
    async function getFreshCsrfToken() {
        try {
            const res = await fetch('/csrf-token', { headers: { 'Accept': 'application/json' } });
            if (res.ok) {
                const data = await res.json();
                if (data && data.csrf_token) {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) meta.setAttribute('content', data.csrf_token);
                    return data.csrf_token;
                }
            }
        } catch (e) {
            console.error('CSRF token refresh error:', e);
        }
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    // Helper to render friendly error/retry bubble
    function renderErrorBubble(message, originalText = '') {
        const errorBubble = document.createElement('div');
        errorBubble.className = 'max-w-[95%] sm:max-w-[85%]';
        errorBubble.innerHTML = `
            <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-5 sm:px-6 py-3.5 sm:py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm space-y-2.5">
                <p class="text-[var(--txt-secondary)]">${escapeHtml(message)}</p>
                ${originalText ? `
                    <div class="pt-1">
                        <button type="button" class="retry-send-btn weisz-btn-secondary text-xs sm:text-sm py-1.5 px-3.5 inline-flex items-center gap-1.5 cursor-pointer font-medium" data-retry-text="${escapeHtml(originalText)}">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Kirim Ulang
                        </button>
                    </div>
                ` : ''}
            </div>
        `;

        if (typingIndicator && typingIndicator.parentNode === chatFeedList) {
            chatFeedList.insertBefore(errorBubble, typingIndicator);
        } else if (chatFeedList) {
            chatFeedList.appendChild(errorBubble);
        }

        const retryBtn = errorBubble.querySelector('.retry-send-btn');
        if (retryBtn) {
            retryBtn.addEventListener('click', () => {
                messageInput.value = retryBtn.dataset.retryText || '';
                autoResizeTextarea();
                errorBubble.remove();
                chatForm.dispatchEvent(new Event('submit'));
            });
        }
        scrollToBottom();
    }

    function handleAiAssistantResponse(data) {
        if (!data || !data.success || !data.assistant_message) return;

        try {
            const aiBubble = document.createElement('div');
            aiBubble.className = 'max-w-[95%] sm:max-w-[85%]';

            const cleanRawMsg = sanitizeForDisplay(data.assistant_message.message || '');
            const safeMsg = cleanRawMsg.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, '<br>');

            aiBubble.innerHTML = `
                <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-5 sm:px-6 py-3.5 sm:py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm">
                    ${safeMsg}
                </div>
            `;

            if (typingIndicator && typingIndicator.parentNode === chatFeedList) {
                chatFeedList.insertBefore(aiBubble, typingIndicator);
            } else if (chatFeedList) {
                chatFeedList.appendChild(aiBubble);
            }

            // Separate Bubble Chat: if in-chat mood checkin is triggered (Style Bubble Biasa)
            if (data.mood_checkin) {
                try {
                    document.querySelectorAll('.mood-checkin-wrapper').forEach(oldWrap => {
                        oldWrap.dataset.answered = 'true';
                        oldWrap.querySelectorAll('.mood-checkin-btn').forEach(b => {
                            b.disabled = true;
                            b.style.pointerEvents = 'none';
                            if (!b.classList.contains('ring-2')) {
                                b.classList.add('opacity-35', 'grayscale-[40%]', 'cursor-not-allowed');
                            }
                        });
                    });

                    const moodBubble = document.createElement('div');
                    moodBubble.className = 'max-w-[95%] sm:max-w-[85%] mood-checkin-wrapper';
                    moodBubble.dataset.msgId = (data.assistant_message && data.assistant_message.id) ? data.assistant_message.id : '';
                    moodBubble.dataset.answered = 'false';
                    moodBubble.innerHTML = `
                        <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-5 sm:px-6 py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm space-y-3">
                            <p class="text-[15px] sm:text-base leading-relaxed text-[var(--txt-secondary)]">
                                Seiring kita mengurai cerita, suasana perasaanmu bisa berkembang. Bagaimana kondisimu saat ini? Pilih salah satu untuk membantu Weisz menyesuaikan nada bicara dengan hatimu:
                            </p>
                            <div class="grid grid-cols-5 gap-2 sm:gap-3 pt-1">
                                ${renderMoodCheckinButtonsHtml()}
                            </div>
                        </div>
                    `;
                    if (typingIndicator && typingIndicator.parentNode === chatFeedList) {
                        chatFeedList.insertBefore(moodBubble, typingIndicator);
                    } else if (chatFeedList) {
                        chatFeedList.appendChild(moodBubble);
                    }
                } catch (mErr) {
                    console.error('Mood checkin render error:', mErr);
                }
            }

            // Separate Bubble Chat: Rekomendasi Tenaga Profesional (Slider Card)
            if (data.recommend_professional) {
                try {
                    const recBubble = document.createElement('div');
                    recBubble.className = 'max-w-[95%] sm:max-w-[85%]';
                    const prosToRender = (data.professionals && Array.isArray(data.professionals) && data.professionals.length > 0)
                        ? data.professionals
                        : registeredProfessionalsData;
                    recBubble.innerHTML = renderProSliderBubbleHtml(prosToRender);
                    if (typingIndicator && typingIndicator.parentNode === chatFeedList) {
                        chatFeedList.insertBefore(recBubble, typingIndicator);
                    } else if (chatFeedList) {
                        chatFeedList.appendChild(recBubble);
                    }
                    setupProSliders(recBubble);
                } catch (pErr) {
                    console.error('Professional slider render error:', pErr);
                }
            }

            // Separate Bubble Chat: if user is detected off-topic
            if (data.is_off_topic) {
                try {
                    const offTopicBubble = document.createElement('div');
                    offTopicBubble.className = 'max-w-[95%] sm:max-w-[85%]';
                    offTopicBubble.innerHTML = `
                        <div class="p-4 sm:p-5 rounded-2xl rounded-tl-sm bg-neutral-50 dark:bg-neutral-900/50 border border-[var(--border-color)] text-[15px] sm:text-base text-[var(--txt-secondary)] space-y-3 shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[var(--brand-main)] animate-pulse"></span>
                                <p class="font-bold text-[var(--brand-main)] text-base sm:text-lg">Ingin Membahas Topik Lain?</p>
                            </div>
                            <p class="text-[15px] sm:text-base leading-relaxed text-[var(--txt-primary)]">
                                Sepertinya ceritamu mulai menyentuh hal di luar topik ini. Agar refleksimu tetap terarah dan efektif, kamu bisa membuat percakapan baru dengan topik yang lebih sesuai.
                            </p>
                            <div class="pt-1 flex items-center gap-2 flex-wrap">
                                <button type="button" onclick="openNewChatExplore()" class="weisz-btn-primary text-sm sm:text-[15px] py-2 px-4 shadow-sm inline-flex items-center gap-1.5 font-semibold cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Buat Percakapan Baru
                                </button>
                                <a href="{{ route('topics.explore') }}" class="weisz-btn-secondary text-sm sm:text-[15px] py-2 px-4">
                                    Lihat Katalog Topik
                                </a>
                            </div>
                        </div>
                    `;
                    if (typingIndicator && typingIndicator.parentNode === chatFeedList) {
                        chatFeedList.insertBefore(offTopicBubble, typingIndicator);
                    } else if (chatFeedList) {
                        chatFeedList.appendChild(offTopicBubble);
                    }
                } catch (oErr) {
                    console.error('Off-topic bubble render error:', oErr);
                }
            }

            // Dynamically update suggestion chips to match the AI's latest response
            if (data.suggested_chips && Array.isArray(data.suggested_chips) && data.suggested_chips.length > 0) {
                renderSuggestionChips(data.suggested_chips);
            }

            scrollToBottom();
        } catch (renderErr) {
            console.error('Critical error in handleAiAssistantResponse:', renderErr);
        } finally {
            hideTypingIndicator();
        }
    }

    // Modern AJAX Chat Submission
    if (chatForm) {
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = messageInput.value.trim();
            if (!text) return;

            userHasSentMessage = true;

            stopIdleTimer();
            if (isIdleSuggestionLoading) {
                cancelInFlightIdleSuggestion();
            }
            isAiGenerating = true;
            hasSentIdleSuggestionInTurn = false;

            // Append user bubble immediately
            const userBubble = document.createElement('div');
            userBubble.className = 'flex justify-end w-full';
            userBubble.innerHTML = `<div class="max-w-[85%] sm:max-w-[75%] rounded-2xl rounded-tr-sm bg-[var(--brand-btn)] text-white px-5 sm:px-6 py-3.5 sm:py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm">${text.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, '<br>')}</div>`;
            
            // Insert before typing indicator safely
            if (typingIndicator && typingIndicator.parentNode === chatFeedList) {
                chatFeedList.insertBefore(userBubble, typingIndicator);
            } else if (chatFeedList) {
                chatFeedList.appendChild(userBubble);
            }

            messageInput.value = '';
            messageInput.style.height = '44px';
            messageInput.style.overflowY = 'hidden';
            const composerPill = document.getElementById('composerInputPill') || messageInput.parentElement;
            if (composerPill) {
                composerPill.classList.remove('items-end');
                composerPill.classList.add('items-center');
            }
            updateComposerButtons();

            showTypingIndicator();
            sendBtn.disabled = true;

            const sessionId = chatForm.dataset.sessionId || '{{ $session?->id ?? "" }}';
            const formAction = chatForm.getAttribute('action') || '';
            let sendUrl = (formAction && formAction !== '#') ? formAction : '';
            if (!sendUrl && sessionId) {
                sendUrl = `/chat/${sessionId}/send`;
            } else if (!sendUrl) {
                sendUrl = '/chat';
            }

            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            let data = null;

            try {
                let response = await fetch(sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: text })
                });

                // Auto-refresh CSRF token and retry once if 419 occurs
                if (response.status === 419) {
                    csrfToken = await getFreshCsrfToken();
                    response = await fetch(sendUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: text })
                    });
                }

                if (response.ok) {
                    data = await response.json();
                } else {
                    try {
                        data = await response.json();
                    } catch (_) {
                        data = {
                            success: false,
                            message: 'Koneksi server sedang sangat padat sehingga respon belum sempat dimuat. Boleh kirim ulang ya.'
                        };
                    }
                }
            } catch (err) {
                console.error('Fetch error during chat submit:', err);
                data = {
                    success: false,
                    message: 'Koneksi internet terputus atau server tidak merespon. Boleh klik tombol di bawah untuk kirim ulang ya.'
                };
            }

            if (data && data.success && data.assistant_message) {
                handleAiAssistantResponse(data);
                hideTypingIndicator();
            } else {
                hideTypingIndicator();
                const errMsg = (data && data.message) ? data.message : 'Koneksi server sedang sangat padat sehingga respon belum sempat dimuat. Boleh kirim ulang ya.';
                renderErrorBubble(errMsg, text);
            }

            sendBtn.disabled = false;
            isAiGenerating = false;
            lastInputTime = Date.now();
            lastInputValue = messageInput ? messageInput.value : '';
            startIdleTimer();
        });
    }

    // Initialize Voice Note player and Composer button state
    setupVoiceNotePlayers();
    updateComposerButtons();

    if (lastMsgSender === 'assistant' && !hasSentIdleSuggestionInTurn) {
        startIdleTimer();
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderProSliderBubbleHtml(pros) {
        if (!pros || !pros.length) {
            pros = registeredProfessionalsData || [];
        }

        const cardsHtml = pros.map(pro => {
            const displayName = pro.display_name || (pro.name ? pro.name.split(',')[0].trim() : 'Tenaga Profesional');
            const degree = pro.degree || (pro.name && pro.name.includes(',') ? pro.name.split(',').slice(1).join(',').trim() : '');
            const avatarUrl = pro.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&background=1f2937&color=ffffff&size=200`;
            const address = pro.address || pro.location || 'Praktek Konseling Profesional';

            let tags = [];
            if (Array.isArray(pro.specialization_tags) && pro.specialization_tags.length > 0) {
                tags = pro.specialization_tags;
            } else if (pro.specialization) {
                tags = pro.specialization.split(/[,&]+/).map(t => t.trim()).filter(Boolean);
            }

            const tagsHtml = tags.length > 0
                ? tags.map(tag => `<span class="px-2 py-0.5 rounded-md bg-neutral-100 dark:bg-neutral-800 text-[var(--txt-secondary)] border border-[var(--border-color)] text-[10px] sm:text-[10.5px] font-medium leading-tight">${escapeHtml(tag)}</span>`).join('')
                : '<span class="text-[10px] text-[var(--txt-tertiary)]">-</span>';

            const phone = pro.phone ? String(pro.phone).trim() : '';
            const phoneClean = phone.replace(/[^0-9]/g, '');
            const contactHtml = phone ? `
                <div class="mt-3 pt-2.5 border-t border-[var(--border-color)]/70">
                    <a href="https://wa.me/${phoneClean}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-xs font-semibold transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.16 12.05 20.16C10.66 20.16 9.3 19.81 8.08 19.14L7.75 18.96L4.54 19.8L5.4 16.67L5.2 16.35C4.46 15.08 4.07 13.52 4.07 11.92C4.07 7.38 7.76 3.67 12.05 3.67M9.06 6.88C8.88 6.88 8.58 6.94 8.33 7.21C8.07 7.49 7.37 8.15 7.37 9.53C7.37 10.91 8.38 12.24 8.52 12.43C8.66 12.61 10.5 15.44 13.33 16.66C14 16.95 14.52 17.12 14.93 17.25C15.6 17.47 16.22 17.43 16.7 17.36C17.24 17.28 18.36 16.68 18.59 16.03C18.82 15.38 18.82 14.82 18.75 14.71C18.68 14.59 18.5 14.53 18.23 14.39C17.96 14.26 16.62 13.6 16.37 13.51C16.12 13.42 15.94 13.37 15.76 13.64C15.58 13.92 15.06 14.53 14.9 14.71C14.74 14.89 14.58 14.92 14.31 14.78C14.04 14.65 13.17 14.36 12.13 13.44C11.32 12.72 10.78 11.83 10.62 11.56C10.46 11.28 10.6 11.13 10.74 11C10.86 10.87 11.01 10.67 11.15 10.5C11.29 10.33 11.33 10.21 11.42 10.03C11.51 9.85 11.47 9.69 11.4 9.56C11.33 9.42 10.79 8.1 10.56 7.56C10.34 7.03 10.11 7.1 9.95 7.1L9.5 7.09C9.28 7.09 9.06 6.88 9.06 6.88Z" />
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                </div>` : '';

            return `
                <div class="w-[220px] sm:w-[240px] shrink-0 snap-start bg-white dark:bg-[#1c1c1c] text-[var(--txt-primary)] rounded-2xl border border-[var(--border-color)] p-3 sm:p-3.5 flex flex-col justify-between shadow-xs hover:shadow-md hover:border-emerald-500/40 dark:hover:border-emerald-500/40 transition-all select-text">
                    <div>
                        <!-- Top: Avatar & Identitas -->
                        <div class="flex items-start gap-2.5 sm:gap-3">
                            <img src="${avatarUrl}" alt="${escapeHtml(displayName)}" class="w-12 h-14 sm:w-14 sm:h-16 rounded-xl object-cover shrink-0 border border-[var(--border-color)] shadow-2xs">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-[var(--txt-primary)] text-sm sm:text-[15px] leading-tight tracking-tight truncate" title="${escapeHtml(displayName)}">${escapeHtml(displayName)}</h4>
                                ${degree ? `<p class="text-[11.5px] text-[var(--txt-secondary)] font-normal mt-0.5 truncate" title="${escapeHtml(degree)}">${escapeHtml(degree)}</p>` : ''}
                            </div>
                        </div>

                        <!-- Middle: Keahlian (tanpa judul) -->
                        <div class="mt-2.5 pt-2 border-t border-[var(--border-color)]/70 flex flex-wrap gap-1">
                            ${tagsHtml}
                        </div>

                        <!-- Bottom: Alamat Praktik (tanpa judul) -->
                        <div class="mt-2.5 pt-2 border-t border-[var(--border-color)]/70 flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[var(--txt-tertiary)] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <p class="text-[11px] sm:text-xs text-[var(--txt-secondary)] leading-relaxed font-normal line-clamp-2" title="${escapeHtml(address)}">${escapeHtml(address)}</p>
                        </div>
                    </div>

                    ${contactHtml}
                </div>
            `;
        }).join('');

        return `
            <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-5 sm:px-6 py-4 text-base sm:text-[16.5px] leading-relaxed shadow-sm space-y-4 pro-slider-container">
                <p class="text-base sm:text-[16.5px] leading-relaxed text-[var(--txt-primary)]">
                    Sistem Weisz mendeteksi beban emosional yang cukup berat dan berkelanjutan. Ceritamu sangat berharga, dan kamu tidak harus menanggung semuanya sendirian. Berkonsultasi dengan psikiater atau psikolog klinis berlisensi dapat membantumu mendapatkan penanganan medis dan evaluasi mendalam yang tepat.
                </p>

                <!-- Slider Cards Track (Bisa digeser dan discroll secara mulus) -->
                <div class="pro-slider-track flex items-stretch gap-3 overflow-x-auto scroll-smooth snap-x snap-mandatory py-1 px-0.5 cursor-grab active:cursor-grabbing select-none">
                    ${cardsHtml}
                </div>
            </div>
        `;
    }

    function setupProSliders(scope = document) {
        const containers = scope.querySelectorAll ? scope.querySelectorAll('.pro-slider-container') : [];
        const targetContainers = (scope.classList && scope.classList.contains('pro-slider-container'))
            ? [scope, ...containers]
            : containers;

        targetContainers.forEach(container => {
            if (container.dataset.sliderInitialized === 'true') return;
            container.dataset.sliderInitialized = 'true';

            const track = container.querySelector('.pro-slider-track');
            if (!track) return;

            // 1. Desktop Mouse Wheel: horizontal smooth scrolling
            track.addEventListener('wheel', (e) => {
                if (e.deltaY !== 0 && !e.shiftKey) {
                    e.preventDefault();
                    track.scrollBy({
                        left: e.deltaY * 1.5,
                        behavior: 'smooth'
                    });
                }
            }, { passive: false });

            // 2. Mouse Drag-to-Scroll (Klik dan Geser)
            let isDown = false;
            let startX = 0;
            let scrollStartLeft = 0;
            let hasDragged = false;

            track.addEventListener('mousedown', (e) => {
                if (e.target.closest('a') || e.target.closest('button')) return;
                isDown = true;
                hasDragged = false;
                track.classList.add('cursor-grabbing');
                track.style.scrollSnapType = 'none';
                track.style.scrollBehavior = 'auto';
                startX = e.pageX - track.offsetLeft;
                scrollStartLeft = track.scrollLeft;
            });

            const onMouseMove = (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - track.offsetLeft;
                const walk = (x - startX) * 1.4;
                if (Math.abs(walk) > 4) {
                    hasDragged = true;
                }
                track.scrollLeft = scrollStartLeft - walk;
            };

            const onMouseUp = () => {
                if (!isDown) return;
                isDown = false;
                track.classList.remove('cursor-grabbing');
                track.style.scrollSnapType = '';
                track.style.scrollBehavior = '';
            };

            track.addEventListener('mousemove', onMouseMove);
            window.addEventListener('mouseup', onMouseUp);

            track.addEventListener('click', (e) => {
                if (hasDragged) {
                    e.preventDefault();
                    e.stopPropagation();
                    hasDragged = false;
                }
            }, true);
        });
    }

    // Initialize all existing pro sliders on page load
    setupProSliders();

    // Initialize all existing voice note players on page load
    setupVoiceNotePlayers();

    // Delegated toggle handler for voice note transcript accordion (smooth open/close with rotating arrow)
    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('.vn-transcript-toggle');
        if (!toggleBtn) return;
        e.preventDefault();
        e.stopPropagation();

        const section = toggleBtn.closest('.vn-transcript-section');
        if (!section) return;

        const collapse = section.querySelector('.vn-transcript-collapse');
        const arrow = toggleBtn.querySelector('.vn-toggle-arrow');
        if (!collapse) return;

        const isCurrentlyOpen = collapse.classList.contains('is-open') || collapse.classList.contains('grid-rows-[1fr]');
        if (isCurrentlyOpen) {
            collapse.classList.remove('is-open', 'grid-rows-[1fr]');
            collapse.classList.add('grid-rows-[0fr]');
            if (arrow) arrow.classList.remove('rotate-180', 'is-open');
            toggleBtn.setAttribute('title', 'Buka Transkrip');
        } else {
            collapse.classList.remove('grid-rows-[0fr]');
            collapse.classList.add('is-open', 'grid-rows-[1fr]');
            if (arrow) arrow.classList.add('rotate-180', 'is-open');
            toggleBtn.setAttribute('title', 'Tutup Transkrip');
        }
    });
</script>
@endpush
