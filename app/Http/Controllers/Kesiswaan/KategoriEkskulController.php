<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\KategoriEkskul;
use App\Support\AturanEkskul;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KategoriEkskulController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:kategori_ekskuls,nama'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Kategori ini sudah ada.',
            'nama.max' => 'Nama kategori maksimal 50 karakter.',
        ]);

        $nama = ucwords(trim($validated['nama']));

        KategoriEkskul::create([
            'nama' => $nama,
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return back()->with('success', "Kategori '{$nama}' berhasil ditambahkan.");
    }

    public function destroy(KategoriEkskul $kategoriEkskul): RedirectResponse
    {
        $fallbackNama = AturanEkskul::KATEGORI_FALLBACK;

        if ($kategoriEkskul->isFallback()) {
            return back()->withErrors(['kategori' => "Kategori {$fallbackNama} adalah kategori fallback sistem. Ekskul yang kategorinya dihapus akan dipindah ke sini, sehingga kategori ini tidak dapat dihapus."]);
        }

        $totalKategoriHapusable = KategoriEkskul::where('nama', '!=', $fallbackNama)->count();

        if ($totalKategoriHapusable <= 1) {
            return back()->withErrors(['kategori' => "Tidak dapat menghapus kategori terakhir. Minimal harus ada 1 kategori selain {$fallbackNama}."]);
        }

        $nama = $kategoriEkskul->nama;

        $fallback = KategoriEkskul::firstOrCreate(
            ['nama' => $fallbackNama],
            ['deskripsi' => 'Kategori umum untuk ekskul yang tidak termasuk kategori lain.']
        );

        $affectedCount = Ekskul::where('kategori', $nama)->count();
        if ($affectedCount > 0) {
            Ekskul::where('kategori', $nama)->update(['kategori' => $fallback->nama]);
        }

        $kategoriEkskul->delete();

        $pesan = "Kategori '{$nama}' berhasil dihapus.";
        $pesan .= $affectedCount > 0
            ? " {$affectedCount} ekskul yang sebelumnya berkategori '{$nama}' otomatis dipindah ke kategori '{$fallback->nama}'."
            : ' Tidak ada ekskul yang terdampak.';

        return back()->with('success', $pesan);
    }
}
