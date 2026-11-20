@extends('layouts.admin')

@section('title', __('Admin Dashboard') . ' — Weisz')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Dashboard Administrator') }}
            </h1>
            <p class="text-xs text-[var(--txt-secondary)] mt-0.5">
                {{ __('Ringkasan metrik sistem Weisz, aktivitas percakapan, dan status aturan pakar.') }}
            </p>
        </div>

        <a href="{{ route('chat.index') }}" class="weisz-btn-secondary text-xs py-2 px-3.5 self-start sm:self-center">
            {{ __('Buka Aplikasi User') }} &rarr;
        </a>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="weisz-card p-5 space-y-1">
            <p class="text-xs font-medium text-[var(--txt-tertiary)]">{{ __('Total Sesi Chat') }}</p>
            <p class="text-2xl font-bold text-[var(--txt-primary)]">{{ $totalSessions }}</p>
            <p class="text-[11px] text-[var(--txt-secondary)]">{{ __('Semua percakapan') }}</p>
        </div>

        <div class="weisz-card p-5 space-y-1">
            <p class="text-xs font-medium text-[var(--txt-tertiary)]">{{ __('Pengguna Terdaftar') }}</p>
            <p class="text-2xl font-bold text-[var(--txt-primary)]">{{ $totalUsers }}</p>
            <p class="text-[11px] text-[var(--txt-secondary)]">{{ __('Akun pengguna reguler') }}</p>
        </div>

        <div class="weisz-card p-5 space-y-1">
            <p class="text-xs font-medium text-[var(--txt-tertiary)]">{{ __('Aturan Pakar Aktif') }}</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $activeRulesCount }}</p>
            <p class="text-[11px] text-[var(--txt-secondary)]">{{ __('Rule expert system') }}</p>
        </div>

        <div class="weisz-card p-5 space-y-1">
            <p class="text-xs font-medium text-[var(--txt-tertiary)]">{{ __('Data Profesional') }}</p>
            <p class="text-2xl font-bold text-[var(--txt-primary)]">{{ $professionalsCount }}</p>
            <p class="text-[11px] text-[var(--txt-secondary)]">{{ __('Psikolog & Psikiater') }}</p>
        </div>

        <div class="weisz-card p-5 space-y-1">
            <p class="text-xs font-medium text-[var(--txt-tertiary)]">{{ __('Sinyal Krisis / Darurat') }}</p>
            <p class="text-2xl font-bold {{ $safetyAlertsCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}">{{ $safetyAlertsCount }}</p>
            <p class="text-[11px] text-[var(--txt-secondary)]">{{ __('Pemicu safety detector') }}</p>
        </div>
    </div>

    <!-- Topic Distribution & Safety Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="weisz-card p-6 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-[var(--text-primary)]">{{ __('Topik Paling Banyak Dibicarakan') }}</h2>
                <a href="{{ route('admin.categories') }}" class="text-xs font-medium text-[var(--text-accent)] hover:underline whitespace-nowrap">{{ __('Kelola') }} &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="clay-table">
                    <thead>
                        <tr>
                            <th>{{ __('Topik') }}</th>
                            <th>{{ __('Subkategori') }}</th>
                            <th class="text-right">{{ __('Jumlah Sesi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topicStats as $t)
                        <tr>
                            <td class="font-medium text-[var(--text-primary)] min-w-[180px]">{{ $t->name }}</td>
                            <td class="text-[var(--text-secondary)] whitespace-nowrap">{{ $t->subcategory->name ?? '-' }}</td>
                            <td class="text-right font-bold text-[var(--text-accent)] whitespace-nowrap">{{ $t->chat_sessions_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-[var(--text-muted)]">{{ __('Belum ada statistik topik.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="weisz-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[var(--color-error)] shrink-0" aria-hidden="true"></div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">{{ __('Pemicu Keselamatan (Safety Events)') }}</h2>
                </div>
                <a href="{{ route('admin.monitoring', ['filter' => 'safety']) }}" class="text-xs text-[var(--text-accent)] hover:underline">{{ __('Pantau Semua') }} &rarr;</a>
            </div>

            @if($recentSafetyEvents->isEmpty())
            <div class="p-8 text-center text-xs text-[var(--text-muted)]">
                {{ __('Tidak ada peristiwa pemicu keselamatan baru-baru ini. Semua obrolan berjalan normal.') }}
            </div>
            @else
            <div class="space-y-3">
                @foreach($recentSafetyEvents as $event)
                <div class="p-3.5 rounded-[16px] bg-[var(--color-error-bg)] border border-[var(--color-error-border)] shadow-[var(--clay-well)] space-y-1.5 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <p class="clay-pill-error uppercase">{{ __('Tingkat') }}: {{ $event->risk_level }}</p>
                        <time class="text-[11px] text-[var(--text-muted)]">{{ $event->triggered_at?->diffForHumans() }}</time>
                    </div>
                    <p class="text-[var(--text-secondary)]">{{ __('User') }}: <strong class="text-[var(--text-primary)]">{{ $event->session->user->name ?? __('Anonim') }}</strong> &bull; {{ __('Topik') }}: {{ $event->session->topic->name ?? '-' }}</p>
                    <p class="text-[11px] text-[var(--text-muted)] italic line-clamp-1">"{{ $event->trigger_text }}"</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="weisz-card p-6 space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-semibold text-[var(--text-primary)]">{{ __('Sesi Percakapan Terbaru') }}</h2>
            <a href="{{ route('admin.monitoring') }}" class="text-xs font-medium text-[var(--text-accent)] hover:underline whitespace-nowrap">{{ __('Lihat Semua di Monitoring') }} &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="clay-table">
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Kategori & Topik') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Mulai') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSessions as $s)
                    <tr>
                        <td class="font-semibold text-[var(--text-primary)] whitespace-nowrap">{{ $s->user->name ?? '-' }}</td>
                        <td class="min-w-[260px] max-w-lg">
                            <p class="font-medium text-[var(--text-primary)] leading-snug line-clamp-1" title="{{ $s->topic->name ?? '-' }}">
                                {{ $s->topic->name ?? '-' }}
                            </p>
                            <p class="text-[11px] text-[var(--text-muted)] mt-0.5 line-clamp-1" title="{{ $s->category->name ?? '-' }}">
                                {{ $s->category->name ?? '-' }}
                            </p>
                        </td>
                        <td class="whitespace-nowrap">
                            <p class="{{ $s->status === 'active' ? 'clay-pill-warning' : 'clay-pill-success' }}">
                                {{ $s->status }}
                            </p>
                        </td>
                        <td class="text-[var(--text-muted)] whitespace-nowrap">
                            <time>{{ $s->created_at->diffForHumans() }}</time>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('chat.show', $s->id) }}" class="text-[var(--text-accent)] font-semibold hover:underline">
                                {{ __('Buka Chat') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
