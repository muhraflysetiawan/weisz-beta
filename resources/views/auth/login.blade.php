@extends('layouts.app')

@section('title', __('Masuk ke Weisz'))

@section('content')
<div class="max-w-md mx-auto py-8 sm:py-16">
    <div class="weisz-card p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-10 h-10 rounded-2xl bg-[var(--brand-main)] text-white flex items-center justify-center font-bold text-lg mx-auto shadow-sm">
                W
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--txt-primary)]">{{ __('Masuk ke Weisz') }}</h1>
            <p class="text-xs text-[var(--txt-secondary)]">{{ __('Lanjutkan sesi refleksi dan kelola riwayat percakapanmu.') }}</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Alamat Email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="nama@email.com" class="weisz-input w-full">
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-xs font-semibold text-[var(--txt-secondary)]">{{ __('Kata Sandi') }}</label>
                </div>
                <input id="password" type="password" name="password" required
                    placeholder="••••••••" class="weisz-input w-full">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-[var(--txt-secondary)]">
                    <input type="checkbox" name="remember" class="rounded border-[var(--border-color)] text-[var(--brand-main)] focus:ring-[var(--brand-main)]">
                    {{ __('Ingat saya di perangkat ini') }}
                </label>
            </div>

            <button type="submit" class="weisz-btn-primary w-full py-2.5">
                {{ __('Masuk') }}
            </button>
        </form>

        <div class="pt-4 border-t border-[var(--border-color)] text-center text-xs text-[var(--txt-secondary)]">
            {{ __('Belum punya akun Weisz?') }}
            <a href="{{ route('register') }}" class="font-semibold text-[var(--brand-main)] hover:underline ml-1">{{ __('Daftar sekarang') }}</a>
        </div>
    </div>
</div>
@endsection
