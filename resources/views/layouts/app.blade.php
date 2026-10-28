<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=resizes-content">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Weisz') — {{ __('Teman Refleksi & Pemahaman Emosi') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    
    <!-- Theme Detection Script (Prevents flash, supports System / Light / Dark) -->
    <script>
        (function() {
            const saved = localStorage.getItem('weisz_theme') || 'system';
            const isDark = (saved === 'dark') || (saved === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col @if(!View::hasSection('body_class')) min-h-screen @endif relative @yield('body_class')">
    <!-- Ambient Psychological Consultation Corner Background Gradients (Dribbble Mental Health Reference) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0" aria-hidden="true">
        <div class="absolute top-0 left-0 w-[340px] sm:w-[460px] md:w-[540px] h-[340px] sm:h-[460px] md:h-[540px] corner-aura-pink blur-xl sm:blur-2xl"></div>
        <div class="absolute top-0 right-0 w-[340px] sm:w-[460px] md:w-[540px] h-[340px] sm:h-[460px] md:h-[540px] corner-aura-lavender blur-xl sm:blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-[340px] sm:w-[460px] md:w-[540px] h-[340px] sm:h-[460px] md:h-[540px] corner-aura-blue blur-xl sm:blur-2xl"></div>
        <div class="absolute bottom-0 right-0 w-[300px] sm:w-[420px] md:w-[500px] h-[300px] sm:h-[420px] md:h-[500px] corner-aura-blue-br blur-xl sm:blur-2xl"></div>
    </div>

    <!-- Top Navbar (Uniform across all pages, only hidden on immersive full-screen chat) -->
    @unless(request()->routeIs('chat.show', 'chat.index'))
    <header class="sticky top-0 z-40 bg-[var(--bg-page)]/85 backdrop-blur-md border-b border-[var(--border-color)] transition-colors shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ Auth::check() ? route('chat.index') : route('landing') }}" class="flex items-center gap-2.5 sm:gap-3 group">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center font-bold text-base sm:text-lg shadow-sm">
                    W
                </div>
                <span class="font-bold text-lg sm:text-xl tracking-tight text-[var(--txt-primary)]">Weisz</span>
            </a>

            <!-- Right Actions: Language Switcher, Theme Button & Auth Buttons -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                <!-- Language Switcher Dropdown -->
                <div class="relative" id="langDropdownContainer">
                    <button id="langDropdownBtn" type="button" aria-expanded="false" aria-haspopup="true" aria-label="{{ __('Bahasa') }}" class="h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl border border-[var(--border-color)] flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] hover:bg-[var(--card-hover)] transition shadow-xs" title="{{ __('Bahasa Aplikasi') }}">
                        <span class="text-sm select-none" aria-hidden="true">{{ app()->getLocale() === 'en' ? '🇺🇸' : '🇮🇩' }}</span>
                        <span class="uppercase tracking-wider font-bold text-[11px] sm:text-xs">{{ app()->getLocale() }}</span>
                        <svg class="w-3.5 h-3.5 text-[var(--txt-tertiary)] transition-transform duration-200" id="langChevron" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div id="langDropdownMenu" class="hidden absolute right-0 mt-1.5 w-44 rounded-2xl bg-[var(--card-surface)] border border-[var(--border-color)] shadow-xl p-1.5 z-50 space-y-1 transition-all">
                        <form method="POST" action="{{ route('locale.update') }}">
                            @csrf
                            <input type="hidden" name="locale" value="id">
                            <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition cursor-pointer {{ app()->getLocale() === 'id' ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-bold' : 'text-[var(--txt-primary)] hover:bg-[var(--card-hover)]' }}">
                                <span class="flex items-center gap-2">
                                    <span class="text-base select-none">🇮🇩</span>
                                    <span>Bahasa Indonesia</span>
                                </span>
                                @if(app()->getLocale() === 'id')
                                <svg class="w-4 h-4 text-[var(--brand-main)]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                @endif
                            </button>
                        </form>
                        <form method="POST" action="{{ route('locale.update') }}">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition cursor-pointer {{ app()->getLocale() === 'en' ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-bold' : 'text-[var(--txt-primary)] hover:bg-[var(--card-hover)]' }}">
                                <span class="flex items-center gap-2">
                                    <span class="text-base select-none">🇺🇸</span>
                                    <span>English</span>
                                </span>
                                @if(app()->getLocale() === 'en')
                                <svg class="w-4 h-4 text-[var(--brand-main)]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Theme Toggle Button -->
                <button id="themeToggleBtn" type="button" aria-label="{{ __('Ganti Tema') }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-[var(--border-color)] flex items-center justify-center text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] hover:bg-[var(--card-hover)] transition shadow-xs cursor-pointer" title="{{ __('Pengaturan Tema') }}">
                    <svg id="sunIcon" class="w-4.5 h-4.5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg id="moonIcon" class="w-4.5 h-4.5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>

                @auth
                <!-- Ruang Curhat Button -->
                <a href="{{ route('chat.index') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-[var(--txt-primary)] border border-[var(--border-color)] bg-[var(--card-secondary)] hover:bg-[var(--card-hover)] transition shadow-xs">
                    {{ __('Ruang Curhat') }}
                </a>
                <a href="{{ route('profile') }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-[var(--border-color)] flex items-center justify-center text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] hover:bg-[var(--card-hover)] transition shadow-xs" title="{{ __('Profil & Pengaturan') }}">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-[var(--border-color)] flex items-center justify-center text-[var(--txt-secondary)] hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition shadow-xs cursor-pointer" title="{{ __('Keluar') }}">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
                @else
                <!-- Button Masuk & Daftar -->
                <a href="{{ route('login') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-[var(--txt-primary)] hover:bg-[var(--card-hover)] transition">
                    {{ __('Masuk') }}
                </a>
                <a href="{{ route('register') }}" class="px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-[#252525] text-white hover:bg-black dark:bg-white dark:text-[#252525] dark:hover:bg-neutral-100 transition shadow-xs">
                    {{ __('Daftar') }}
                </a>
                @endauth
            </div>
        </div>
    </header>
    @endunless



    <!-- Flash Notifications (for standard pages) -->
    @unless(request()->routeIs('chat.show', 'chat.index'))
        @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4 w-full">
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>{{ __(session('success')) }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4 w-full">
            <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    @endunless

    <!-- Content Slot -->
    @if(View::hasSection('full_content'))
        <div class="relative z-10 flex-1 flex flex-col min-h-0 h-full">
            @yield('full_content')
        </div>
    @else
        <!-- Main Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-3 sm:pt-4 pb-8 sm:pb-12 relative z-10">
            @yield('content')
        </main>
    @endif

    <!-- Footer for standard pages (Clean, Focused & Informative) -->
    @unless(View::hasSection('hide_footer') || request()->routeIs('chat.show', 'chat.index'))
    <footer class="border-t border-[var(--border-color)] bg-[var(--bg-page)]/95 dark:bg-[#181818]/95 backdrop-blur-md mt-auto transition-colors shrink-0 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Kolom Kiri: Logo Brand & Kontak Email -->
                <div class="lg:col-span-5 space-y-3.5 text-left">
                    <a href="{{ Auth::check() ? route('chat.index') : route('landing') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center font-bold text-base shadow-sm">
                            W
                        </div>
                        <span class="font-bold text-xl tracking-tight text-[var(--txt-primary)]">Weisz</span>
                    </a>

                    <!-- Kontak Email -->
                    <div class="space-y-2 pt-1">
                        <p class="text-xs text-[var(--txt-secondary)] leading-relaxed">
                            {{ __('Punya pertanyaan, kendala, atau ingin memberikan masukan? Hubungi kami melalui surat elektronik:') }}
                        </p>
                        <a href="mailto:support@weisz.id" class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-[var(--card-secondary)] hover:bg-[var(--card-hover)] border border-[var(--border-color)] text-xs sm:text-sm font-medium text-[var(--txt-primary)] transition shadow-xs group">
                            <svg class="w-4 h-4 text-[var(--txt-secondary)] group-hover:text-[var(--txt-primary)] transition shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <span>support@weisz.id</span>
                        </a>
                    </div>
                </div>

                <!-- Kolom Kanan: Layanan Bantuan Krisis Emosional 24 Jam -->
                <div class="lg:col-span-7">
                    <div class="p-5 sm:p-6 rounded-2xl bg-amber-500/8 dark:bg-amber-400/5 border border-amber-500/25 dark:border-amber-400/20 space-y-2.5">
                        <div class="flex items-center gap-2.5 text-amber-700 dark:text-amber-300">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/15 dark:bg-amber-400/15 flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-sm sm:text-base text-[var(--txt-primary)]">
                                {{ __('Layanan Bantuan Krisis Emosional 24 Jam') }}
                            </h4>
                        </div>
                        <p class="text-xs sm:text-sm text-[var(--txt-secondary)] leading-relaxed">
                            {{ __('Jika Anda atau orang terdekat sedang berada dalam krisis emosional darurat atau dorongan menyakiti diri, mohon segera hubungi') }} <strong class="text-[var(--txt-primary)]">{{ __('Hotline Kesehatan Jiwa Kemenkes 119 (ext. 8)') }}</strong> {{ __('atau') }} <strong class="text-[var(--txt-primary)]">{{ __('Halo Kemenkes 1500-567') }}</strong>. {{ __('Weisz adalah pendamping refleksi dan evaluasi pakar terpandu, bukan pengganti intervensi medis darurat.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Sub-Footer Bar -->
            <div class="border-t border-[var(--border-color)]/70 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[var(--txt-tertiary)]">
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
    @stack('scripts')
</body>
</html>
