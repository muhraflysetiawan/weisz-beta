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
                    <div class="absolute inset-x-[16.6%] top-1/2 -translate-y-1/2 h-2 sm:h-2.5 z-0 pointer-events-none" aria-hidden="true">
                        <div class="w-full h-full bg-[var(--surface-well)] rounded-full overflow-hidden">
                            <div id="exploreProgressBar" class="h-full bg-[var(--sky-600)] dark:bg-[var(--sky-400)] rounded-full transition-all duration-500" style="width: 0%;" data-target="0"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 relative z-10 text-center">
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
                    </div>
                </div>

                <div class="grid grid-cols-3 text-center mt-2.5">
                    <p class="text-xs sm:text-sm font-semibold text-[var(--text-primary)]">
                        {{ __('Kategori') }}
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

        <div class="flex flex-col gap-3.5 sm:gap-4">
            @foreach($categories as $category)
            <div id="exploreCatItem_{{ $category->id }}" class="category-accordion-item rounded-3xl bg-white dark:bg-[#1e2836] border border-[var(--border-clay)] transition-all duration-300 shadow-xs overflow-hidden">
                <button type="button" 
                    onclick="toggleExploreAccordion('{{ $category->id }}')" 
                    class="w-full text-left p-4 sm:p-5 flex items-center justify-between gap-4 cursor-pointer hover:bg-neutral-50/50 dark:hover:bg-white/[0.02] transition-colors group focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--brand-main)]"
                    aria-expanded="false"
                    aria-controls="exploreCatDropdown_{{ $category->id }}">
                    
                    <div class="flex items-center gap-3.5 sm:gap-4 min-w-0 flex-1">
                        <div class="clay-icon-pill w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-[var(--surface-clay-blue)] border border-[var(--border-clay)] flex items-center justify-center text-[var(--text-accent)] shrink-0 transition-colors group-hover:border-[var(--brand-main)]/50">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                            </svg>
                        </div>
                        
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base sm:text-lg font-semibold text-[var(--text-primary)] group-hover:text-[var(--text-accent)] transition truncate">
                                {{ $category->name }}
                            </h2>
                            <p class="text-xs sm:text-sm text-[var(--text-secondary)] line-clamp-1 sm:line-clamp-2 mt-0.5 leading-relaxed">
                                {{ $category->description }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                        <p class="clay-pill-well text-[11px] sm:text-xs">
                            {{ $category->subcategories_count ?? $category->subcategories->count() }} {{ __('Subkategori') }}
                        </p>
                        <div class="explore-chevron w-8 h-8 rounded-full flex items-center justify-center bg-[var(--surface-well)] text-[var(--text-secondary)] group-hover:text-[var(--text-primary)] transition-transform duration-300">
                            <svg class="w-4 h-4 transition-transform duration-300 transform" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </button>

                <div id="exploreCatDropdown_{{ $category->id }}" class="explore-dropdown hidden border-t border-[var(--border-clay)]/80 bg-neutral-50/50 dark:bg-black/20 p-3 sm:p-5 transition-all">
                    <div class="flex flex-col gap-2.5 sm:gap-3">
                        @forelse($category->subcategories as $sub)
                        <a href="{{ route('explore.topics', [$category->slug, $sub->slug]) }}" 
                            class="w-full text-left p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-[#151c26] border border-[var(--border-clay)] hover:border-[var(--brand-main)]/60 hover:shadow-md hover:shadow-black/5 dark:hover:shadow-black/20 transition-all duration-200 group flex items-center justify-between gap-3">
                            
                            <div class="flex items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-[var(--surface-clay-blue)] border border-[var(--border-clay)] flex items-center justify-center text-[var(--text-accent)] shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-xs sm:text-sm font-semibold text-[var(--text-primary)] group-hover:text-[var(--text-accent)] transition truncate">
                                        {{ $sub->name }}
                                    </h3>
                                    @if($sub->description)
                                    <p class="text-[11px] sm:text-xs text-[var(--text-secondary)] line-clamp-1 mt-0.5">
                                        {{ $sub->description }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2.5 shrink-0">
                                <p class="clay-pill-well text-[10px] sm:text-[11px] py-1 px-2.5">
                                    {{ $sub->topics_count ?? $sub->topics->count() }} {{ __('Topik') }}
                                </p>
                                <div class="w-7 h-7 rounded-xl flex items-center justify-center bg-[var(--surface-well)] text-[var(--text-secondary)] group-hover:bg-[var(--brand-main)] group-hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                        @empty
                        <p class="text-xs text-[var(--text-secondary)] italic p-3 text-center">
                            {{ __('Belum ada subkategori tersedia untuk kategori ini.') }}
                        </p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

<script>
function toggleExploreAccordion(catId) {
    const dropdown = document.getElementById('exploreCatDropdown_' + catId);
    const item = document.getElementById('exploreCatItem_' + catId);
    if (!dropdown || !item) return;

    const isCurrentlyOpen = !dropdown.classList.contains('hidden');

    document.querySelectorAll('.explore-dropdown').forEach(dd => dd.classList.add('hidden'));
    document.querySelectorAll('.category-accordion-item button[aria-expanded]').forEach(btn => {
        btn.setAttribute('aria-expanded', 'false');
        const chevron = btn.querySelector('.explore-chevron svg');
        if (chevron) chevron.classList.remove('rotate-180');
    });
    document.querySelectorAll('.category-accordion-item').forEach(it => {
        it.classList.remove('border-[var(--brand-main)]/60', 'shadow-md');
        it.classList.add('border-[var(--border-clay)]');
    });

    if (!isCurrentlyOpen) {
        dropdown.classList.remove('hidden');
        const header = item.querySelector('button[aria-expanded]');
        if (header) {
            header.setAttribute('aria-expanded', 'true');
            const chevron = header.querySelector('.explore-chevron svg');
            if (chevron) chevron.classList.add('rotate-180');
        }
        item.classList.add('border-[var(--brand-main)]/60', 'shadow-md');
        item.classList.remove('border-[var(--border-clay)]');
    }
}
</script>

    </div>
</div>
@endsection
