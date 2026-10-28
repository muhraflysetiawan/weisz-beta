<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) — Weisz Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    
    <script>
        if (localStorage.getItem('weisz_theme') === 'dark' || (!localStorage.getItem('weisz_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col md:flex-row bg-[var(--bg-page)] relative">
    <!-- Ambient Psychological Consultation Corner Background Gradients -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0" aria-hidden="true">
        <div class="absolute top-0 left-0 w-[340px] sm:w-[460px] md:w-[540px] h-[340px] sm:h-[460px] md:h-[540px] corner-aura-pink blur-xl sm:blur-2xl"></div>
        <div class="absolute top-0 right-0 w-[340px] sm:w-[460px] md:w-[540px] h-[340px] sm:h-[460px] md:h-[540px] corner-aura-lavender blur-xl sm:blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-[340px] sm:w-[460px] md:w-[540px] h-[340px] sm:h-[460px] md:h-[540px] corner-aura-blue blur-xl sm:blur-2xl"></div>
        <div class="absolute bottom-0 right-0 w-[300px] sm:w-[420px] md:w-[500px] h-[300px] sm:h-[420px] md:h-[500px] corner-aura-blue-br blur-xl sm:blur-2xl"></div>
    </div>

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-[var(--card-surface)]/90 backdrop-blur-md border-b md:border-b-0 md:border-r border-[var(--border-color)] shrink-0 flex flex-col relative z-10">
        <!-- Brand & Actions -->
        <div class="h-16 px-4 sm:px-5 flex items-center justify-between border-b border-[var(--border-color)] gap-2">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-[var(--brand-main)] text-white flex items-center justify-center font-bold text-sm shrink-0">
                    W
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-bold text-sm sm:text-base tracking-tight text-[var(--txt-primary)] truncate">Weisz Admin</span>
                    <span class="text-[9px] sm:text-[10px] text-[var(--txt-tertiary)] uppercase font-semibold truncate">{{ __('Expert Rules') }}</span>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Language Toggle for Admin -->
                <div class="relative" id="adminLangDropdownContainer">
                    <button id="adminLangDropdownBtn" type="button" aria-label="{{ __('Bahasa') }}" class="h-8 sm:h-9 px-2 rounded-xl border border-[var(--border-color)] flex items-center gap-1 text-xs font-bold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] hover:bg-[var(--card-hover)] transition shadow-2xs cursor-pointer">
                        <span class="text-xs select-none">{{ app()->getLocale() === 'en' ? '🇺🇸' : '🇮🇩' }}</span>
                        <span class="uppercase text-[10px]">{{ app()->getLocale() }}</span>
                    </button>
                    <div id="adminLangDropdownMenu" class="hidden absolute right-0 mt-1.5 w-36 rounded-xl bg-[var(--card-surface)] border border-[var(--border-color)] shadow-xl p-1 z-50 space-y-0.5">
                        <form method="POST" action="{{ route('locale.update') }}">
                            @csrf
                            <input type="hidden" name="locale" value="id">
                            <button type="submit" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium cursor-pointer transition {{ app()->getLocale() === 'id' ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-bold' : 'text-[var(--txt-primary)] hover:bg-[var(--card-hover)]' }}">
                                <span class="flex items-center gap-1.5">
                                    <span>🇮🇩</span>
                                    <span>Indonesia</span>
                                </span>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('locale.update') }}">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <button type="submit" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium cursor-pointer transition {{ app()->getLocale() === 'en' ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-bold' : 'text-[var(--txt-primary)] hover:bg-[var(--card-hover)]' }}">
                                <span class="flex items-center gap-1.5">
                                    <span>🇺🇸</span>
                                    <span>English</span>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Theme Toggle Button -->
                <button id="themeToggleBtnAdmin" type="button" aria-label="{{ __('Ganti Tema') }}" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl border border-[var(--border-color)] flex items-center justify-center text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] transition shadow-2xs cursor-pointer">
                    <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-1.5 flex-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-semibold' : 'text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)]' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                15. {{ __('Dashboard') }}
            </a>

            <a href="{{ route('admin.categories') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.categories*') ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-semibold' : 'text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)]' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                </svg>
                16. {{ __('Kategori & Topik') }}
            </a>

            <a href="{{ route('admin.knowledge') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.knowledge*') ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-semibold' : 'text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)]' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                17. {{ __('Knowledge Base') }}
            </a>

            <a href="{{ route('admin.rules') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.rules*') ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-semibold' : 'text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)]' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                </svg>
                18. {{ __('Expert Rules') }}
            </a>

            <a href="{{ route('admin.professionals') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.professionals*') ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-semibold' : 'text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)]' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                19. {{ __('Data Profesional') }}
            </a>

            <a href="{{ route('admin.monitoring') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.monitoring*') ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-semibold' : 'text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)]' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
                20. {{ __('Monitoring Sesi') }}
            </a>
        </nav>

        <!-- Back to App / User Info -->
        <div class="p-4 border-t border-[var(--border-color)]">
            <a href="{{ route('chat.index') }}" class="flex items-center gap-2 text-xs font-medium text-[var(--txt-secondary)] hover:text-[var(--brand-main)] transition py-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                {{ __('Kembali ke Aplikasi User') }}
            </a>
        </div>
    </aside>

    <!-- Content Area -->
    <div class="flex-1 flex flex-col min-w-0 relative z-10">
        <!-- Top Flash Alerts -->
        @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border-b border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ __(session('success')) }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-950/40 border-b border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ __($error) }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>
    </div>

    <script>
        const themeBtnAdmin = document.getElementById('themeToggleBtnAdmin');
        if (themeBtnAdmin) {
            themeBtnAdmin.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('weisz_theme', isDark ? 'dark' : 'light');
            });
        }

        const adminLangBtn = document.getElementById('adminLangDropdownBtn');
        const adminLangMenu = document.getElementById('adminLangDropdownMenu');
        if (adminLangBtn && adminLangMenu) {
            adminLangBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                adminLangMenu.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
                if (!adminLangMenu.contains(e.target) && !adminLangBtn.contains(e.target)) {
                    adminLangMenu.classList.add('hidden');
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
