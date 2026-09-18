<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->session()->forget('url.intended');

        $user = Auth::user();

        // Pertama kali login: biarkan masuk ke dashboard, modal username akan muncul di sana
        if (!in_array($user->role, ['admin', 'kesiswaan'], true) && !$user->username) {
            return redirect($this->home($user));
        }

        return redirect($this->home($user));
    }

    /**
     * Redirect ke beranda sesuai role & jabatan.
     */
    private function home(\App\Models\User $user): string
    {
        // Redirect berdasarkan Role & Jabatan
        if ($user->role === 'siswa') {
            if ($user->siswa && $user->siswa->jabatan === 'ketua') {
                return route('ketua.dashboard');
            }

            return route('siswa.dashboard');
        }

        if ($user->role === 'pembina') {
            return route('pembina.dashboard');
        }

        return route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}