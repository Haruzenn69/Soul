<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use App\Models\Siswa;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanKeluarController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $ekskul = $this->ekskul();
        $base = PengajuanKeluar::where('ekskul_id', $ekskul->id);
        $total = (clone $base)->count();

        [$sort, $direction] = TableKit::sort(['tanggal_pengajuan', 'nama', 'status'], 'tanggal_pengajuan', 'desc');

        $pengajuanKeluars = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(function ($sub) use ($cari) {
                    $sub->whereHas('siswa', fn ($s) => $s->where('nama', 'like', "%{$cari}%"))
                        ->orWhere('alasan', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('status') && $request->input('status') !== 'semua', fn ($query) => $query->where('status', $request->input('status')))
            ->with('siswa')
            ->orderBy($sort === 'nama' ? Siswa::select('nama')->whereColumn('siswas.id', 'pengajuan_keluars.siswa_id') : $sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('ketua.pengajuan-keluar.index', compact('pengajuanKeluars', 'total', 'sort', 'direction'));
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
                    ->update(['status' => Pendaftaran::STATUS_KELUAR]);

                NotifikasiService::pengajuanKeluarDiterima($pengajuanKeluar);
            } else {
                NotifikasiService::pengajuanKeluarDitolak($pengajuanKeluar);
            }
        });

        return redirect()->route('ketua.pengajuan-keluar.index')->with('success', 'Pengajuan keluar berhasil diupdate.');
    }
}
