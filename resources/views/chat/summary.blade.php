@extends('layouts.app')

@section('title', '11. ' . __('Rangkuman Sesi') . ' — Weisz')

@section('content')
<div class="max-w-2xl mx-auto py-6 sm:py-10 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#252525]/10 dark:bg-white/10 text-[var(--brand-main)] mb-2 border border-[var(--border-color)]">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                {{ __('Rangkuman Refleksi Percakapan') }}
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--txt-primary)]">
                {{ __('Rangkuman Refleksi Percakapan') }}
            </h1>
            <p class="text-xs text-[var(--txt-secondary)] mt-0.5">
                {{ __('Berikut adalah rangkuman dari apa yang telah kamu bagikan selama sesi ini. Kamu tetap bisa melanjutkan obrolan kapan saja.') }}
            </p>
        </div>

        <a href="{{ route('chat.index') }}" class="weisz-btn-secondary text-xs py-2 px-3.5 self-start sm:self-center">
            &larr; {{ __('Ruang Curhat') }}
        </a>
    </div>

    <!-- Summary Content Card -->
    <div class="weisz-card p-6 sm:p-8 space-y-6">
        <!-- 1. Main Topic -->
        <div class="space-y-1.5 pb-4 border-b border-[var(--border-color)]">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[var(--txt-tertiary)]">{{ __('Topik Utama') }}</span>
            <h2 class="text-lg font-bold text-[var(--brand-main)]">
                {{ $summary->main_topic }}
            </h2>
            <p class="text-xs text-[var(--txt-secondary)]">
                {{ __('Konteks') }}: {{ $session->category->name }} &bull; {{ $session->subcategory->name }}
            </p>
        </div>

        <!-- 2. What User Shared -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[var(--txt-tertiary)] flex items-center gap-2">
                <span>💬 {{ __('Apa yang Kamu Bagikan') }}</span>
            </h3>
            <div class="p-4 rounded-xl bg-[var(--card-secondary)] text-xs text-[var(--txt-primary)] leading-relaxed">
                {{ $summary->user_shared }}
            </div>
        </div>

        <!-- 3. Possible Factors -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[var(--txt-tertiary)] flex items-center gap-2">
                <span>🔍 {{ __('Kemungkinan Faktor yang Memengaruhi') }}</span>
            </h3>
            <div class="p-4 rounded-xl bg-[var(--card-secondary)] text-xs text-[var(--txt-secondary)] leading-relaxed space-y-1">
                {!! nl2br(e($summary->possible_factors)) !!}
            </div>
        </div>

        <!-- 4. Suggested Next Steps -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[var(--txt-tertiary)] flex items-center gap-2">
                <span>🌱 {{ __('Saran Langkah Kecil Berikutnya') }}</span>
            </h3>
            <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-[var(--border-color)] text-xs text-[var(--txt-primary)] leading-relaxed space-y-1">
                {!! nl2br(e($summary->suggested_steps)) !!}
            </div>
        </div>

        <!-- 5. Professional Support -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[var(--txt-tertiary)] flex items-center gap-2">
                <span>🩺 {{ __('Dukungan Profesional') }}</span>
            </h3>
            <div class="p-4 rounded-xl bg-[var(--card-secondary)] text-xs text-[var(--txt-secondary)] leading-relaxed">
                {{ $summary->professional_support }}
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-[var(--border-color)] text-[11px] text-[var(--txt-tertiary)] leading-relaxed">
            <span class="font-semibold text-[var(--txt-secondary)]">{{ __('Catatan Keamanan:') }}</span> {{ __('Rangkuman ini dibuat secara otomatis berdasarkan apa yang kamu ceritakan dan tidak dimaksudkan sebagai diagnosis klinis medis, psikiatri, atau evaluasi psikologis resmi.') }}
        </div>

        <!-- Navigation Actions -->
        <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
            <a href="{{ route('chat.professionals', $session->id) }}" class="weisz-btn-primary w-full sm:w-auto py-2.5 px-5 text-xs font-semibold justify-center">
                {{ __('Lihat Rekomendasi Profesional') }} &rarr;
            </a>
            <a href="{{ route('chat.show', $session->id) }}" class="weisz-btn-secondary w-full sm:w-auto py-2.5 px-4 text-xs font-semibold justify-center">
                {{ __('Lanjutkan Obrolan') }} &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
