@extends('layouts.admin')

@section('title', __('17. Knowledge Base — Weisz Admin'))

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Knowledge Base Pakar') }}
            </h1>
            <p class="text-xs text-[var(--txt-secondary)] mt-0.5">
                {{ __('Kelola basis pengetahuan, bukti referensi psikologi, dan materi coping terstruktur yang digunakan sistem.') }}
            </p>
        </div>
    </div>

    <!-- Add Knowledge Base Form -->
    <div class="weisz-card p-6 space-y-4">
        <h2 class="text-sm font-bold text-[var(--txt-primary)]">{{ __('Tambah Entri Knowledge Base Baru') }}</h2>

        <form method="POST" action="{{ route('admin.knowledge.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Judul Materi / Konsep') }}</label>
                    <input type="text" name="title" required placeholder="{{ __('Contoh: Intervensi Langkah Mikro untuk Prokrastinasi') }}" class="weisz-input w-full text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Status Publikasi') }}</label>
                    <select name="status" class="weisz-input w-full text-xs">
                        <option value="published">{{ __('Published (Aktif Digunakan)') }}</option>
                        <option value="draft">{{ __('Draft (Disimpan Sementara)') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Kategori Terkait (Opsional)') }}</label>
                    <select name="category_id" class="weisz-input w-full text-xs">
                        <option value="">{{ __('-- Semua Kategori / Umum --') }}</option>
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Topik Terkait (Opsional)') }}</label>
                    <select name="topic_id" class="weisz-input w-full text-xs">
                        <option value="">{{ __('-- Semua Topik / Umum --') }}</option>
                        @foreach($topics as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Konten Pengetahuan / Panduan Pakar') }}</label>
                <textarea name="content" rows="3" required placeholder="{{ __('Tuliskan materi pemahaman, mekanisme koping, atau batasan refleksi...') }}" class="weisz-input w-full text-xs leading-relaxed"></textarea>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Sumber Referensi Ilmiah / Buku / Organisasi') }}</label>
                <input type="text" name="reference_source" placeholder="{{ __('Contoh: APA Guidelines on Adolescent Stress & Weisz Lab (2017)') }}" class="weisz-input w-full text-xs">
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="weisz-btn-primary py-2 px-5 text-xs font-semibold">
                    {{ __('Simpan ke Knowledge Base') }}
                </button>
            </div>
        </form>
    </div>

    <div class="weisz-card p-6 space-y-4">
        <h2 class="text-sm font-semibold text-[var(--text-primary)]">{{ __('Daftar Entri Knowledge Base') }}</h2>

        <div class="overflow-x-auto">
            <table class="clay-table">
                <thead>
                    <tr>
                        <th>{{ __('Judul & Referensi') }}</th>
                        <th>{{ __('Kategori / Topik') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($knowledgeItems as $item)
                    <tr>
                        <td class="min-w-[260px] max-w-md space-y-1">
                            <p class="font-semibold text-[var(--text-primary)]">{{ $item->title }}</p>
                            <p class="text-[11px] text-[var(--text-secondary)] line-clamp-2 leading-relaxed">{{ $item->content }}</p>
                            @if($item->reference_source)
                            <p class="text-[11px] text-[var(--text-accent)] italic">Ref: {{ $item->reference_source }}</p>
                            @endif
                        </td>
                        <td class="min-w-[180px] text-[var(--text-secondary)]">
                            <p class="font-medium text-[var(--text-primary)]">{{ $item->category->name ?? __('Semua Kategori') }}</p>
                            <p class="text-[11px] text-[var(--text-muted)] mt-0.5">{{ $item->topic->name ?? __('Semua Topik') }}</p>
                        </td>
                        <td class="whitespace-nowrap">
                            <p class="{{ $item->status === 'published' ? 'clay-pill-success' : 'clay-pill-well' }}">
                                {{ $item->status }}
                            </p>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.knowledge.destroy', $item->id) }}" onsubmit="return confirm('{{ __('Hapus entri knowledge base ini?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-[var(--color-error)] hover:underline cursor-pointer">
                                    {{ __('Hapus') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-xs text-[var(--text-muted)]">
                            {{ __('Belum ada entri knowledge base yang tercatat.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4">
            {{ $knowledgeItems->links() }}
        </div>
    </div>
</div>
@endsection
