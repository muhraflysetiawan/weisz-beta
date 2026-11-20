@extends('layouts.app')

@section('title', '13. ' . __('Riwayat Percakapan') . ' | Weisz')

@section('content')
<div class="max-w-5xl mx-auto py-6 sm:py-8 space-y-6">
    <div>
        <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[var(--text-secondary)] hover:text-[var(--text-gentle)] transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            {{ __('Kembali ke Ruang Curhat') }}
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Riwayat Sesi Percakapan') }}
            </h1>
            <p class="text-xs sm:text-sm text-[var(--text-secondary)] mt-1">
                {{ __('Daftar seluruh sesi percakapanmu. Semua riwayat obrolan dapat diakses dan dilanjutkan kembali kapan saja.') }}
            </p>
        </div>

        <a href="{{ route('explore.categories') }}" class="weisz-btn-primary text-xs py-2 px-4 self-start sm:self-center">
            + {{ __('Sesi Baru') }}
        </a>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('history') }}" class="flex items-center gap-2 max-w-md">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="{{ __('Cari topik atau judul sesi...') }}"
            class="weisz-input w-full text-xs py-2.5">
        <button type="submit" class="weisz-btn-primary py-2.5 px-4 text-xs shrink-0">
            {{ __('Cari') }}
        </button>
        @if(request('search'))
        <a href="{{ route('history') }}" class="weisz-btn-secondary py-2.5 px-3 text-xs shrink-0">
            {{ __('Reset') }}
        </a>
        @endif
    </form>

    <!-- Sessions List -->
    <div class="space-y-4">
        @forelse($sessions as $session)
        <div class="clay-card-static p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="clay-pill-blue text-[11px]">
                            {{ $session->topic->name }}
                        </p>
                        <time class="text-xs text-[var(--text-muted)]">{{ $session->created_at->translatedFormat('d M Y, H:i') }}</time>
                    </div>

                    <h2 class="text-base font-semibold text-[var(--text-primary)]">
                        {{ $session->title ?: $session->topic->name }}
                    </h2>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-center">
                    @if($session->summary)
                    <a href="{{ route('chat.summary', $session->id) }}" class="weisz-btn-secondary text-xs py-1.5 px-3">
                        {{ __('Rangkuman') }}
                    </a>
                    @endif

                    <a href="{{ route('chat.show', $session->id) }}" class="weisz-btn-primary text-xs py-1.5 px-3.5 inline-flex items-center gap-1 font-semibold">
                        {{ __('Lanjutkan Obrolan') }} &rarr;
                    </a>
                </div>
            </div>

            <div class="pt-3 border-t border-[var(--border-clay)] flex items-center justify-between text-xs text-[var(--text-secondary)]">
                <p>
                    {{ __('Konteks') }}: <strong class="font-medium text-[var(--text-primary)]">{{ $session->category->name }}</strong> &bull; {{ $session->subcategory->name }} &bull; <strong class="font-medium text-[var(--text-accent)]">{{ $session->topic->name }}</strong>
                </p>
                <p class="clay-pill-well text-[11px]">{{ $session->messages()->count() }} {{ __('pesan') }}</p>
            </div>
        </div>
        @empty
        <div class="weisz-card p-10 sm:p-12 text-center space-y-4">
            @if(request('search'))
            <p class="text-sm sm:text-base text-[var(--text-secondary)]">
                {{ __('Tidak ada sesi yang cocok dengan pencarianmu.') }}
            </p>
            <a href="{{ route('history') }}" class="weisz-btn-secondary text-xs sm:text-sm py-2 px-4 inline-flex">
                {{ __('Reset Pencarian') }}
            </a>
            @else
            <p class="text-sm sm:text-base text-[var(--text-secondary)]">
                {{ __('Belum ada refleksi. Mulai dari topik yang terasa berat sekarang.') }}
            </p>
            <a href="{{ route('explore.categories') }}" class="weisz-btn-primary text-xs sm:text-sm py-2.5 px-5 inline-flex">
                {{ __('Lihat Topik Refleksi') }}
            </a>
            @endif
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $sessions->links() }}
    </div>
</div>
@endsection
