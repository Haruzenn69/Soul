<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $siswa = auth()->user()?->siswa;

        // Seorang siswa bisa punya maksimal dua ekskul aktif, jadi katalog
        // menampilkan semua ekskul yang sedang diikuti, bukan hanya satu.
        $ekskulAktifs = $siswa ? $siswa->activeEkskuls() : collect();
        $ekskulAktif = $ekskulAktifs->first();
        $menunggu = $siswa?->pendingPendaftaran();

        $cari = Ekskul::query()
            ->with('pembina')
            ->where('status', true)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');

                $query->where(function ($sub) use ($cari) {
                    $sub->where('nama_ekskul', 'like', "%{$cari}%")
                        ->orWhere('deskripsi', 'like', "%{$cari}%")
                        ->orWhere('tagline', 'like', "%{$cari}%")
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$cari}%"));
                });
            });

        $jumlah = [
            'semua' => (clone $cari)->count(),
        ];

        $ekskuls = $cari->get();

        $alasanPerEkskul = $siswa
            ? $ekskuls->mapWithKeys(fn (Ekskul $e) => [$e->id => $siswa->alasanTidakBisaDaftar($e)])
            : collect();

        return view('siswa.katalog', [
            'ekskuls' => $ekskuls,
            'jumlah' => $jumlah,
            'status' => null,
            'ekskulAktif' => $ekskulAktif,
            'ekskulAktifs' => $ekskulAktifs,
            'menunggu' => $menunggu !== null,
            'alasanPerEkskul' => $alasanPerEkskul,
        ]);
    }
}
