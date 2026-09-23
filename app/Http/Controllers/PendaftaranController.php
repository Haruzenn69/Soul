<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    use KetuaEkskul;

    public function index()
    {
        $ekskul = $this->ekskul();
        $pendaftarans = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->with('siswa')
            ->latest('tanggal_daftar')
            ->get();

        return view('ketua.pendaftaran.index', compact('pendaftarans'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $this->ensureEkskul($pendaftaran);

        $pendaftaran->load(['siswa', 'ekskul']);

        return view('ketua.pendaftaran.show', compact('pendaftaran'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $this->ensureEkskul($pendaftaran);

        if ($pendaftaran->status !== Pendaftaran::STATUS_PENDING) {
            return back()->with('error', 'Hanya pendaftaran berstatus pending yang dapat diproses.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,ditolak'],
        ]);

        DB::transaction(function () use ($pendaftaran, $validated) {
            $pendaftaran = Pendaftaran::query()->lockForUpdate()->findOrFail($pendaftaran->id);
            Siswa::query()->lockForUpdate()->findOrFail($pendaftaran->siswa_id);

            abort_unless($pendaftaran->status === Pendaftaran::STATUS_PENDING, 422, 'Pendaftaran ini sudah diproses.');

            if ($validated['status'] === Pendaftaran::STATUS_DITERIMA) {
                $hasActiveEkskul = Pendaftaran::query()
                    ->where('siswa_id', $pendaftaran->siswa_id)
                    ->where('id', '!=', $pendaftaran->id)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->exists();

                abort_if($hasActiveEkskul, 422, 'Siswa ini sudah aktif di ekskul lain.');
            }

            $pendaftaran->update($validated);

            if ($validated['status'] === Pendaftaran::STATUS_DITERIMA) {
                $pendaftaran->siswa->update(['jabatan' => 'anggota']);
                NotifikasiService::pendaftaranDiterima($pendaftaran);
            } else {
                NotifikasiService::pendaftaranDitolak($pendaftaran);
            }
        });

        return redirect()->route('ketua.pendaftaran.index')->with('success', 'Status pendaftaran berhasil diupdate.');
    }
}
