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

        $cari = Ekskul::query()
            ->with(['pembina', 'parent', 'subdivisis.penguruses.siswa'])
            ->where('status', true)
            ->whereNull('parent_id')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');

                $query->where(function ($sub) use ($cari) {
                    $sub->where('nama_ekskul', 'like', "%{$cari}%")
                        ->orWhere('deskripsi', 'like', "%{$cari}%")
                        ->orWhere('tagline', 'like', "%{$cari}%")
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$cari}%"))
                        ->orWhereHas('subdivisis', fn ($s) => $s->where('nama_ekskul', 'like', "%{$cari}%"));
                });
            });

        $tipe = $request->input('tipe');
        if ($tipe === 'komunitas') {
            $filtered = (clone $cari)->where('tipe', Ekskul::TIPE_KOMUNITAS);
        } elseif ($tipe === 'ekskul') {
            $filtered = (clone $cari)->where(function ($q) {
                $q->where('tipe', '!=', Ekskul::TIPE_KOMUNITAS)->orWhereNull('tipe');
            });
        } else {
            $filtered = $cari;
            $tipe = null;
        }

        // Jumlah mengikuti pencarian dan filter
        $jumlah = [
            'semua' => (clone $cari)->count(),
            'ekskul' => (clone $cari)->where(function ($q) {
                $q->where('tipe', '!=', Ekskul::TIPE_KOMUNITAS)->orWhereNull('tipe');
            })->count(),
            'komunitas' => (clone $cari)->where('tipe', Ekskul::TIPE_KOMUNITAS)->count(),
        ];

        $ekskuls = $filtered->get();

        $alasanPerEkskul = $siswa
            ? $ekskuls->mapWithKeys(fn (Ekskul $e) => [$e->id => $siswa->alasanTidakBisaDaftar($e)])
            : collect();

        return view('siswa.katalog', [
            'ekskuls' => $ekskuls,
            'jumlah' => $jumlah,
            'status' => null,
            'tipe' => $tipe,
            'ekskulAktif' => $ekskulAktif,
            'ekskulAktifs' => $ekskulAktifs,
            'alasanPerEkskul' => $alasanPerEkskul,
        ]);
    }
}
