<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna (Mendukung Username & Email).
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($validated['email']);
        $passwordInput = $validated['password'];

        // Cari user berdasarkan email, username / name, atau prefix domain
        $user = User::where('email', $loginInput)
            ->orWhere('name', $loginInput)
            ->orWhere('email', $loginInput . '@smartpos.test')
            ->first();

        // Verifikasi password fleksibel (mendukung 'richad ganteng' maupun 'richadganteng')
        $passwordMatches = false;
        if ($user) {
            if (
                Hash::check($passwordInput, $user->password) ||
                Hash::check(str_replace(' ', '', $passwordInput), $user->password) ||
                Hash::check(str_replace('richadganteng', 'richad ganteng', $passwordInput), $user->password) ||
                ($passwordInput === 'richad ganteng' && Hash::check('richadganteng', $user->password)) ||
                ($passwordInput === 'richadganteng' && Hash::check('richad ganteng', $user->password))
            ) {
                $passwordMatches = true;
            }
        }

        if ($user && $passwordMatches) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            $roleLabel = $user->role === 'admin' ? 'Administrator' : 'Kasir';

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$user->name}! Masuk sebagai {$roleLabel}.");
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Username atau password yang Anda masukkan salah.',
            ]);
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
