<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;
        $presensis = $pendaftaran
            ? Presensi::where('pendaftaran_id', $pendaftaran->id)->with('kegiatan')
                ->when($request->filled('cari'), function ($query) use ($request) {
                    $cari = $request->input('cari');
                    $query->whereHas('kegiatan', fn ($k) => $k->where('materi', 'like', "%{$cari}%"));
                })
                ->when($request->filled('bulan'), function ($query) use ($request) {
                    [$tahun, $bulan] = explode('-', $request->input('bulan'));
                    $query->whereHas('kegiatan', fn ($k) => $k->whereYear('tanggal_kegiatan', $tahun)->whereMonth('tanggal_kegiatan', $bulan));
                })
                ->get()
            : collect();

        return view('siswa.presensi', compact('presensis', 'siswa'));
    }
}
