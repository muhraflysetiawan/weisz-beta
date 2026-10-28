@extends('layouts.app')

@section('title', __('Konfirmasi Konteks Percakapan') . ' — ' . $topic->name . ' — Weisz')

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
            <a href="{{ route('explore.topics', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}" class="inline-flex items-center gap-2 self-start px-4 py-2 rounded-xl bg-[var(--card-surface)]/85 dark:bg-[#1f1f1f]/85 backdrop-blur-md hover:bg-[var(--card-hover)] text-xs sm:text-sm font-semibold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] border border-[var(--border-color)] transition shadow-xs group" title="{{ __('Kembali ke Pilih Topik') }}">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>{{ __('Kembali ke Pilih Topik') }}</span>
            </a>
        </div>

        <!-- Header Card: Text Header & Progress Bar -->
        <div class="rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-6 sm:p-9 space-y-5 shadow-xs">
            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-[var(--txt-primary)]">
                    {{ __('Bagaimana kondisi atau perasaanmu saat ini?') }}
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-[var(--txt-secondary)] max-w-3xl leading-relaxed">
                    {{ __('Pilih ekspresi yang paling menggambarkan suasana hatimu agar Weisz AI dapat memahami dan masuk ke suasana hatimu secara tepat.') }}
                </p>
            </div>

            <!-- Stepper Progress Bar 4 Step -->
            <div class="relative w-full pt-2">
                <div class="relative w-full">
                    <div class="absolute inset-x-[12.5%] top-1/2 -translate-y-1/2 h-2 sm:h-2.5 z-0 pointer-events-none" aria-hidden="true">
                        <div class="w-full h-full bg-neutral-200/80 dark:bg-neutral-800 rounded-full overflow-hidden">
                            <div id="exploreProgressBar" class="h-full bg-[#252525] dark:bg-white rounded-full transition-all duration-500 shadow-xs" style="width: 100%;" data-target="100"></div>
                        </div>
                    </div>

                    <!-- 4 Circles Grid -->
                    <div class="grid grid-cols-4 relative z-10 text-center">
                        <div class="flex justify-center">
                            <a href="{{ route('explore.categories') }}" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-sm sm:text-base flex items-center justify-center shadow-md ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] hover:scale-110 transition-transform cursor-pointer" title="{{ __('Kategori') }}">
                                1
                            </a>
                        </div>

                        <div class="flex justify-center">
                            <a href="{{ route('explore.subcategories', $category->slug) }}" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-sm sm:text-base flex items-center justify-center shadow-md ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] hover:scale-110 transition-transform cursor-pointer" title="{{ __('Subkategori') }}">
                                2
                            </a>
                        </div>

                        <div class="flex justify-center">
                            <a href="{{ route('explore.topics', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-sm sm:text-base flex items-center justify-center shadow-md ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] hover:scale-110 transition-transform cursor-pointer" title="{{ __('Topik') }}">
                                3
                            </a>
                        </div>

                        <div class="flex justify-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-sm sm:text-base flex items-center justify-center shadow-lg shadow-black/10 dark:shadow-black/40 ring-4 sm:ring-6 ring-[var(--card-surface)] dark:ring-[#1f1f1f] scale-105 transition-all">
                                4
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step Labels Grid -->
                <div class="grid grid-cols-4 text-center mt-2.5">
                    <a href="{{ route('explore.categories') }}" class="text-xs sm:text-sm font-semibold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] transition">
                        {{ __('Kategori') }}
                    </a>
                    <a href="{{ route('explore.subcategories', $category->slug) }}" class="text-xs sm:text-sm font-semibold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] transition">
                        {{ __('Subkategori') }}
                    </a>
                    <a href="{{ route('explore.topics', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}" class="text-xs sm:text-sm font-semibold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] transition">
                        {{ __('Topik') }}
                    </a>
                    <span class="text-xs sm:text-sm font-bold text-[var(--txt-primary)]">
                        {{ __('Konfirmasi') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Card: Pilihan Ekspresi Suasana Hati & Mulai Sesi -->
        <div class="rounded-3xl bg-white dark:bg-[#1f1f1f] border border-[var(--border-color)] p-6 sm:p-9 space-y-6 sm:space-y-8 shadow-xs">
            <div class="grid grid-cols-5 gap-2.5 sm:gap-4 md:gap-6">
                @php
                    $moods = \App\Services\MoodExpertService::getMoods();
                @endphp

                @foreach($moods as $key => $mood)
                <button type="button" 
                    class="mood-card relative aspect-square p-1.5 sm:p-2.5 rounded-2xl sm:rounded-3xl border-2 border-[var(--border-color)] hover:border-[var(--brand-main)]/70 bg-[var(--card-secondary)] transition-all duration-200 flex items-center justify-center group cursor-pointer hover:shadow-md overflow-hidden" 
                    data-mood="{{ $key }}"
                    title="{{ __($mood['label']) }}">
                    <img src="{{ asset('images/moods/' . $key . '.jpg') }}" 
                         alt="{{ __($mood['label']) }}" 
                         class="w-full h-full object-cover rounded-xl sm:rounded-2xl group-hover:scale-105 transition-transform duration-300 pointer-events-none">
                    
                    <div class="check-indicator absolute top-2 right-2 sm:top-3 sm:right-3 w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center shadow-md opacity-0 transition-opacity pointer-events-none">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </button>
                @endforeach
            </div>

            <!-- Action Form -->
            <form id="startSessionForm" method="POST" action="{{ route('chat.start', $topic->slug) }}" class="pt-2">
                @csrf
                <input type="hidden" name="initial_mood" id="initialMoodInput" value="">

                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('explore.topics', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}" 
                       class="weisz-btn-secondary flex-1 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold justify-center text-center">
                        {{ __('Ganti Topik Lain') }}
                    </a>

                    <button type="submit" id="startSessionBtn" disabled 
                        class="weisz-btn-primary flex-1 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold justify-center opacity-40 cursor-not-allowed transition-all duration-200">
                        {{ __('Mulai') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const moodCards = document.querySelectorAll('.mood-card');
        const initialMoodInput = document.getElementById('initialMoodInput');
        const startSessionBtn = document.getElementById('startSessionBtn');

        moodCards.forEach(card => {
            card.addEventListener('click', () => {
                const selectedMood = card.dataset.mood;
                initialMoodInput.value = selectedMood;

                // Reset all cards
                moodCards.forEach(c => {
                    c.classList.remove('border-[#252525]', 'dark:border-white', 'ring-4', 'ring-[#252525]/20', 'dark:ring-white/20', 'scale-[1.03]', 'shadow-lg');
                    c.classList.add('border-[var(--border-color)]');
                    const indicator = c.querySelector('.check-indicator');
                    if (indicator) {
                        indicator.classList.add('opacity-0');
                    }
                });

                // Highlight active card
                card.classList.remove('border-[var(--border-color)]');
                card.classList.add('border-[#252525]', 'dark:border-white', 'ring-4', 'ring-[#252525]/20', 'dark:ring-white/20', 'scale-[1.03]', 'shadow-lg');

                const indicator = card.querySelector('.check-indicator');
                if (indicator) {
                    indicator.classList.remove('opacity-0');
                }

                // Enable start session button
                startSessionBtn.disabled = false;
                startSessionBtn.classList.remove('opacity-40', 'cursor-not-allowed');
                startSessionBtn.classList.add('cursor-pointer');
                startSessionBtn.removeAttribute('title');
            });
        });
    });
</script>
@endpush
