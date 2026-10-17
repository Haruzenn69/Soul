<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;

class NilaiController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()?->siswa;
        abort_unless($siswa, 404, 'Data siswa tidak ditemukan.');

        $pendaftarans = $siswa->activePendaftarans()->with('ekskul')->orderByDesc('id')->get();

        $ekskulId = request()->input('ekskul');
        $pendaftaran = $siswa->resolveActivePendaftaran($ekskulId ? (int) $ekskulId : null);

        $periode = Penilaian::periodeSekarang();
        $ekskul = $pendaftaran?->ekskul;

        $penilaian = $pendaftaran
            ? Penilaian::where('pendaftaran_id', $pendaftaran->id)
                ->where('periode', $periode['label'])
                ->where('status', Penilaian::STATUS_TERKIRIM)
                ->with('penilai')
                ->latest('updated_at')
                ->first()
            : null;

        $view = request()->routeIs('ketua.nilai') ? 'ketua.nilai' : 'siswa.nilai';

        return view($view, compact('siswa', 'pendaftarans', 'pendaftaran', 'ekskul', 'periode', 'penilaian'));
    }
}
