@extends('layouts.app')

@section('title', __('Buat Akun Weisz'))

@section('content')
<div class="max-w-md mx-auto py-8 sm:py-16">
    <div class="weisz-card p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-10 h-10 rounded-[14px] bg-[var(--sky-600)] text-white dark:bg-[var(--sky-400)] dark:text-[#121821] shadow-[var(--clay-btn-primary)] flex items-center justify-center font-bold text-xl mx-auto">
                W
            </div>
            <h1 class="font-serif-display text-2xl sm:text-3xl font-medium tracking-tight text-[var(--text-primary)]">{{ __('Buat Akun Weisz') }}</h1>
            <p class="text-xs sm:text-sm text-[var(--text-secondary)]">{{ __('Mulai perjalanan memahami emosi dan mengurai stres harianmu.') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="name" class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Nama Panggilan') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="{{ __('Contoh: Alex') }}" class="weisz-input w-full @error('name') weisz-input-error @enderror"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')
                <p id="name-error" class="weisz-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="email" class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Alamat Email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    placeholder="nama@email.com" class="weisz-input w-full @error('email') weisz-input-error @enderror"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                <p id="email-error" class="weisz-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password" class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Kata Sandi') }}</label>
                <input id="password" type="password" name="password" required
                    placeholder="{{ __('Minimal 8 karakter') }}" class="weisz-input w-full @error('password') weisz-input-error @enderror"
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')
                <p id="password-error" class="weisz-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Konfirmasi Kata Sandi') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    placeholder="{{ __('Ulangi kata sandi') }}" class="weisz-input w-full @error('password_confirmation') weisz-input-error @enderror"
                    @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror>
                @error('password_confirmation')
                <p id="password_confirmation-error" class="weisz-field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="weisz-btn-primary w-full py-2.5">
                {{ __('Daftar') }}
            </button>
        </form>

        <div class="pt-4 border-t border-[var(--border-color)] text-center text-xs text-[var(--txt-secondary)]">
            {{ __('Sudah punya akun?') }}
            <a href="{{ route('login') }}" class="font-semibold text-[var(--brand-main)] hover:underline ml-1">{{ __('Masuk di sini') }}</a>
        </div>
    </div>
</div>
@endsection
