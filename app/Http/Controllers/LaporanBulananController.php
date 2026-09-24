<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Kegiatan;
use App\Models\LaporanBulanan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Services\NotifikasiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanBulananController extends Controller
{
    use KetuaEkskul;

    private function generateKelas($ekskul)
    {
        $tingkats = $ekskul->pendaftarans()
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->with('siswa.kelas')
            ->get()
            ->pluck('siswa.kelas.tingkat')
            ->unique()
            ->sort()
            ->values();

        if ($tingkats->isEmpty()) {
            return '-';
        }

        $labels = $tingkats->map(fn ($t) => config("kelas.tingkat.{$t}"))->values();

        if ($labels->count() === 1) {
            return $labels->first();
        }

        if ($labels->count() === 2) {
            return $labels->join(' & ');
        }

        $last = $labels->pop();

        return $labels->implode(', ').', dan '.$last;
    }

    private function generateDokumentasiKegiatan($ekskul, $bulan)
    {
        return Kegiatan::where('ekskul_id', $ekskul->id)
            ->whereYear('tanggal_kegiatan', substr($bulan, 0, 4))
            ->whereMonth('tanggal_kegiatan', substr($bulan, 5, 2))
            ->whereNotNull('dokumentasi')
            ->orderBy('tanggal_kegiatan')
            ->pluck('dokumentasi')
            ->values()
            ->toArray();
    }

    private function generateMateri($ekskul, $bulan)
    {
        $kegiatans = Kegiatan::where('ekskul_id', $ekskul->id)
            ->whereYear('tanggal_kegiatan', substr($bulan, 0, 4))
            ->whereMonth('tanggal_kegiatan', substr($bulan, 5, 2))
            ->orderBy('tanggal_kegiatan')
            ->get();

        if ($kegiatans->isEmpty()) {
            return 'Belum ada kegiatan yang tercatat untuk bulan ini.';
        }

        $teks = '';
        foreach ($kegiatans as $k) {
            $tanggal = $k->tanggal_kegiatan->translatedFormat('d F Y');
            $line = "Pada tanggal {$tanggal}, kegiatan yang dilaksanakan berupa {$k->materi}.";
            if ($k->deskripsi) {
                $line .= " {$k->deskripsi}";
            }
            $teks .= $line."\n";
        }

        return trim($teks);
    }

    private function generateKehadiran($ekskul, $bulan)
    {
        $anggotas = $ekskul->pendaftarans()->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->with('siswa.kelas')->get();

        $kegiatanIds = Kegiatan::where('ekskul_id', $ekskul->id)
            ->whereYear('tanggal_kegiatan', substr($bulan, 0, 4))
            ->whereMonth('tanggal_kegiatan', substr($bulan, 5, 2))
            ->pluck('id');

        $totalKegiatan = $kegiatanIds->count();

        if ($totalKegiatan === 0) {
            return 'Belum ada kegiatan yang tercatat untuk bulan ini.';
        }

        $byTingkat = $anggotas->groupBy('siswa.kelas.tingkat');
        $teksParts = [];

        foreach ($byTingkat as $tingkat => $anggotaTingkat) {
            $label = config("kelas.tingkat.{$tingkat}");
            $total = $anggotaTingkat->count();

            $hadirCount = 0;
            foreach ($anggotaTingkat as $anggota) {
                $hadir = Presensi::where('pendaftaran_id', $anggota->id)
                    ->whereIn('kegiatan_id', $kegiatanIds->toArray())
                    ->whereRaw('status = ?', ['hadir'])
                    ->count();
                $hadirCount += $hadir;
            }

            $totalKesempatan = $total * $totalKegiatan;
            $persentase = $totalKesempatan > 0 ? round(($hadirCount / $totalKesempatan) * 100) : 0;

            $teksParts[] = "Kelas {$label} memiliki {$total} siswa aktif dengan persentase kehadiran sebesar {$persentase}%.";
        }

        return implode(' ', $teksParts);
    }

    public function index()
    {
        $ekskul = $this->ekskul();
        $laporans = LaporanBulanan::where('ekskul_id', $ekskul->id)
            ->latest('bulan')
            ->get();

        return view('ketua.laporan-bulanan.index', compact('laporans'));
    }

    public function create()
    {
        return view('ketua.laporan-bulanan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tujuan' => 'nullable|string',
            'evaluasi_keberhasilan' => 'nullable|string',
            'evaluasi_kendala' => 'nullable|string',
            'evaluasi_solusi' => 'nullable|string',
        ]);

        $ekskul = $this->ekskul();

        $bulan = now()->format('Y-m');

        $existing = LaporanBulanan::where('ekskul_id', $ekskul->id)->where('bulan', $bulan)->first();
        if ($existing) {
            return back()->with('error', 'Laporan untuk bulan ini sudah pernah dibuat.');
        }

        $materi = $this->generateMateri($ekskul, $bulan);
        $kehadiran = $this->generateKehadiran($ekskul, $bulan);
        $dokumentasiKegiatan = $this->generateDokumentasiKegiatan($ekskul, $bulan);

        $laporan = LaporanBulanan::create([
            'ekskul_id' => $ekskul->id,
            'bulan' => $bulan,
            'materi_kegiatan' => $materi,
            'tujuan' => $validated['tujuan'] ?? null,
            'kehadiran' => $kehadiran,
            'evaluasi_keberhasilan' => $validated['evaluasi_keberhasilan'] ?? null,
            'evaluasi_kendala' => $validated['evaluasi_kendala'] ?? null,
            'evaluasi_solusi' => $validated['evaluasi_solusi'] ?? null,
            'dokumentasi_kegiatan' => $dokumentasiKegiatan,
            'status' => LaporanBulanan::STATUS_DRAFT,
        ]);

        return redirect()->route('ketua.laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil dibuat.');
    }

    public function show(LaporanBulanan $laporan_bulanan)
    {
        $this->ensureEkskul($laporan_bulanan);

        return view('ketua.laporan-bulanan.show', ['laporan' => $laporan_bulanan]);
    }

    public function edit(LaporanBulanan $laporan_bulanan)
    {
        $this->ensureEkskul($laporan_bulanan);
        abort_if(in_array($laporan_bulanan->status, [LaporanBulanan::STATUS_MENUNGGU, LaporanBulanan::STATUS_DISETUJUI]), 403, 'Laporan yang sudah diserahkan atau disetujui tidak dapat diubah.');

        return view('ketua.laporan-bulanan.edit', ['laporan' => $laporan_bulanan]);
    }

    public function update(Request $request, LaporanBulanan $laporan_bulanan)
    {
        $this->ensureEkskul($laporan_bulanan);
        abort_if(in_array($laporan_bulanan->status, [LaporanBulanan::STATUS_MENUNGGU, LaporanBulanan::STATUS_DISETUJUI]), 403, 'Laporan yang sudah diserahkan atau disetujui tidak dapat diubah.');

        $validated = $request->validate([
            'tujuan' => 'nullable|string',
            'evaluasi_keberhasilan' => 'nullable|string',
            'evaluasi_kendala' => 'nullable|string',
            'evaluasi_solusi' => 'nullable|string',
        ]);

        $ekskul = $laporan_bulanan->ekskul;
        $bulan = $laporan_bulanan->bulan;

        $materi = $this->generateMateri($ekskul, $bulan);
        $kehadiran = $this->generateKehadiran($ekskul, $bulan);
        $dokumentasiKegiatan = $this->generateDokumentasiKegiatan($ekskul, $bulan);

        $laporan_bulanan->update([
            'materi_kegiatan' => $materi,
            'tujuan' => $validated['tujuan'] ?? null,
            'kehadiran' => $kehadiran,
            'evaluasi_keberhasilan' => $validated['evaluasi_keberhasilan'] ?? null,
            'evaluasi_kendala' => $validated['evaluasi_kendala'] ?? null,
            'evaluasi_solusi' => $validated['evaluasi_solusi'] ?? null,
            'dokumentasi_kegiatan' => $dokumentasiKegiatan,
            'status' => LaporanBulanan::STATUS_DRAFT,
        ]);

        return redirect()->route('ketua.laporan-bulanan.show', $laporan_bulanan)
            ->with('success', 'Laporan bulanan berhasil diperbarui.');
    }

    public function submitToPembina(LaporanBulanan $laporan_bulanan)
    {
        $this->ensureEkskul($laporan_bulanan);
        abort_if(in_array($laporan_bulanan->status, [LaporanBulanan::STATUS_MENUNGGU, LaporanBulanan::STATUS_DISETUJUI]), 403, 'Laporan ini sudah diserahkan atau disetujui.');

        $laporan_bulanan->update(['status' => LaporanBulanan::STATUS_MENUNGGU]);

        NotifikasiService::laporanDiserahkan($laporan_bulanan);

        return redirect()->route('ketua.laporan-bulanan.show', $laporan_bulanan)
            ->with('success', 'Laporan berhasil diserahkan ke pembina.');
    }

    public function downloadPdf(LaporanBulanan $laporan_bulanan)
    {
        $this->ensureEkskul($laporan_bulanan);

        $ekskul = $laporan_bulanan->ekskul;
        $kelas = $this->generateKelas($ekskul);

        $dokumentasiKegiatan = Kegiatan::where('ekskul_id', $ekskul->id)
            ->whereYear('tanggal_kegiatan', substr($laporan_bulanan->bulan, 0, 4))
            ->whereMonth('tanggal_kegiatan', substr($laporan_bulanan->bulan, 5, 2))
            ->whereNotNull('dokumentasi')
            ->orderBy('tanggal_kegiatan')
            ->pluck('dokumentasi')
            ->values()
            ->toArray();

        $pdf = Pdf::loadView('ketua.laporan-bulanan.pdf', [
            'laporan' => $laporan_bulanan,
            'kelas' => $kelas,
            'dokumentasiKegiatan' => $dokumentasiKegiatan,
        ]);
        $filename = 'laporan-'.str_replace('/', '-', $laporan_bulanan->bulan).'-'.($ekskul->nama_ekskul ?? 'ekskul').'.pdf';

        return $pdf->download($filename);
    }
}
