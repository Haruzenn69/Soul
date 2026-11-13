<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Services\NotifikasiService;
use App\Services\PenilaianExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PenilaianController extends Controller
{
    private function getEkskuls(): Collection
    {
        $user = auth()->user();

        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return Ekskul::orderBy('nama_ekskul')->get();
        }

        $pembina = $user?->pembina;

        return $pembina ? $pembina->ekskuls()->get() : collect();
    }

    private function resolveEkskul(Request $request): ?Ekskul
    {
        $ekskuls = $this->getEkskuls();
        $id = $request->integer('ekskul');

        if ($id && $ekskuls->pluck('id')->contains($id)) {
            return $ekskuls->firstWhere('id', $id);
        }

        // Jika hanya membina 1 ekskul, otomatis pilih ekskul tersebut
        if ($ekskuls->count() === 1) {
            return $ekskuls->first();
        }

        return null;
    }

    public function index(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskul = $this->resolveEkskul($request);

        $periode = Penilaian::periodeSekarang();

        // JIKA BELUM MEMILIH EKSKUL: TAMPILKAN PILIHAN EKSKUL TERLEBIH DAHULU
        if (! $ekskul) {
            $ekskulCards = $ekskuls->map(function (Ekskul $e) use ($periode) {
                $anggotaIds = $e->pendaftarans()
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->pluck('id');
                $totalAnggota = $anggotaIds->count();
                $sudahDinilai = Penilaian::where('periode', $periode['label'])
                    ->whereIn('pendaftaran_id', $anggotaIds)
                    ->count();
                $terkirim = Penilaian::where('periode', $periode['label'])
                    ->whereIn('pendaftaran_id', $anggotaIds)
                    ->where('status', Penilaian::STATUS_TERKIRIM)
                    ->exists();

                return (object) [
                    'ekskul' => $e,
                    'totalAnggota' => $totalAnggota,
                    'sudahDinilai' => $sudahDinilai,
                    'terkirim' => $terkirim,
                ];
            });

            return view('pembina.penilaian', compact('ekskuls', 'ekskul', 'ekskulCards', 'periode'));
        }

        $allRows = app(PenilaianExportService::class)->rows($ekskul, null, $periode['label']);

        // Ambil daftar kelas dari anggota ekskul agar opsi filter relevan
        $kelasOptions = $allRows->map(fn ($r) => $r->siswa?->kelas)
            ->filter()
            ->unique('id')
            ->sortBy('nama')
            ->values();

        // Jika ekskul belum ada anggota, tampilkan daftar kelas aktif sekolah
        if ($kelasOptions->isEmpty()) {
            $kelasOptions = Kelas::query()
                ->whereHas('tahunAjaran', fn ($query) => $query->where('status', 'aktif'))
                ->whereHas('siswas', fn ($query) => $query->where('status', 'aktif'))
                ->orderBy('nama')
                ->get();
        }

        $selectedKelasId = $request->input('kelas_id') ?: $request->input('kelas');
        $statusPenilaian = $request->input('status_penilaian');

        $rows = $allRows;
        if ($selectedKelasId) {
            $rows = $rows->filter(function ($row) use ($selectedKelasId) {
                if (is_numeric($selectedKelasId)) {
                    return (int) ($row->siswa?->kelas_id ?? 0) === (int) $selectedKelasId;
                }
                return ($row->kelas ?? '') === $selectedKelasId;
            })->values();
        }

        if ($statusPenilaian === 'sudah') {
            $rows = $rows->filter(fn ($row) => $row->penilaian !== null)->values();
        } elseif ($statusPenilaian === 'belum') {
            $rows = $rows->filter(fn ($row) => $row->penilaian === null)->values();
        }

        $sudahDikirim = (bool) $ekskul
            && Penilaian::where('periode', $periode['label'])
                ->whereIn('pendaftaran_id', $ekskul->pendaftarans()->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->pluck('id'))
                ->where('status', Penilaian::STATUS_TERKIRIM)
                ->exists();

        return view('pembina.penilaian', compact('ekskuls', 'ekskul', 'rows', 'allRows', 'kelasOptions', 'selectedKelasId', 'statusPenilaian', 'periode', 'sudahDikirim'));
    }

    public function store(Request $request)
    {
        $ekskul = $this->resolveEkskul($request);
        abort_unless($ekskul, 404, 'Tidak ada ekskul yang dapat dinilai.');

        $periode = Penilaian::periodeSekarang();
        $payload = $this->normalizePayload($request, $ekskul, $periode['label']);

        if ($payload->isNotEmpty()) {
            foreach ($payload as $item) {
                $this->simpan($item, Penilaian::STATUS_DRAFT, null);
            }

            return back()->with('success', 'Draf penilaian ekskul '.$ekskul->nama_ekskul.' berhasil disimpan.');
        }

        return back()->with('error', 'Tidak ada data penilaian yang diubah.');
    }

    public function kirim(Request $request)
    {
        $ekskul = $this->resolveEkskul($request);
        abort_unless($ekskul, 404, 'Tidak ada ekskul yang dapat dinilai.');

        $periode = Penilaian::periodeSekarang();
        $payload = $this->normalizePayload($request, $ekskul, $periode['label']);

        if ($payload->isEmpty()) {
            return back()->with('error', 'Tidak ada data penilaian yang dapat dikirim.');
        }

        foreach ($payload as $item) {
            $validated = validator($item['nilai'], [
                'nilai_sikap' => ['required', 'numeric', 'min:1', 'max:100'],
                'nilai_keaktifan' => ['required', 'numeric', 'min:1', 'max:100'],
                'nilai_keterampilan' => ['required', 'numeric', 'min:1', 'max:100'],
                'catatan' => ['nullable', 'string', 'max:1000'],
            ], [
                'min' => 'Nilai minimal 1.',
                'max' => 'Nilai maksimal 100.',
                'required' => ':attribute wajib diisi.',
            ])->validate();

            $this->simpan([
                'pendaftaran_id' => $item['pendaftaran_id'],
                'nilai_sikap' => $validated['nilai_sikap'],
                'nilai_keaktifan' => $validated['nilai_keaktifan'],
                'nilai_keterampilan' => $validated['nilai_keterampilan'],
                'catatan' => $validated['catatan'] ?? null,
            ], Penilaian::STATUS_TERKIRIM, now());
        }

        NotifikasiService::penilaianDikirim($ekskul);

        return back()->with('success', 'Laporan penilaian ekskul '.$ekskul->nama_ekskul.' berhasil dikirim ke kesiswaan.');
    }

    public function downloadPdf(Request $request)
    {
        $ekskul = $this->resolveEkskul($request);
        abort_unless($ekskul, 404, 'Tidak ada ekskul yang dapat diunduh.');

        $rows = $this->exportRows($ekskul);

        $selectedKelasId = $request->input('kelas_id') ?: $request->input('kelas');
        $statusPenilaian = $request->input('status_penilaian');
        $namaKelasFilter = null;

        if ($selectedKelasId) {
            $rows = $rows->filter(function ($row) use ($selectedKelasId) {
                if (is_numeric($selectedKelasId)) {
                    return (int) ($row->siswa?->kelas_id ?? 0) === (int) $selectedKelasId;
                }
                return ($row->kelas ?? '') === $selectedKelasId;
            })->values();

            $kelasModel = is_numeric($selectedKelasId) ? Kelas::find($selectedKelasId) : null;
            $namaKelasFilter = $kelasModel?->nama ?? $selectedKelasId;
        }

        if ($statusPenilaian === 'sudah') {
            $rows = $rows->filter(fn ($row) => $row->penilaian !== null)->values();
        } elseif ($statusPenilaian === 'belum') {
            $rows = $rows->filter(fn ($row) => $row->penilaian === null)->values();
        }

        $pdf = Pdf::loadView('penilaian.pdf', [
            'ekskul' => $ekskul,
            'periode' => Penilaian::periodeSekarang()['label'],
            'rows' => $rows,
            'pembina' => auth()->user()?->pembina,
            'namaKelasFilter' => $namaKelasFilter,
        ])->setPaper('a4', 'landscape');

        $kelasSuffix = $namaKelasFilter ? '-kelas-'.str_replace(' ', '-', $namaKelasFilter) : '';

        return $pdf->download('laporan-penilaian-'.str_replace(' ', '-', $ekskul->nama_ekskul).$kelasSuffix.'.pdf');
    }

    /**
     * Baris laporan untuk unduhan pembina: semua anggota, nilai terbaru (draft/terkirim).
     */
    private function exportRows(Ekskul $ekskul): Collection
    {
        return app(PenilaianExportService::class)->rows($ekskul, null, null);
    }

    /**
     * Ambil & validasi input per anggota dari form.
     */
    private function normalizePayload(Request $request, Ekskul $ekskul, string $periode): Collection
    {
        $inputs = $request->input('penilaian', []);

        if (! is_array($inputs) || $inputs === []) {
            return collect();
        }

        $allowed = $ekskul->pendaftarans()
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->whereIn('id', array_keys($inputs))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return collect($inputs)
            ->filter(fn ($nilai, $id) => in_array((int) $id, $allowed, true))
            ->filter(function ($nilai) {
                return collect($nilai)
                    ->only(['nilai_sikap', 'nilai_keaktifan', 'nilai_keterampilan', 'catatan'])
                    ->contains(fn ($v) => $v !== null && $v !== '');
            })
            ->map(function ($nilai, $id) use ($periode) {
                return [
                    'pendaftaran_id' => (int) $id,
                    'periode' => $periode,
                    'nilai' => $nilai,
                ];
            })
            ->values();
    }

    /**
     * Simpan/update satu penilaian lengkap dengan perhitungan otomatis.
     */
    private function simpan(array $item, string $status, $dikirimAt): void
    {
        $periode = Penilaian::periodeSekarang();
        $pendaftaran = Pendaftaran::findOrFail($item['pendaftaran_id']);
        $kehadiran = Penilaian::kehadiran($pendaftaran, $periode);

        $nilaiSikap = (float) ($item['nilai_sikap'] ?? 0);
        $nilaiKeaktifan = (float) ($item['nilai_keaktifan'] ?? 0);
        $nilaiKeterampilan = (float) ($item['nilai_keterampilan'] ?? 0);

        $nilaiAkhir = Penilaian::hitungNilaiAkhir(
            $kehadiran['persentase_kehadiran'],
            $nilaiSikap,
            $nilaiKeaktifan,
            $nilaiKeterampilan
        );

        $penilaian = Penilaian::firstOrNew([
            'pendaftaran_id' => $item['pendaftaran_id'],
            'periode' => $periode['label'],
        ]);

        $penilaian->fill([
            'total_pertemuan' => $kehadiran['total_pertemuan'],
            'total_hadir' => $kehadiran['total_hadir'],
            'total_izin' => $kehadiran['total_izin'],
            'total_sakit' => $kehadiran['total_sakit'],
            'total_alpha' => $kehadiran['total_alpha'],
            'persentase_kehadiran' => $kehadiran['persentase_kehadiran'],
            'nilai_sikap' => $nilaiSikap,
            'nilai_keaktifan' => $nilaiKeaktifan,
            'nilai_keterampilan' => $nilaiKeterampilan,
            'nilai_akhir' => $nilaiAkhir,
            'predikat' => Penilaian::hitungPredikat($nilaiAkhir),
            'catatan' => $item['catatan'] ?? null,
            'status' => $status,
            'dikirim_at' => $status === Penilaian::STATUS_TERKIRIM ? $dikirimAt : null,
            'dinilai_oleh' => auth()->user()?->pembina?->id,
        ]);

        $penilaian->save();
    }
}
