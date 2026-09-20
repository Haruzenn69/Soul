<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    use KetuaEkskul;

    public function create(Kegiatan $kegiatan)
    {
        $this->ensureEkskul($kegiatan);

        $ekskul = $this->ekskul();
        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->with('siswa')
            ->get();

        $presensiExisting = Presensi::where('kegiatan_id', $kegiatan->id)
            ->pluck('status', 'pendaftaran_id')
            ->toArray();

        return view('ketua.presensi.create', compact('kegiatan', 'anggotas', 'presensiExisting'));
    }

    public function store(Request $request, Kegiatan $kegiatan)
    {
        $this->ensureEkskul($kegiatan);

        $validated = $request->validate([
            'presensi' => 'required|array',
            'presensi.*.pendaftaran_id' => 'required|exists:pendaftarans,id',
            'presensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        $pendaftaranIds = collect($validated['presensi'])->pluck('pendaftaran_id');

        $milikEkskul = Pendaftaran::whereIn('id', $pendaftaranIds)
            ->where('ekskul_id', $kegiatan->ekskul_id)
            ->count();

        abort_unless($milikEkskul === $pendaftaranIds->count(), 422, 'Presensi hanya bisa diisi untuk anggota ekskul kegiatan ini.');

        foreach ($validated['presensi'] as $item) {
            Presensi::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'pendaftaran_id' => $item['pendaftaran_id']],
                ['status' => $item['status']]
            );
        }

        return redirect()->route('ketua.kegiatan.show', $kegiatan)->with('success', 'Presensi berhasil disimpan.');
    }
}
