@extends('layouts.admin')

@section('title', __('16. Manajemen Kategori & Topik'))

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Manajemen Hirarki Masalah') }}
            </h1>
            <p class="text-xs text-[var(--txt-secondary)] mt-0.5">
                {{ __('Kelola Kategori Utama &rarr; Subkategori &rarr; Topik Percakapan yang menjadi sumber konteks AI Weisz.') }}
            </p>
        </div>
    </div>

    <!-- Quick Add Forms Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="weisz-card p-5 space-y-4">
            <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                <div class="clay-badge-num w-6 h-6 text-xs">1</div>
                {{ __('Tambah Kategori Utama') }}
            </h2>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3">
                @csrf
                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Nama Kategori') }}</label>
                    <input type="text" name="name" required placeholder="{{ __('Contoh: Finansial & Karier') }}" class="weisz-input w-full text-xs py-2">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Deskripsi Singkat') }}</label>
                    <textarea name="description" rows="2" placeholder="{{ __('Penjelasan masalah yang dicakup...') }}" class="weisz-input w-full text-xs py-2"></textarea>
                </div>

                <button type="submit" class="weisz-btn-primary w-full py-2 text-xs font-semibold">
                    {{ __('Simpan Kategori') }}
                </button>
            </form>
        </div>

        <div class="weisz-card p-5 space-y-4">
            <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                <div class="clay-badge-num w-6 h-6 text-xs">2</div>
                {{ __('Tambah Subkategori') }}
            </h2>

            <form method="POST" action="{{ route('admin.subcategories.store') }}" class="space-y-3">
                @csrf
                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Pilih Kategori Induk') }}</label>
                    <select name="category_id" required class="weisz-input w-full text-xs py-2">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Nama Subkategori') }}</label>
                    <input type="text" name="name" required placeholder="{{ __('Contoh: Ekspektasi Karier') }}" class="weisz-input w-full text-xs py-2">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Deskripsi Singkat') }}</label>
                    <textarea name="description" rows="2" placeholder="{{ __('Penjelasan subkategori...') }}" class="weisz-input w-full text-xs py-2"></textarea>
                </div>

                <button type="submit" class="weisz-btn-primary w-full py-2 text-xs font-semibold">
                    {{ __('Simpan Subkategori') }}
                </button>
            </form>
        </div>

        <div class="weisz-card p-5 space-y-4">
            <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                <div class="clay-badge-num w-6 h-6 text-xs">3</div>
                {{ __('Tambah Topik Percakapan') }}
            </h2>

            <form method="POST" action="{{ route('admin.topics.store') }}" class="space-y-3">
                @csrf
                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Subkategori Induk') }}</label>
                    <select name="subcategory_id" required class="weisz-input w-full text-xs py-2">
                        @foreach($categories as $cat)
                            <optgroup label="{{ $cat->name }}">
                                @foreach($cat->subcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Nama Topik') }}</label>
                    <input type="text" name="name" required placeholder="{{ __('Contoh: Career Anxiety') }}" class="weisz-input w-full text-xs py-2">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Konteks AI (Prompt Directive)') }}</label>
                    <textarea name="prompt_context" rows="2" placeholder="{{ __('Panduan bagi AI saat topik ini terkunci...') }}" class="weisz-input w-full text-xs py-2"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-semibold text-[var(--txt-secondary)]">{{ __('Pilihan Chip Saat Bingung (Pisahkan Koma)') }}</label>
                    <input type="text" name="suggested_chips" placeholder="{{ __('Contoh: Takut nganggur, Bingung passion') }}" class="weisz-input w-full text-xs py-2">
                </div>

                <button type="submit" class="weisz-btn-primary w-full py-2 text-xs font-semibold">
                    {{ __('Simpan Topik') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Tree View of Categories, Subcategories & Topics -->
    <div class="space-y-4">
        <h2 class="text-base font-bold text-[var(--txt-primary)]">{{ __('Struktur Data Hirarki Saat Ini') }}</h2>

        <div class="space-y-4">
            @foreach($categories as $cat)
            <div class="weisz-card p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="clay-badge-num w-8 h-8 text-xs">
                            K
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-[var(--text-primary)]">{{ $cat->name }}</h3>
                            <p class="text-xs text-[var(--text-secondary)]">{{ $cat->description }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" onsubmit="return confirm('{{ __('Hapus kategori ini beserta seluruh subkategori & topiknya?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-[var(--color-error)] hover:underline">
                            {{ __('Hapus Kategori') }}
                        </button>
                    </form>
                </div>

                <div class="pl-4 sm:pl-8 space-y-3 border-l-2 border-[var(--border-clay)]">
                    @forelse($cat->subcategories as $sub)
                    <div class="p-3.5 rounded-[16px] bg-[var(--surface-well)] border border-[var(--border-clay)] shadow-[var(--clay-well)] space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-xs text-[var(--text-primary)]">{{ __('Subkategori') }}: {{ $sub->name }}</p>
                            
                            <form method="POST" action="{{ route('admin.subcategories.destroy', $sub->id) }}" onsubmit="return confirm('{{ __('Hapus subkategori ini?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[11px] text-[var(--color-error)] hover:underline">{{ __('Hapus') }}</button>
                            </form>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-1">
                            @forelse($sub->topics as $topic)
                            <div class="clay-pill text-xs">
                                <p class="font-medium text-[var(--text-primary)]">{{ $topic->name }}</p>
                                <form method="POST" action="{{ route('admin.topics.destroy', $topic->id) }}" class="inline" onsubmit="return confirm('{{ __('Hapus topik ini?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] text-[var(--text-muted)] hover:text-[var(--color-error)]">&times;</button>
                                </form>
                            </div>
                            @empty
                            <p class="text-[11px] text-[var(--text-muted)] italic">{{ __('Belum ada topik di subkategori ini.') }}</p>
                            @endforelse
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-[var(--txt-tertiary)] italic">{{ __('Belum ada subkategori.') }}</p>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
