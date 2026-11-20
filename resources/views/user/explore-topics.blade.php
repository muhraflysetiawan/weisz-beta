@extends('layouts.app')

@section('title', __('Pilih Topik') . ' | ' . $subcategory->name . ' | Weisz')

@section('full_content')
<div class="w-full relative overflow-hidden min-h-[calc(100vh-4rem)] flex flex-col justify-between py-8 sm:py-12 bg-[var(--bg-page)] transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full my-auto space-y-8 sm:space-y-10">
        <div class="flex items-center justify-between gap-3 pb-1">
            <a href="{{ route('explore.subcategories', $category->slug) }}" class="weisz-btn-secondary text-xs sm:text-sm py-2 px-4 group" title="{{ __('Kembali ke Subkategori') }}">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                {{ __('Kembali ke Subkategori') }}
            </a>
        </div>

        <div class="clay-card-static p-6 sm:p-9 space-y-5">
            <div class="space-y-2">
                <h1 class="font-serif-display text-2xl sm:text-3xl lg:text-4xl font-medium tracking-tight text-[var(--text-primary)]">
                    {{ __('Pilih Topik Percakapan') }}
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-[var(--text-secondary)] max-w-3xl leading-relaxed">
                    {{ __('Pilih topik yang paling mewakili situasi yang sedang kamu alami. Weisz akan mengunci konteks ini agar sesi berlangsung terarah dan tidak melebar.') }}
                </p>
            </div>

            <div class="relative w-full pt-2">
                <div class="relative w-full">
                    <div class="absolute inset-x-[12.5%] top-1/2 -translate-y-1/2 h-2 sm:h-2.5 z-0 pointer-events-none" aria-hidden="true">
                        <div class="w-full h-full bg-[var(--surface-well)] rounded-full overflow-hidden">
                            <div id="exploreProgressBar" class="h-full bg-[var(--sky-600)] dark:bg-[var(--sky-400)] rounded-full transition-all duration-500" style="width: 66.66%;" data-target="66.66"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 relative z-10 text-center">
                        <div class="flex justify-center">
                            <a href="{{ route('explore.categories') }}" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] font-bold text-sm sm:text-base flex items-center justify-center shadow-[var(--clay-btn-primary)] ring-4 sm:ring-6 ring-[var(--surface-clay)] hover:scale-105 transition-transform cursor-pointer" title="{{ __('Kembali ke Kategori') }}">
                                1
                            </a>
                        </div>

                        <div class="flex justify-center">
                            <a href="{{ route('explore.subcategories', $category->slug) }}" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] font-bold text-sm sm:text-base flex items-center justify-center shadow-[var(--clay-btn-primary)] ring-4 sm:ring-6 ring-[var(--surface-clay)] hover:scale-105 transition-transform cursor-pointer" title="{{ __('Kembali ke Subkategori') }}">
                                2
                            </a>
                        </div>

                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] font-bold text-sm sm:text-base flex items-center justify-center shadow-[var(--clay-btn-primary)] ring-4 sm:ring-6 ring-[var(--surface-clay)] scale-105 transition-all">
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
                    <a href="{{ route('explore.subcategories', $category->slug) }}" class="text-xs sm:text-sm font-medium text-[var(--text-secondary)] hover:text-[var(--text-accent)] transition" title="{{ __('Kembali ke Subkategori') }}">
                        {{ __('Subkategori') }}
                    </a>
                    <p class="text-xs sm:text-sm font-semibold text-[var(--text-primary)]">
                        {{ __('Topik') }}
                    </p>
                    <p class="text-xs sm:text-sm font-medium text-[var(--text-muted)]">
                        {{ __('Konfirmasi') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-7 items-stretch">
            @foreach($topics as $topic)
            <a href="{{ route('chat.confirm', $topic->slug) }}" data-clay-tilt class="clay-card-interactive group p-6 sm:p-7 flex flex-col justify-between">
                <div class="space-y-4 flex-1 flex flex-col justify-start">
                    <div class="space-y-2">
                        <h2 class="text-base sm:text-lg font-semibold text-[var(--text-primary)] group-hover:text-[var(--text-accent)] transition line-clamp-2">
                            {{ $topic->name }}
                        </h2>
                        <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
                            {{ $topic->description }}
                        </p>
                    </div>

                    @if(!empty($topic->suggested_chips))
                    <div class="pt-3 mt-1 border-t border-[var(--border-clay)] space-y-2.5">
                        <p class="text-xs font-medium text-[var(--text-secondary)]">
                            {{ __('Mungkin ini yang sedang kamu rasakan:') }}
                        </p>
                        <div class="grid grid-cols-1 gap-1.5">
                            @foreach(array_slice($topic->suggested_chips, 0, 3) as $chip)
                            <p class="px-3 py-2 rounded-[14px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)] text-xs text-[var(--text-secondary)] group-hover:text-[var(--text-primary)] truncate italic transition">
                                &ldquo;{{ $chip }}&rdquo;
                            </p>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

    </div>
</div>
@endsection
