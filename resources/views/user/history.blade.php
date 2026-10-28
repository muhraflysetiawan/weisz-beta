@extends('layouts.app')

@section('title', '13. ' . __('Riwayat Percakapan') . ' — Weisz')

@section('content')
<div class="max-w-6xl mx-auto py-6 sm:py-8 space-y-6">
    <!-- Back to Chat -->
    <div>
        <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            {{ __('Kembali ke Ruang Curhat') }}
        </a>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--txt-primary)]">
                {{ __('Riwayat Sesi Percakapan') }}
            </h1>
            <p class="text-xs text-[var(--txt-secondary)] mt-0.5">
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
        <div class="weisz-card p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#252525]/10 dark:bg-white/10 text-[var(--brand-main)] border border-[var(--border-color)]">
                            {{ $session->topic->name }}
                        </span>
                        <span class="text-xs text-[var(--txt-tertiary)]">{{ $session->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>

                    <h2 class="text-base font-bold text-[var(--txt-primary)]">
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
                        <span>{{ __('Lanjutkan Obrolan') }}</span>
                        <span class="text-xs">&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="pt-3 border-t border-[var(--border-color)] flex items-center justify-between text-xs text-[var(--txt-secondary)]">
                <div>
                    {{ __('Konteks') }}: <span class="font-medium text-[var(--txt-primary)]">{{ $session->category->name }}</span> &bull; {{ $session->subcategory->name }} &bull; <span class="text-[var(--brand-main)]">{{ $session->topic->name }}</span>
                </div>
                <span>{{ $session->messages()->count() }} {{ __('pesan') }}</span>
            </div>
        </div>
        @empty
        <div class="weisz-card p-12 text-center space-y-3">
            <p class="text-sm font-semibold text-[var(--txt-primary)]">{{ __('Belum ada riwayat percakapan yang cocok') }}</p>
            <p class="text-xs text-[var(--txt-secondary)]">{{ __('Mulai sesi baru untuk membicarakan kondisi pikiranmu hari ini.') }}</p>
            <a href="{{ route('explore.categories') }}" class="weisz-btn-primary text-xs py-2 px-4 inline-flex">
                {{ __('Mulai Sesi Sekarang') }}
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $sessions->links() }}
    </div>
</div>
@endsection
