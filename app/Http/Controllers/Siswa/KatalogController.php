<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    /** Nilai yang diterima pada query string `status`. */
    private const FILTER = ['buka', 'tutup'];

    public function index(Request $request)
    {
        $siswa = auth()->user()?->siswa;

        // Satu siswa hanya boleh punya satu ekskul aktif dan satu pendaftaran
        // yang menunggu, jadi cukup ambiguity-nya status, bukan isi daftar.
        $pendaftaran = $siswa?->activePendaftaran();
        $menunggu = $siswa?->pendingPendaftaran();

        $status = $request->query('status');
        $status = in_array($status, self::FILTER, true) ? $status : null;

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

        // Jumlah dihitung dari hasil pencarian, bukan dari seluruh tabel, supaya
        // angka di rail selalu menjawab "dari ekskul yang ini, berapa yang bisa
        // saya daftar?".
        $jumlah = [
            'semua' => (clone $cari)->count(),
            'buka' => (clone $cari)->where('is_open_recruitment', true)->count(),
            'tutup' => (clone $cari)->where('is_open_recruitment', false)->count(),
        ];

        $ekskuls = (clone $cari)
            ->when($status === 'buka', fn ($query) => $query->where('is_open_recruitment', true))
            ->when($status === 'tutup', fn ($query) => $query->where('is_open_recruitment', false))
            ->get();

        return view('siswa.katalog', [
            'ekskuls' => $ekskuls,
            'jumlah' => $jumlah,
            'status' => $status,
            'ekskulAktif' => $pendaftaran?->ekskul,
            'menunggu' => $menunggu !== null,
        ]);
    }
}
