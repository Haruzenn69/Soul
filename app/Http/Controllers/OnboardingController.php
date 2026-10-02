<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    /**
     * Simpan kredensial akun saat login pertama kali.
     */
    public function setup(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user, 401);
        abort_unless(in_array($user->role, ['siswa', 'pembina'], true), 403);
        abort_unless($user->needsOnboarding(), 403);

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.min' => 'Username minimal 3 karakter.',
            'username.unique' => 'Username tersebut sudah digunakan oleh akun lain. Silakan pilih username lain.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'onboarding_completed_at' => now(),
            ]);
        });

        return back()->with('success', 'Pengaturan akun berhasil disimpan.');
    }

    public function returnToLogin(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->needsOnboarding(), 403);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
