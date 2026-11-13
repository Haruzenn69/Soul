<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Kegiatan;
use App\Models\LaporanBulanan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Services\NotifikasiService;
use App\Support\TableKit;
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

        $rutin = $kegiatans->reject(fn ($k) => $k->isEvent());
        $event = $kegiatans->filter(fn ($k) => $k->isEvent());

        $teks = '';

        if ($rutin->isNotEmpty()) {
            $teks .= "Kegiatan Rutin:\n";
            foreach ($rutin as $k) {
                $teks .= '- '.$this->narasiKegiatan($k)."\n";
            }
        }

        if ($event->isNotEmpty()) {
            $teks .= "Kegiatan Event (Diklat, Lomba, dll.):\n";
            foreach ($event as $k) {
                $teks .= '- '.$this->narasiKegiatan($k)."\n";
            }
        }

        return trim($teks) ?: 'Belum ada kegiatan yang tercatat untuk bulan ini.';
    }

    private function narasiKegiatan($kegiatan)
    {
        $tanggal = $kegiatan->tanggalText();
        $line = "Pada tanggal {$tanggal}, kegiatan yang dilaksanakan berupa {$kegiatan->materi}.";
        if ($kegiatan->deskripsi) {
            $line .= " {$kegiatan->deskripsi}";
        }

        return $line;
    }

    private function generateKehadiran($ekskul, $bulan)
    {
        $anggotas = $ekskul->pendaftarans()->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->with('siswa.kelas')->get();

        $kegiatanIds = Kegiatan::where('ekskul_id', $ekskul->id)
            ->whereYear('tanggal_kegiatan', substr($bulan, 0, 4))
            ->whereMonth('tanggal_kegiatan', substr($bulan, 5, 2))
            ->whereNull('jenis_kegiatan')
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

    public function index(Request $request)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);

        if ($isKesiswaan && ! $request->filled('ekskul')) {
            $ekskuls = \App\Models\Ekskul::orderBy('nama_ekskul')->get();
            $cards = $ekskuls->map(function (\App\Models\Ekskul $e) {
                $base = LaporanBulanan::where('ekskul_id', $e->id);
                $total = (clone $base)->count();
                $draftCount = (clone $base)->where('status', 'draft')->count();
                $menungguCount = (clone $base)->where('status', 'menunggu')->count();
                $disetujuiCount = (clone $base)->where('status', 'disetujui')->count();
                $terbaru = (clone $base)->latest('bulan')->first();

                $badge = null;
                if ($menungguCount > 0) {
                    $badge = ['text' => $menungguCount.' Menunggu Pembina', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300'];
                } elseif ($draftCount > 0) {
                    $badge = ['text' => $draftCount.' Draft Tersedia', 'class' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300'];
                }

                $stats = [
                    ['label' => 'Total Laporan', 'value' => $total.' Laporan', 'class' => 'font-bold text-slate-800 dark:text-slate-200'],
                    ['label' => 'Draft / Belum Kirim', 'value' => $draftCount.' Laporan', 'class' => 'font-semibold text-slate-600 dark:text-slate-300'],
                    ['label' => 'Telah Disetujui', 'value' => $disetujuiCount.' Laporan', 'class' => 'font-bold text-emerald-600 dark:text-emerald-400'],
                ];
                if ($terbaru) {
                    $stats[] = ['label' => 'Periode Terbaru', 'value' => \Carbon\Carbon::createFromFormat('Y-m', $terbaru->bulan)->translatedFormat('M Y'), 'class' => 'font-medium text-slate-500 text-[11px]'];
                }

                return (object) [
                    'ekskul' => $e,
                    'stats' => $stats,
                    'badge' => $badge,
                    'actionText' => 'Kelola Laporan',
                    'actionUrl' => route('ketua.laporan-bulanan.index', ['ekskul' => $e->id]),
                ];
            });

            return view('ketua.laporan-bulanan.index', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => true,
            ]);
        }

        $ekskul = $this->ekskul();
        $base = LaporanBulanan::where('ekskul_id', $ekskul->id);
        $total = (clone $base)->count();

        [$sort, $direction] = TableKit::sort(['bulan', 'status'], 'bulan', 'desc');

        $laporans = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(function ($sub) use ($cari) {
                    $sub->where('bulan', 'like', "%{$cari}%")
                        ->orWhere('materi_kegiatan', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('status') && $request->input('status') !== 'semua', fn ($query) => $query->where('status', $request->input('status')))
            ->orderBy($sort, $direction)
            ->paginate(8)
            ->withQueryString();

        return view('ketua.laporan-bulanan.index', compact('laporans', 'total', 'sort', 'direction', 'ekskul', 'isKesiswaan'));
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

        $tahun = substr($laporan_bulanan->bulan, 0, 4);
        $bulan = substr($laporan_bulanan->bulan, 5, 2);

        $kegiatanQuery = Kegiatan::where('ekskul_id', $ekskul->id)
            ->whereYear('tanggal_kegiatan', $tahun)
            ->whereMonth('tanggal_kegiatan', $bulan)
            ->orderBy('tanggal_kegiatan');

        $rutinKegiatans = (clone $kegiatanQuery)->whereNull('jenis_kegiatan')->get();
        $eventKegiatans = (clone $kegiatanQuery)->whereNotNull('jenis_kegiatan')->get();

        $dokumentasiRutin = $rutinKegiatans->filter(fn ($k) => filled($k->dokumentasi))->pluck('dokumentasi')->values()->toArray();
        $dokumentasiEvent = $eventKegiatans->filter(fn ($k) => filled($k->dokumentasi))->pluck('dokumentasi')->values()->toArray();

        $rekapData = app(\App\Services\RekapAbsensiService::class)->rekap($ekskul, $laporan_bulanan->bulan);

        $pdf = Pdf::loadView('ketua.laporan-bulanan.pdf', array_merge($rekapData, [
            'laporan' => $laporan_bulanan,
            'kelas' => $kelas,
            'rutinKegiatans' => $rutinKegiatans,
            'eventKegiatans' => $eventKegiatans,
            'dokumentasiRutin' => $dokumentasiRutin,
            'dokumentasiEvent' => $dokumentasiEvent,
        ]));
        $filename = 'laporan-'.str_replace('/', '-', $laporan_bulanan->bulan).'-'.($ekskul->nama_ekskul ?? 'ekskul').'.pdf';

        return $pdf->download($filename);
    }
}
