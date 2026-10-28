<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            if (! empty($user->locale) && in_array($user->locale, ['id', 'en'], true)) {
                session(['locale' => $user->locale]);
                cookie()->queue(cookie('weisz_locale', $user->locale, 60 * 24 * 365));
            } elseif (session()->has('locale')) {
                $user->locale = session('locale');
                $user->save();
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('chat.index'));
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang kamu masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $currentLocale = session('locale') ?? $request->cookie('weisz_locale') ?? config('app.locale', 'id');
        if (! in_array($currentLocale, ['id', 'en'], true)) {
            $currentLocale = 'id';
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'locale' => $currentLocale,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        session(['locale' => $currentLocale]);
        cookie()->queue(cookie('weisz_locale', $currentLocale, 60 * 24 * 365));

        return redirect()->route('chat.index')->with('success', 'Selamat datang di Weisz! Akunmu berhasil dibuat.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    public function showProfile(): RedirectResponse
    {
        return redirect()->route('chat.index', ['profile' => 1]);
    }

    public function updateProfile(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if (! empty($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (! empty($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => ! empty($validated['password']) ? 'Kata sandi berhasil diperbarui.' : 'Nama akun berhasil diperbarui.',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'initial' => strtoupper(substr($user->name, 0, 1)),
                ],
            ]);
        }

        return back()->with('success', 'Nama akun berhasil diperbarui.');
    }

    public function updateLocale(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:en,id'],
        ]);

        $locale = $validated['locale'];

        session(['locale' => $locale]);
        cookie()->queue(cookie('weisz_locale', $locale, 60 * 24 * 365));

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $user->locale = $locale;
            $user->save();
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
            ]);
        }

        return back();
    }
}
