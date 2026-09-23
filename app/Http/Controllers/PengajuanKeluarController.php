<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanKeluarController extends Controller
{
    use KetuaEkskul;

    public function index()
    {
        $ekskul = $this->ekskul();
        $pengajuanKeluars = PengajuanKeluar::where('ekskul_id', $ekskul->id)
            ->with('siswa')
            ->latest('tanggal_pengajuan')
            ->get();

        return view('ketua.pengajuan-keluar.index', compact('pengajuanKeluars'));
    }

    public function show(PengajuanKeluar $pengajuanKeluar)
    {
        $this->ensureEkskul($pengajuanKeluar);

        $pengajuanKeluar->load(['siswa', 'ekskul']);

        return view('ketua.pengajuan-keluar.show', compact('pengajuanKeluar'));
    }

    public function update(Request $request, PengajuanKeluar $pengajuanKeluar)
    {
        $this->ensureEkskul($pengajuanKeluar);

        if ($pengajuanKeluar->status !== PengajuanKeluar::STATUS_PENDING) {
            return back()->with('error', 'Hanya pengajuan berstatus pending yang dapat diproses.');
        }

        $validated = $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        DB::transaction(function () use ($pengajuanKeluar, $validated) {
            $pengajuanKeluar = PengajuanKeluar::query()->lockForUpdate()->findOrFail($pengajuanKeluar->id);
            abort_unless($pengajuanKeluar->status === PengajuanKeluar::STATUS_PENDING, 422, 'Pengajuan ini sudah diproses.');

            $pengajuanKeluar->update($validated);

            if ($validated['status'] === PengajuanKeluar::STATUS_DITERIMA) {
                $pengajuanKeluar->siswa->update(['jabatan' => 'siswa']);
                Pendaftaran::where('siswa_id', $pengajuanKeluar->siswa_id)
                    ->where('ekskul_id', $pengajuanKeluar->ekskul_id)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->update(['status' => Pendaftaran::STATUS_NONAKTIF]);

                NotifikasiService::pengajuanKeluarDiterima($pengajuanKeluar);
            } else {
                NotifikasiService::pengajuanKeluarDitolak($pengajuanKeluar);
            }
        });

        return redirect()->route('ketua.pengajuan-keluar.index')->with('success', 'Pengajuan keluar berhasil diupdate.');
    }
}
