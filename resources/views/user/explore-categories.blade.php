@extends('layouts.app')

@section('title', __('Pilih Kategori Masalah') . ' | Weisz')

@section('full_content')
<div class="w-full relative overflow-hidden min-h-[calc(100vh-4rem)] flex flex-col justify-between py-8 sm:py-12 bg-[var(--bg-page)] transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full my-auto space-y-8 sm:space-y-10">
        <div class="flex items-center justify-between gap-3 pb-1">
            <a href="{{ route('chat.index') }}" class="weisz-btn-secondary text-xs sm:text-sm py-2 px-4 group" title="{{ __('Kembali ke Ruang Curhat') }}">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                {{ __('Kembali ke Ruang Curhat') }}
            </a>
        </div>

        <div class="clay-card-static p-6 sm:p-9 space-y-5">
            <div class="space-y-2">
                <h1 class="font-serif-display text-2xl sm:text-3xl lg:text-4xl font-medium tracking-tight text-[var(--text-primary)]">
                    {{ __('Apa yang Sedang Mengganjal Pikiranmu?') }}
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-[var(--text-secondary)] max-w-3xl leading-relaxed">
                    {{ __('Pilih kategori situasi yang paling mendekati kondisimu saat ini. Setiap kategori dirancang dengan konteks psikologis spesifik untuk mengurai keresahanmu secara bertahap dan terarah.') }}
                </p>
            </div>

            <div class="relative w-full pt-2">
                <div class="relative w-full">
                    <div class="absolute inset-x-[12.5%] top-1/2 -translate-y-1/2 h-2 sm:h-2.5 z-0 pointer-events-none" aria-hidden="true">
                        <div class="w-full h-full bg-[var(--surface-well)] rounded-full overflow-hidden">
                            <div id="exploreProgressBar" class="h-full bg-[var(--sky-600)] dark:bg-[var(--sky-400)] rounded-full transition-all duration-500" style="width: 0%;" data-target="0"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 relative z-10 text-center">
                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] font-bold text-sm sm:text-base flex items-center justify-center shadow-[var(--clay-btn-primary)] ring-4 sm:ring-6 ring-[var(--surface-clay)] scale-105 transition-all">
                                1
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--surface-well)] text-[var(--text-muted)] border border-[var(--border-clay)] font-bold text-sm sm:text-base flex items-center justify-center ring-4 sm:ring-6 ring-[var(--surface-clay)] transition-all">
                                2
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--surface-well)] text-[var(--text-muted)] border border-[var(--border-clay)] font-bold text-sm sm:text-base flex items-center justify-center ring-4 sm:ring-6 ring-[var(--surface-clay)] transition-all">
                                3
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--surface-well)] text-[var(--text-muted)] border border-[var(--border-clay)] font-bold text-sm sm:text-base flex items-center justify-center ring-4 sm:ring-6 ring-[var(--surface-clay)] transition-all">
                                4
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-4 text-center mt-2.5">
                    <p class="text-xs sm:text-sm font-semibold text-[var(--text-primary)]">
                        {{ __('Kategori') }}
                    </p>
                    <p class="text-xs sm:text-sm font-medium text-[var(--text-muted)]">
                        {{ __('Subkategori') }}
                    </p>
                    <p class="text-xs sm:text-sm font-medium text-[var(--text-muted)]">
                        {{ __('Topik') }}
                    </p>
                    <p class="text-xs sm:text-sm font-medium text-[var(--text-muted)]">
                        {{ __('Konfirmasi') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch">
            @foreach($categories as $category)
            <a href="{{ route('explore.subcategories', $category->slug) }}" data-clay-tilt class="clay-card-interactive group p-6 sm:p-7 flex flex-col justify-between">
                <div class="space-y-4 flex-1 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="clay-icon-pill w-11 h-11 rounded-[14px] bg-[var(--surface-clay-blue)] border border-[var(--border-clay)] flex items-center justify-center text-[var(--text-accent)]">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                </svg>
                            </div>
                            <p class="clay-pill-well">
                                {{ $category->subcategories_count ?? $category->subcategories->count() }} {{ __('Subkategori') }}
                            </p>
                        </div>

                        <div class="space-y-1.5 pt-1">
                            <h2 class="text-base sm:text-lg font-semibold text-[var(--text-primary)] group-hover:text-[var(--text-accent)] transition line-clamp-2">
                                {{ $category->name }}
                            </h2>
                            <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed line-clamp-3">
                                {{ $category->description }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[var(--border-clay)] flex items-center justify-between text-xs font-semibold text-[var(--text-secondary)] group-hover:text-[var(--text-accent)] transition">
                        <p class="clay-arrow-cue">{{ __('Pilih Kategori') }}</p>
                        <svg class="clay-arrow-cue w-4 h-4 text-[var(--text-accent)]" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
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
