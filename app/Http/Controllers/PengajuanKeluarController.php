<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use App\Models\RiwayatJabatan;
use App\Models\Siswa;
use App\Services\ArsipEkskulService;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanKeluarController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);

        if ($isKesiswaan && ! $request->filled('ekskul')) {
            $ekskuls = \App\Models\Ekskul::orderBy('nama_ekskul')->get();
            $cards = $ekskuls->map(function (\App\Models\Ekskul $e) {
                $base = PengajuanKeluar::where('ekskul_id', $e->id);
                $total = (clone $base)->count();
                $menungguCount = (clone $base)->where('status', 'menunggu')->count();
                $disetujuiCount = (clone $base)->where('status', 'disetujui')->count();
                $ditolakCount = (clone $base)->where('status', 'ditolak')->count();

                $badge = null;
                if ($menungguCount > 0) {
                    $badge = ['text' => $menungguCount.' Menunggu', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300'];
                }

                $stats = [
                    ['label' => 'Perlu Persetujuan', 'value' => $menungguCount.' Pengajuan', 'class' => $menungguCount > 0 ? 'font-extrabold text-amber-600 dark:text-amber-400' : 'font-semibold text-slate-500 dark:text-slate-400'],
                    ['label' => 'Disetujui', 'value' => $disetujuiCount.' Siswa', 'class' => 'font-bold text-emerald-600 dark:text-emerald-400'],
                    ['label' => 'Ditolak', 'value' => $ditolakCount.' Siswa', 'class' => 'font-semibold text-slate-500 dark:text-slate-400'],
                ];

                return (object) [
                    'ekskul' => $e,
                    'stats' => $stats,
                    'badge' => $badge,
                    'actionText' => 'Lihat Pengajuan',
                    'actionUrl' => route('ketua.pengajuan-keluar.index', ['ekskul' => $e->id]),
                ];
            });

            return view('ketua.pengajuan-keluar.index', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => true,
            ]);
        }

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

        return view('ketua.pengajuan-keluar.index', compact('pengajuanKeluars', 'total', 'sort', 'direction', 'ekskul', 'isKesiswaan'));
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
                $ekskul = $pengajuanKeluar->ekskul;

                $pengajuanKeluar->siswa->update(['jabatan' => 'siswa']);

                // Kalau yang keluar adalah ketua, periodenya ditutup supaya
                // tidak menggantung aktif dan masuk daftar arsip.
                if ($ekskul) {
                    app(ArsipEkskulService::class)
                        ->akhiriPeriodeKetua(
                            $ekskul,
                            $pengajuanKeluar->siswa,
                            null,
                            RiwayatJabatan::ALASAN_KELUAR
                        );
                }

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
