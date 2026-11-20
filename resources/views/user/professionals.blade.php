@extends('layouts.app')

@section('title', '12. ' . __('Daftar Tenaga Ahli Profesional') . ' | Weisz')

@section('content')
<div class="max-w-6xl mx-auto py-6 sm:py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--border-clay)] pb-4">
        <div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">
                {{ __('Daftar Psikolog & Psikiater Terverifikasi') }}
            </h1>
            <p class="text-xs sm:text-sm text-[var(--text-secondary)] mt-1">
                {{ __('Rujukan tenaga ahli di Kabupaten Karawang, Bekasi, dan Purwakarta untuk evaluasi klinis dan pendampingan kesehatan mental.') }}
            </p>
        </div>

        <a href="{{ route('chat.index') }}" class="weisz-btn-secondary text-xs py-2 px-3.5 self-start sm:self-center">
            &larr; {{ __('Ruang Curhat') }}
        </a>
    </div>

    <form method="GET" action="{{ route('professionals') }}" class="flex items-center gap-2 max-w-lg">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="{{ __('Cari nama, kecamatan, desa, jalan poros, atau spesialisasi...') }}"
            class="weisz-input w-full text-xs py-2.5">
        <button type="submit" class="weisz-btn-primary py-2.5 px-4 text-xs font-semibold shrink-0">
            {{ __('Cari') }}
        </button>
        @if(request('search'))
        <a href="{{ route('professionals') }}" class="weisz-btn-secondary py-2.5 px-3 text-xs shrink-0">
            {{ __('Reset') }}
        </a>
        @endif
    </form>

    <div class="clay-card-blue p-4 sm:p-5 text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
        <strong class="font-semibold text-[var(--text-primary)]">{{ __('Transparansi Weisz:') }}</strong> {{ __('Seluruh data profesional di bawah ini murni sebagai rujukan kontak resmi yang terhubung langsung dengan koridor jalan utama 70 kecamatan (Karawang, Bekasi, Purwakarta) dan tidak melibatkan komisi konsultasi atau pemesanan melalui aplikasi Weisz.') }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($professionals as $pro)
        <div class="clay-card-static p-5 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <!-- Header: Foto / Avatar + Nama + Gelar + Profesi -->
                <div class="flex items-start gap-3.5">
                    <img src="{{ $pro->avatar_url }}" alt="{{ $pro->display_name }}" class="w-14 h-16 rounded-xl object-cover shrink-0 border border-[var(--border-color)] shadow-2xs group-hover:scale-105 transition-transform">
                    <div class="min-w-0 flex-1">
                        <h2 class="font-bold text-[15px] text-[var(--txt-primary)] leading-snug truncate" title="{{ $pro->name }}">
                            {{ $pro->display_name }}
                        </h2>
                        @if($pro->degree)
                        <p class="text-[11.5px] font-medium text-[var(--txt-secondary)] mt-0.5 truncate" title="{{ $pro->degree }}">
                            {{ $pro->degree }}
                        </p>
                        @endif
                        <p class="clay-pill-blue text-[10px] mt-1.5 line-clamp-1">
                            {{ $pro->profession }}
                        </p>
                    </div>
                </div>

                <div class="pt-2 border-t border-[var(--border-clay)] flex flex-wrap gap-1.5">
                    @forelse($pro->specialization_tags as $tag)
                    <p class="clay-pill-well text-[10.5px] py-0.5 px-2.5">
                        {{ $tag }}
                    </p>
                    @empty
                    <p class="text-[10px] text-[var(--text-muted)]">-</p>
                    @endforelse
                </div>

                <div class="pt-2 border-t border-[var(--border-clay)] space-y-1">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-[var(--text-primary)]">
                        <svg class="w-3.5 h-3.5 text-[var(--text-accent)] shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        <p>{{ $pro->location ?: '-' }}</p>
                    </div>
                    @if($pro->address)
                    <p class="text-[11px] text-[var(--text-muted)] pl-5 leading-relaxed line-clamp-2" title="{{ $pro->address }}">
                        {{ $pro->address }}
                    </p>
                    @endif
                </div>
            </div>

            <div class="pt-3 border-t border-[var(--border-clay)] space-y-2">
                @if($pro->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pro->phone) }}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-[16px] bg-[var(--color-success)] hover:opacity-95 text-white shadow-[var(--clay-rest)] text-xs font-semibold transition">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.16 12.05 20.16C10.66 20.16 9.3 19.81 8.08 19.14L7.75 18.96L4.54 19.8L5.4 16.67L5.2 16.35C4.46 15.08 4.07 13.52 4.07 11.92C4.07 7.38 7.76 3.67 12.05 3.67M9.06 6.88C8.88 6.88 8.58 6.94 8.33 7.21C8.07 7.49 7.37 8.15 7.37 9.53C7.37 10.91 8.38 12.24 8.52 12.43C8.66 12.61 10.5 15.44 13.33 16.66C14 16.95 14.52 17.12 14.93 17.25C15.6 17.47 16.22 17.43 16.7 17.36C17.24 17.28 18.36 16.68 18.59 16.03C18.82 15.38 18.82 14.82 18.75 14.71C18.68 14.59 18.5 14.53 18.23 14.39C17.96 14.26 16.62 13.6 16.37 13.51C16.12 13.42 15.94 13.37 15.76 13.64C15.58 13.92 15.06 14.53 14.9 14.71C14.74 14.89 14.58 14.92 14.31 14.78C14.04 14.65 13.17 14.36 12.13 13.44C11.32 12.72 10.78 11.83 10.62 11.56C10.46 11.28 10.6 11.13 10.74 11C10.86 10.87 11.01 10.67 11.15 10.5C11.29 10.33 11.33 10.21 11.42 10.03C11.51 9.85 11.47 9.69 11.4 9.56C11.33 9.42 10.79 8.1 10.56 7.56C10.34 7.03 10.11 7.1 9.95 7.1L9.5 7.09C9.28 7.09 9.06 6.88 9.06 6.88Z" />
                    </svg>
                    Hubungi via WhatsApp
                </a>
                @endif

                <div class="flex items-center justify-between text-[11px] text-[var(--text-secondary)] pt-1">
                    @if($pro->phone)
                    <a href="tel:{{ $pro->phone }}" class="hover:underline flex items-center gap-1">
                        <svg class="w-3 h-3 text-[var(--text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $pro->phone }}
                    </a>
                    @endif
                    @if($pro->email)
                    <a href="mailto:{{ $pro->email }}" class="hover:underline truncate max-w-[150px] text-right ml-auto" title="{{ $pro->email }}">
                        {{ $pro->email }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 weisz-card p-12 text-center text-xs text-[var(--txt-secondary)]">
            {{ __('Tidak ditemukan data profesional yang sesuai kriteria pencarian.') }}
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $professionals->links() }}
    </div>
</div>
@endsection
