<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=resizes-content">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Weisz') | {{ __('Teman Refleksi & Pemahaman Emosi') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,400..800;1,400..800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        (function() {
            const saved = localStorage.getItem('weisz_theme') || 'system';
            const isDark = (saved === 'dark') || (saved === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col @if(!View::hasSection('body_class')) min-h-screen @endif relative @yield('body_class')">
    <a href="#mainContent" class="skip-to-content">{{ __('Lompat ke konten utama') }}</a>

    @unless(request()->routeIs('chat.show', 'chat.index'))
    <header class="sticky top-0 z-40 bg-[var(--bg-page)]/95 backdrop-blur-sm border-b border-[var(--border)] transition-colors shrink-0">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ Auth::check() ? route('chat.index') : route('landing') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-[14px] bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] shadow-[var(--clay-btn-primary)] flex items-center justify-center font-bold text-lg">
                    W
                </div>
                <p class="font-bold text-xl tracking-tight text-[var(--text-primary)]">Weisz</p>
            </a>

            <div class="flex items-center gap-2">
                <div class="relative" id="langDropdownContainer">
                    <button id="langDropdownBtn" type="button" aria-expanded="false" aria-haspopup="true" aria-label="{{ __('Bahasa') }}" class="min-h-[40px] h-10 px-3 rounded-[14px] border border-[var(--border-clay)] bg-[var(--surface-clay)] shadow-[var(--clay-rest)] flex items-center gap-1.5 text-xs font-semibold text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-clay-hover)] transition cursor-pointer" title="{{ __('Bahasa Aplikasi') }}">
                        <p class="uppercase tracking-wide font-semibold text-xs">{{ app()->getLocale() }}</p>
                        <svg class="w-3.5 h-3.5 text-[var(--text-muted)] transition-transform duration-150" id="langChevron" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div id="langDropdownMenu" class="hidden absolute right-0 mt-2 w-44 rounded-[20px] bg-[var(--surface-clay)] border border-[var(--border-clay)] shadow-[var(--clay-lift)] p-1.5 z-50 space-y-1">
                        <form method="POST" action="{{ route('locale.update') }}">
                            @csrf
                            <input type="hidden" name="locale" value="id">
                            <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-[12px] text-xs sm:text-sm font-medium transition cursor-pointer {{ app()->getLocale() === 'id' ? 'bg-[var(--surface-clay-blue)] text-[var(--text-accent)] font-semibold' : 'text-[var(--text-primary)] hover:bg-[var(--surface-clay-hover)]' }}">
                                Bahasa Indonesia
                                @if(app()->getLocale() === 'id')
                                <svg class="w-4 h-4 text-[var(--text-accent)]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                @endif
                            </button>
                        </form>
                        <form method="POST" action="{{ route('locale.update') }}">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-[12px] text-xs sm:text-sm font-medium transition cursor-pointer {{ app()->getLocale() === 'en' ? 'bg-[var(--surface-clay-blue)] text-[var(--text-accent)] font-semibold' : 'text-[var(--text-primary)] hover:bg-[var(--surface-clay-hover)]' }}">
                                English
                                @if(app()->getLocale() === 'en')
                                <svg class="w-4 h-4 text-[var(--text-gentle)]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>

                <button id="themeToggleBtn" type="button" aria-label="{{ __('Ganti Tema') }}" class="w-10 h-10 rounded-xl border border-[var(--border)] bg-[var(--surface)] flex items-center justify-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-hover)] transition cursor-pointer" title="{{ __('Pengaturan Tema') }}">
                    <svg id="sunIcon" class="w-4.5 h-4.5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg id="moonIcon" class="w-4.5 h-4.5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>

                @auth
                <a href="{{ route('chat.index') }}" class="weisz-btn-secondary px-3.5 text-xs sm:text-sm">
                    {{ __('Ruang Curhat') }}
                </a>
                <button type="button" id="globalCrisisBtn" onclick="document.getElementById('globalCrisisModal').classList.remove('hidden');document.getElementById('globalCrisisModal').querySelector('a[href], button')?.focus();" class="w-10 h-10 rounded-xl border border-[var(--color-warning-border)] bg-[var(--color-warning-bg)] flex items-center justify-center text-[var(--color-warning)] hover:bg-amber-100 dark:hover:bg-amber-950/40 transition cursor-pointer shrink-0" title="{{ __('Bantuan Krisis 24 Jam') }}" aria-label="{{ __('Akses Bantuan Darurat dan Krisis') }}">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </button>
                <a href="{{ route('profile') }}" class="w-10 h-10 rounded-xl border border-[var(--border)] bg-[var(--surface)] flex items-center justify-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-hover)] transition" title="{{ __('Profil & Pengaturan') }}" aria-label="{{ __('Profil & Pengaturan') }}">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-10 h-10 rounded-xl border border-[var(--border)] bg-[var(--surface)] flex items-center justify-center text-[var(--text-secondary)] hover:text-[var(--color-error)] hover:bg-[var(--color-error-bg)] transition cursor-pointer" title="{{ __('Keluar') }}" aria-label="{{ __('Keluar') }}">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="min-h-[40px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-[var(--text-primary)] hover:bg-[var(--surface-hover)] inline-flex items-center transition">
                    {{ __('Masuk') }}
                </a>
                <a href="{{ route('register') }}" class="weisz-btn-primary text-xs sm:text-sm px-4 sm:px-5">
                    {{ __('Daftar') }}
                </a>
                @endauth
            </div>
        </div>
    </header>
    @endunless

    @auth
    @unless(request()->routeIs('chat.show', 'chat.index'))
    <div id="globalCrisisModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" role="dialog" aria-modal="true" aria-labelledby="globalCrisisModalTitle">
        <div class="bg-[var(--surface)] border border-[var(--border)] rounded-2xl shadow-2xl max-w-lg w-full p-6 sm:p-7 space-y-5 relative">
            <div class="flex items-center justify-between border-b border-[var(--border)] pb-3">
                <div class="flex items-center gap-2.5 text-[var(--color-warning)]">
                    <div class="w-8 h-8 rounded-xl bg-[var(--color-warning-bg)] border border-[var(--color-warning-border)] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 id="globalCrisisModalTitle" class="font-bold text-base sm:text-lg text-[var(--text-primary)]">{{ __('Layanan Bantuan & Keselamatan') }}</h3>
                </div>
                <button type="button" id="globalCrisisCloseBtn" onclick="closeGlobalCrisisModal()" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] p-1.5 rounded-xl transition cursor-pointer" aria-label="{{ __('Tutup') }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
                {{ __('Jika kamu atau seseorang yang kamu kenal sedang berada dalam krisis emosional darurat atau dorongan menyakiti diri, mohon segera hubungi layanan profesional bebas pulsa berikut:') }}
            </p>
            <div class="space-y-2.5">
                <a href="tel:119" class="clay-card-interactive p-3.5 rounded-[18px] bg-[var(--surface-clay-blue)] flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-xs sm:text-sm text-[var(--text-primary)]">{{ __('Hotline Kesehatan Jiwa (Kemenkes)') }}</p>
                        <p class="text-[11px] text-[var(--text-secondary)]">{{ __('Layanan darurat psikologis 24 jam') }}</p>
                    </div>
                    <p class="px-3 py-1.5 rounded-[14px] bg-[var(--sky-600)] text-white shadow-[var(--clay-btn-primary)] text-xs font-bold shrink-0">119 (ext. 8)</p>
                </a>
                <a href="tel:1500567" class="clay-card-interactive p-3.5 rounded-[18px] bg-[var(--surface-clay-blue)] flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-xs sm:text-sm text-[var(--text-primary)]">{{ __('Halo Kemenkes') }}</p>
                        <p class="text-[11px] text-[var(--text-secondary)]">{{ __('Pusat informasi & konseling kesehatan') }}</p>
                    </div>
                    <p class="px-3 py-1.5 rounded-[14px] bg-[var(--sky-600)] text-white shadow-[var(--clay-btn-primary)] text-xs font-bold shrink-0">1500-567</p>
                </a>
            </div>
            <div class="pt-2 border-t border-[var(--border)] text-[11px] text-[var(--text-muted)] leading-relaxed">
                {{ __('Weisz adalah pendamping refleksi dan evaluasi pakar terpandu, bukan pengganti intervensi medis darurat.') }}
            </div>
        </div>
    </div>
    <script>
        function closeGlobalCrisisModal() {
            const modal = document.getElementById('globalCrisisModal');
            if (!modal) return;
            modal.classList.add('hidden');
            const btn = document.getElementById('globalCrisisBtn');
            if (btn) btn.focus();
        }
        (function () {
            const modal = document.getElementById('globalCrisisModal');
            if (!modal) return;
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeGlobalCrisisModal();
                }
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeGlobalCrisisModal();
            });
            modal.addEventListener('keydown', (e) => {
                if (e.key !== 'Tab') return;
                const focusable = Array.from(modal.querySelectorAll('a[href], button:not([disabled])'));
                if (!focusable.length) return;
                const first = focusable[0], last = focusable[focusable.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            });
        })();
    </script>
    @endunless
    @endauth

    <!-- Flash Notifications (Poin 49 - Standard Pages) -->
    @unless(request()->routeIs('chat.show', 'chat.index'))
        @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4 w-full" id="flashSuccessAlert">
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <p>{{ __(session('success')) }}</p>
                </div>
                <button type="button" onclick="document.getElementById('flashSuccessAlert')?.remove()" class="text-emerald-700 dark:text-emerald-400 hover:opacity-75 p-1 rounded cursor-pointer" aria-label="{{ __('Tutup') }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4 w-full" id="flashErrorAlert">
            <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-sm flex items-start justify-between gap-3 shadow-2xs">
                <div class="flex items-start gap-2.5">
                    <svg class="w-4.5 h-4.5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" onclick="document.getElementById('flashErrorAlert')?.remove()" class="text-red-700 dark:text-red-400 hover:opacity-75 p-1 rounded cursor-pointer shrink-0" aria-label="{{ __('Tutup') }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        @endif
    @endunless

    <!-- Content Slot -->
    @if(View::hasSection('full_content'))
        <div id="mainContent" class="relative z-10 flex-1 flex flex-col min-h-0 h-full">
            @yield('full_content')
        </div>
    @else
        <!-- Main Content (Poin 47 Accessible landmark) -->
        <main id="mainContent" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-3 sm:pt-4 pb-8 sm:pb-12 relative z-10">
            @yield('content')
        </main>
    @endif

    @unless(View::hasSection('hide_footer') || request()->routeIs('chat.show', 'chat.index'))
    <footer class="border-t border-[var(--border)] bg-[var(--bg-subtle)] mt-auto transition-colors shrink-0 relative z-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-5 space-y-3.5 text-left">
                    <a href="{{ Auth::check() ? route('chat.index') : route('landing') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-[14px] bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] shadow-[var(--clay-btn-primary)] flex items-center justify-center font-bold text-lg">
                            W
                        </div>
                        <p class="font-bold text-xl tracking-tight text-[var(--text-primary)]">Weisz</p>
                    </a>

                    <div class="space-y-2.5 pt-1">
                        <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
                            {{ __('Punya pertanyaan, kendala, atau ingin memberikan masukan? Hubungi kami melalui surat elektronik:') }}
                        </p>
                        <a href="mailto:support@weisz.id" class="clay-pill hover:bg-[var(--surface-clay-blue)] hover:text-[var(--text-accent)] px-4 py-2.5 text-xs sm:text-sm font-medium text-[var(--text-primary)] transition group">
                            <svg class="w-4 h-4 text-[var(--text-accent)] shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            support@weisz.id
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="p-5 sm:p-6 rounded-2xl bg-[var(--color-warning-bg)] border border-[var(--color-warning-border)] space-y-2.5">
                        <div class="flex items-center gap-2.5 text-[var(--color-warning)]">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <h4 class="font-semibold text-sm sm:text-base text-[var(--text-primary)]">
                                {{ __('Layanan Bantuan Krisis Emosional 24 Jam') }}
                            </h4>
                        </div>
                        <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
                            {{ __('Jika Anda atau orang terdekat sedang berada dalam krisis emosional darurat atau dorongan menyakiti diri, mohon segera hubungi') }} <strong class="text-[var(--text-primary)]">{{ __('Hotline Kesehatan Jiwa Kemenkes 119 (ext. 8)') }}</strong> {{ __('atau') }} <strong class="text-[var(--text-primary)]">{{ __('Halo Kemenkes 1500-567') }}</strong>. {{ __('Weisz adalah pendamping refleksi dan evaluasi pakar terpandu, bukan pengganti intervensi medis darurat.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-[var(--border)] pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[var(--text-muted)]">
                <p>
                    &copy; {{ date('Y') }} Weisz. {{ __('Dirancang secara tenang, aman, dan tanpa penghakiman.') }}
                </p>
            </div>
        </div>
    </footer>
    @endunless

    <!-- Theme Toggle & Language Dropdown Script -->
    <script>
        // Language Dropdown Toggle
        const langDropdownBtn = document.getElementById('langDropdownBtn');
        const langDropdownMenu = document.getElementById('langDropdownMenu');
        const langChevron = document.getElementById('langChevron');

        if (langDropdownBtn && langDropdownMenu) {
            langDropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = langDropdownMenu.classList.toggle('hidden');
                langDropdownBtn.setAttribute('aria-expanded', !isHidden);
                if (langChevron) {
                    langChevron.classList.toggle('rotate-180', !isHidden);
                }
            });

            document.addEventListener('click', (e) => {
                if (!langDropdownMenu.contains(e.target) && !langDropdownBtn.contains(e.target)) {
                    langDropdownMenu.classList.add('hidden');
                    langDropdownBtn.setAttribute('aria-expanded', 'false');
                    if (langChevron) langChevron.classList.remove('rotate-180');
                }
            });
        }

        document.querySelectorAll('#themeToggleBtn, .theme-toggle-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                const newTheme = isDark ? 'dark' : 'light';
                localStorage.setItem('weisz_theme', newTheme);
                window.dispatchEvent(new CustomEvent('weisz-theme-changed', { detail: { theme: newTheme } }));
            });
        });

        try {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                const saved = localStorage.getItem('weisz_theme') || 'system';
                if (saved === 'system') {
                    document.documentElement.classList.toggle('dark', e.matches);
                    window.dispatchEvent(new CustomEvent('weisz-theme-changed', { detail: { theme: 'system' } }));
                }
            });
        } catch (e) {}
    </script>

    <!-- Explore Progress Bar Animation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const progressBar = document.getElementById('exploreProgressBar');
            if (progressBar) {
                const rawAttr = progressBar.getAttribute('data-target');
                const targetWidth = (rawAttr !== null && !isNaN(parseFloat(rawAttr)))
                    ? parseFloat(rawAttr)
                    : (parseFloat(progressBar.style.width) || 0);

                const prevWidthStr = sessionStorage.getItem('weisz_explore_step_percent');
                
                if (prevWidthStr !== null && !isNaN(parseFloat(prevWidthStr))) {
                    const prevWidth = parseFloat(prevWidthStr);
                    progressBar.style.transition = 'none';
                    progressBar.style.width = prevWidth + '%';
                    
                    void progressBar.offsetWidth;
                    
                    requestAnimationFrame(() => {
                        setTimeout(() => {
                            progressBar.style.transition = 'width 0.75s cubic-bezier(0.16, 1, 0.3, 1)';
                            progressBar.style.width = targetWidth + '%';
                        }, 40);
                    });
                } else {
                    progressBar.style.transition = 'none';
                    progressBar.style.width = '0%';
                    void progressBar.offsetWidth;
                    requestAnimationFrame(() => {
                        setTimeout(() => {
                            progressBar.style.transition = 'width 0.75s cubic-bezier(0.16, 1, 0.3, 1)';
                            progressBar.style.width = targetWidth + '%';
                        }, 40);
                    });
                }
                
                sessionStorage.setItem('weisz_explore_step_percent', targetWidth.toString());
            }
        });
    </script>
    <!-- Toast Notification Container (Poin 49) -->
    <div id="weiszToastContainer" class="weisz-toast-container" role="status" aria-live="polite"></div>

    <script>
        // Global Toast Notification Helper (Poin 49)
        window.weiszToast = function(message, type = 'info', duration = 4000) {
            const container = document.getElementById('weiszToastContainer');
            if (!container) return;

            // Cap at 3 stacked toasts
            while (container.children.length >= 3) {
                container.firstElementChild.remove();
            }

            const toast = document.createElement('div');
            toast.className = `weisz-toast weisz-toast-${type}`;

            const textEl = document.createElement('p');
            textEl.className = 'flex-1 text-xs sm:text-sm font-medium';
            textEl.textContent = message;

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'text-[var(--text-muted)] hover:text-[var(--text-primary)] p-1 rounded cursor-pointer shrink-0';
            closeBtn.setAttribute('aria-label', 'Tutup pemberitahuan');
            closeBtn.innerHTML = `
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            `;

            let timer = null;
            const removeToast = () => {
                if (timer) clearTimeout(timer);
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(8px)';
                setTimeout(() => toast.remove(), 200);
            };

            closeBtn.addEventListener('click', removeToast);
            toast.appendChild(textEl);
            toast.appendChild(closeBtn);
            container.appendChild(toast);

            if (duration > 0) {
                timer = setTimeout(removeToast, duration);
            }
        };

        if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('[data-clay-tilt]').forEach((card) => {
                card.addEventListener('mousemove', (e) => {
                    const r = card.getBoundingClientRect();
                    const x = (e.clientX - r.left) / r.width - 0.5;
                    const y = (e.clientY - r.top) / r.height - 0.5;
                    card.style.transform = `perspective(800px) translateY(-4px) rotateX(${(-y * 5).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg)`;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                });
            });
        }
    </script>

    @auth
    @if(!Auth::user()->isAdmin() && Auth::user()->onboarding_shown_at === null)
    <div id="onboardingConsentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" role="dialog" aria-modal="true" aria-labelledby="onboardingModalTitle">
        <div class="clay-card-static max-w-lg w-full p-6 sm:p-7 space-y-5 relative">
            <div class="space-y-2">
                <h2 id="onboardingModalTitle" class="font-serif-display text-xl sm:text-2xl font-medium text-[var(--text-primary)]">
                    {{ __('Selamat datang di ruang tenangmu') }}
                </h2>
                <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
                    {{ __('Sebelum mulai bercerita, ada tiga hal singkat yang perlu kamu tahu tentang Weisz:') }}
                </p>
            </div>
            <ul class="space-y-3 text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
                <li class="p-3.5 rounded-[16px] bg-[var(--surface-clay-blue)] border border-[var(--border-clay)] shadow-[var(--clay-rest)]">
                    <strong class="font-semibold text-[var(--text-primary)]">{{ __('Pendamping refleksi, bukan diagnosis medis.') }}</strong>
                    {{ __('Weisz membantu mengurai pikiran dan emosi, bukan menggantikan psikolog, psikiater, atau penanganan darurat.') }}
                </li>
                <li class="p-3.5 rounded-[16px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)]">
                    <strong class="font-semibold text-[var(--text-primary)]">{{ __('Privasi dan kendali ada di tanganmu.') }}</strong>
                    {{ __('Kamu bebas berhenti kapan saja, menyalin transkrip, atau keluar tanpa tekanan.') }}
                </li>
                <li class="p-3.5 rounded-[16px] bg-[var(--color-warning-bg)] border border-[var(--color-warning-border)] shadow-[var(--clay-well)]">
                    <strong class="font-semibold text-[var(--color-warning)]">{{ __('Bantuan krisis selalu satu ketukan.') }}</strong>
                    {{ __('Jika kondisi terasa darurat, hubungi Hotline Kemenkes 119 (ext. 8) atau Halo Kemenkes 1500-567 kapan saja.') }}
                </li>
            </ul>
            <form id="onboardingAcknowledgeForm" method="POST" action="{{ route('onboarding.acknowledge') }}" class="flex items-center justify-end gap-2 pt-1">
                @csrf
                <button type="submit" id="onboardingAcknowledgeBtn" class="weisz-btn-primary text-xs sm:text-sm px-5 py-2.5">
                    {{ __('Saya Mengerti, Lanjut') }}
                </button>
            </form>
        </div>
    </div>
    <script>
        (function () {
            const modal = document.getElementById('onboardingConsentModal');
            const form = document.getElementById('onboardingAcknowledgeForm');
            const submitBtn = document.getElementById('onboardingAcknowledgeBtn');
            if (!modal || !form) return;

            const acknowledge = () => {
                modal.classList.add('hidden');
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                }).catch(() => {});
            };

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                acknowledge();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    acknowledge();
                }
            });
            modal.addEventListener('keydown', (e) => {
                if (e.key !== 'Tab') return;
                e.preventDefault();
                if (submitBtn) submitBtn.focus();
            });
        })();
    </script>
    @endif
    @endauth
    @stack('scripts')
</body>
</html>
