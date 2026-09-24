<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
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
        $pembina = auth()->user()?->pembina;

        return $pembina ? $pembina->ekskuls()->get() : collect();
    }

    private function resolveEkskul(Request $request): ?Ekskul
    {
        $ekskuls = $this->getEkskuls();
        $id = $request->integer('ekskul');

        return $id && $ekskuls->pluck('id')->contains($id) ? $ekskuls->firstWhere('id', $id) : $ekskuls->first();
    }

    public function index(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskul = $this->resolveEkskul($request);

        $periode = Penilaian::periodeSekarang();

        $rows = $ekskul
            ? app(PenilaianExportService::class)->rows($ekskul, null, $periode['label'])
            : collect();

        $sudahDikirim = (bool) $ekskul
            && Penilaian::where('periode', $periode['label'])
                ->whereIn('pendaftaran_id', $ekskul->pendaftarans()->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->pluck('id'))
                ->where('status', Penilaian::STATUS_TERKIRIM)
                ->exists();

        return view('pembina.penilaian', compact('ekskuls', 'ekskul', 'rows', 'periode', 'sudahDikirim'));
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

        $pdf = Pdf::loadView('penilaian.pdf', [
            'ekskul' => $ekskul,
            'periode' => Penilaian::periodeSekarang()['label'],
            'rows' => $rows,
            'pembina' => auth()->user()?->pembina,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penilaian-'.str_replace(' ', '-', $ekskul->nama_ekskul).'.pdf');
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
