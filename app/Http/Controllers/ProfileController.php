<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $siswa = $user->siswa;
        
        // Jika user adalah siswa, tampilkan profile dengan data ekskul
        if ($user->role === 'siswa' && $siswa) {
            $pendaftaran = $siswa->pendaftarans()->where('status', 'diterima')->with('ekskul.pembina')->first();
            $ekskul = $pendaftaran ? $pendaftaran->ekskul : null;
            $pengajuan = $siswa->pengajuanKeluar()->get();
            
            return view('profile.edit', compact('siswa', 'ekskul', 'pengajuan'));
        }
        
        // Untuk role lain (admin, kesiswaan, pembina)
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}