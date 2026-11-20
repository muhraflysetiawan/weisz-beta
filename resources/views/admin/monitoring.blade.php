@extends('layouts.admin')

@section('title', __('20. Monitoring Percakapan & Keselamatan'))

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-clay)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Monitoring Percakapan & Keselamatan') }}
            </h1>
            <p class="text-xs sm:text-sm text-[var(--text-secondary)] mt-0.5">
                {{ __('Pengawasan sesi aktif, sinyal yang terdeteksi oleh expert system, dan log penanganan sinyal krisis.') }}
            </p>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('admin.monitoring') }}" class="px-3.5 py-2 rounded-[14px] border transition {{ !request('filter') ? 'bg-[var(--surface-clay-blue)] text-[var(--text-accent)] font-semibold border-[var(--sky-500)]' : 'border-[var(--border-clay)] text-[var(--text-secondary)] hover:bg-[var(--surface-clay-hover)]' }}">
                {{ __('Semua Sesi') }}
            </a>
            <a href="{{ route('admin.monitoring', ['filter' => 'safety']) }}" class="px-3.5 py-2 rounded-[14px] border transition {{ request('filter') === 'safety' ? 'bg-[var(--color-error-bg)] text-[var(--color-error)] font-semibold border-[var(--color-error-border)]' : 'border-[var(--border-clay)] text-[var(--text-secondary)] hover:bg-[var(--surface-clay-hover)]' }}">
                {{ __('Hanya Sinyal Krisis') }}
            </a>
        </div>
    </div>

    @if($safetyAssessments->isNotEmpty())
    <div class="clay-card-static p-6 space-y-4 border-[var(--color-error-border)]">
        <div class="flex items-center gap-2.5">
            <div class="w-2.5 h-2.5 rounded-full bg-[var(--color-error)] shrink-0" aria-hidden="true"></div>
            <h2 class="text-sm font-semibold text-[var(--text-primary)]">{{ __('Log Protokol Keselamatan (Crisis Alerts)') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="clay-table">
                <thead>
                    <tr>
                        <th>{{ __('Waktu') }}</th>
                        <th>{{ __('User & Sesi') }}</th>
                        <th>{{ __('Teks Pemicu') }}</th>
                        <th>{{ __('Tindakan Sistem') }}</th>
                        <th>{{ __('Tingkat Risiko') }}</th>
                        <th class="text-right">{{ __('Status Tinjauan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($safetyAssessments as $sa)
                    <tr>
                        <td class="text-[var(--text-muted)] whitespace-nowrap"><time>{{ $sa->triggered_at?->format('d M, H:i') }}</time></td>
                        <td class="min-w-[160px]">
                            <p class="font-semibold text-[var(--text-primary)] whitespace-nowrap">{{ $sa->session->user->name ?? '-' }}</p>
                            <p class="text-[11px] text-[var(--text-secondary)] line-clamp-1">{{ $sa->session->topic->name ?? '-' }}</p>
                        </td>
                        <td class="min-w-[180px] max-w-xs font-mono text-[11px] text-[var(--color-error)]">
                            "{{ $sa->trigger_text }}"
                        </td>
                        <td class="min-w-[200px] max-w-sm text-[11px] text-[var(--text-secondary)]">
                            {{ $sa->safety_action }}
                        </td>
                        <td class="whitespace-nowrap">
                            <p class="clay-pill-error uppercase">
                                {{ $sa->risk_level }}
                            </p>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            @if($sa->is_acknowledged)
                            <p class="clay-pill-success">
                                {{ __('Sudah Ditinjau') }}
                            </p>
                            @else
                            <form method="POST" action="{{ route('admin.safety.acknowledge', $sa) }}" class="inline">
                                @csrf
                                <button type="submit" class="weisz-btn-secondary text-[11px] min-h-[34px] py-1 px-3 whitespace-nowrap">
                                    {{ __('Tandai Ditinjau') }}
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="clay-card-static p-6 space-y-4">
        <h2 class="text-sm font-semibold text-[var(--text-primary)]">{{ __('Log Aktivitas Percakapan') }}</h2>

        <div class="overflow-x-auto">
            <table class="clay-table">
                <thead>
                    <tr>
                        <th>{{ __('ID Sesi') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Topik Terkunci') }}</th>
                        <th>{{ __('Mood') }}</th>
                        <th>{{ __('Fase Percakapan') }}</th>
                        <th>{{ __('Sinyal Expert System') }}</th>
                        <th>{{ __('Waktu') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $s)
                    <tr>
                        <td class="font-mono text-[var(--text-muted)] whitespace-nowrap">#{{ $s->id }}</td>
                        <td class="font-semibold text-[var(--text-primary)] whitespace-nowrap">{{ $s->user->name ?? __('Anonim') }}</td>
                        <td class="min-w-[220px] max-w-sm">
                            <p class="font-medium text-[var(--text-primary)] line-clamp-1" title="{{ $s->topic->name ?? '-' }}">{{ $s->topic->name ?? '-' }}</p>
                            <p class="text-[11px] text-[var(--text-muted)] line-clamp-1" title="{{ $s->category->name ?? '-' }}">{{ $s->category->name ?? '-' }}</p>
                        </td>
                        <td class="whitespace-nowrap">
                            @if($s->mood_label)
                            <p class="clay-pill-blue text-[11px]">
                                {{ $s->mood_label }} ({{ $s->mood_score }}/5)
                            </p>
                            @else
                            <p class="text-[var(--text-muted)]">-</p>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            <p class="clay-pill-well text-[11px] text-[var(--text-accent)]">
                                {{ $s->context?->current_state ?? 'ACTIVE' }}
                            </p>
                        </td>
                        <td class="min-w-[160px] max-w-xs text-[11px] text-[var(--text-muted)] font-mono">
                            @if(!empty($s->context?->signals))
                                <p class="line-clamp-2" title="{{ json_encode($s->context->signals) }}">{{ json_encode($s->context->signals) }}</p>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-[var(--text-muted)] whitespace-nowrap"><time>{{ $s->created_at->diffForHumans() }}</time></td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('chat.show', $s->id) }}" class="text-[var(--text-accent)] font-semibold hover:underline">
                                {{ __('Buka') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-xs text-[var(--text-muted)]">
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
