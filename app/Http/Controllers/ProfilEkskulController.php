<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\EkskulGaleri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilEkskulController extends Controller
{
    use KetuaEkskul;

    public function edit(Request $request)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);

        if ($isKesiswaan && ! $request->filled('ekskul')) {
            $ekskuls = \App\Models\Ekskul::withCount('galeris')->with('pembina.user')->orderBy('nama_ekskul')->get();
            $cards = $ekskuls->map(function (\App\Models\Ekskul $e) {
                $hasDeskripsi = ! empty($e->deskripsi);
                $jadwal = $e->jadwal ?: 'Belum diatur';

                $stats = [
                    ['label' => 'Jadwal Latihan', 'value' => $jadwal, 'class' => 'font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[130px]'],
                    ['label' => 'Foto Galeri', 'value' => $e->galeris_count.' Foto', 'class' => 'font-bold text-sky-600 dark:text-sky-400'],
                    ['label' => 'Pembina', 'value' => $e->pembina?->nama ?? 'Belum ada', 'class' => 'font-medium text-slate-500 text-[11px] truncate max-w-[130px]'],
                ];

                $badge = null;
                if (! $hasDeskripsi) {
                    $badge = ['text' => 'Profil Belum Lengkap', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300'];
                }

                return (object) [
                    'ekskul' => $e,
                    'stats' => $stats,
                    'badge' => $badge,
                    'actionText' => 'Edit Profil & Galeri',
                    'actionUrl' => route('ketua.profil-ekskul.edit', ['ekskul' => $e->id]),
                ];
            });

            return view('ketua.profil-ekskul.edit', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => true,
            ]);
        }

        $ekskul = $this->ekskul()->load('galeris');

        return view('ketua.profil-ekskul.edit', compact('ekskul', 'isKesiswaan'));
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
