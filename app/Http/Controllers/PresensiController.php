<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class PresensiController extends Controller
{
    use KetuaEkskul;

    public function create(Kegiatan $kegiatan)
    {
        $this->ensureEkskul($kegiatan);

        $ekskul = $this->ekskul();
        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->with('siswa')
            ->get();

        $presensiExisting = Presensi::where('kegiatan_id', $kegiatan->id)
            ->pluck('status', 'pendaftaran_id')
            ->toArray();

        return view('ketua.presensi.create', compact('kegiatan', 'anggotas', 'presensiExisting'));
    }

    public function store(Request $request, Kegiatan $kegiatan)
    {
        $this->ensureEkskul($kegiatan);

        $validated = $request->validate([
            'presensi' => 'required|array',
            'presensi.*.pendaftaran_id' => 'required|exists:pendaftarans,id',
            'presensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        $pendaftaranIds = collect($validated['presensi'])->pluck('pendaftaran_id');

        $milikEkskul = Pendaftaran::whereIn('id', $pendaftaranIds)
            ->where('ekskul_id', $kegiatan->ekskul_id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->count();

        abort_unless($milikEkskul === $pendaftaranIds->unique()->count(), 422, 'Presensi hanya bisa diisi untuk anggota aktif ekskul kegiatan ini.');

        foreach ($validated['presensi'] as $item) {
            Presensi::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'pendaftaran_id' => $item['pendaftaran_id']],
                ['status' => $item['status']]
            );
        }

        return redirect()->route('ketua.kegiatan.show', $kegiatan)->with('success', 'Presensi berhasil disimpan.');
    }

    public function rekap(Request $request)
    {
        $sekarang = now();
        $tahun = $sekarang->year;
        $bulanMaksimal = $sekarang->format('Y-m');
        $bulanTersedia = collect(range(1, $sekarang->month))
            ->map(fn (int $month) => sprintf('%04d-%02d', $tahun, $month))
            ->all();
        $bulanOptions = collect(range(1, 12))
            ->map(fn (int $month) => sprintf('%04d-%02d', $tahun, $month))
            ->all();

        $validated = $request->validate([
            'bulan' => ['nullable', Rule::in($bulanTersedia)],
        ]);

        $bulan = $validated['bulan'] ?? $bulanMaksimal;
        $tanggalMulai = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth()->toDateString();
        $tanggalAkhir = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth()->toDateString();
        $ekskul = $this->ekskul();

        $kegiatans = $ekskul->kegiatans()
            ->whereBetween('tanggal_kegiatan', [$tanggalMulai, $tanggalAkhir])
            ->with('presensis')
            ->get();
        $kegiatanIds = $kegiatans->modelKeys();

        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->with([
                'siswa' => fn($q) => $q->orderBy('nama'),
                'siswa.kelas',
                'presensis' => fn($q) => $q->whereIn('kegiatan_id', $kegiatanIds),
            ])
            ->get();

        return view('ketua.presensi.rekap', compact('ekskul', 'kegiatans', 'anggotas', 'bulan', 'bulanOptions', 'bulanMaksimal', 'tahun'));
    }
}
