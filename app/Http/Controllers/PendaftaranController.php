<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $ekskul = $this->ekskul();
        $base = Pendaftaran::where('ekskul_id', $ekskul->id);

        $total = (clone $base)->count();
        $pendingCount = (clone $base)->where('status', 'pending')->count();
        $diterimaCount = (clone $base)->where('status', 'diterima')->count();
        $ditolakCount = (clone $base)->where('status', 'ditolak')->count();

        [$sort, $direction] = TableKit::sort(['tanggal_daftar', 'status', 'nama'], 'tanggal_daftar', 'desc');

        $pendaftarans = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->whereHas('siswa', fn ($s) => $s->where('nama', 'like', "%{$cari}%")->orWhere('nis', 'like', "%{$cari}%"));
            })
            ->when($request->filled('status') && $request->input('status') !== 'semua', fn ($query) => $query->where('status', $request->input('status')))
            ->with('siswa.kelas')
            ->orderBy($sort === 'nama' ? Siswa::select('nama')->whereColumn('siswas.id', 'pendaftarans.siswa_id') : $sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('ketua.pendaftaran.index', compact('pendaftarans', 'pendingCount', 'diterimaCount', 'ditolakCount', 'total', 'sort', 'direction'));
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
