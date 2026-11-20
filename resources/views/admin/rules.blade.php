@extends('layouts.admin')

@section('title', __('18. Manajemen Aturan Expert System'))

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-clay)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Manajemen Aturan Expert System') }}
            </h1>
            <p class="text-xs sm:text-sm text-[var(--text-secondary)] mt-0.5">
                {{ __('Kelola logika IF-THEN, bobot certainty factor (CF), kondisi sinyal emosi, dan tindakan otomatis sistem.') }}
            </p>
        </div>
    </div>

    <div class="clay-card-static p-6 space-y-4">
        <h2 class="text-sm font-semibold text-[var(--text-primary)]">{{ __('Tambah Aturan Expert System Baru') }}</h2>

        <form method="POST" action="{{ route('admin.rules.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-semibold text-[var(--text-secondary)]">{{ __('Nama Aturan') }}</label>
                    <input type="text" name="name" required placeholder="{{ __('Contoh: Tekanan Skripsi Kronis + Insomnia') }}" class="weisz-input w-full text-xs">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--text-secondary)]">{{ __('Prioritas (0 - 100)') }}</label>
                    <input type="number" name="priority" value="10" required class="weisz-input w-full text-xs">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--text-secondary)]">{{ __('Confidence Factor (0.00 - 1.00)') }}</label>
                    <input type="number" name="confidence_factor" value="0.80" step="0.05" min="0" max="1" required class="weisz-input w-full text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Topik Terkait') }}</label>
                    <select name="topic_id" class="weisz-input w-full text-xs">
                        <option value="">{{ __('-- Aturan Global (Semua Topik) --') }}</option>
                        @foreach($topics as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->subcategory->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Tindakan Sistem (Action Type)') }}</label>
                    <select name="action_type" required class="weisz-input w-full text-xs">
                        <option value="suggest_professional">{{ __('suggest_professional (Rekomendasikan Psikolog)') }}</option>
                        <option value="provide_chips">{{ __('provide_chips (Tampilkan Chip Bantuan Saat Stuck)') }}</option>
                        <option value="trigger_safety">{{ __('trigger_safety (Aktivasi Protokol Krisis)') }}</option>
                        <option value="prompt_directive">{{ __('prompt_directive (Instruksi Tambahan AI)') }}</option>
                    </select>
                </div>
            </div>

            <div class="p-4 rounded-[18px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)] space-y-3">
                <p class="text-xs font-semibold text-[var(--text-primary)]">{{ __('Kondisi Logika IF (Single Condition)') }}</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Fact Key (Sinyal)') }}</label>
                        <input type="text" name="condition_fact_key" required placeholder="academic_pressure" class="weisz-input w-full text-xs bg-[var(--card-surface)]">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Operator') }}</label>
                        <select name="condition_operator" class="weisz-input w-full text-xs bg-[var(--card-surface)]">
                            <option value="==">{{ __('Sama dengan (==)') }}</option>
                            <option value="!=">{{ __('Tidak sama dengan (!=)') }}</option>
                            <option value=">=">{{ __('Lebih besar sama dengan (>=)') }}</option>
                            <option value="contains">{{ __('Mengandung kata (contains)') }}</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Nilai Sinyal (Fact Value)') }}</label>
                        <input type="text" name="condition_fact_value" required placeholder="high / true" class="weisz-input w-full text-xs bg-[var(--card-surface)]">
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Petunjuk Tambahan AI / Directive Payload') }}</label>
                <textarea name="action_directive" rows="2" placeholder="{{ __('Instruksi khusus bagi AI saat aturan ini terpenuhi...') }}" class="weisz-input w-full text-xs"></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="weisz-btn-primary py-2 px-5 text-xs font-semibold">
                    {{ __('Simpan Aturan Pakar') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Active Rules List -->
    <div class="weisz-card p-6 space-y-4">
        <h2 class="text-sm font-bold text-[var(--txt-primary)]">{{ __('Daftar Aturan Expert System Terdaftar') }}</h2>

        <div class="space-y-4">
            @foreach($rules as $rule)
            <div class="p-4 rounded-xl border {{ $rule->is_active ? 'border-[var(--border-color)] bg-[var(--card-surface)]' : 'border-dashed border-gray-300 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30 opacity-75' }} space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="clay-badge-num w-8 h-8 text-xs shrink-0" title="{{ __('Prioritas') }}">
                            {{ $rule->priority }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-semibold text-sm text-[var(--text-primary)]">{{ $rule->name }}</h3>
                                <p class="clay-pill-well text-[10px] text-[var(--text-accent)]">
                                    CF {{ number_format((float) ($rule->confidence_factor ?? 0.80), 2) }}
                                </p>
                            </div>
                            <p class="text-[11px] text-[var(--text-muted)]">
                                {{ __('Cakupan') }}: {{ $rule->topic->name ?? __('Global (Semua Sesi)') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('admin.rules.toggle', $rule->id) }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold px-2.5 py-1 rounded-lg border {{ $rule->is_active ? 'border-emerald-200 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40' : 'border-gray-300 text-gray-500' }}">
                                {{ $rule->is_active ? __('Aktif') : __('Non-aktif') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.rules.destroy', $rule->id) }}" onsubmit="return confirm('{{ __('Hapus aturan expert system ini?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:underline">
                                {{ __('Hapus') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2 text-xs">
                    <div class="p-3.5 rounded-[14px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)] space-y-1">
                        <p class="font-semibold text-[11px] text-[var(--text-muted)]">{{ __('Kondisi (IF)') }}</p>
                        @foreach($rule->conditions as $cond)
                        <p class="text-[var(--text-primary)] font-mono text-[11px]">
                            {{ $cond->fact_key }} {{ $cond->operator }} "{{ $cond->fact_value }}"
                        </p>
                        @endforeach
                    </div>

                    <div class="p-3.5 rounded-[14px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)] space-y-1">
                        <p class="font-semibold text-[11px] text-[var(--text-muted)]">{{ __('Aksi Sistem (THEN)') }}</p>
                        @foreach($rule->actions as $act)
                        <p class="text-[var(--text-accent)] font-semibold text-[11px]">
                            &rarr; {{ $act->action_type }}
                        </p>
                        @if(!empty($act->action_payload['directive']))
                        <p class="text-[10px] text-[var(--text-secondary)] line-clamp-1 italic">"{{ $act->action_payload['directive'] }}"</p>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
