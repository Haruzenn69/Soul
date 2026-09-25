<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\EkskulGaleri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilEkskulController extends Controller
{
    use KetuaEkskul;

    public function edit()
    {
        $ekskul = $this->ekskul()->load('galeris');

        return view('ketua.profil-ekskul.edit', compact('ekskul'));
    }

    public function update(Request $request)
    {
        $ekskul = $this->ekskul();

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
        $ekskul = $this->ekskul();
        $ekskul->update(['is_open_recruitment' => ! $ekskul->is_open_recruitment]);

        $status = $ekskul->is_open_recruitment ? 'dibuka' : 'ditutup';

        return back()->with('success', "Pendaftaran ekskul telah {$status}.");
    }

    public function storeGaleri(Request $request)
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'foto' => ['required', 'array', 'max:10'],
            'foto.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['foto'] as $file) {
            $ekskul->galeris()->create([
                'foto' => $file->store('ekskul/galeri', 'public'),
                'caption' => $validated['caption'] ?? null,
            ]);
        }

        return back()->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function destroyGaleri(EkskulGaleri $galeri)
    {
        $this->ensureEkskul($galeri);

        Storage::disk('public')->delete($galeri->foto);
        $galeri->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}
