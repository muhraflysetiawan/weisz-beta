@extends('layouts.admin')

@section('title', __('19. Manajemen Data Profesional'))

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-color)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Manajemen Data Tenaga Profesional') }}
            </h1>
            <p class="text-xs text-[var(--text-secondary)] mt-0.5">
                {{ __('Kelola data rujukan psikolog dan psikiater berlisensi.') }} <strong class="font-semibold text-[var(--color-warning)]">{{ __('Catatan: Halaman ini bukan akun login untuk profesional.') }}</strong>
            </p>
        </div>
    </div>

    <!-- Add Professional Form -->
    <div class="weisz-card p-6 space-y-4">
        <h2 class="text-sm font-bold text-[var(--txt-primary)]">{{ __('Tambah Data Profesional Baru') }}</h2>

        <form method="POST" action="{{ route('admin.professionals.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Nama Lengkap & Gelar') }}</label>
                    <input type="text" name="name" required placeholder="{{ __('Contoh: dr. Amanda Pratama, Sp.KJ') }}" class="weisz-input w-full text-xs">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Profesi') }}</label>
                    <input type="text" name="profession" required placeholder="{{ __('Contoh: Psikolog Klinis Dewasa / Psikiater') }}" class="weisz-input w-full text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Fokus Spesialisasi') }}</label>
                    <input type="text" name="specialization" placeholder="{{ __('Contoh: Gangguan Cemas, Stres Akademik') }}" class="weisz-input w-full text-xs">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Wilayah / Kota Layanan') }}</label>
                    <input type="text" name="location" placeholder="{{ __('Contoh: Jakarta Selatan & Online') }}" class="weisz-input w-full text-xs">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Status') }}</label>
                    <select name="status" class="weisz-input w-full text-xs">
                        <option value="active">{{ __('Active (Tampil di Rekomendasi)') }}</option>
                        <option value="inactive">{{ __('Inactive (Disembunyikan)') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Nomor Telepon / WhatsApp Konsultasi') }}</label>
                    <input type="text" name="phone" placeholder="+62 812-xxxx-xxxx" class="weisz-input w-full text-xs">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Alamat Email Resmi') }}</label>
                    <input type="email" name="email" placeholder="kontak@klinik.id" class="weisz-input w-full text-xs">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Alamat Lengkap / Lokasi Praktik Fisik') }}</label>
                <input type="text" name="address" placeholder="{{ __('Nama klinik, jalan, gedung, nomor...') }}" class="weisz-input w-full text-xs">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Deskripsi Profil Singkat') }}</label>
                <textarea name="description" rows="2" placeholder="{{ __('Pengalaman, pendekatan terapi yang digunakan...') }}" class="weisz-input w-full text-xs"></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="weisz-btn-primary py-2 px-5 text-xs font-semibold">
                    {{ __('Simpan Data Profesional') }}
                </button>
            </div>
        </form>
    </div>

    <div class="weisz-card p-6 space-y-4">
        <h2 class="text-sm font-semibold text-[var(--text-primary)]">{{ __('Data Profesional Terdaftar') }}</h2>

        <div class="overflow-x-auto">
            <table class="clay-table">
                <thead>
                    <tr>
                        <th>{{ __('Nama & Profesi') }}</th>
                        <th>{{ __('Spesialisasi & Lokasi') }}</th>
                        <th>{{ __('Kontak') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($professionals as $pro)
                    <tr>
                        <td class="min-w-[180px]">
                            <p class="font-semibold text-[var(--text-primary)]">{{ $pro->name }}</p>
                            <p class="text-[11px] text-[var(--text-accent)] mt-0.5">{{ $pro->profession }}</p>
                        </td>
                        <td class="min-w-[200px] max-w-sm text-[var(--text-secondary)]">
                            <p class="line-clamp-1" title="{{ $pro->specialization ?: '-' }}">{{ $pro->specialization ?: '-' }}</p>
                            <p class="text-[11px] text-[var(--text-muted)] mt-0.5">{{ $pro->location ?: '-' }}</p>
                        </td>
                        <td class="whitespace-nowrap text-[var(--text-secondary)]">
                            <p>{{ $pro->phone ?: '-' }}</p>
                            <p class="text-[11px] text-[var(--text-muted)] mt-0.5">{{ $pro->email ?: '-' }}</p>
                        </td>
                        <td class="whitespace-nowrap">
                            <p class="{{ $pro->status === 'active' ? 'clay-pill-success' : 'clay-pill-well' }}">
                                {{ $pro->status }}
                            </p>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.professionals.destroy', $pro->id) }}" onsubmit="return confirm('{{ __('Hapus data profesional ini?') }}')">
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
                        <td colspan="5" class="py-8 text-center text-xs text-[var(--text-muted)]">
                            {{ __('Belum ada data profesional.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4">
            {{ $professionals->links() }}
        </div>
    </div>
</div>
@endsection
