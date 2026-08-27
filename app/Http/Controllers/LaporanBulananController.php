<?php

namespace App\Http\Controllers;

use App\Models\LaporanBulanan;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanBulananController extends Controller
{
    private function getEkskul()
    {
        $pendaftaran = auth()->user()->siswa?->pendaftarans()->where('status', 'diterima')->first();
        abort_unless($pendaftaran, 404, 'Anda belum tergabung dalam ekskul mana pun.');
        return $pendaftaran->ekskul;
    }

    private function generateKelas($ekskul)
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
            $teks .= $line . "\n";
        }

        return trim($teks);
    }

    private function generateKehadiran($ekskul, $bulan)
    {
        $anggotas = $ekskul->pendaftarans()->where('status', 'diterima')->with('siswa.kelas')->get();

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
            $label = strtoupper($tingkat);
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
        $ekskul = $this->getEkskul();
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
            'bulan' => 'required|string',
            'tujuan' => 'nullable|string',
            'evaluasi_keberhasilan' => 'nullable|string',
            'evaluasi_kendala' => 'nullable|string',
            'evaluasi_solusi' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $ekskul = $this->getEkskul();

        $dokumentasiPath = null;
        if ($request->hasFile('dokumentasi')) {
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi', 'public');
        }

        $materi = $this->generateMateri($ekskul, $validated['bulan']);
        $kehadiran = $this->generateKehadiran($ekskul, $validated['bulan']);

        LaporanBulanan::create([
            'ekskul_id' => $ekskul->id,
            'bulan' => $validated['bulan'],
            'materi_kegiatan' => $materi,
            'tujuan' => $validated['tujuan'] ?? null,
            'kehadiran' => $kehadiran,
            'evaluasi_keberhasilan' => $validated['evaluasi_keberhasilan'] ?? null,
            'evaluasi_kendala' => $validated['evaluasi_kendala'] ?? null,
            'evaluasi_solusi' => $validated['evaluasi_solusi'] ?? null,
            'dokumentasi' => $dokumentasiPath,
            'status' => 'draft',
        ]);

        return redirect()->route('ketua.laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil dibuat.');
    }

    public function show(LaporanBulanan $laporan_bulanan)
    {
        return view('ketua.laporan-bulanan.show', ['laporan' => $laporan_bulanan]);
    }

    public function downloadPdf(LaporanBulanan $laporan_bulanan)
    {
        $ekskul = $laporan_bulanan->ekskul;
        $kelas = $this->generateKelas($ekskul);
        $pdf = Pdf::loadView('ketua.laporan-bulanan.pdf', ['laporan' => $laporan_bulanan, 'kelas' => $kelas]);
        $filename = 'laporan-' . str_replace('/', '-', $laporan_bulanan->bulan) . '-' . ($ekskul->nama_ekskul ?? 'ekskul') . '.pdf';
        return $pdf->download($filename);
    }
}
