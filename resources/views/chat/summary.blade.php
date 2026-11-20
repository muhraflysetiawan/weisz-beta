@extends('layouts.app')

@section('title', '11. ' . __('Rangkuman Sesi') . ' | Weisz')

@section('content')
<div class="max-w-2xl mx-auto py-6 sm:py-10 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Rangkuman Refleksi Percakapan') }}
            </h1>
            <p class="text-xs sm:text-sm text-[var(--text-secondary)] mt-1">
                {{ __('Berikut adalah rangkuman dari apa yang telah kamu bagikan selama sesi ini. Kamu tetap bisa melanjutkan obrolan kapan saja.') }}
            </p>
        </div>

        <a href="{{ route('chat.index') }}" class="weisz-btn-secondary text-xs py-2 px-3.5 self-start sm:self-center">
            &larr; {{ __('Ruang Curhat') }}
        </a>
    </div>

    <div class="clay-card-static p-6 sm:p-8 space-y-6">
        <div class="space-y-1.5 pb-4 border-b border-[var(--border-clay)]">
            <p class="clay-pill-blue text-[11px]">{{ __('Topik Utama') }}</p>
            <h2 class="font-serif-display text-xl font-medium text-[var(--text-primary)] pt-1">
                {{ $summary->main_topic }}
            </h2>
            <p class="text-xs text-[var(--text-secondary)]">
                {{ __('Konteks') }}: {{ $session->category->name }} &bull; {{ $session->subcategory->name }}
            </p>
        </div>

        <div class="space-y-2">
            <h3 class="text-xs font-semibold text-[var(--text-muted)]">
                {{ __('Apa yang Kamu Bagikan') }}
            </h3>
            <div class="p-4 rounded-[16px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)] text-xs sm:text-sm text-[var(--text-primary)] leading-relaxed">
                {{ $summary->user_shared }}
            </div>
        </div>

        <div class="space-y-2">
            <h3 class="text-xs font-semibold text-[var(--text-muted)]">
                {{ __('Kemungkinan Faktor yang Memengaruhi') }}
            </h3>
            <div class="p-4 rounded-[16px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)] text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed space-y-1">
                {!! nl2br(e($summary->possible_factors)) !!}
            </div>
        </div>

        <div class="space-y-2">
            <h3 class="text-xs font-semibold text-[var(--text-muted)]">
                {{ __('Saran Langkah Kecil Berikutnya') }}
            </h3>
            <div class="clay-card-blue p-4 rounded-[16px] text-xs sm:text-sm text-[var(--text-primary)] leading-relaxed space-y-1">
                {!! nl2br(e($summary->suggested_steps)) !!}
            </div>
        </div>

        <div class="space-y-2">
            <h3 class="text-xs font-semibold text-[var(--text-muted)]">
                {{ __('Dukungan Profesional') }}
            </h3>
            <div class="p-4 rounded-[16px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)] text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
                {{ $summary->professional_support }}
            </div>
        </div>

        <div class="p-3.5 rounded-[16px] bg-[var(--color-warning-bg)] border border-[var(--color-warning-border)] text-[11px] text-[var(--text-secondary)] leading-relaxed">
            <strong class="font-semibold text-[var(--color-warning)]">{{ __('Catatan Keamanan:') }}</strong> {{ __('Rangkuman ini dibuat secara otomatis berdasarkan apa yang kamu ceritakan dan tidak dimaksudkan sebagai diagnosis klinis medis, psikiatri, atau evaluasi psikologis resmi.') }}
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
