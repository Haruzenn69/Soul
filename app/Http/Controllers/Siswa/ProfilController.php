<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;
        $pendaftaran?->load('ekskul.pembina');
        $ekskul = $pendaftaran ? $pendaftaran->ekskul : null;
        $pengajuan = $siswa ? $siswa->pengajuanKeluars()->latest('tanggal_pengajuan')->get() : collect();

        return view('profile.edit', compact('siswa', 'ekskul', 'pengajuan'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        abort_unless($siswa, 404);

        $validated = $request->validate([
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
        ]);

        DB::transaction(function () use ($user, $siswa, $validated) {
            $siswa->update([
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ]);

            if (filled($validated['email']) && $validated['email'] !== $user->email) {
                $user->update([
                    'email' => $validated['email'],
                    'email_verified_at' => null,
                ]);
            }

            if ($siswa->isProfileComplete()) {
                $user->update(['onboarding_completed_at' => $user->onboarding_completed_at ?? now()]);
            }
        });

        return redirect()->route('siswa.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
