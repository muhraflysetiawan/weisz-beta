@extends('layouts.app')

@section('title', __('Katalog Topik') . ' | Weisz')

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

            <p class="clay-pill">
                {{ __('Katalog Lengkap :count Topik', ['count' => $topics->count()]) }}
            </p>
        </div>

        <div class="clay-card-static p-6 sm:p-9 space-y-6">
            <div class="space-y-2">
                <h1 class="font-serif-display text-2xl sm:text-3xl lg:text-4xl font-medium tracking-tight text-[var(--text-primary)]">
                    {{ __('Pilih Topik yang Sedang Kamu Hadapi') }}
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-[var(--text-secondary)] max-w-3xl leading-relaxed">
                    {{ __('Cari langsung situasi atau keresahan yang kamu rasakan berdasarkan kata kunci, atau saring berdasarkan kategori utama.') }}
                </p>
            </div>

            <form method="GET" action="{{ route('topics.explore') }}" class="space-y-3 pt-1">
                <div class="flex items-center gap-2.5">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="{{ __('Cari topik... (misal: overthinking, skripsi, insecure, patah hati)') }}"
                            class="weisz-input w-full text-sm py-3 pl-11">
                        <svg class="w-4.5 h-4.5 text-[var(--text-muted)] absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <button type="submit" class="weisz-btn-primary px-5 sm:px-6 py-3 text-xs sm:text-sm shrink-0">
                        {{ __('Cari') }}
                    </button>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 text-xs">
                    <a href="{{ route('topics.explore', ['search' => $search]) }}"
                        class="px-3.5 py-2 rounded-[14px] border shrink-0 transition font-medium {{ empty($selectedCategory) ? 'bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] border-transparent font-semibold shadow-[var(--clay-btn-primary)]' : 'bg-[var(--surface-well)] border-[var(--border-clay)] text-[var(--text-secondary)] hover:bg-[var(--surface-clay-blue)] hover:text-[var(--text-accent)]' }}">
                        {{ __('Semua Kategori') }}
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('topics.explore', ['category' => $cat->id, 'search' => $search]) }}"
                        class="px-3.5 py-2 rounded-[14px] border shrink-0 transition font-medium {{ (string)$selectedCategory === (string)$cat->id ? 'bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] border-transparent font-semibold shadow-[var(--clay-btn-primary)]' : 'bg-[var(--surface-well)] border-[var(--border-clay)] text-[var(--text-secondary)] hover:bg-[var(--surface-clay-blue)] hover:text-[var(--text-accent)]' }}">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 items-stretch">
            @forelse($topics as $topic)
            <a href="{{ route('chat.confirm', $topic->slug) }}" data-clay-tilt class="clay-card-interactive group p-6 sm:p-7 flex flex-col justify-between">
                <div class="space-y-3 flex-1 flex flex-col justify-start">
                    <p class="clay-pill-blue text-[11px] self-start line-clamp-1">
                        {{ $topic->subcategory->category->name }} &bull; {{ $topic->subcategory->name }}
                    </p>

                    <h2 class="text-base sm:text-lg font-semibold text-[var(--text-primary)] group-hover:text-[var(--text-accent)] transition line-clamp-2">
                        {{ $topic->name }}
                    </h2>
                    <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed line-clamp-3">
                        {{ $topic->description }}
                    </p>
                </div>

                <div class="pt-4 mt-6 border-t border-[var(--border-clay)] flex items-center justify-between text-xs sm:text-[13px] font-semibold text-[var(--text-secondary)] group-hover:text-[var(--text-accent)] transition">
                    <p class="clay-arrow-cue">{{ __('Mulai Percakapan') }}</p>
                    <div class="clay-icon-pill w-8 h-8 rounded-[12px] bg-[var(--surface-clay-blue)] text-[var(--text-accent)] border border-[var(--border-clay)] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
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
