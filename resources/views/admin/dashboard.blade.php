@extends('layouts.admin')

@section('title', __('Admin Dashboard') . ' — Weisz')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--txt-primary)]">
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
        <!-- Topic Distribution Table -->
        <div class="weisz-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-[var(--txt-primary)]">{{ __('Topik Paling Banyak Dibicarakan') }}</h2>
                <a href="{{ route('admin.categories') }}" class="text-xs text-[var(--brand-main)] hover:underline">{{ __('Kelola') }} &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[var(--border-color)] text-[var(--txt-tertiary)]">
                            <th class="pb-2.5 font-medium">{{ __('Topik') }}</th>
                            <th class="pb-2.5 font-medium">{{ __('Subkategori') }}</th>
                            <th class="pb-2.5 font-medium text-right">{{ __('Jumlah Sesi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]">
                        @forelse($topicStats as $t)
                        <tr>
                            <td class="py-3 font-medium text-[var(--txt-primary)]">{{ $t->name }}</td>
                            <td class="py-3 text-[var(--txt-secondary)]">{{ $t->subcategory->name ?? '-' }}</td>
                            <td class="py-3 text-right font-bold text-[var(--brand-main)]">{{ $t->chat_sessions_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-[var(--txt-tertiary)]">{{ __('Belum ada statistik topik.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Safety Alerts -->
        <div class="weisz-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    <h2 class="text-sm font-bold text-[var(--txt-primary)]">{{ __('Pemicu Keselamatan (Safety Events)') }}</h2>
                </div>
                <a href="{{ route('admin.monitoring', ['filter' => 'safety']) }}" class="text-xs text-[var(--brand-main)] hover:underline">{{ __('Pantau Semua') }} &rarr;</a>
            </div>

            @if($recentSafetyEvents->isEmpty())
            <div class="p-8 text-center text-xs text-[var(--txt-tertiary)]">
                {{ __('Tidak ada peristiwa pemicu keselamatan baru-baru ini. Semua obrolan berjalan normal.') }}
            </div>
            @else
            <div class="space-y-3">
                @foreach($recentSafetyEvents as $event)
                <div class="p-3.5 rounded-xl bg-red-50/60 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 space-y-1 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-red-700 dark:text-red-400 uppercase tracking-wide text-[10px]">{{ __('Tingkat') }}: {{ $event->risk_level }}</span>
                        <span class="text-[11px] text-[var(--txt-tertiary)]">{{ $event->triggered_at?->diffForHumans() }}</span>
                    </div>
                    <p class="text-[var(--txt-secondary)]">{{ __('User') }}: <strong class="text-[var(--txt-primary)]">{{ $event->session->user->name ?? __('Anonim') }}</strong> &bull; {{ __('Topik') }}: {{ $event->session->topic->name ?? '-' }}</p>
                    <p class="text-[11px] text-[var(--txt-tertiary)] italic line-clamp-1">"{{ $event->trigger_text }}"</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Sessions -->
    <div class="weisz-card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-[var(--txt-primary)]">{{ __('Sesi Percakapan Terbaru') }}</h2>
            <a href="{{ route('admin.monitoring') }}" class="text-xs text-[var(--brand-main)] hover:underline">{{ __('Lihat Semua di Monitoring') }} &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[var(--border-color)] text-[var(--txt-tertiary)]">
                        <th class="pb-2.5 font-medium">{{ __('User') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Kategori & Topik') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Status') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Mulai') }}</th>
                        <th class="pb-2.5 font-medium text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-color)]">
                    @foreach($recentSessions as $s)
                    <tr>
                        <td class="py-3 font-semibold text-[var(--txt-primary)]">{{ $s->user->name ?? '-' }}</td>
                        <td class="py-3 text-[var(--txt-secondary)]">{{ $s->category->name ?? '-' }} &bull; {{ $s->topic->name ?? '-' }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $s->status === 'active' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400' }}">
                                {{ $s->status }}
                            </span>
                        </td>
                        <td class="py-3 text-[var(--txt-tertiary)]">{{ $s->created_at->diffForHumans() }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('chat.show', $s->id) }}" class="text-[var(--brand-main)] font-semibold hover:underline">
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
