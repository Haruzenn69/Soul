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
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);

        if ($isKesiswaan && ! $request->filled('ekskul')) {
            $ekskuls = \App\Models\Ekskul::orderBy('nama_ekskul')->get();
            $cards = $ekskuls->map(function (\App\Models\Ekskul $e) {
                $base = Pendaftaran::where('ekskul_id', $e->id);
                $total = (clone $base)->count();
                $pendingCount = (clone $base)->where('status', 'pending')->count();
                $diterimaCount = (clone $base)->where('status', 'diterima')->count();
                $ditolakCount = (clone $base)->where('status', 'ditolak')->count();

                $badge = null;
                if ($pendingCount > 0) {
                    $badge = ['text' => $pendingCount.' Menunggu', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300'];
                }

                $stats = [
                    ['label' => 'Perlu Verifikasi', 'value' => $pendingCount.' Pendaftar', 'class' => $pendingCount > 0 ? 'font-extrabold text-amber-600 dark:text-amber-400' : 'font-semibold text-slate-500 dark:text-slate-400'],
                    ['label' => 'Total Diterima', 'value' => $diterimaCount.' Siswa', 'class' => 'font-bold text-emerald-600 dark:text-emerald-400'],
                    ['label' => 'Total Ditolak', 'value' => $ditolakCount.' Siswa', 'class' => 'font-semibold text-slate-500 dark:text-slate-400'],
                ];

                return (object) [
                    'ekskul' => $e,
                    'stats' => $stats,
                    'badge' => $badge,
                    'actionText' => 'Tinjau Pendaftaran',
                    'actionUrl' => route('ketua.pendaftaran.index', ['ekskul' => $e->id]),
                ];
            });

            return view('ketua.pendaftaran.index', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => true,
            ]);
        }

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

        return view('ketua.pendaftaran.index', compact('pendaftarans', 'pendingCount', 'diterimaCount', 'ditolakCount', 'total', 'sort', 'direction', 'ekskul', 'isKesiswaan'));
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
                $alasan = $pendaftaran->siswa->alasanTidakDapatMenerima($pendaftaran);

                abort_if($alasan !== null, 422, $alasan);
            }

            $pendaftaran->update($validated);

            if ($validated['status'] === Pendaftaran::STATUS_DITERIMA) {
                if ($pendaftaran->siswa->jabatan !== 'ketua') {
                    $pendaftaran->siswa->update(['jabatan' => 'anggota']);
                }
                NotifikasiService::pendaftaranDiterima($pendaftaran);
            } else {
                NotifikasiService::pendaftaranDitolak($pendaftaran);
            }
        });

        return redirect()->route('ketua.pendaftaran.index')->with('success', 'Status pendaftaran berhasil diupdate.');
    }
}
