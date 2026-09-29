<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    /**
     * Simpan pengaturan awal saat login pertama kali (Username mandiri, Foto Profil, Medsos mandiri).
     */
    public function setup(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user, 401);

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'medsos' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.min' => 'Username minimal 3 karakter.',
            'username.unique' => 'Username tersebut sudah digunakan oleh akun lain. Silakan pilih username lain.',
            'foto.image' => 'File foto profil harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WebP.',
            'foto.max' => 'Ukuran file foto maksimal 2MB.',
        ]);

        DB::transaction(function () use ($user, $validated, $request) {
            $user->update([
                'username' => $validated['username'],
                'onboarding_completed_at' => now(),
            ]);

            $model = $user->siswa ?? $user->pembina;

            if ($model) {
                $fotoPath = null;
                if ($request->hasFile('foto')) {
                    if ($model->foto && Storage::disk('public')->exists($model->foto)) {
                        Storage::disk('public')->delete($model->foto);
                    }
                    $fotoPath = $request->file('foto')->store('profile-photos', 'public');
                }

                $updateData = [
                    'medsos' => $validated['medsos'] ?? $model->medsos,
                ];

                if ($fotoPath) {
                    $updateData['foto'] = $fotoPath;
                }

                $model->update($updateData);
            }
        });

        return back()->with('success', 'Selamat datang! Username dan profil awal Anda berhasil disimpan.');
    }

    public function complete(): RedirectResponse
    {
        $user = auth()->user();

        if ($user) {
            $user->update(['onboarding_completed_at' => now()]);
        }

        return back();
    }
}
