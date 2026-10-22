<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\PenilaianExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanPenilaianController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::getActive();
        $daftarPeriode = Penilaian::daftarPeriode();
        $defaultPeriode = Penilaian::periodeSekarang($tahunAjaranAktif)['label'];
        $periode = $request->input('periode', $defaultPeriode);

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

                $penilaianTerakhir = Penilaian::where('periode', $periode)
                    ->whereIn('pendaftaran_id', $anggotaIds)
                    ->whereNotNull('dinilai_oleh')
                    ->with('penilai')
                    ->latest('updated_at')
                    ->first();

                $pembinaPenilai = $penilaianTerakhir?->penilai ?? $ekskul->pembina;

                $totalAnggota = $anggotaIds->count();
                $sudahDinilai = (int) ($counts->terkirim ?? 0);
                $statusKelengkapan = $sudahDinilai > 0 ? ($sudahDinilai >= $totalAnggota ? 'lengkap' : 'sebagian') : 'menunggu';

                return (object) [
                    'ekskul' => $ekskul,
                    'totalAnggota' => $totalAnggota,
                    'sudahDinilai' => $sudahDinilai,
                    'rataNilai' => round((float) ($rata ?? 0), 2),
                    'statusKelengkapan' => $statusKelengkapan,
                    'pembinaPenilai' => $pembinaPenilai,
                ];
            })
            ->filter(function ($item) use ($q, $statusFilter) {
                if ($q) {
                    $namaEkskul = strtolower($item->ekskul->nama_ekskul);
                    $namaPembina = strtolower($item->ekskul->pembina?->nama ?? '');
                    $namaPenilai = strtolower($item->pembinaPenilai?->nama ?? '');
                    if (!str_contains($namaEkskul, $q) && !str_contains($namaPembina, $q) && !str_contains($namaPenilai, $q)) {
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

        return view('kesiswaan.laporan-penilaian.index', compact('ekskuls', 'periode', 'daftarPeriode', 'tahunAjaranAktif', 'sort', 'direction'));
    }

    public function show(Request $request, Ekskul $ekskul)
    {
        $tahunAjaranAktif = TahunAjaran::getActive();
        $daftarPeriode = Penilaian::daftarPeriode();
        $defaultPeriode = Penilaian::periodeSekarang($tahunAjaranAktif)['label'];
        $periode = $request->input('periode', $defaultPeriode);

        $service = app(PenilaianExportService::class);
        $rows = $service->rows($ekskul, Penilaian::STATUS_TERKIRIM, $periode);
        $summary = $service->summary($ekskul, $periode);

        // Ambil pembina penilai yang tercatat di penilaian atau pembina ekskul
        $contohPenilaian = Penilaian::where('periode', $periode)
            ->whereIn('pendaftaran_id', $ekskul->pendaftarans()->pluck('id'))
            ->whereNotNull('dinilai_oleh')
            ->with('penilai')
            ->latest('updated_at')
            ->first();
        $pembinaPenilai = $contohPenilaian?->penilai ?? $ekskul->pembina;

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

        return view('kesiswaan.laporan-penilaian.show', compact('ekskul', 'rows', 'summary', 'periode', 'daftarPeriode', 'tahunAjaranAktif', 'pembinaPenilai', 'sort', 'direction'));
    }

    public function downloadPdf(Request $request, Ekskul $ekskul)
    {
        $periode = $request->input('periode', Penilaian::periodeSekarang()['label']);
        $rows = app(PenilaianExportService::class)->rows($ekskul, Penilaian::STATUS_TERKIRIM, $periode);
        
        $contohPenilaian = Penilaian::where('periode', $periode)
            ->whereIn('pendaftaran_id', $ekskul->pendaftarans()->pluck('id'))
            ->whereNotNull('dinilai_oleh')
            ->with('penilai')
            ->latest('updated_at')
            ->first();
        $pembina = $contohPenilaian?->penilai ?? $ekskul->pembina;

        $pdf = Pdf::loadView('penilaian.pdf', [
            'ekskul' => $ekskul,
            'periode' => $periode,
            'rows' => $rows,
            'pembina' => $pembina,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penilaian-'.str_replace(' ', '-', $ekskul->nama_ekskul).'-'.str_replace([' ', '/'], '-', $periode).'.pdf');
    }

    /**
     * Halaman Historic / Riwayat Nilai Seluruh Siswa
     */
    public function historisSiswa(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $kelasId = $request->input('kelas_id');
        $tahunAjaranId = $request->input('tahun_ajaran_id');

        $daftarKelas = Kelas::orderBy('nama')->get();
        $daftarTahunAjaran = TahunAjaran::orderBy('id', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::getActive();

        $query = Siswa::query()
            ->with([
                'kelas.tahunAjaran',
                'pendaftarans.ekskul',
                'pendaftarans.penilaians' => fn ($pQuery) => $pQuery->where('status', Penilaian::STATUS_TERKIRIM)->with('penilai'),
            ]);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($tahunAjaranId) {
            $taModel = TahunAjaran::find($tahunAjaranId);
            if ($taModel) {
                $taNama = trim($taModel->nama);
                $query->whereHas('pendaftarans.penilaians', function ($pSub) use ($taNama) {
                    $pSub->where('status', Penilaian::STATUS_TERKIRIM)
                        ->where('periode', 'like', "%{$taNama}%");
                });
            }
        }

        $siswas = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('kesiswaan.laporan-penilaian.historis-siswa', compact(
            'siswas',
            'daftarKelas',
            'daftarTahunAjaran',
            'tahunAjaranAktif'
        ));
    }

    /**
     * Halaman Detail Historic / Riwayat Nilai Satu Siswa di Setiap Tahun Ajaran
     */
    public function detailSiswa(Siswa $siswa)
    {
        $siswa->load(['kelas.tahunAjaran', 'user', 'pendaftarans.ekskul.pembina']);

        $tahunAjaranAktif = TahunAjaran::getActive();

        // Ambil semua penilaian terkirim dari pendaftaran siswa
        $pendaftaranIds = $siswa->pendaftarans->pluck('id');
        $penilaians = Penilaian::whereIn('pendaftaran_id', $pendaftaranIds)
            ->where('status', Penilaian::STATUS_TERKIRIM)
            ->with(['pendaftaran.ekskul.pembina', 'penilai'])
            ->orderByDesc('id')
            ->get();

        // Kelompokkan per Tahun Ajaran, lalu per Semester
        $groupedPenilaians = $penilaians->groupBy(function (Penilaian $p) {
            $info = Penilaian::parsePeriode($p->periode);
            return $info['tahun_ajaran'];
        })->map(function ($itemsByTa) {
            return $itemsByTa->groupBy(function (Penilaian $p) {
                $info = Penilaian::parsePeriode($p->periode);
                return $info['semester'];
            });
        });

        // Hitung statistik akumulatif siswa
        $totalPenilaian = $penilaians->count();
        $rataNilaiKeseluruhan = $totalPenilaian > 0 ? round($penilaians->avg('nilai_akhir'), 2) : 0;
        $nilaiTertinggi = $totalPenilaian > 0 ? $penilaians->max('nilai_akhir') : 0;
        $predikatTerbaik = $totalPenilaian > 0 ? Penilaian::hitungPredikat($nilaiTertinggi) : '-';
        $totalEkskulDiikuti = $siswa->pendaftarans->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->count();

        return view('kesiswaan.laporan-penilaian.detail-siswa', compact(
            'siswa',
            'penilaians',
            'groupedPenilaians',
            'tahunAjaranAktif',
            'totalPenilaian',
            'rataNilaiKeseluruhan',
            'nilaiTertinggi',
            'predikatTerbaik',
            'totalEkskulDiikuti'
        ));
    }
}
