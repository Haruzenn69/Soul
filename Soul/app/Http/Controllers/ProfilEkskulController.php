<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilEkskulController extends Controller
{
    private function getEkskul()
    {
        $pendaftaran = auth()->user()->siswa?->pendaftarans()->where('status', 'diterima')->first();
        abort_unless($pendaftaran, 404, 'Anda belum tergabung dalam ekskul mana pun.');
        return $pendaftaran->ekskul;
    }

    public function edit()
    {
        $ekskul = $this->getEkskul();
        return view('ketua.profil-ekskul.edit', compact('ekskul'));
    }

    public function update(Request $request)
    {
        $ekskul = $this->getEkskul();

        $validated = $request->validate([
            'nama_ekskul' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'tujuan' => 'nullable|string',
            'jadwal' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'nama_ekskul' => $validated['nama_ekskul'],
            'tagline' => $validated['tagline'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tujuan' => $validated['tujuan'] ?? null,
            'jadwal' => $validated['jadwal'] ?? null,
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('ekskul/logo', 'public');
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('ekskul/cover', 'public');
        }

        $ekskul->update($data);

        return back()->with('success', 'Profil ekskul berhasil diperbarui.');
    }

    public function toggleRecruitment()
    {
        $ekskul = $this->getEkskul();
        $ekskul->update(['is_open_recruitment' => !$ekskul->is_open_recruitment]);

        $status = $ekskul->is_open_recruitment ? 'dibuka' : 'ditutup';
        return back()->with('success', "Pendaftaran ekskul telah {$status}.");
    }
}