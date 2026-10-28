@extends('layouts.app')

@section('title', __('Weisz — Teman Refleksi & Pemahaman Emosi'))

@section('full_content')
<div class="w-full flex flex-col">

    <!-- ==================================================================== -->
    <!-- SECTION 1: HERO SECTION (FULL WIDTH EDGE-TO-EDGE)                   -->
    <!-- Ditinggikan lebih luas: py-24 sm:py-32 lg:py-40 min-h-[75vh]        -->
    <!-- Distinct Background: Radial Micro-Dots Matrix + Amber & Sky Orbs     -->
    <!-- Scroll Movement: Parallax Ambient Orbs moving on scroll              -->
    <!-- ==================================================================== -->
    <section class="w-full relative overflow-hidden py-24 sm:py-32 lg:py-40 min-h-[75vh] flex items-center border-b border-[var(--border-color)]/60 bg-[var(--bg-page)] transition-colors">
        <!-- Parallax Moving Orbs (Scroll Reactive) -->
        <div class="absolute -top-36 -left-24 w-[520px] h-[520px] rounded-full bg-gradient-to-br from-amber-200/40 via-rose-200/25 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="0.12" aria-hidden="true"></div>
        <div class="absolute -bottom-36 -right-24 w-[580px] h-[580px] rounded-full bg-gradient-to-tl from-sky-200/40 via-blue-200/25 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="-0.10" aria-hidden="true"></div>

        <!-- Inner Content (Centered & Responsive) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                <!-- Left Column: Copywriting & Action Buttons -->
                <div class="lg:col-span-7 space-y-7 text-left">
                    <h1 class="text-3xl sm:text-5xl lg:text-[48px] xl:text-[52px] font-extrabold tracking-tight text-[var(--txt-primary)] leading-[1.18]">
                        {{ __('Ruang Nyaman Mengurai') }} <span class="text-[var(--txt-primary)] font-semibold">{{ __('Rasa dan') }}</span><br class="hidden sm:inline">
                        <span class="text-[var(--txt-primary)] italic font-semibold">{{ __('Pikiran') }}</span> {{ __('Bersama Weisz') }}
                    </h1>

                    <p class="text-sm sm:text-base text-[var(--txt-secondary)] leading-relaxed max-w-xl">
                        {{ __('Temukan ketenangan saat kepala terasa penuh. Menggabungkan sistem pakar psikologis terstruktur dengan teman ngobrol AI penuh empati, siap mendengarkan tanpa menghakimi 24/7.') }}
                    </p>

                    <!-- Dual Action Buttons (Tanpa Icon) -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
                        <a href="{{ Auth::check() ? route('explore.categories') : route('register') }}" class="px-6 py-3 rounded-xl bg-[#252525] hover:bg-black text-white dark:bg-white dark:text-[#252525] dark:hover:bg-neutral-100 font-semibold text-sm sm:text-base text-center transition shadow-xs">
                            {{ __('Mulai Sekarang') }}
                        </a>

                        <a href="{{ route('explore.categories') }}" class="px-6 py-3 rounded-xl bg-[var(--card-secondary)] hover:bg-[var(--card-hover)] text-[var(--txt-primary)] border border-[var(--border-color)] font-semibold text-sm sm:text-base text-center transition shadow-xs">
                            {{ __('Explore') }}
                        </a>
                    </div>
                </div>

                <!-- Right Column: Interactive Weisz Companion Card Mockup -->
                <div class="lg:col-span-5 w-full">
                    <div class="relative rounded-2xl bg-[var(--card-surface)] border border-[var(--border-color)] shadow-lg shadow-black/5 dark:shadow-black/20 p-5 space-y-4">
                        <!-- Chat Conversation Preview -->
                        <div class="space-y-4 py-1">
                            <!-- User Bubble (Sama persis seperti chat/show.blade.php) -->
                            <div class="flex justify-end w-full">
                                <div class="max-w-[85%] rounded-2xl rounded-tr-sm bg-[#252525] text-white px-4 py-3 text-xs sm:text-[13px] leading-relaxed shadow-xs">
                                    &ldquo;{{ __('Jujur aku kewalahan banget sama skripsi dan ekspektasi keluarga belakangan ini. Pengin istirahat tapi rasa bersalah terus muncul...') }}&rdquo;
                                </div>
                            </div>

                            <!-- Assistant Bubble (Sama persis seperti chat/show.blade.php) -->
                            <div class="flex gap-2.5 max-w-[95%]">
                                <div class="w-8 h-8 rounded-xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-xs">
                                    W
                                </div>
                                <div class="space-y-2.5 flex-1">
                                    <div class="rounded-2xl rounded-tl-sm bg-[var(--card-surface)] border border-[var(--border-color)] text-[var(--txt-primary)] px-4 py-3 text-xs sm:text-[13px] leading-relaxed shadow-xs space-y-2">
                                        <p>
                                            {{ __('Wajar sekali merasa lelah saat menanggung dua beban besar sekaligus. Tarik napas sebentar ya. Pikiranmu yang butuh jeda bukanlah sebuah kegagalan.') }}
                                        </p>
                                        <p class="text-[var(--txt-secondary)]">
                                            {{ __('Apakah rasa sesak ini lebih terasa di pundak atau dadamu saat ini?') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Input Field: Mockup Desain Murni (Bukan input/button asli, tanpa mic) -->
                        <div class="flex items-center gap-2 pt-2 pointer-events-none select-none" aria-hidden="true">
                            <div class="flex-1 rounded-xl bg-[var(--card-secondary)] border border-[var(--border-color)] px-4 py-2.5 text-xs text-[var(--txt-tertiary)] flex items-center justify-between">
                                <span>{{ __('Ceritakan apa yang dirasa...') }}</span>
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center shrink-0 shadow-xs">
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


    <!-- ==================================================================== -->
    <!-- SECTION 2: ALUR TERSTRUKTUR WEISZ (FULL WIDTH EDGE-TO-EDGE)         -->
    <!-- Distinct Background: Isometric Linear Grid + Emerald/Cyan Orbs      -->
    <!-- Scroll Movement: Parallax Cool Orbs & Dynamic Progress Stepper       -->
    <!-- ==================================================================== -->
    <section class="w-full relative overflow-hidden py-16 sm:py-24 border-b border-[var(--border-color)]/60 bg-[var(--bg-secondary)]/50 dark:bg-[#1e1e1e]/40 transition-colors">
        <!-- Parallax Moving Orbs -->
        <div class="absolute -top-24 -right-20 w-[500px] h-[500px] rounded-full bg-gradient-to-bl from-emerald-200/35 via-teal-200/20 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="0.14" aria-hidden="true"></div>
        <div class="absolute -bottom-28 -left-20 w-[500px] h-[500px] rounded-full bg-gradient-to-tr from-cyan-200/35 via-blue-200/20 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="-0.12" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-12">
            <!-- Section Header -->
            <div class="text-center space-y-2 max-w-xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-[var(--txt-primary)]">{{ __('Alur Terstruktur Weisz') }}</h2>
                <p class="text-xs sm:text-sm text-[var(--txt-secondary)]">{{ __('Bukan sekadar chat biasa — setiap sesi terkunci pada konteks spesifikmu.') }}</p>
            </div>

            <!-- Stepper Progress Bar & Milestone Bulatan-Bulatan -->
            <div id="alurSection" class="relative max-w-5xl mx-auto pt-2 pb-2">
                <!-- Connecting Line & Active Progress Bar (Desktop: Tepat dari pusat circle 1 ke circle 4, tidak melebihi bulatan) -->
                <div class="hidden md:block absolute top-7 -translate-y-1/2 left-[12.5%] right-[12.5%] h-1.5 z-0" aria-hidden="true">
                    <!-- Background Track -->
                    <div class="w-full h-full bg-neutral-200/80 dark:bg-neutral-800 rounded-full overflow-hidden">
                        <!-- Moving Animated Progress Bar -->
                        <div id="alurProgressBar" class="h-full bg-[#252525] dark:bg-white rounded-full transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] shadow-xs" style="width: 33.33%;"></div>
                    </div>
                </div>

                <!-- Step Milestones Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-6 relative z-10 text-center">
                    <!-- Step 1 -->
                    <div class="alur-step-item flex flex-col items-center group cursor-pointer" data-step="1">
                        <div id="alurCircle-1" class="alur-step-circle w-14 h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-lg flex items-center justify-center shadow-md ring-8 ring-[var(--bg-secondary)] dark:ring-[#262626] group-hover:scale-110 transition-transform duration-200">
                            1
                        </div>
                        <h3 class="font-bold text-base text-[var(--txt-primary)] mt-4 group-hover:text-[var(--brand-main)] transition">{{ __('Pilih Masalah') }}</h3>
                        <p class="text-xs text-[var(--txt-secondary)] mt-1.5 leading-relaxed max-w-xs">
                            {{ __('Pilih kategori utama emosi atau relasi, lalu kerucutkan ke topik spesifik.') }}
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="alur-step-item flex flex-col items-center group cursor-pointer" data-step="2">
                        <div id="alurCircle-2" class="alur-step-circle w-14 h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-lg flex items-center justify-center shadow-md ring-8 ring-[var(--bg-secondary)] dark:ring-[#262626] group-hover:scale-110 transition-transform duration-200">
                            2
                        </div>
                        <h3 class="font-bold text-base text-[var(--txt-primary)] mt-4 group-hover:text-[var(--brand-main)] transition">{{ __('Kunci Konteks') }}</h3>
                        <p class="text-xs text-[var(--txt-secondary)] mt-1.5 leading-relaxed max-w-xs">
                            {{ __('Konfirmasi topikmu agar obrolan AI tetap fokus dan tidak melebar ke mana-mana.') }}
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="alur-step-item flex flex-col items-center group cursor-pointer" data-step="3">
                        <div id="alurCircle-3" class="alur-step-circle w-14 h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-lg flex items-center justify-center shadow-md ring-8 ring-[var(--bg-secondary)] dark:ring-[#262626] group-hover:scale-110 transition-transform duration-200">
                            3
                        </div>
                        <h3 class="font-bold text-base text-[var(--txt-primary)] mt-4 group-hover:text-[var(--brand-main)] transition">{{ __('AI + Expert System') }}</h3>
                        <p class="text-xs text-[var(--txt-secondary)] mt-1.5 leading-relaxed max-w-xs">
                            {{ __('AI mendengarkan ceritamu, sementara aturan pakar mengevaluasi faktor pemicu secara objektif.') }}
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="alur-step-item flex flex-col items-center group cursor-pointer" data-step="4">
                        <div id="alurCircle-4" class="alur-step-circle w-14 h-14 rounded-full bg-[#252525] text-white dark:bg-white dark:text-[#252525] font-extrabold text-lg flex items-center justify-center shadow-md ring-8 ring-[var(--bg-secondary)] dark:ring-[#262626] group-hover:scale-110 transition-transform duration-200">
                            4
                        </div>
                        <h3 class="font-bold text-base text-[var(--txt-primary)] mt-4 group-hover:text-[var(--brand-main)] transition">{{ __('Rangkuman & Rujukan') }}</h3>
                        <p class="text-xs text-[var(--txt-secondary)] mt-1.5 leading-relaxed max-w-xs">
                            {{ __('Dapatkan rangkuman poin refleksi dan rekomendasi psikolog berlisensi jika diperlukan.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================================================================== -->
    <!-- SECTION 3: KATEGORI MASALAH (FULL WIDTH EDGE-TO-EDGE)               -->
    <!-- Hapus button '<', '>', dan 'Lihat Semua'                            -->
    <!-- Geser dengan touch dan scroll; tanpa garis & text Subkategori/Pilih  -->
    <!-- ==================================================================== -->
    @if($categories->isNotEmpty())
    <section class="w-full relative overflow-hidden py-16 sm:py-24 border-b border-[var(--border-color)]/60 bg-[var(--bg-page)] transition-colors">
        <!-- Parallax Moving Orbs -->
        <div class="absolute -top-32 left-1/4 w-[550px] h-[550px] rounded-full bg-gradient-to-br from-indigo-200/30 via-purple-100/20 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="0.15" aria-hidden="true"></div>
        <div class="absolute -bottom-32 right-10 w-[550px] h-[550px] rounded-full bg-gradient-to-tl from-amber-200/35 via-rose-100/20 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="-0.13" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
            <!-- Section Header (Clean: Tanpa tombol '<', '>', dan tanpa 'Lihat Semua') -->
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-[var(--txt-primary)]">{{ __('Kategori Masalah yang Sering Dihadapi') }}</h2>
                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] mt-1">{{ __('Mulai dengan memilih situasi yang paling menggambarkan kondisimu.') }}</p>
            </div>

            <!-- Carousel Cards Track: Geser dengan touch, wheel & drag (no-scrollbar) -->
            <div id="categoriesCarousel" class="flex gap-5 overflow-x-auto scroll-smooth snap-x snap-mandatory no-scrollbar pb-3 pt-1 cursor-grab select-none active:cursor-grabbing">
                @foreach($categories as $category)
                <a href="{{ route('explore.subcategories', $category->slug) }}" class="w-[280px] sm:w-[320px] md:w-[340px] shrink-0 snap-start bg-[var(--card-surface)] border border-[var(--border-color)] rounded-2xl p-5 sm:p-6 hover:border-[#252525] dark:hover:border-white transition-all duration-200 shadow-xs flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[var(--card-secondary)] border border-[var(--border-color)]/80 flex items-center justify-center text-[var(--brand-main)] group-hover:bg-[var(--card-surface)] transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[var(--card-secondary)] text-[var(--txt-secondary)] border border-[var(--border-color)]/70">
                                {{ $category->subcategories_count ?? $category->subcategories->count() }} {{ __('Subkategori') }}
                            </span>
                        </div>

                        <div class="space-y-1.5 pt-1">
                            <h3 class="font-bold text-base text-[var(--txt-primary)] group-hover:text-[var(--brand-main)] transition line-clamp-1">
                                {{ $category->name }}
                            </h3>
                            <p class="text-xs text-[var(--txt-secondary)] leading-relaxed line-clamp-3">
                                {{ $category->description }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-3 mt-4 border-t border-[var(--border-color)]/60 flex items-center justify-between text-xs font-semibold text-[var(--txt-secondary)] group-hover:text-[var(--txt-primary)] transition">
                        <span>{{ __('Mulai Refleksi') }}</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1 text-[var(--brand-main)]" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif


    <!-- ==================================================================== -->
    <!-- SECTION 4: DIREKTORI TERPERCAYA (FULL WIDTH EDGE-TO-EDGE)           -->
    <!-- Layout 2 Kolom: Kiri Ilustrasi Psikiater 3D Clay, Kanan Teks + Button-->
    <!-- ==================================================================== -->
    <section class="w-full relative overflow-hidden py-16 sm:py-20 lg:py-24 border-b border-[var(--border-color)]/60 bg-[var(--bg-secondary)]/50 dark:bg-[#1a1a1a]/50 transition-colors">
        <!-- Parallax Moving Orbs -->
        <div class="absolute -right-28 -top-28 w-[480px] h-[480px] rounded-full bg-gradient-to-l from-emerald-200/25 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="0.10" aria-hidden="true"></div>
        <div class="absolute -left-28 -bottom-28 w-[450px] h-[450px] rounded-full bg-gradient-to-r from-teal-200/20 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="-0.08" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Kolom Kiri: Ilustrasi Psikiater & Profesional (Style 3D Clay yang sama) -->
                <div class="lg:col-span-5 w-full">
                    <div class="overflow-hidden rounded-2xl aspect-[4/3] bg-[var(--card-secondary)] shadow-md border border-[var(--border-color)]">
                        <img src="{{ asset('images/psychiatrist_illustration.jpg') }}" alt="Ilustrasi Konsultasi Psikiater & Profesional" class="w-full h-full object-cover" loading="lazy">
                    </div>
                </div>

                <!-- Kolom Kanan: Paragraf Penjelasan & Button 'Rekomendasi Pakar' -->
                <div class="lg:col-span-7 space-y-5 text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--txt-tertiary)]">{{ __('Direktori Terpercaya') }}</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-[var(--txt-primary)] tracking-tight">{{ __('Tersedia :count Data Profesional Berlisensi', ['count' => $professionalsCount]) }}</h3>
                    </div>
                    <p class="text-sm sm:text-base text-[var(--txt-secondary)] leading-relaxed max-w-xl">
                        {{ __('Weisz tidak menjual konsultasi atau memungut biaya. Kami menyediakan informasi kontak psikolog & psikiater terverifikasi yang dapat kamu hubungi secara mandiri jika sistem pakar mendeteksi kebutuhan dukungan profesional.') }}
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('professionals') }}" class="px-5 py-2.5 rounded-xl bg-[#252525] text-white hover:bg-black dark:bg-white dark:text-[#252525] dark:hover:bg-neutral-100 font-semibold text-sm transition shadow-xs inline-flex items-center justify-center">
                            {{ __('Rekomendasi Pakar') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ==================================================================== -->
    <!-- SECTION 5: ABOUT WEISZ (FULL WIDTH EDGE-TO-EDGE)                     -->
    <!-- Layout 2 Kolom: Kiri Penjelasan (Text), Kanan Ilustrasi 3D Clay      -->
    <!-- ==================================================================== -->
    <section class="w-full relative overflow-hidden py-16 sm:py-20 lg:py-24 border-b border-[var(--border-color)]/60 bg-[var(--bg-page)] transition-colors">
        <!-- Parallax Moving Orbs -->
        <div class="absolute -left-28 -top-28 w-[500px] h-[500px] rounded-full bg-gradient-to-r from-rose-200/25 via-amber-100/20 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="0.11" aria-hidden="true"></div>
        <div class="absolute -right-28 -bottom-28 w-[480px] h-[480px] rounded-full bg-gradient-to-l from-violet-200/25 via-purple-100/15 to-transparent blur-3xl pointer-events-none parallax-orb" data-speed="-0.09" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                <!-- Kolom Kiri: Penjelasan (Text) Tanpa Tombol -->
                <div class="lg:col-span-7 space-y-6 text-left">
                    <div class="space-y-2.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--txt-tertiary)]">{{ __('About Weisz') }}</span>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[var(--txt-primary)] tracking-tight leading-[1.25]">
                            {{ __('Ruang Aman Tanpa Prasangka, Menemani Setiap Emosi dan Langkahmu') }}
                        </h2>
                    </div>

                    <p class="text-sm sm:text-base text-[var(--txt-secondary)] leading-relaxed">
                        {{ __('Weisz hadir sebagai sahabat refleksi diri yang menggabungkan kehangatan dialog empati berbasis kecerdasan buatan dengan ketegasan sistem pakar psikologis. Kami percaya bahwa setiap beban pikiran berhak didengar, diurai dengan tenang, dan dipahami tanpa rasa takut akan penghakiman.') }}
                    </p>

                    <!-- Key Pillars List (Tanpa Emoji) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-1">
                        <div class="p-3.5 rounded-xl bg-[var(--card-secondary)] border border-[var(--border-color)] space-y-1">
                            <span class="text-base font-bold text-[var(--txt-primary)] block">{{ __('Privasi Penuh') }}</span>
                            <span class="text-xs text-[var(--txt-secondary)] leading-relaxed block">{{ __('Bebas bercerita tanpa syarat identitas KTP atau nomor HP.') }}</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[var(--card-secondary)] border border-[var(--border-color)] space-y-1">
                            <span class="text-base font-bold text-[var(--txt-primary)] block">{{ __('Sistem Pakar') }}</span>
                            <span class="text-xs text-[var(--txt-secondary)] leading-relaxed block">{{ __('Evaluasi konteks terstruktur berlandaskan metode psikologis.') }}</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[var(--card-secondary)] border border-[var(--border-color)] space-y-1">
                            <span class="text-base font-bold text-[var(--txt-primary)] block">{{ __('Pendengar 24/7') }}</span>
                            <span class="text-xs text-[var(--txt-secondary)] leading-relaxed block">{{ __('Selalu tersedia kapan pun kepalamu butuh ruang untuk jeda.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Ilustrasi 3D Clay About Weisz -->
                <div class="lg:col-span-5 w-full">
                    <div class="overflow-hidden rounded-2xl aspect-[4/3] bg-[var(--card-secondary)] shadow-md border border-[var(--border-color)]">
                        <img src="{{ asset('images/about_weisz_illustration.jpg') }}" alt="Ilustrasi About WeisZ - Mindful Self Reflection" class="w-full h-full object-cover" loading="lazy">
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
        // 1. Mouse Drag-to-Scroll & Touch Scroll for Categories Track (Point 2)
        const carousel = document.getElementById('categoriesCarousel');
        if (carousel) {
            let isDown = false;
            let startX;
            let scrollLeft;
            let hasMoved = false;

            carousel.addEventListener('mousedown', (e) => {
                isDown = true;
                hasMoved = false;
                carousel.classList.add('cursor-grabbing');
                carousel.classList.remove('cursor-grab');
                startX = e.pageX - carousel.offsetLeft;
                scrollLeft = carousel.scrollLeft;
            });

            carousel.addEventListener('mouseleave', () => {
                isDown = false;
                carousel.classList.remove('cursor-grabbing');
                carousel.classList.add('cursor-grab');
            });

            carousel.addEventListener('mouseup', () => {
                isDown = false;
                carousel.classList.remove('cursor-grabbing');
                carousel.classList.add('cursor-grab');
            });

            carousel.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                const x = e.pageX - carousel.offsetLeft;
                const walk = (x - startX) * 1.5;
                if (Math.abs(walk) > 6) {
                    hasMoved = true;
                }
                carousel.scrollLeft = scrollLeft - walk;
            });

            // Prevent card navigation when user is dragging
            carousel.addEventListener('click', (e) => {
                if (hasMoved) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);
        }

        // 2. Parallax Scroll Physics Engine Across Full Width Sections
        const parallaxOrbs = document.querySelectorAll('.parallax-orb');

        let isTicking = false;

        const updateParallaxPositions = () => {
            const viewportHeight = window.innerHeight;

            parallaxOrbs.forEach(orb => {
                const speed = parseFloat(orb.getAttribute('data-speed')) || 0.1;
                const parent = orb.parentElement;
                if (!parent) return;
                const rect = parent.getBoundingClientRect();

                // Calculate when element is near or within viewport
                if (rect.bottom > -150 && rect.top < viewportHeight + 150) {
                    const relativeCenter = rect.top - (viewportHeight / 2);
                    const translateY = relativeCenter * speed;
                    orb.style.transform = `translate3d(0, ${translateY.toFixed(1)}px, 0)`;
                }
            });

            isTicking = false;
        };

        // 3. Alur Terstruktur Moving Progress Bar on Hover (Point 1)
        const alurBar = document.getElementById('alurProgressBar');
        const alurSteps = document.querySelectorAll('.alur-step-item');
        const alurCircles = document.querySelectorAll('.alur-step-circle');

        if (alurBar && alurSteps.length > 0) {
            const stepPercentages = {
                '1': '0%',
                '2': '33.33%',
                '3': '66.66%',
                '4': '100%'
            };

            const setAlurStep = (step) => {
                const targetPercent = stepPercentages[step];
                if (targetPercent !== undefined) {
                    alurBar.style.width = targetPercent;
                }
                const stepInt = parseInt(step, 10);
                alurCircles.forEach((circle, idx) => {
                    const circleNum = idx + 1;
                    if (circleNum <= stepInt) {
                        circle.classList.add('ring-offset-2', 'ring-[#252525]', 'dark:ring-white');
                    } else {
                        circle.classList.remove('ring-offset-2', 'ring-[#252525]', 'dark:ring-white');
                    }
                });
            };

            alurSteps.forEach(item => {
                item.addEventListener('mouseenter', () => {
                    const step = item.getAttribute('data-step');
                    setAlurStep(step);
                });
            });

            // Smooth entrance animation when scrolled into view
            const alurSection = document.getElementById('alurSection');
            if (alurSection) {
                const alurObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            alurBar.style.width = '0%';
                            setTimeout(() => {
                                setAlurStep('2');
                            }, 350);
                            alurObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.25 });
                alurObserver.observe(alurSection);
            }
        }

        // Parallax Scroll Event Listener
        window.addEventListener('scroll', () => {
            if (!isTicking) {
                window.requestAnimationFrame(updateParallaxPositions);
                isTicking = true;
            }
        }, { passive: true });

        // Initial trigger
        updateParallaxPositions();
    });
</script>
@endpush
