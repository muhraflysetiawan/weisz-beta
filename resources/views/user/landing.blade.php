@extends('layouts.app')

@section('title', __('Weisz | Teman Refleksi & Pemahaman Emosi'))

@section('full_content')
<div class="w-full flex flex-col">

    <section class="w-full py-16 sm:py-24 lg:py-28 border-b border-[var(--border)] bg-[var(--bg-page)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div class="lg:col-span-7 space-y-6 text-left">
                    <h1 class="font-serif-display text-4xl sm:text-5xl lg:text-[52px] font-medium tracking-tight text-[var(--text-primary)] leading-[1.16]">
                        {{ __('Ruang Nyaman Mengurai') }} {{ __('Rasa dan') }}
                        <em class="italic text-[var(--text-accent)]">{{ __('Pikiran') }}</em> {{ __('Bersama Weisz') }}
                    </h1>

                    <p class="text-sm sm:text-base text-[var(--text-secondary)] leading-relaxed max-w-xl">
                        {{ __('Temukan ketenangan saat kepala terasa penuh. Menggabungkan sistem pakar psikologis terstruktur dengan teman ngobrol AI penuh empati, siap mendengarkan tanpa menghakimi 24/7.') }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                        <a href="{{ Auth::check() ? route('chat.quick') : route('register') }}" class="weisz-btn-primary min-h-[44px] px-6 py-3 text-sm sm:text-base">
                            {{ __('Mulai Refleksi Sekarang') }}
                        </a>

                        <a href="{{ route('explore.categories') }}" class="weisz-btn-secondary min-h-[44px] px-6 py-3 text-sm sm:text-base">
                            {{ __('Lihat Topik Refleksi') }}
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 w-full">
                    <div class="clay-card-static p-5 sm:p-6 space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-[var(--border-clay)]">
                            <p class="text-xs font-medium text-[var(--text-muted)]">{{ __('Pratinjau Ruang Refleksi') }}</p>
                            <p class="clay-pill-blue text-[11px]">
                                {{ __('Tekanan Akademik') }}
                            </p>
                        </div>

                        <div class="space-y-5">
                            <div class="flex justify-end w-full">
                                <div class="max-w-[86%] rounded-2xl rounded-tr-sm bg-[var(--sky-100)] dark:bg-[var(--surface-gentle)] border border-[var(--sky-200)] dark:border-[var(--border-gentle)] text-[var(--sky-800)] dark:text-[var(--text-primary)] px-4 py-3 text-xs sm:text-[13.5px] leading-relaxed">
                                    &ldquo;{{ __('Jujur aku kewalahan banget sama skripsi dan ekspektasi keluarga belakangan ini. Pengin istirahat tapi rasa bersalah terus muncul...') }}&rdquo;
                                </div>
                            </div>

                            <div class="space-y-3 pl-1">
                                <p class="text-xs sm:text-[14px] leading-relaxed text-[var(--text-primary)]">
                                    {{ __('Wajar sekali merasa lelah saat menanggung dua beban besar sekaligus. Tarik napas sebentar ya. Pikiranmu yang butuh jeda bukanlah sebuah kegagalan.') }}
                                </p>
                                <div class="weisz-card-gentle p-3.5 space-y-1">
                                    <p class="text-sm font-semibold text-[var(--text-accent)]">
                                        {{ __('Cek pelan-pelan:') }}
                                    </p>
                                    <p class="text-xs sm:text-[13px] text-[var(--text-secondary)] leading-relaxed">
                                        {{ __('Apakah rasa sesak ini lebih terasa di pundak atau dadamu saat ini?') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2 border-t border-[var(--border)] pointer-events-none select-none" aria-hidden="true">
                            <div class="flex-1 rounded-xl bg-[var(--bg-subtle)] border border-[var(--border)] px-3.5 py-2.5 text-xs text-[var(--text-muted)]">
                                {{ __('Ceritakan apa yang dirasa...') }}
                            </div>
                            <div class="w-9 h-9 rounded-full bg-[var(--sky-600)] dark:bg-[var(--sky-300)] text-white dark:text-[#14181d] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="w-full py-16 sm:py-22 border-b border-[var(--border)] bg-[var(--bg-subtle)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="max-w-xl space-y-2">
                <h2 class="font-serif-display text-2xl sm:text-3xl font-medium text-[var(--text-primary)]">{{ __('Alur Terstruktur Weisz') }}</h2>
                <p class="text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">{{ __('Bukan sekadar chat biasa. Setiap sesi terkunci pada konteks spesifikmu agar refleksi tetap fokus.') }}</p>
            </div>

            <div id="alurSection" class="relative pt-2 pb-2">
                <div class="hidden md:block absolute top-6 left-[12.5%] right-[12.5%] h-1 z-0" aria-hidden="true">
                    <div class="w-full h-full bg-[var(--border)] rounded-full overflow-hidden">
                        <div id="alurProgressBar" class="h-full bg-[var(--sky-600)] dark:bg-[var(--sky-300)] rounded-full transition-all duration-300" style="width: 33.33%;"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-6 relative z-10">
                    <div class="alur-step-item flex flex-col items-start md:items-center md:text-center group cursor-pointer" data-step="1">
                        <div id="alurCircle-1" class="alur-step-circle w-12 h-12 rounded-full bg-[var(--surface-gentle)] text-[var(--text-gentle)] border border-[var(--border-gentle)] font-semibold text-base flex items-center justify-center transition">
                            1
                        </div>
                        <h3 class="font-semibold text-base text-[var(--text-primary)] mt-4">{{ __('Pilih Masalah') }}</h3>
                        <p class="text-xs sm:text-[13px] text-[var(--text-secondary)] mt-1.5 leading-relaxed max-w-xs">
                            {{ __('Pilih kategori utama emosi atau relasi, lalu kerucutkan ke topik spesifik.') }}
                        </p>
                    </div>

                    <div class="alur-step-item flex flex-col items-start md:items-center md:text-center group cursor-pointer" data-step="2">
                        <div id="alurCircle-2" class="alur-step-circle w-12 h-12 rounded-full bg-[var(--surface-gentle)] text-[var(--text-gentle)] border border-[var(--border-gentle)] font-semibold text-base flex items-center justify-center transition">
                            2
                        </div>
                        <h3 class="font-semibold text-base text-[var(--text-primary)] mt-4">{{ __('Kunci Konteks') }}</h3>
                        <p class="text-xs sm:text-[13px] text-[var(--text-secondary)] mt-1.5 leading-relaxed max-w-xs">
                            {{ __('Konfirmasi topikmu agar obrolan AI tetap fokus dan tidak melebar ke mana-mana.') }}
                        </p>
                    </div>

                    <div class="alur-step-item flex flex-col items-start md:items-center md:text-center group cursor-pointer" data-step="3">
                        <div id="alurCircle-3" class="alur-step-circle w-12 h-12 rounded-full bg-[var(--surface-gentle)] text-[var(--text-gentle)] border border-[var(--border-gentle)] font-semibold text-base flex items-center justify-center transition">
                            3
                        </div>
                        <h3 class="font-semibold text-base text-[var(--text-primary)] mt-4">{{ __('AI + Expert System') }}</h3>
                        <p class="text-xs sm:text-[13px] text-[var(--text-secondary)] mt-1.5 leading-relaxed max-w-xs">
                            {{ __('AI mendengarkan ceritamu, sementara aturan pakar mengevaluasi faktor pemicu secara objektif.') }}
                        </p>
                    </div>

                    <div class="alur-step-item flex flex-col items-start md:items-center md:text-center group cursor-pointer" data-step="4">
                        <div id="alurCircle-4" class="alur-step-circle w-12 h-12 rounded-full bg-[var(--surface-gentle)] text-[var(--text-gentle)] border border-[var(--border-gentle)] font-semibold text-base flex items-center justify-center transition">
                            4
                        </div>
                        <h3 class="font-semibold text-base text-[var(--text-primary)] mt-4">{{ __('Rangkuman & Rujukan') }}</h3>
                        <p class="text-xs sm:text-[13px] text-[var(--text-secondary)] mt-1.5 leading-relaxed max-w-xs">
                            {{ __('Dapatkan rangkuman poin refleksi dan rekomendasi psikolog berlisensi jika diperlukan.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($categories->isNotEmpty())
    <section class="w-full py-16 sm:py-22 border-b border-[var(--border)] bg-[var(--bg-page)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="space-y-1.5 max-w-2xl">
                    <h2 class="font-serif-display text-2xl sm:text-3xl font-medium text-[var(--text-primary)]">{{ __('Kategori Masalah yang Sering Dihadapi') }}</h2>
                    <p class="text-xs sm:text-sm text-[var(--text-secondary)]">{{ __('Arahkan kursor pada kartu untuk melihat topik subkategori secara ringkas.') }}</p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('explore.categories') }}" class="weisz-btn-secondary inline-flex items-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-[16px] shadow-[var(--clay-rest)] hover:shadow-[var(--clay-lift)]">
                        <p>{{ __('Lihat Selengkapnya') }}</p>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-7">
                @foreach($categories as $category)
                <a href="{{ route('explore.subcategories', $category->slug) }}" class="book clay-book p-5 flex flex-col justify-between group cursor-pointer">
                    <!-- Inside Content: Terbuka saat cover berotasi -80deg pada hover -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2 border-b border-[var(--border-clay)] pb-2.5">
                            <p class="clay-pill-blue text-[11px] font-semibold tracking-wide">
                                {{ __('Subkategori') }}
                            </p>
                            <p class="text-[11px] text-[var(--text-muted)] font-medium">
                                {{ $category->subcategories_count }} {{ __('Topik') }}
                            </p>
                        </div>

                        <ul class="space-y-2 pt-1">
                            @forelse($category->subcategories->take(3) as $sub)
                            <li class="flex items-center gap-2 text-xs text-[var(--text-primary)] font-medium">
                                <div class="w-1.5 h-1.5 rounded-full bg-[var(--sky-600)] dark:bg-[var(--sky-400)] shrink-0"></div>
                                <p class="truncate">{{ $sub->name }}</p>
                            </li>
                            @empty
                            <li class="text-xs text-[var(--text-muted)] italic">
                                {{ __('Daftar topik segera hadir.') }}
                            </li>
                            @endforelse

                            @if($category->subcategories_count > 3)
                            <li class="text-[11px] text-[var(--text-accent)] font-medium pl-3.5 pt-0.5">
                                + {{ $category->subcategories_count - 3 }} {{ __('subkategori lainnya') }}
                            </li>
                            @endif
                        </ul>
                    </div>

                    <div class="pt-3 border-t border-[var(--border-clay)] flex items-center justify-between text-xs font-semibold text-[var(--sky-700)] dark:text-[var(--sky-400)]">
                        <p>{{ __('Mulai Eksplorasi') }}</p>
                        <svg class="w-4 h-4 transition group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </div>

                    <!-- 3D Book Cover: Membuka ke kiri -80deg saat hover -->
                    <div class="cover clay-book-cover">
                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <div class="clay-icon-pill w-10 h-10 rounded-[14px] bg-[var(--surface-clay-blue)] border border-[var(--border-clay)] flex items-center justify-center text-[var(--text-accent)]">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                    </svg>
                                </div>
                                <p class="clay-pill-well text-[11px]">
                                    {{ $category->subcategories_count }} {{ __('Subkategori') }}
                                </p>
                            </div>

                            <div class="space-y-1.5">
                                <h3 class="font-semibold text-base text-[var(--text-primary)] transition line-clamp-1">
                                    {{ $category->name }}
                                </h3>
                                <p class="text-xs sm:text-[13px] text-[var(--text-secondary)] leading-relaxed line-clamp-3">
                                    {{ $category->description }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-3.5 mt-3 border-t border-[var(--border-clay)] flex items-center justify-between text-xs font-semibold text-[var(--text-accent)]">
                            <p class="clay-arrow-cue text-[11px] text-[var(--text-secondary)]">{{ __('Arahkan kursor untuk membuka') }}</p>
                            <svg class="w-4 h-4 text-[var(--text-accent)]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <div class="flex justify-center pt-2">
                <a href="{{ route('explore.categories') }}" class="weisz-btn-secondary inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold rounded-[18px] shadow-[var(--clay-rest)] hover:shadow-[var(--clay-lift)]">
                    <p>{{ __('Lihat Selengkapnya (:count Kategori)', ['count' => $totalCategoriesCount ?? $categories->count()]) }}</p>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    <section class="w-full py-16 sm:py-22 border-b border-[var(--border-clay)] bg-[var(--bg-subtle)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-5 w-full">
                    <div class="clay-card-static overflow-hidden aspect-[4/3]">
                        <img src="{{ asset('images/psychiatrist_illustration.jpg') }}" alt="{{ __('Ilustrasi Konsultasi Psikiater & Profesional') }}" class="w-full h-full object-cover" loading="lazy">
                    </div>
                </div>

                <div class="lg:col-span-7 space-y-5 text-left">
                    <div class="space-y-2">
                        <p class="clay-pill-blue text-xs">{{ __('Direktori Terpercaya') }}</p>
                        <h2 class="font-serif-display text-2xl sm:text-3xl font-medium text-[var(--text-primary)] tracking-tight">{{ __('Tersedia :count Data Profesional Berlisensi', ['count' => $professionalsCount]) }}</h2>
                    </div>
                    <p class="text-sm sm:text-base text-[var(--text-secondary)] leading-relaxed max-w-xl">
                        {{ __('Weisz tidak menjual konsultasi atau memungut biaya. Kami menyediakan informasi kontak psikolog & psikiater terverifikasi yang dapat kamu hubungi secara mandiri jika sistem pakar mendeteksi kebutuhan dukungan profesional.') }}
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('professionals') }}" class="weisz-btn-primary min-h-[44px] px-5 py-2.5 text-sm">
                            {{ __('Rekomendasi Pakar') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="w-full py-16 sm:py-22 bg-[var(--bg-page)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                <div class="lg:col-span-7 space-y-6 text-left">
                    <div class="space-y-2">
                        <p class="clay-pill-blue text-xs">{{ __('Tentang Weisz') }}</p>
                        <h2 class="font-serif-display text-2xl sm:text-3xl lg:text-4xl font-medium text-[var(--text-primary)] tracking-tight leading-[1.22]">
                            {{ __('Ruang Aman Tanpa Prasangka, Menemani Setiap Emosi dan Langkahmu') }}
                        </h2>
                    </div>

                    <p class="text-sm sm:text-base text-[var(--text-secondary)] leading-relaxed">
                        {{ __('Weisz hadir sebagai sahabat refleksi diri yang menggabungkan kehangatan dialog empati berbasis kecerdasan buatan dengan ketegasan sistem pakar psikologis. Kami percaya bahwa setiap beban pikiran berhak didengar, diurai dengan tenang, dan dipahami tanpa rasa takut akan penghakiman.') }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-1">
                        <div class="clay-card-blue p-4 space-y-1.5">
                            <h3 class="text-sm font-semibold text-[var(--text-accent)]">{{ __('Privasi Penuh') }}</h3>
                            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">{{ __('Bebas bercerita tanpa syarat identitas KTP atau nomor HP.') }}</p>
                        </div>
                        <div class="clay-card-blue p-4 space-y-1.5">
                            <h3 class="text-sm font-semibold text-[var(--text-accent)]">{{ __('Sistem Pakar') }}</h3>
                            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">{{ __('Evaluasi konteks terstruktur berlandaskan metode psikologis.') }}</p>
                        </div>
                        <div class="clay-card-blue p-4 space-y-1.5">
                            <h3 class="text-sm font-semibold text-[var(--text-accent)]">{{ __('Pendengar 24/7') }}</h3>
                            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">{{ __('Selalu tersedia kapan pun kepalamu butuh ruang untuk jeda.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 w-full">
                    <div class="clay-card-static overflow-hidden aspect-[4/3]">
                        <img src="{{ asset('images/about_weisz_illustration.jpg') }}" alt="{{ __('Ilustrasi Tentang Weisz') }}" class="w-full h-full object-cover" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const alurBar = document.getElementById('alurProgressBar');
        const alurSteps = document.querySelectorAll('.alur-step-item');
        const alurCircles = document.querySelectorAll('.alur-step-circle');

        if (alurBar && alurSteps.length > 0) {
            const stepPercentages = { '1': '0%', '2': '33.33%', '3': '66.66%', '4': '100%' };
            const setAlurStep = (step) => {
                if (stepPercentages[step] !== undefined) {
                    alurBar.style.width = stepPercentages[step];
                }
                const stepInt = parseInt(step, 10);
                alurCircles.forEach((circle, idx) => {
                    const active = (idx + 1) <= stepInt;
                    circle.classList.toggle('bg-[var(--sky-600)]', active);
                    circle.classList.toggle('text-white', active);
                    circle.classList.toggle('dark:bg-[var(--sky-300)]', active);
                    circle.classList.toggle('dark:text-[#14181d]', active);
                    circle.classList.toggle('bg-[var(--surface-gentle)]', !active);
                    circle.classList.toggle('text-[var(--text-gentle)]', !active);
                });
            };

            alurSteps.forEach(item => {
                item.addEventListener('mouseenter', () => setAlurStep(item.getAttribute('data-step')));
                item.addEventListener('click', () => setAlurStep(item.getAttribute('data-step')));
            });
            setAlurStep('2');
        }
    });
</script>
@endpush
