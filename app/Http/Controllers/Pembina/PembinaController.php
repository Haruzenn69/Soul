<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Kegiatan;
use App\Models\LaporanBulanan;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PembinaController extends Controller
{
    /**
     * Semua ekskul yang dibina oleh pembina yang sedang login.
     */
    private function getEkskuls(): Collection
    {
        $pembina = auth()->user()?->pembina;

        if (!$pembina) {
            return collect();
        }

        return $pembina->ekskuls()->get();
    }

    private function getEkskul()
    {
        return $this->getEkskuls()->first();
    }

    public function dashboard()
    {
        $ekskuls = $this->getEkskuls();
        $ekskul = $ekskuls->first();
        $ekskulIds = $ekskuls->pluck('id');

        $anggota = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->where('status', 'diterima')
            ->with(['siswa', 'siswa.kelas'])
            ->latest('tanggal_daftar')
            ->get();

        $pendaftaranPending = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->where('status', 'pending')
            ->with(['siswa', 'siswa.kelas'])
            ->latest('tanggal_daftar')
            ->get();

        $kegiatanMendatang = Kegiatan::whereIn('ekskul_id', $ekskulIds)
            ->whereDate('tanggal_kegiatan', '>=', today())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->get();

        $laporanDraft = LaporanBulanan::whereIn('ekskul_id', $ekskulIds)
            ->where('status', 'draft')
            ->latest('bulan')
            ->get();

        return view('pembina.dashboard', compact('ekskul', 'anggota', 'pendaftaranPending', 'kegiatanMendatang', 'laporanDraft'));
    }

    public function anggota(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $query = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->where('status', 'diterima')
            ->with(['siswa', 'siswa.kelas', 'ekskul']);

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->whereHas('siswa', function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        $ekskulFilter = $request->input('ekskul');
        if ($ekskulFilter && $ekskuls->pluck('id')->contains($ekskulFilter)) {
            $query->where('ekskul_id', $ekskulFilter);
        }

        $anggota = $query->get();

        return view('pembina.anggota', compact('anggota', 'ekskuls'));
    }

    public function pendaftaran(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $query = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->with(['siswa', 'siswa.kelas', 'ekskul'])
            ->latest('tanggal_daftar');

        $status = $request->input('status');
        if (in_array($status, ['pending', 'diterima', 'ditolak', 'nonaktif'])) {
            $query->where('status', $status);
        }

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->whereHas('siswa', function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        $pendaftarans = $query->get();

        $grouped = $pendaftarans->groupBy('status');

        return view('pembina.pendaftaran', compact('pendaftarans', 'ekskuls', 'grouped'));
    }

    public function laporan()
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $laporans = LaporanBulanan::whereIn('ekskul_id', $ekskulIds)
            ->where('status', '!=', 'draft')
            ->with('ekskul')
            ->latest('bulan')
            ->get();

        $kegiatans = Kegiatan::whereIn('ekskul_id', $ekskulIds)
            ->withCount(['presensis as hadir_count' => function ($q) {
                $q->where('status', 'hadir');
            }])
            ->withCount(['presensis as izin_count' => function ($q) {
                $q->where('status', 'izin');
            }])
            ->withCount(['presensis as sakit_count' => function ($q) {
                $q->where('status', 'sakit');
            }])
            ->withCount(['presensis as alpha_count' => function ($q) {
                $q->where('status', 'alpha');
            }])
            ->withCount('presensis as total_count')
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get();

        return view('pembina.laporan', compact('laporans', 'kegiatans', 'ekskuls'));
    }

    public function laporanShow(LaporanBulanan $laporanBulanan)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($laporanBulanan->ekskul_id), 403);
        abort_if($laporanBulanan->status === 'draft', 404);

        $laporanBulanan->load('ekskul');

        return view('pembina.laporan-show', ['laporan' => $laporanBulanan]);
    }

    public function laporanDownload(LaporanBulanan $laporanBulanan)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($laporanBulanan->ekskul_id), 403);
        abort_if($laporanBulanan->status === 'draft', 404);

        $ekskul = $laporanBulanan->ekskul;
        $kelas = $this->generateKelas($ekskul);
        $pdf = Pdf::loadView('ketua.laporan-bulanan.pdf', ['laporan' => $laporanBulanan, 'kelas' => $kelas]);
        $filename = 'laporan-' . str_replace('/', '-', $laporanBulanan->bulan) . '-' . ($ekskul->nama_ekskul ?? 'ekskul') . '.pdf';
        return $pdf->download($filename);
    }

    public function laporanApprove(LaporanBulanan $laporanBulanan)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($laporanBulanan->ekskul_id), 403);
        abort_if($laporanBulanan->status !== 'menunggu', 403, 'Laporan hanya bisa disetujui saat berstatus menunggu.');

        $laporanBulanan->update([
            'status' => 'disetujui',
            'catatan_pembina' => null,
        ]);

        $this->notifyKetua($laporanBulanan, 'Laporan Disetujui', 'terima');

        return redirect()->route('pembina.laporan.show', $laporanBulanan)
            ->with('success', 'Laporan disetujui.');
    }

    public function laporanReject(Request $request, LaporanBulanan $laporanBulanan)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($laporanBulanan->ekskul_id), 403);
        abort_if($laporanBulanan->status !== 'menunggu', 403, 'Laporan hanya bisa ditolak saat berstatus menunggu.');

        $validated = $request->validate([
            'catatan_pembina' => 'nullable|string',
        ]);

        $laporanBulanan->update([
            'status' => 'ditolak',
            'catatan_pembina' => $validated['catatan_pembina'] ?? null,
        ]);

        $this->notifyKetua($laporanBulanan, 'Laporan Ditolak', 'tolak');

        return redirect()->route('pembina.laporan.show', $laporanBulanan)
            ->with('success', 'Laporan ditolak.');
    }

    private function notifyKetua(LaporanBulanan $laporanBulanan, string $judul, string $tipe)
    {
        $ketua = Pendaftaran::query()
            ->where('ekskul_id', $laporanBulanan->ekskul_id)
            ->where('status', 'diterima')
            ->with(['siswa.user'])
            ->get()
            ->map(fn($p) => $p->siswa)
            ->firstWhere('jabatan', 'ketua');

        if (!$ketua) {
            return;
        }

        $bulanLabel = \Carbon\Carbon::createFromFormat('Y-m', $laporanBulanan->bulan)->translatedFormat('F Y');

        Notifikasi::create([
            'siswa_id' => $ketua->id,
            'laporan_bulanan_id' => $laporanBulanan->id,
            'judul' => $judul,
            'pesan' => 'Laporan bulanan periode ' . $bulanLabel . ' ekskul ' . $laporanBulanan->ekskul->nama_ekskul . ' telah ditinjau oleh pembina.',
            'tipe' => $tipe === 'terima' ? 'diterima' : 'ditolak',
        ]);
    }

    public function presensi()
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $kegiatans = Kegiatan::whereIn('ekskul_id', $ekskulIds)
            ->with(['presensis.pendaftaran.siswa'])
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get();

        return view('pembina.presensi', compact('kegiatans'));
    }

    public function profile()
    {
        return view('pembina.profile');
    }

    private function generateKelas(Ekskul $ekskul)
    {
        $tingkats = $ekskul->pendaftarans()
            ->where('status', 'diterima')
            ->with('siswa.kelas')
            ->get()
            ->pluck('siswa.kelas.tingkat')
            ->unique()
            ->sort()
            ->values();

        if ($tingkats->isEmpty()) {
            return '-';
        }

        $labels = $tingkats->map(fn($t) => strtoupper($t))->values();

        if ($labels->count() === 1) {
            return $labels->first();
        }

        if ($labels->count() === 2) {
            return $labels->join(' & ');
        }

        $last = $labels->pop();
        return $labels->implode(', ') . ', dan ' . $last;
    }
}