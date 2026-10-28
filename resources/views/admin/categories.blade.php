@extends('layouts.admin')

@section('title', __('16. Manajemen Kategori & Topik'))

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--txt-primary)]">
                {{ __('Manajemen Hirarki Masalah') }}
            </h1>
            <p class="text-xs text-[var(--txt-secondary)] mt-0.5">
                {{ __('Kelola Kategori Utama &rarr; Subkategori &rarr; Topik Percakapan yang menjadi sumber konteks AI Weisz.') }}
            </p>
        </div>
    </div>

    <!-- Quick Add Forms Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Add Category Form -->
        <div class="weisz-card p-5 space-y-4">
            <h2 class="text-sm font-bold text-[var(--txt-primary)] flex items-center gap-1.5">
                <span class="w-5 h-5 rounded-md bg-[var(--brand-main)]/10 text-[var(--brand-main)] flex items-center justify-center text-xs">1</span>
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

        <!-- Add Subcategory Form -->
        <div class="weisz-card p-5 space-y-4">
            <h2 class="text-sm font-bold text-[var(--txt-primary)] flex items-center gap-1.5">
                <span class="w-5 h-5 rounded-md bg-[var(--brand-main)]/10 text-[var(--brand-main)] flex items-center justify-center text-xs">2</span>
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

        <!-- Add Topic Form -->
        <div class="weisz-card p-5 space-y-4">
            <h2 class="text-sm font-bold text-[var(--txt-primary)] flex items-center gap-1.5">
                <span class="w-5 h-5 rounded-md bg-[var(--brand-main)]/10 text-[var(--brand-main)] flex items-center justify-center text-xs">3</span>
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
                        <span class="w-8 h-8 rounded-xl bg-[#252525]/10 dark:bg-white/10 text-[var(--brand-main)] flex items-center justify-center font-bold text-xs">
                            K
                        </span>
                        <div>
                            <h3 class="font-bold text-base text-[var(--txt-primary)]">{{ $cat->name }}</h3>
                            <p class="text-xs text-[var(--txt-secondary)]">{{ $cat->description }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" onsubmit="return confirm('{{ __('Hapus kategori ini beserta seluruh subkategori & topiknya?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline">
                            {{ __('Hapus Kategori') }}
                        </button>
                    </form>
                </div>

                <!-- Subcategories & Topics -->
                <div class="pl-4 sm:pl-8 space-y-3 border-l-2 border-[var(--border-color)]">
                    @forelse($cat->subcategories as $sub)
                    <div class="p-3.5 rounded-xl bg-[var(--card-secondary)] border border-[var(--border-color)] space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-xs text-[var(--txt-primary)]">{{ __('Subkategori') }}: {{ $sub->name }}</span>
                            
                            <form method="POST" action="{{ route('admin.subcategories.destroy', $sub->id) }}" onsubmit="return confirm('{{ __('Hapus subkategori ini?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[11px] text-red-500 hover:underline">{{ __('Hapus') }}</button>
                            </form>
                        </div>

                        <!-- Topics -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            @forelse($sub->topics as $topic)
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[var(--card-surface)] border border-[var(--border-color)] text-xs">
                                <span class="font-medium text-[var(--txt-primary)]">{{ $topic->name }}</span>
                                <form method="POST" action="{{ route('admin.topics.destroy', $topic->id) }}" class="inline" onsubmit="return confirm('{{ __('Hapus topik ini?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] text-gray-400 hover:text-red-500">&times;</button>
                                </form>
                            </div>
                            @empty
                            <span class="text-[11px] text-[var(--txt-tertiary)] italic">{{ __('Belum ada topik di subkategori ini.') }}</span>
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
