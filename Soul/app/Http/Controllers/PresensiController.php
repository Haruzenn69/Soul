<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    private function getEkskul()
    {
        $pendaftaran = auth()->user()->siswa?->pendaftarans()->where('status', 'diterima')->first();
        abort_unless($pendaftaran, 404, 'Anda belum tergabung dalam ekskul mana pun.');
        return $pendaftaran->ekskul;
    }

    public function create(Kegiatan $kegiatan)
    {
        $ekskul = $this->getEkskul();
        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->where('status', 'diterima')
            ->with('siswa')
            ->get();

        $presensiExisting = Presensi::where('kegiatan_id', $kegiatan->id)
            ->pluck('pendaftaran_id')
            ->toArray();

        return view('ketua.presensi.create', compact('kegiatan', 'anggotas', 'presensiExisting'));
    }

    public function store(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'presensi' => 'required|array',
            'presensi.*.pendaftaran_id' => 'required|exists:pendaftarans,id',
            'presensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        foreach ($validated['presensi'] as $item) {
            Presensi::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'pendaftaran_id' => $item['pendaftaran_id']],
                ['status' => $item['status']]
            );
        }

        return redirect()->route('ketua.kegiatan.show', $kegiatan)->with('success', 'Presensi berhasil disimpan.');
    }
}
