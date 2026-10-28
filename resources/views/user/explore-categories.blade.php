@extends('layouts.app')

@section('title', __('Pilih Kategori Masalah') . ' — Weisz')

@section('full_content')
<div class="w-full relative overflow-hidden min-h-[calc(100vh-4rem)] flex flex-col justify-between py-8 sm:py-12 bg-[var(--bg-page)] transition-colors">
    <!-- Parallax Background Motif: Radial Micro-Dots -->
    <div class="absolute inset-0 pointer-events-none opacity-45 dark:opacity-20 bg-[radial-gradient(#9ca3af_1.2px,transparent_1.2px)] [background-size:24px_24px] parallax-motif" data-speed="0.04" aria-hidden="true"></div>

    <!-- Parallax Ambient Glowing Orbs -->
    <div class="absolute -top-36 -left-24 w-[520px] h-[520px] rounded-full bg-gradient-to-br from-amber-200/40 via-rose-200/25 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="0.12" aria-hidden="true"></div>
    <div class="absolute -bottom-36 -right-24 w-[580px] h-[580px] rounded-full bg-gradient-to-tl from-sky-200/40 via-blue-200/25 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="-0.10" aria-hidden="true"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[620px] h-[620px] rounded-full bg-gradient-to-tr from-purple-200/15 via-rose-100/10 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="0.05" aria-hidden="true"></div>

    <!-- Inner Content (Centered & Responsive) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full my-auto space-y-8 sm:space-y-10">
        
        <!-- Navigation Breadcrumb -->
        <div class="flex items-center justify-between gap-3 pb-1">
            <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-2 self-start px-4 py-2 rounded-xl bg-[var(--card-surface)]/85 dark:bg-[#1f1f1f]/85 backdrop-blur-md hover:bg-[var(--card-hover)] text-xs sm:text-sm font-semibold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] border border-[var(--border-color)] transition shadow-xs group" title="{{ __('Kembali ke Ruang Curhat') }}">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>{{ __('Kembali ke Ruang Curhat') }}</span>
            </a>
        </div>

        <!-- Header Card: Text Header & Progress Bar -->
        <div class="rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-6 sm:p-9 space-y-5 shadow-xs">
            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-[var(--txt-primary)]">
                    {{ __('Apa yang Sedang Mengganjal Pikiranmu?') }}
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-[var(--txt-secondary)] max-w-3xl leading-relaxed">
                    {{ __('Pilih kategori situasi yang paling mendekati kondisimu saat ini. Setiap kategori dirancang dengan konteks psikologis spesifik untuk mengurai keresahanmu secara bertahap dan terarah.') }}
                </p>
            </div>

            <!-- Stepper Progress Bar 4 Step -->
            <div class="relative w-full pt-2">
                <div class="relative w-full">
                    <div class="absolute inset-x-[12.5%] top-1/2 -translate-y-1/2 h-2 sm:h-2.5 z-0 pointer-events-none" aria-hidden="true">
                        <div class="w-full h-full bg-neutral-200/80 dark:bg-neutral-800 rounded-full overflow-hidden">
                            <div id="exploreProgressBar" class="h-full bg-[#252525] dark:bg-white rounded-full transition-all duration-500 shadow-xs" style="width: 0%;" data-target="0"></div>
                        </div>
                    </div>

                    <!-- 4 Circles Grid -->
                    <div class="grid grid-cols-4 relative z-10 text-center">
                        <!-- Step 1: Kategori (Aktif) -->
                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-sm sm:text-base flex items-center justify-center shadow-lg shadow-black/10 dark:shadow-black/40 ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] scale-105 transition-all">
                                1
                            </div>
                        </div>

                        <!-- Step 2: Subkategori -->
                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--card-secondary)] text-[var(--txt-tertiary)] border-2 border-[var(--border-color)] font-extrabold text-sm sm:text-base flex items-center justify-center ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] transition-all">
                                2
                            </div>
                        </div>

                        <!-- Step 3: Topik -->
                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--card-secondary)] text-[var(--txt-tertiary)] border-2 border-[var(--border-color)] font-extrabold text-sm sm:text-base flex items-center justify-center ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] transition-all">
                                3
                            </div>
                        </div>

                        <!-- Step 4: Konfirmasi -->
                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--card-secondary)] text-[var(--txt-tertiary)] border-2 border-[var(--border-color)] font-extrabold text-sm sm:text-base flex items-center justify-center ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] transition-all">
                                4
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step Labels Grid -->
                <div class="grid grid-cols-4 text-center mt-2.5">
                    <span class="text-xs sm:text-sm font-bold text-[var(--txt-primary)]">
                        {{ __('Kategori') }}
                    </span>
                    <span class="text-xs sm:text-sm font-medium text-[var(--txt-tertiary)]">
                        {{ __('Subkategori') }}
                    </span>
                    <span class="text-xs sm:text-sm font-medium text-[var(--txt-tertiary)]">
                        {{ __('Topik') }}
                    </span>
                    <span class="text-xs sm:text-sm font-medium text-[var(--txt-tertiary)]">
                        {{ __('Konfirmasi') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Category Grid: Card Bersih & Modern Tanpa Ilustrasi Gambar -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch">
            @foreach($categories as $category)
            <a href="{{ route('explore.subcategories', $category->slug) }}" class="group relative rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-6 sm:p-7 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/40 hover:-translate-y-1 hover:border-[#252525] dark:hover:border-white/50 overflow-hidden shadow-xs">
                <!-- Inner ambient hover glow -->
                <div class="absolute -right-16 -top-16 w-36 h-36 rounded-full bg-gradient-to-br from-amber-400/15 via-rose-300/10 to-transparent blur-2xl group-hover:scale-150 transition-transform duration-500 pointer-events-none" aria-hidden="true"></div>

                <div class="space-y-4 flex-1 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)]/80 flex items-center justify-center text-[var(--txt-primary)] group-hover:border-[var(--brand-main)]/50 group-hover:bg-[var(--card-surface)] transition">
                                <svg class="w-5 h-5 text-[var(--brand-main)]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[var(--card-secondary)] text-[var(--txt-secondary)] border border-[var(--border-color)]/70">
                                {{ $category->subcategories_count ?? $category->subcategories->count() }} {{ __('Subkategori') }}
                            </span>
                        </div>

                        <div class="space-y-1.5 pt-1">
                            <h2 class="text-lg sm:text-xl font-bold text-[var(--txt-primary)] group-hover:text-[var(--brand-main)] transition line-clamp-2">
                                {{ $category->name }}
                            </h2>
                            <p class="text-xs sm:text-sm text-[var(--txt-secondary)] leading-relaxed line-clamp-3">
                                {{ $category->description }}
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
            </a>
            @endforeach
        </div>

    </div>
</div>
@endsection
