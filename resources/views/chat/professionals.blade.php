@extends('layouts.app')

@section('title', '12. ' . __('Rekomendasi Ahli Profesional') . ' — Weisz')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 mb-2">
                {{ __('Dukungan Eksternal Terpercaya') }}
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--txt-primary)]">
                {{ __('Rekomendasi Tenaga Profesional') }}
            </h1>
            <p class="text-xs text-[var(--txt-secondary)] mt-0.5">
                {{ __('Berdasarkan evaluasi sesi') }} <span class="font-medium text-[var(--txt-primary)]">{{ $session->topic->name }}</span>, {{ __('berikut adalah psikolog atau psikiater berlisensi yang dapat kamu hubungi secara mandiri.') }}
            </p>
        </div>

        <a href="{{ route('chat.summary', $session->id) }}" class="weisz-btn-secondary text-xs py-2 px-3.5 self-start sm:self-center">
            &larr; {{ __('Kembali ke Rangkuman') }}
        </a>
    </div>

    <!-- Info Notice -->
    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-[var(--border-color)] text-xs text-[var(--txt-secondary)] leading-relaxed">
        <p class="font-semibold text-[var(--txt-primary)] mb-1">{{ __('Informasi Penting:') }}</p>
        {{ __('Weisz tidak menyediakan sistem booking langsung, obrolan privat dengan profesional di aplikasi, maupun pembayaran. Data di bawah ini disediakan resmi sebagai rujukan kontak untuk kamu hubungi secara langsung di luar platform Weisz.') }}
    </div>

    <!-- Professionals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($professionals as $pro)
        <div class="weisz-card p-6 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-base text-[var(--txt-primary)]">{{ $pro->name }}</h2>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#252525]/10 dark:bg-white/10 text-[var(--brand-main)] border border-[var(--border-color)] mt-1">
                            {{ $pro->profession }}
                        </span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        {{ __('Terverifikasi') }}
                    </span>
                </div>

                <div class="text-xs space-y-1.5 pt-1 text-[var(--txt-secondary)]">
                    <p><strong class="text-[var(--txt-primary)]">{{ __('Spesialisasi') }}:</strong> {{ $pro->specialization ?: '-' }}</p>
                    <p><strong class="text-[var(--txt-primary)]">{{ __('Lokasi') }}:</strong> {{ $pro->location ?: '-' }}</p>
                    @if($pro->address)
                    <p><strong class="text-[var(--txt-primary)]">{{ __('Alamat / Praktik:') }}</strong> {{ $pro->address }}</p>
                    @endif
                    @if($pro->description)
                    <p class="text-[11px] text-[var(--txt-tertiary)] pt-1 leading-relaxed border-t border-[var(--border-color)]">
                        {{ $pro->description }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Contact Details -->
            <div class="pt-3 border-t border-[var(--border-color)] space-y-2 text-xs">
                @if($pro->phone)
                <div class="flex items-center justify-between">
                    <span class="text-[var(--txt-tertiary)]">{{ __('Telepon / WhatsApp:') }}</span>
                    <a href="tel:{{ $pro->phone }}" class="font-semibold text-[var(--brand-main)] hover:underline">
                        {{ $pro->phone }}
                    </a>
                </div>
                @endif

                @if($pro->email)
                <div class="flex items-center justify-between">
                    <span class="text-[var(--txt-tertiary)]">{{ __('Email:') }}</span>
                    <a href="mailto:{{ $pro->email }}" class="font-medium text-[var(--txt-primary)] hover:underline">
                        {{ $pro->email }}
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-2 weisz-card p-8 text-center text-xs text-[var(--txt-secondary)]">
            {{ __('Belum ada data profesional aktif di database saat ini.') }}
        </div>
        @endforelse
    </div>
</div>
@endsection
