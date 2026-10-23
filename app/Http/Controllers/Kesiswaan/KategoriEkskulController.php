<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\KategoriEkskul;
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
        // Hanya kategori Lainnya yang tidak bisa dihapus (sebagai fallback sistem)
        if ($kategoriEkskul->isFallback()) {
            return back()->withErrors(['kategori' => 'Kategori Lainnya adalah kategori fallback sistem. Ekskul yang kategorinya dihapus akan dipindah ke sini, sehingga kategori ini tidak dapat dihapus.']);
        }

        // Cegah hapus jika hanya ada 1 kategori tersisa (selain Lainnya)
        $totalKategoriHapusable = KategoriEkskul::where('nama', '!=', \App\Support\AturanEkskul::KATEGORI_FALLBACK)->count();

        if ($totalKategoriHapusable <= 1) {
            return back()->withErrors(['kategori' => 'Tidak dapat menghapus kategori terakhir. Minimal harus ada 1 kategori selain Lainnya.']);
        }

        $nama = $kategoriEkskul->nama;

        // Pastikan fallback "Lainnya" ada di database (auto-buat jika belum)
        $fallback = KategoriEkskul::firstOrCreate(
            ['nama' => \App\Support\AturanEkskul::KATEGORI_FALLBACK],
            ['deskripsi' => 'Kategori umum untuk ekskul yang tidak termasuk kategori lain.']
        );

        // Pindahkan semua ekskul dari kategori yang dihapus ke "Lainnya"
        $affectedCount = Ekskul::where('kategori', $nama)->count();
        if ($affectedCount > 0) {
            Ekskul::where('kategori', $nama)->update(['kategori' => $fallback->nama]);
        }

        $kategoriEkskul->delete();

        $pesan = "Kategori '{$nama}' berhasil dihapus.";
        $pesan .= $affectedCount > 0
            ? " {$affectedCount} ekskul yang sebelumnya berkategori '{$nama}' otomatis dipindah ke kategori 'Lainnya'."
            : ' Tidak ada ekskul yang terdampak.';

        return back()->with('success', $pesan);
    }
}
