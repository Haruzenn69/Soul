<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\SiswaProfileHistory;
use App\Services\NotifikasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function edit(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if ($request->input('from') === 'ketua' && $siswa?->ekskulDipimpin()) {
            $ekskul = $siswa->ekskulDipimpin();
        } else {
            $ekskul = $siswa ? $siswa->resolveActiveEkskul($request->filled('ekskul') ? (int) $request->input('ekskul') : null) : null;
        }

        $pendaftaran = $siswa ? $siswa->activePendaftaranUntuk($ekskul) : null;
        $pendaftaran?->load('ekskul.pembina');
        $ekskul = $pendaftaran ? $pendaftaran->ekskul : $ekskul;
        $pengajuan = $siswa ? $siswa->pengajuanKeluars()->with('ekskul')->latest('tanggal_pengajuan')->get() : collect();

        return view('profile.edit', compact('siswa', 'ekskul', 'pengajuan'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        abort_unless($siswa, 404);

        $validated = $request->validate([
            'username' => ['nullable', 'string', 'min:3', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'no_telp' => ['nullable', 'string', 'max:25'],
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan akun lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $oldUsername = $user->username;
        $oldEmail = $user->email;
        $oldFoto = $siswa->foto;
        $oldNoTelp = $siswa->no_telp;
        $oldJenisKelamin = $siswa->jenis_kelamin;
        $oldTempatLahir = $siswa->tempat_lahir;
        $oldTanggalLahir = $siswa->tanggal_lahir?->format('Y-m-d');
        $oldAgama = $siswa->agama;
        $newUsername = !empty($validated['username']) ? $validated['username'] : $oldUsername;
        $newEmail = $validated['email'] ?? null;
        $newNoTelp = $validated['no_telp'] ?? null;
        $newJenisKelamin = $validated['jenis_kelamin'] ?? null;
        $newTempatLahir = $validated['tempat_lahir'] ?? null;
        $newTanggalLahir = $validated['tanggal_lahir'] ?? null;
        $newAgama = $validated['agama'] ?? null;
        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('profile-photos', 'public')
            : $oldFoto;

        $changedFields = [];

        DB::transaction(function () use ($user, $siswa, $validated, $oldUsername, $oldEmail, $oldFoto, $oldNoTelp, $oldJenisKelamin, $oldTempatLahir, $oldTanggalLahir, $oldAgama, $newUsername, $newEmail, $newNoTelp, $newJenisKelamin, $newTempatLahir, $newTanggalLahir, $newAgama, $fotoPath, &$changedFields) {
            $userUpdates = [];
            if (!empty($validated['username']) && $validated['username'] !== $user->username) {
                $userUpdates['username'] = $validated['username'];
            }
            if ($newEmail !== $oldEmail) {
                $userUpdates['email'] = $newEmail;
                $userUpdates['email_verified_at'] = null;
            }
            if (!empty($userUpdates)) {
                $user->update($userUpdates);
            }

            $changes = [
                'username' => [$oldUsername, $newUsername],
                'email' => [$oldEmail, $newEmail],
                'no_telp' => [$oldNoTelp, $newNoTelp],
                'foto' => [$oldFoto, $fotoPath],
                'jenis_kelamin' => [$oldJenisKelamin, $newJenisKelamin],
                'tempat_lahir' => [$oldTempatLahir, $newTempatLahir],
                'tanggal_lahir' => [$oldTanggalLahir, $newTanggalLahir],
                'agama' => [$oldAgama, $newAgama],
            ];

            $siswa->update([
                'email' => $newEmail,
                'no_telp' => $newNoTelp,
                'foto' => $fotoPath,
                'jenis_kelamin' => $newJenisKelamin,
                'tempat_lahir' => $newTempatLahir,
                'tanggal_lahir' => $newTanggalLahir,
                'agama' => $newAgama,
            ]);

            foreach ($changes as $field => [$oldValue, $newValue]) {
                if ($oldValue !== $newValue) {
                    $changedFields[] = $field;
                    SiswaProfileHistory::create([
                        'siswa_id' => $siswa->id,
                        'changed_by_user_id' => $user->id,
                        'field' => $field,
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                    ]);
                }
            }

            if (!empty($changedFields)) {
                NotifikasiService::profilSiswaDiubah($siswa, $changedFields);
            }

            if ($siswa->isProfileComplete()) {
                $user->update(['onboarding_completed_at' => $user->onboarding_completed_at ?? now()]);
            }
        });

        return redirect()->route('siswa.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
