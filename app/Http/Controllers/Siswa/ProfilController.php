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
            'username' => ['nullable', 'string', 'min:3', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
            'angkatan' => ['nullable', 'string', 'max:20'],
            'no_telp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string'],
            'medsos' => ['nullable', 'string', 'max:255'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan akun lain.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        DB::transaction(function () use ($user, $siswa, $validated, $request) {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                if ($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->foto);
                }
                $fotoPath = $request->file('foto')->store('profile-photos', 'public');
            }

            $updateData = [
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? $siswa->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $siswa->tanggal_lahir,
                'agama' => $validated['agama'] ?? $siswa->agama,
                'angkatan' => $validated['angkatan'] ?? $siswa->angkatan,
                'email' => $validated['email'] ?? $siswa->email,
                'no_telp' => $validated['no_telp'] ?? $siswa->no_telp,
                'alamat' => $validated['alamat'] ?? $siswa->alamat,
                'medsos' => $validated['medsos'] ?? $siswa->medsos,
            ];

            if ($fotoPath) {
                $updateData['foto'] = $fotoPath;
            }

            $siswa->update($updateData);

            $userUpdates = [];
            if (!empty($validated['username']) && $validated['username'] !== $user->username) {
                $userUpdates['username'] = $validated['username'];
            }
            if (filled($validated['email']) && $validated['email'] !== $user->email) {
                $userUpdates['email'] = $validated['email'];
                $userUpdates['email_verified_at'] = null;
            }
            if (!empty($userUpdates)) {
                $user->update($userUpdates);
            }

            if ($siswa->isProfileComplete()) {
                $user->update(['onboarding_completed_at' => $user->onboarding_completed_at ?? now()]);
            }
        });

        return redirect()->route('siswa.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
