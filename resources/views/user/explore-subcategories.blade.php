@extends('layouts.app')

@section('title', __('Pilih Subkategori') . ' | ' . $category->name . ' | Weisz')

@section('full_content')
<div class="w-full relative overflow-hidden min-h-[calc(100vh-4rem)] flex flex-col justify-between py-8 sm:py-12 bg-[var(--bg-page)] transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full my-auto space-y-8 sm:space-y-10">
        <div class="flex items-center justify-between gap-3 pb-1">
            <a href="{{ route('explore.categories') }}" class="weisz-btn-secondary text-xs sm:text-sm py-2 px-4 group" title="{{ __('Kembali ke Kategori') }}">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                {{ __('Kembali ke Kategori') }}
            </a>
        </div>

        <div class="clay-card-static p-6 sm:p-9 space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 flex-1">
                    <h1 class="font-serif-display text-2xl sm:text-3xl lg:text-4xl font-medium tracking-tight text-[var(--text-primary)]">
                        {{ __('Kerucutkan Subkategori') }}
                    </h1>
                    <p class="text-xs sm:text-sm md:text-base text-[var(--text-secondary)] max-w-2xl leading-relaxed">
                        {{ __('Pilih area yang paling relevan untuk membantumu memfokuskan cerita pada aspek yang paling mengganggu pikiranmu.') }}
                    </p>
                </div>

                <div class="hidden sm:flex items-center gap-3 px-4 py-2.5 rounded-[18px] bg-[var(--surface-clay-blue)] border border-[var(--border-clay)] shrink-0 max-w-xs">
                    <div class="w-10 h-10 rounded-[14px] bg-[var(--surface-clay)] border border-[var(--border-clay)] flex items-center justify-center shrink-0 text-[var(--text-accent)]">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                    </div>
                    <div class="min-w-0 pr-1">
                        <p class="text-xs text-[var(--text-muted)] font-medium">{{ __('Kategori Aktif') }}</p>
                        <p class="text-xs sm:text-sm font-semibold text-[var(--text-primary)] truncate">{{ $category->name }}</p>
                    </div>
                </div>
            </div>

            <div class="relative w-full pt-2">
                <div class="relative w-full">
                    <div class="absolute inset-x-[12.5%] top-1/2 -translate-y-1/2 h-2 sm:h-2.5 z-0 pointer-events-none" aria-hidden="true">
                        <div class="w-full h-full bg-[var(--surface-well)] rounded-full overflow-hidden">
                            <div id="exploreProgressBar" class="h-full bg-[var(--sky-600)] dark:bg-[var(--sky-400)] rounded-full transition-all duration-500" style="width: 33.33%;" data-target="33.33"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 relative z-10 text-center">
                        <div class="flex justify-center">
                            <a href="{{ route('explore.categories') }}" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] font-bold text-sm sm:text-base flex items-center justify-center shadow-[var(--clay-btn-primary)] ring-4 sm:ring-6 ring-[var(--surface-clay)] hover:scale-105 transition-transform cursor-pointer" title="{{ __('Kembali ke Kategori') }}">
                                1
                            </a>
                        </div>

                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] font-bold text-sm sm:text-base flex items-center justify-center shadow-[var(--clay-btn-primary)] ring-4 sm:ring-6 ring-[var(--surface-clay)] scale-105 transition-all">
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
                    <a href="{{ route('explore.categories') }}" class="text-xs sm:text-sm font-medium text-[var(--text-secondary)] hover:text-[var(--text-accent)] transition" title="{{ __('Kembali ke Kategori') }}">
                        {{ __('Kategori') }}
                    </a>
                    <p class="text-xs sm:text-sm font-semibold text-[var(--text-primary)]">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch">
            @foreach($subcategories as $sub)
            <a href="{{ route('explore.topics', ['category' => $category->slug, 'subcategory' => $sub->slug]) }}" data-clay-tilt class="clay-card-interactive group p-6 sm:p-7 flex flex-col justify-between">
                <div class="space-y-4 flex-1 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="clay-icon-pill w-11 h-11 rounded-[14px] bg-[var(--surface-clay-blue)] border border-[var(--border-clay)] flex items-center justify-center text-[var(--text-accent)]">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                </svg>
                            </div>
                            <p class="clay-pill-well">
                                {{ $sub->topics_count ?? $sub->topics->count() }} {{ __('Topik') }}
                            </p>
                        </div>

                        <div class="space-y-1.5 pt-1">
                            <h2 class="text-base sm:text-lg font-semibold text-[var(--text-primary)] group-hover:text-[var(--text-accent)] transition line-clamp-2">
                                {{ $sub->name }}
                            </h2>
                            <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed line-clamp-3">
                                {{ $sub->description }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[var(--border-clay)] flex items-center justify-between text-xs font-semibold text-[var(--text-secondary)] group-hover:text-[var(--text-accent)] transition">
                        <p class="clay-arrow-cue">{{ __('Pilih Subkategori') }}</p>
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
