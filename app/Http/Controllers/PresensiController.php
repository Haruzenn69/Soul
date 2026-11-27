<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\PresensiPelatih;
use App\Services\RekapAbsensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PresensiController extends Controller
{
    use KetuaEkskul;

    public function create(Kegiatan $kegiatan)
    {
        $this->ensureKegiatanEkskul($kegiatan);
        $this->pastikanBolehKelolaKegiatan();

        $ekskul = $kegiatan->ekskul;
        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->with('siswa.kelas')
            ->get();

        $kelasList = $anggotas->pluck('siswa.kelas')->filter()->unique('id')->sortBy('nama')->values();

        $presensiExisting = Presensi::where('kegiatan_id', $kegiatan->id)
            ->pluck('status', 'pendaftaran_id')
            ->toArray();

        $pelatih = $ekskul->pelatih;
        $pelatihStatus = $pelatih
            ? PresensiPelatih::where('kegiatan_id', $kegiatan->id)->where('pelatih_id', $pelatih->id)->value('status')
            : null;

        return view('ketua.presensi.create', compact('kegiatan', 'anggotas', 'presensiExisting', 'pelatih', 'pelatihStatus', 'kelasList'));
    }

    public function store(Request $request, Kegiatan $kegiatan)
    {
        $this->ensureKegiatanEkskul($kegiatan);
        $this->pastikanBolehKelolaKegiatan();

        $validated = $request->validate([
            'presensi' => 'nullable|array',
            'presensi.*.pendaftaran_id' => 'required|exists:pendaftarans,id',
            'presensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
            'pelatih_presensi' => 'nullable|array',
            'pelatih_presensi.pelatih_id' => 'nullable|exists:pelatihs,id',
            'pelatih_presensi.status' => 'nullable|in:hadir,sakit,izin,alpha',
        ]);

        $presensiItems = $validated['presensi'] ?? [];
        $pendaftaranIds = collect($presensiItems)->pluck('pendaftaran_id');

        $milikEkskul = Pendaftaran::whereIn('id', $pendaftaranIds)
            ->where('ekskul_id', $kegiatan->ekskul_id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->count();

        abort_unless($milikEkskul === $pendaftaranIds->unique()->count(), 422, 'Presensi hanya bisa diisi untuk anggota aktif ekskul kegiatan ini.');

        foreach ($presensiItems as $item) {
            Presensi::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'pendaftaran_id' => $item['pendaftaran_id']],
                ['status' => $item['status']]
            );
        }

        $pelatih = $request->input('pelatih_presensi.pelatih_id');
        $pelatihStatus = $request->input('pelatih_presensi.status');
        if ($pelatih && $pelatihStatus) {
            $ekskul = $kegiatan->ekskul;
            abort_unless($ekskul->pelatih_id === (int) $pelatih, 422, 'Kehadiran pelatih tidak sesuai dengan ekskul kegiatan ini.');

            PresensiPelatih::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'pelatih_id' => $pelatih],
                ['status' => $pelatihStatus]
            );
        }

        return redirect()->route('ketua.kegiatan.show', $kegiatan)->with('success', 'Presensi berhasil disimpan.');
    }

    public function rekap(Request $request)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);

        if ($isKesiswaan && ! $request->filled('ekskul')) {
            $ekskuls = \App\Models\Ekskul::orderBy('nama_ekskul')->get();
            $cards = $ekskuls->map(function (\App\Models\Ekskul $e) {
                $totalKegiatan = Kegiatan::where('ekskul_id', $e->id)->count();
                $totalAnggota = Pendaftaran::where('ekskul_id', $e->id)->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->count();

                $stats = [
                    ['label' => 'Total Pertemuan', 'value' => $totalKegiatan.' Kegiatan', 'class' => 'font-bold text-slate-800 dark:text-slate-200'],
                    ['label' => 'Anggota Aktif', 'value' => $totalAnggota.' Siswa', 'class' => 'font-semibold text-slate-600 dark:text-slate-300'],
                ];

                return (object) [
                    'ekskul' => $e,
                    'stats' => $stats,
                    'badge' => null,
                    'actionText' => 'Lihat Rekap Absensi',
                    'actionUrl' => route('ketua.presensi.rekap', ['ekskul' => $e->id]),
                ];
            });

            return view('ketua.presensi.rekap', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => true,
                'ekskul' => null,
                'bulan' => now()->format('Y-m'),
            ]);
        }

        $service = app(RekapAbsensiService::class);
        $bulan = $service->normalizeBulan($request->input('bulan'));
        $ekskul = $this->kegiatanEkskul();
        $isMonitoring = $this->isKetuaUtamaMonitoring($ekskul);

        if ($isMonitoring && ! $ekskul->isSubdivisi()) {
            $cards = $ekskul->subdivisis()->orderBy('nama_ekskul')->get()->map(function (\App\Models\Ekskul $subdivisi) {
                $kegiatanRutin = Kegiatan::where('ekskul_id', $subdivisi->id)->whereNull('jenis_kegiatan');
                $totalPertemuan = (clone $kegiatanRutin)->count();
                $presensiKeseluruhan = Presensi::query()
                    ->join('kegiatans', 'kegiatans.id', '=', 'presensis.kegiatan_id')
                    ->where('kegiatans.ekskul_id', $subdivisi->id)
                    ->whereNull('kegiatans.jenis_kegiatan')
                    ->selectRaw('count(presensis.id) as total, sum(case when presensis.status = ? then 1 else 0 end) as hadir', [Presensi::STATUS_HADIR])
                    ->first();
                $persentaseKehadiran = (int) $presensiKeseluruhan->total > 0
                    ? round(((int) $presensiKeseluruhan->hadir / (int) $presensiKeseluruhan->total) * 100, 1)
                    : 0;
                $totalAnggota = Pendaftaran::where('ekskul_id', $subdivisi->id)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->count();
                $kegiatanTerakhir = Kegiatan::where('ekskul_id', $subdivisi->id)->latest('tanggal_kegiatan')->first();
                $stats = [
                    ['label' => 'Pertemuan', 'value' => $totalPertemuan.' Pertemuan', 'class' => 'font-bold text-slate-800 dark:text-slate-200'],
                    ['label' => 'Kehadiran', 'value' => $persentaseKehadiran.'%', 'class' => 'font-bold text-amber-600 dark:text-amber-400'],
                    ['label' => 'Anggota Aktif', 'value' => $totalAnggota.' Siswa', 'class' => 'font-semibold text-slate-600 dark:text-slate-300'],
                ];
                if ($kegiatanTerakhir) {
                    $stats[] = ['label' => 'Terakhir', 'value' => $kegiatanTerakhir->tanggal_kegiatan->translatedFormat('d M Y'), 'class' => 'font-medium text-slate-500 text-[11px]'];
                }

                return (object) [
                    'ekskul' => $subdivisi,
                    'stats' => $stats,
                    'badge' => null,
                    'actionText' => 'Lihat Rekap Absensi',
                    'actionUrl' => route('ketua.presensi.rekap', ['ekskul' => $subdivisi->id]),
                ];
            });

            return view('ketua.presensi.rekap', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => false,
                'ekskul' => null,
                'bulan' => $bulan,
                'catalogTitle' => 'Rekap Absensi Subdivisi '.$ekskul->nama_ekskul,
                'catalogSubtitle' => 'Pilih subdivisi untuk melihat rekap kehadirannya satu per satu.',
                'backUrl' => route('ketua.dashboard'),
            ]);
        }

        $data = $service->rekap($ekskul, $bulan);

        return view('ketua.presensi.rekap', array_merge($data, ['isKesiswaan' => $isKesiswaan, 'isMonitoring' => $isMonitoring]));
    }

    public function rekapPdf(Request $request)
    {
        $service = app(RekapAbsensiService::class);
        $bulan = $service->normalizeBulan($request->input('bulan'));
        $ekskul = $this->kegiatanEkskul();
        abort_if($this->isKetuaUtamaMonitoring($ekskul) && ! $ekskul->isSubdivisi(), 403, 'Pilih subdivisi untuk mencetak rekap absensi.');

        $data = $service->rekap($ekskul, $bulan);

        $pdf = Pdf::loadView('ketua.presensi.rekap-pdf', $data);
        $filename = 'rekap-absensi-'.str_replace('/', '-', $bulan).'-'.Str::slug($ekskul->nama_ekskul ?? 'ekskul').'.pdf';

        return $pdf->download($filename);
    }
}
