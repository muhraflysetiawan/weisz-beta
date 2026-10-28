@extends('layouts.app')

@section('title', __('Katalog Topik') . ' — Weisz')

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
            <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[var(--card-surface)]/85 dark:bg-[#1f1f1f]/85 backdrop-blur-md hover:bg-[var(--card-hover)] text-xs sm:text-sm font-semibold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] border border-[var(--border-color)] transition shadow-xs group" title="{{ __('Kembali ke Ruang Curhat') }}">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>{{ __('Kembali ke Ruang Curhat') }}</span>
            </a>

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[var(--card-surface)]/85 dark:bg-[#1f1f1f]/85 backdrop-blur-md border border-[var(--border-color)] text-[var(--txt-secondary)] shadow-xs">
                <span>{{ __('Katalog Lengkap :count Topik', ['count' => $topics->count()]) }}</span>
            </div>
        </div>

        <!-- Header Card: Title, Search, and Category Filter -->
        <div class="rounded-3xl bg-[var(--card-surface)]/85 dark:bg-[#1f1f1f]/85 backdrop-blur-md border border-[var(--border-color)] p-6 sm:p-9 space-y-6 shadow-xs">
            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-[var(--txt-primary)]">
                    {{ __('Pilih Topik yang Sedang Kamu Hadapi') }}
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-[var(--txt-secondary)] max-w-3xl leading-relaxed">
                    {{ __('Cari langsung situasi atau keresahan yang kamu rasakan berdasarkan kata kunci, atau saring berdasarkan kategori utama.') }}
                </p>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('topics.explore') }}" class="space-y-3 pt-1">
                <div class="flex items-center gap-2.5">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="{{ __('Cari topik... (misal: overthinking, skripsi, insecure, patah hati)') }}"
                            class="weisz-input w-full text-sm py-3 pl-11 rounded-2xl bg-[var(--card-secondary)]">
                        <svg class="w-4.5 h-4.5 text-[var(--txt-tertiary)] absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <button type="submit" class="px-5 sm:px-6 py-3 rounded-2xl bg-[#252525] text-white hover:bg-black dark:bg-white dark:text-[#252525] dark:hover:bg-neutral-100 font-semibold text-xs sm:text-sm transition shadow-xs shrink-0">
                        {{ __('Cari') }}
                    </button>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 text-xs">
                    <a href="{{ route('topics.explore', ['search' => $search]) }}"
                        class="px-3.5 py-2 rounded-xl border shrink-0 transition font-medium {{ empty($selectedCategory) ? 'bg-[#252525] text-white dark:bg-white dark:text-[#252525] border-transparent font-semibold shadow-xs' : 'bg-[var(--card-secondary)] border-[var(--border-color)] text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)]' }}">
                        {{ __('Semua Kategori') }}
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('topics.explore', ['category' => $cat->id, 'search' => $search]) }}"
                        class="px-3.5 py-2 rounded-xl border shrink-0 transition font-medium {{ (string)$selectedCategory === (string)$cat->id ? 'bg-[#252525] text-white dark:bg-white dark:text-[#252525] border-transparent font-semibold shadow-xs' : 'bg-[var(--card-secondary)] border-[var(--border-color)] text-[var(--txt-secondary)] hover:bg-[var(--card-hover)] hover:text-[var(--txt-primary)]' }}">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Topics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch">
            @forelse($topics as $topic)
            <a href="{{ route('chat.confirm', $topic->slug) }}" class="group relative rounded-3xl bg-[var(--card-surface)]/85 dark:bg-[#1f1f1f]/85 backdrop-blur-md border border-[var(--border-color)] p-6 sm:p-7 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/30 hover:-translate-y-1.5 hover:border-[#252525] dark:hover:border-white/50 overflow-hidden">
                <!-- Inner ambient hover glow -->
                <div class="absolute -right-16 -top-16 w-36 h-36 rounded-full bg-gradient-to-br from-indigo-400/15 via-purple-300/10 to-transparent blur-2xl group-hover:scale-150 transition-transform duration-500 pointer-events-none" aria-hidden="true"></div>

                <div class="space-y-3 flex-1 flex flex-col justify-start">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-[#252525]/10 dark:bg-white/10 text-[var(--brand-main)] border border-[var(--border-color)] self-start line-clamp-1">
                        {{ $topic->subcategory->category->name }} &bull; {{ $topic->subcategory->name }}
                    </span>

                    <h2 class="text-lg font-bold text-[var(--txt-primary)] group-hover:text-[var(--brand-main)] transition line-clamp-2">
                        {{ $topic->name }}
                    </h2>
                    <p class="text-xs sm:text-sm text-[var(--txt-secondary)] leading-relaxed line-clamp-3">
                        {{ $topic->description }}
                    </p>
                </div>

                <div class="pt-4 mt-6 border-t border-[var(--border-color)]/70 flex items-center justify-between text-xs sm:text-[13px] font-semibold text-[var(--txt-tertiary)] group-hover:text-[var(--txt-primary)] transition">
                    <span>{{ __('Mulai Percakapan') }}</span>
                    <div class="w-8 h-8 rounded-xl bg-[var(--card-secondary)] group-hover:bg-[#252525] group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-[#252525] flex items-center justify-center transition-all duration-200 group-hover:translate-x-0.5 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full rounded-3xl bg-[var(--card-surface)]/85 dark:bg-[#1f1f1f]/85 backdrop-blur-md border border-[var(--border-color)] p-10 sm:p-14 text-center space-y-4 shadow-xs">
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
                    <a href="{{ route('topics.explore') }}" class="px-5 py-2.5 rounded-xl bg-[var(--card-secondary)] hover:bg-[var(--card-hover)] border border-[var(--border-color)] text-xs sm:text-sm font-semibold text-[var(--txt-primary)] transition shadow-2xs inline-flex items-center gap-2">
                        {{ __('Reset Pencarian') }}
                    </a>
                </div>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
