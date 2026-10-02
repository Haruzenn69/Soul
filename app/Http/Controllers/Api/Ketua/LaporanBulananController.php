<?php

namespace App\Http\Controllers\Api\Ketua;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Http\Resources\LaporanBulananResource;
use App\Models\Kegiatan;
use App\Models\LaporanBulanan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Services\NotifikasiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class LaporanBulananController extends ApiController
{
    use KetuaEkskul;

    public function index(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $query = LaporanBulanan::where('ekskul_id', $ekskul->id);

        $total = (clone $query)->count();

        if ($cari = $request->input('cari')) {
            $query->where(fn ($q) => $q
                ->where('bulan', 'like', "%{$cari}%")
                ->orWhere('materi_kegiatan', 'like', "%{$cari}%"));
        }

        if ($status = $request->input('status')) {
            if ($status !== 'semua') {
                $query->where('status', $status);
            }
        }

        $sort = $request->input('sort', 'bulan');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sort, ['bulan', 'status'], true)) {
            $sort = 'bulan';
        }

        $laporans = $query->orderBy($sort, $direction)->paginate(8);

        return $this->ok([
            'total' => $total,
            'laporans' => LaporanBulananResource::collection($laporans)->resolve(),
            'pagination' => [
                'current' => $laporans->currentPage(),
                'last' => $laporans->lastPage(),
                'per_page' => $laporans->perPage(),
            ],
        ]);
    }

    public function show(LaporanBulanan $laporan_bulanan): JsonResponse
    {
        $this->ensureEkskul($laporan_bulanan);

        return $this->ok([
            'laporan' => (new LaporanBulananResource($laporan_bulanan))->resolve(),
        ]);
    }

    public function downloadPdf(LaporanBulanan $laporan_bulanan): Response
    {
        $this->ensureEkskul($laporan_bulanan);

        $ekskul = $laporan_bulanan->ekskul;
        $kelas = $ekskul->pendaftarans()
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->with('siswa.kelas')
            ->get()
            ->pluck('siswa.kelas.tingkat')
            ->unique()
            ->sort()
            ->map(fn ($tingkat) => config("kelas.tingkat.{$tingkat}"))
            ->values();
        $kelasLabel = match ($kelas->count()) {
            0 => '-',
            1 => $kelas->first(),
            2 => $kelas->join(' & '),
            default => $kelas->slice(0, -1)->implode(', ').', dan '.$kelas->last(),
        };
        $tahun = substr($laporan_bulanan->bulan, 0, 4);
        $bulan = substr($laporan_bulanan->bulan, 5, 2);
        $kegiatanQuery = Kegiatan::where('ekskul_id', $ekskul->id)
            ->whereYear('tanggal_kegiatan', $tahun)
            ->whereMonth('tanggal_kegiatan', $bulan)
            ->orderBy('tanggal_kegiatan');
        $rutinKegiatans = (clone $kegiatanQuery)->whereNull('jenis_kegiatan')->get();
        $eventKegiatans = (clone $kegiatanQuery)->whereNotNull('jenis_kegiatan')->get();
        $dokumentasiRutin = $rutinKegiatans->whereNotNull('dokumentasi')
            ->pluck('dokumentasi')->values()->all();
        $dokumentasiEvent = $eventKegiatans->whereNotNull('dokumentasi')
            ->pluck('dokumentasi')->values()->all();

        $pdf = Pdf::loadView('ketua.laporan-bulanan.pdf', [
            'laporan' => $laporan_bulanan,
            'kelas' => $kelasLabel,
            'rutinKegiatans' => $rutinKegiatans,
            'eventKegiatans' => $eventKegiatans,
            'dokumentasiRutin' => $dokumentasiRutin,
            'dokumentasiEvent' => $dokumentasiEvent,
        ]);
        $filename = 'laporan-'.str_replace('/', '-', $laporan_bulanan->bulan).'-'.Str::slug($ekskul->nama_ekskul ?? 'ekskul').'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tujuan' => 'nullable|string',
            'evaluasi_keberhasilan' => 'nullable|string',
            'evaluasi_kendala' => 'nullable|string',
            'evaluasi_solusi' => 'nullable|string',
        ]);

        $ekskul = $this->ekskul();
        $bulan = now()->format('Y-m');

        $existing = LaporanBulanan::where('ekskul_id', $ekskul->id)->where('bulan', $bulan)->exists();

        if ($existing) {
            return $this->error('Laporan untuk bulan ini sudah pernah dibuat.', 422);
        }

        $laporan = LaporanBulanan::create([
            'ekskul_id' => $ekskul->id,
            'bulan' => $bulan,
            'materi_kegiatan' => $this->generateMateri($ekskul, $bulan),
            'tujuan' => $validated['tujuan'] ?? null,
            'kehadiran' => $this->generateKehadiran($ekskul, $bulan),
            'evaluasi_keberhasilan' => $validated['evaluasi_keberhasilan'] ?? null,
            'evaluasi_kendala' => $validated['evaluasi_kendala'] ?? null,
            'evaluasi_solusi' => $validated['evaluasi_solusi'] ?? null,
            'dokumentasi_kegiatan' => $this->generateDokumentasiKegiatan($ekskul, $bulan),
            'status' => LaporanBulanan::STATUS_DRAFT,
        ]);

        return $this->created(
            ['laporan' => (new LaporanBulananResource($laporan))->resolve()],
            'Laporan bulanan berhasil dibuat.',
        );
    }

    public function update(Request $request, LaporanBulanan $laporan_bulanan): JsonResponse
    {
        $this->ensureEkskul($laporan_bulanan);

        abort_if(
            in_array($laporan_bulanan->status, [LaporanBulanan::STATUS_MENUNGGU, LaporanBulanan::STATUS_DISETUJUI]),
            403,
            'Laporan yang sudah diserahkan atau disetujui tidak dapat diubah.',
        );

        $validated = $request->validate([
            'tujuan' => 'nullable|string',
            'evaluasi_keberhasilan' => 'nullable|string',
            'evaluasi_kendala' => 'nullable|string',
            'evaluasi_solusi' => 'nullable|string',
        ]);

        $ekskul = $laporan_bulanan->ekskul;
        $bulan = $laporan_bulanan->bulan;

        $laporan_bulanan->update([
            'materi_kegiatan' => $this->generateMateri($ekskul, $bulan),
            'tujuan' => $validated['tujuan'] ?? null,
            'kehadiran' => $this->generateKehadiran($ekskul, $bulan),
            'evaluasi_keberhasilan' => $validated['evaluasi_keberhasilan'] ?? null,
            'evaluasi_kendala' => $validated['evaluasi_kendala'] ?? null,
            'evaluasi_solusi' => $validated['evaluasi_solusi'] ?? null,
            'dokumentasi_kegiatan' => $this->generateDokumentasiKegiatan($ekskul, $bulan),
            'status' => LaporanBulanan::STATUS_DRAFT,
        ]);

        return $this->ok(
            ['laporan' => (new LaporanBulananResource($laporan_bulanan->fresh()))->resolve()],
            'Laporan bulanan berhasil diperbarui.',
        );
    }

    public function submitToPembina(LaporanBulanan $laporan_bulanan): JsonResponse
    {
        $this->ensureEkskul($laporan_bulanan);

        abort_if(
            in_array($laporan_bulanan->status, [LaporanBulanan::STATUS_MENUNGGU, LaporanBulanan::STATUS_DISETUJUI]),
            403,
            'Laporan ini sudah diserahkan atau disetujui.',
        );

        $laporan_bulanan->update(['status' => LaporanBulanan::STATUS_MENUNGGU]);

        NotifikasiService::laporanDiserahkan($laporan_bulanan);

        return $this->noContent('Laporan berhasil diserahkan ke pembina.');
    }

    private function generateMateri($ekskul, $bulan): string
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

    private function narasiKegiatan($kegiatan): string
    {
        $line = "Pada tanggal {$kegiatan->tanggalText()}, kegiatan yang dilaksanakan berupa {$kegiatan->materi}.";

        if ($kegiatan->deskripsi) {
            $line .= " {$kegiatan->deskripsi}";
        }

        return $line;
    }

    private function generateKehadiran($ekskul, $bulan): string
    {
        $anggotas = $ekskul->pendaftarans()
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->with('siswa.kelas')
            ->get();

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
                $hadirCount += Presensi::where('pendaftaran_id', $anggota->id)
                    ->whereIn('kegiatan_id', $kegiatanIds->toArray())
                    ->where('status', 'hadir')
                    ->count();
            }

            $totalKesempatan = $total * $totalKegiatan;
            $persentase = $totalKesempatan > 0 ? round(($hadirCount / $totalKesempatan) * 100) : 0;

            $teksParts[] = "Kelas {$label} memiliki {$total} siswa aktif dengan persentase kehadiran sebesar {$persentase}%.";
        }

        return implode(' ', $teksParts);
    }

    private function generateDokumentasiKegiatan($ekskul, $bulan): array
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
}
