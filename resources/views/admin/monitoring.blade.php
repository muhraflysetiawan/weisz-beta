@extends('layouts.admin')

@section('title', __('20. Monitoring Percakapan & Keselamatan'))

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--txt-primary)]">
                {{ __('Monitoring Percakapan & Keselamatan') }}
            </h1>
            <p class="text-xs text-[var(--txt-secondary)] mt-0.5">
                {{ __('Pengawasan sesi aktif, sinyal yang terdeteksi oleh expert system, dan log penanganan sinyal krisis.') }}
            </p>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('admin.monitoring') }}" class="px-3 py-1.5 rounded-xl border {{ !request('filter') ? 'bg-[var(--brand-main)]/10 text-[var(--brand-main)] font-semibold border-[var(--brand-main)]' : 'border-[var(--border-color)] text-[var(--txt-secondary)]' }}">
                {{ __('Semua Sesi') }}
            </a>
            <a href="{{ route('admin.monitoring', ['filter' => 'safety']) }}" class="px-3 py-1.5 rounded-xl border {{ request('filter') === 'safety' ? 'bg-red-50 text-red-600 font-semibold border-red-300' : 'border-[var(--border-color)] text-[var(--txt-secondary)]' }}">
                {{ __('Hanya Sinyal Krisis') }}
            </a>
        </div>
    </div>

    <!-- Safety Alerts Log Table -->
    @if($safetyAssessments->isNotEmpty())
    <div class="weisz-card p-6 space-y-4 border-red-200 dark:border-red-900/40">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></span>
            <h2 class="text-sm font-bold text-[var(--txt-primary)]">{{ __('Log Protokol Keselamatan (Crisis Alerts)') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[var(--border-color)] text-[var(--txt-tertiary)]">
                        <th class="pb-2.5 font-medium">{{ __('Waktu') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('User & Sesi') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Teks Pemicu') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Tindakan Sistem') }}</th>
                        <th class="pb-2.5 font-medium text-right">{{ __('Tingkat Risiko') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-color)]">
                    @foreach($safetyAssessments as $sa)
                    <tr>
                        <td class="py-3 text-[var(--txt-tertiary)]">{{ $sa->triggered_at?->format('d M, H:i') }}</td>
                        <td class="py-3">
                            <p class="font-semibold text-[var(--txt-primary)]">{{ $sa->session->user->name ?? '-' }}</p>
                            <p class="text-[11px] text-[var(--txt-secondary)]">{{ $sa->session->topic->name ?? '-' }}</p>
                        </td>
                        <td class="py-3 max-w-xs font-mono text-[11px] text-red-700 dark:text-red-400">
                            "{{ $sa->trigger_text }}"
                        </td>
                        <td class="py-3 max-w-sm text-[11px] text-[var(--txt-secondary)]">
                            {{ $sa->safety_action }}
                        </td>
                        <td class="py-3 text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-400">
                                {{ $sa->risk_level }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- All Sessions Log Table -->
    <div class="weisz-card p-6 space-y-4">
        <h2 class="text-sm font-bold text-[var(--txt-primary)]">{{ __('Log Aktivitas Percakapan') }}</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[var(--border-color)] text-[var(--txt-tertiary)]">
                        <th class="pb-2.5 font-medium">{{ __('ID Sesi') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('User') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Topik Terkunci') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Fase Percakapan') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Sinyal Expert System') }}</th>
                        <th class="pb-2.5 font-medium">{{ __('Waktu') }}</th>
                        <th class="pb-2.5 font-medium text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-color)]">
                    @forelse($sessions as $s)
                    <tr>
                        <td class="py-3 font-mono text-[var(--txt-tertiary)]">#{{ $s->id }}</td>
                        <td class="py-3 font-semibold text-[var(--txt-primary)]">{{ $s->user->name ?? __('Anonim') }}</td>
                        <td class="py-3 text-[var(--txt-secondary)]">
                            <p class="font-medium text-[var(--txt-primary)]">{{ $s->topic->name ?? '-' }}</p>
                            <p class="text-[10px] text-[var(--txt-tertiary)]">{{ $s->category->name ?? '-' }}</p>
                        </td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#252525]/10 dark:bg-white/10 text-[var(--brand-main)] border border-[var(--border-color)]">
                                {{ $s->context?->current_state ?? 'ACTIVE' }}
                            </span>
                        </td>
                        <td class="py-3 max-w-xs text-[10px] text-[var(--txt-tertiary)] font-mono">
                            @if(!empty($s->context?->signals))
                                {{ json_encode($s->context->signals) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-3 text-[var(--txt-tertiary)]">{{ $s->created_at->diffForHumans() }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('chat.show', $s->id) }}" class="text-[var(--brand-main)] font-semibold hover:underline">
                                {{ __('Buka') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-xs text-[var(--txt-tertiary)]">
                            {{ __('Belum ada percakapan yang sesuai kriteria filter.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
@endsection
