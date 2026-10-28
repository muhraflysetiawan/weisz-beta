@extends('layouts.app')

@section('title', '14. ' . __('Profil & Pengaturan') . ' — Weisz')

@section('content')
<div class="max-w-2xl mx-auto py-6 sm:py-8 space-y-6">
    <!-- Back to Chat -->
    <div>
        <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--txt-secondary)] hover:text-[var(--txt-primary)] transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            {{ __('Kembali ke Ruang Obrolan') }}
        </a>
    </div>

    <!-- Profile Card -->
    <div class="weisz-card p-6 sm:p-8 space-y-6">
        <!-- a. Informasi Akun -->
        <div class="flex items-center gap-4 sm:gap-5 pb-6 border-b border-[var(--border-color)]">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#252525] text-white dark:bg-white dark:text-[#252525] flex items-center justify-center font-extrabold text-2xl sm:text-3xl shadow-md ring-4 ring-[var(--card-secondary)] shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[var(--txt-primary)] truncate">
                    {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] truncate mt-0.5">
                    {{ $user->email }}
                </p>
            </div>
        </div>

        <!-- b. Ubah Nama Akun (Button Save Change di sebelah input) -->
        <div class="space-y-3">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[var(--txt-primary)]">
                    {{ __('Ubah Nama Akun') }}
                </h2>
                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] mt-0.5 leading-relaxed">
                    {{ __('Perbarui nama akun atau nama panggilan tampilanmu.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">
                @csrf
                @method('PUT')

                <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                        placeholder="{{ __('Masukkan nama akun...') }}" aria-label="{{ __('Nama Akun') }}"
                        class="weisz-input flex-1 h-11 sm:h-12 px-4 leading-normal">

                    <button type="submit" class="weisz-btn-primary shrink-0 h-11 sm:h-12 px-5 text-xs sm:text-sm font-bold inline-flex items-center justify-center">
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- c. Log Out (Dipindahkan tepat setelah Ubah Nama Akun) -->
        <div class="pt-6 border-t border-[var(--border-color)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-[var(--txt-primary)]">{{ __('Log Out') }}</h3>
                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] mt-0.5">
                    {{ __('Keluar dari sesi akun ini dengan aman.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-950/70 border border-red-200 dark:border-red-900/40 transition flex items-center justify-center gap-2 cursor-pointer shadow-2xs" title="{{ __('Log Out') }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    <span>{{ __('Log Out') }}</span>
                </button>
            </form>
        </div>

        <!-- d. Pengaturan Bahasa Aplikasi (Dropdown di samping text) -->
        <div class="pt-6 border-t border-[var(--border-color)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-6">
            <div class="space-y-0.5">
                <h2 class="text-base sm:text-lg font-bold text-[var(--txt-primary)]">
                    {{ __('Bahasa Aplikasi') }}
                </h2>
                <p class="text-xs sm:text-sm text-[var(--txt-secondary)] leading-relaxed">
                    {{ __('Pilih bahasa tampilan antarmuka sistem. Pesan percakapan AI akan tetap merespons secara otomatis sesuai bahasa ceritamu.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('locale.update') }}" class="shrink-0">
                @csrf
                <div class="relative min-w-[200px]">
                    <select name="locale" onchange="this.form.submit()" class="w-full appearance-none pl-4 sm:pl-5 pr-11 sm:pr-12 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl bg-[var(--card-secondary)] border border-[var(--border-color)] text-xs sm:text-sm font-semibold text-[var(--txt-primary)] focus:outline-none focus:border-[#252525] dark:focus:border-white focus:ring-2 focus:ring-[#252525]/10 dark:focus:ring-white/10 transition cursor-pointer shadow-2xs">
                        <option value="id" {{ app()->getLocale() === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 sm:pr-5 text-[var(--txt-secondary)]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
