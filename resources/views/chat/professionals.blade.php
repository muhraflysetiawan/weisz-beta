@extends('layouts.app')

@section('title', '12. ' . __('Rekomendasi Ahli Profesional') . ' | Weisz')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-clay)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Rekomendasi Tenaga Profesional') }}
            </h1>
            <p class="text-xs sm:text-sm text-[var(--text-secondary)] mt-1">
                {{ __('Berdasarkan evaluasi sesi') }} <strong class="font-medium text-[var(--text-primary)]">{{ $session->topic->name }}</strong>, {{ __('berikut adalah psikolog atau psikiater berlisensi yang dapat kamu hubungi secara mandiri.') }}
            </p>
        </div>

        <a href="{{ route('chat.summary', $session->id) }}" class="weisz-btn-secondary text-xs py-2 px-3.5 self-start sm:self-center">
            &larr; {{ __('Kembali ke Rangkuman') }}
        </a>
    </div>

    <div class="clay-card-blue p-4 sm:p-5 text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
        <p class="font-semibold text-[var(--text-primary)] mb-1">{{ __('Informasi Penting:') }}</p>
        {{ __('Weisz tidak menyediakan sistem booking langsung, obrolan privat dengan profesional di aplikasi, maupun pembayaran. Data di bawah ini disediakan resmi sebagai rujukan kontak untuk kamu hubungi secara langsung di luar platform Weisz.') }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($professionals as $pro)
        <div class="clay-card-static p-6 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1.5">
                        <h2 class="font-semibold text-base text-[var(--text-primary)]">{{ $pro->name }}</h2>
                        <p class="clay-pill-blue text-[11px]">
                            {{ $pro->profession }}
                        </p>
                    </div>
                    <p class="clay-pill-success">
                        {{ __('Terverifikasi') }}
                    </p>
                </div>

                <div class="text-xs space-y-1.5 pt-1 text-[var(--text-secondary)]">
                    <p><strong class="text-[var(--text-primary)]">{{ __('Spesialisasi') }}:</strong> {{ $pro->specialization ?: '-' }}</p>
                    <p><strong class="text-[var(--text-primary)]">{{ __('Lokasi') }}:</strong> {{ $pro->location ?: '-' }}</p>
                    @if($pro->address)
                    <p><strong class="text-[var(--text-primary)]">{{ __('Alamat / Praktik:') }}</strong> {{ $pro->address }}</p>
                    @endif
                    @if($pro->description)
                    <p class="text-[11px] text-[var(--text-muted)] pt-1 leading-relaxed border-t border-[var(--border-clay)]">
                        {{ $pro->description }}
                    </p>
                    @endif
                </div>
            </div>

            <div class="pt-3 border-t border-[var(--border-clay)] space-y-2 text-xs">
                @if($pro->phone)
                <div class="flex items-center justify-between">
                    <p class="text-[var(--text-muted)]">{{ __('Telepon / WhatsApp:') }}</p>
                    <a href="tel:{{ $pro->phone }}" class="font-semibold text-[var(--text-accent)] hover:underline">
                        {{ $pro->phone }}
                    </a>
                </div>
                @endif

                @if($pro->email)
                <div class="flex items-center justify-between">
                    <p class="text-[var(--text-muted)]">{{ __('Email:') }}</p>
                    <a href="mailto:{{ $pro->email }}" class="font-medium text-[var(--text-primary)] hover:underline">
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
