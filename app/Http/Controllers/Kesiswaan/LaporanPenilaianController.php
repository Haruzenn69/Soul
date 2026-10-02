<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Services\PenilaianExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanPenilaianController extends Controller
{
    public function index(Request $request)
    {
        $periode = Penilaian::periodeSekarang()['label'];
        $q = strtolower(trim((string) $request->input('q')));
        $statusFilter = $request->input('status');
        $sort = in_array($request->input('sort'), ['nama', 'anggota', 'dinilai', 'rata'], true) ? $request->input('sort') : 'dinilai';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $ekskuls = Ekskul::query()
            ->with(['pembina.user', 'pendaftarans' => fn ($query) => $query->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])])
            ->whereHas('pendaftarans', fn ($query) => $query->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN]))
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

                $totalAnggota = $anggotaIds->count();
                $sudahDinilai = (int) ($counts->terkirim ?? 0);
                $statusKelengkapan = $sudahDinilai > 0 ? ($sudahDinilai >= $totalAnggota ? 'lengkap' : 'sebagian') : 'menunggu';

                return (object) [
                    'ekskul' => $ekskul,
                    'totalAnggota' => $totalAnggota,
                    'sudahDinilai' => $sudahDinilai,
                    'rataNilai' => round((float) ($rata ?? 0), 2),
                    'statusKelengkapan' => $statusKelengkapan,
                ];
            })
            ->filter(function ($item) use ($q, $statusFilter) {
                if ($q) {
                    $namaEkskul = strtolower($item->ekskul->nama_ekskul);
                    $namaPembina = strtolower($item->ekskul->pembina?->nama ?? '');
                    if (!str_contains($namaEkskul, $q) && !str_contains($namaPembina, $q)) {
                        return false;
                    }
                }
                if ($statusFilter && $item->statusKelengkapan !== $statusFilter) {
                    return false;
                }
                return true;
            });

        if ($direction === 'asc') {
            $ekskuls = $ekskuls->sortBy(function ($item) use ($sort) {
                return match ($sort) {
                    'nama' => $item->ekskul->nama_ekskul,
                    'anggota' => $item->totalAnggota,
                    'rata' => $item->rataNilai,
                    default => $item->sudahDinilai,
                };
            })->values();
        } else {
            $ekskuls = $ekskuls->sortByDesc(function ($item) use ($sort) {
                return match ($sort) {
                    'nama' => $item->ekskul->nama_ekskul,
                    'anggota' => $item->totalAnggota,
                    'rata' => $item->rataNilai,
                    default => $item->sudahDinilai,
                };
            })->values();
        }

        return view('kesiswaan.laporan-penilaian.index', compact('ekskuls', 'periode', 'sort', 'direction'));
    }

    public function show(Request $request, Ekskul $ekskul)
    {
        $periode = Penilaian::periodeSekarang()['label'];
        $service = app(PenilaianExportService::class);

        $rows = $service->rows($ekskul, Penilaian::STATUS_TERKIRIM, $periode);
        $summary = $service->summary($ekskul);

        $q = strtolower(trim((string) $request->input('q')));
        $predikat = $request->input('predikat');
        $sort = in_array($request->input('sort'), ['nama', 'nilai_akhir', 'kehadiran', 'predikat'], true) ? $request->input('sort') : 'nilai_akhir';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        if ($q || $predikat) {
            $rows = $rows->filter(function ($row) use ($q, $predikat) {
                if ($q) {
                    $nama = strtolower($row->siswa?->nama ?? '');
                    $nis = strtolower($row->siswa?->nis ?? '');
                    if (!str_contains($nama, $q) && !str_contains($nis, $q)) {
                        return false;
                    }
                }
                if ($predikat && $row->predikat !== $predikat) {
                    return false;
                }
                return true;
            });
        }

        if ($direction === 'asc') {
            $rows = $rows->sortBy(function ($row) use ($sort) {
                return match ($sort) {
                    'nama' => $row->siswa?->nama ?? '',
                    'kehadiran' => (float) $row->persentase_kehadiran,
                    'predikat' => (string) $row->predikat,
                    default => (float) $row->nilai_akhir,
                };
            })->values();
        } else {
            $rows = $rows->sortByDesc(function ($row) use ($sort) {
                return match ($sort) {
                    'nama' => $row->siswa?->nama ?? '',
                    'kehadiran' => (float) $row->persentase_kehadiran,
                    'predikat' => (string) $row->predikat,
                    default => (float) $row->nilai_akhir,
                };
            })->values();
        }

        return view('kesiswaan.laporan-penilaian.show', compact('ekskul', 'rows', 'summary', 'periode', 'sort', 'direction'));
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
