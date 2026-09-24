<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Services\PenilaianExportService;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenilaianController extends Controller
{
    public function index()
    {
        $periode = Penilaian::periodeSekarang()['label'];

        $ekskuls = Ekskul::query()
            ->with(['pembina.user', 'pendaftarans' => fn ($q) => $q->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])])
            ->whereHas('pendaftarans', fn ($q) => $q->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN]))
            ->get()
            ->map(function (Ekskul $ekskul) use ($periode) {
                $anggotaIds = $ekskul->pendaftarans->pluck('id');

                $counts = Penilaian::where('periode', $periode)
                    ->whereIn('pendaftaran_id', $anggotaIds)
                    ->selectRaw("count(*) as total, sum(status = 'terkirim') as terkirim")
                    ->first();

                $rata = Penilaian::where('periode', $periode)
                    ->whereIn('pendaftaran_id', $anggotaIds)
                    ->where('status', Penilaian::STATUS_TERKIRIM)
                    ->avg('nilai_akhir');

                return (object) [
                    'ekskul' => $ekskul,
                    'totalAnggota' => $anggotaIds->count(),
                    'sudahDinilai' => (int) ($counts->terkirim ?? 0),
                    'rataNilai' => round((float) ($rata ?? 0), 2),
                ];
            })
            ->sortByDesc(fn ($item) => $item->sudahDinilai)
            ->values();

        return view('kesiswaan.laporan-penilaian.index', compact('ekskuls', 'periode'));
    }

    public function show(Ekskul $ekskul)
    {
        $periode = Penilaian::periodeSekarang()['label'];
        $service = app(PenilaianExportService::class);

        $rows = $service->rows($ekskul, Penilaian::STATUS_TERKIRIM, $periode);
        $summary = $service->summary($ekskul);

        return view('kesiswaan.laporan-penilaian.show', compact('ekskul', 'rows', 'summary', 'periode'));
    }

    public function downloadPdf(Ekskul $ekskul)
    {
        $rows = app(PenilaianExportService::class)->rows($ekskul, Penilaian::STATUS_TERKIRIM);
        $pembina = $ekskul->pembina;

        $pdf = Pdf::loadView('penilaian.pdf', [
            'ekskul' => $ekskul,
            'periode' => Penilaian::periodeSekarang()['label'],
            'rows' => $rows,
            'pembina' => $pembina,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penilaian-'.str_replace(' ', '-', $ekskul->nama_ekskul).'.pdf');
    }
}
