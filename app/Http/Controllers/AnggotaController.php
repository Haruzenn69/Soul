<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Pendaftaran;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $ekskul = $this->ekskul();
        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_NONAKTIF, Pendaftaran::STATUS_PERINGATAN])
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->whereHas('siswa', fn ($s) => $s->where('nama', 'like', "%{$cari}%")->orWhere('nis', 'like', "%{$cari}%"));
            })
            ->when($request->filled('status') && $request->input('status') !== 'semua', fn ($query) => $query->where('status', $request->input('status')))
            ->with('siswa.kelas')
            ->latest('tanggal_daftar')
            ->get();

        $peringatanCount = $anggotas->where('status', 'peringatan')->count();
        $nonaktifCount = $anggotas->where('status', 'nonaktif')->count();

        return view('ketua.anggota.index', compact('anggotas', 'peringatanCount', 'nonaktifCount'));
    }

    public function updateStatus(Request $request, Pendaftaran $pendaftaran)
    {
        $this->ensureEkskul($pendaftaran);

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,peringatan,nonaktif'],
        ], [
            'status.required' => 'Status anggota wajib dipilih.',
            'status.in' => 'Status anggota tidak valid.',
        ]);

        $targetStatus = $validated['status'];

        $protected = $pendaftaran->siswa_id === auth()->user()->siswa?->id
            || $pendaftaran->siswa?->jabatan === 'ketua';

        if ($protected && in_array($targetStatus, [Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_NONAKTIF])) {
            return back()->with('error', 'Kamu (ketua) tidak bisa memberi peringatan atau menonaktifkan dirimu sendiri.');
        }

        if ($pendaftaran->status === $targetStatus) {
            return back()->with('error', 'Status anggota sudah '.$this->labelStatus($targetStatus).'.');
        }

        $pendaftaran->update(['status' => $targetStatus]);

        $namaSiswa = $pendaftaran->siswa?->nama ?? 'Siswa';

        [$judul, $pesan, $tipe] = match ($targetStatus) {
            Pendaftaran::STATUS_PERINGATAN => [
                'Peringatan dari Ketua Ekskul',
                $namaSiswa.', kamu mendapatkan peringatan dari ketua '.$pendaftaran->ekskul->nama_ekskul.'. Tingkatkan keaktifan dan kehadiranmu, ya!',
                'ditolak',
            ],
            Pendaftaran::STATUS_NONAKTIF => [
                'Dinonaktifkan dari Ekskul',
                $namaSiswa.', kamu telah dinonaktifkan dari ekskul '.$pendaftaran->ekskul->nama_ekskul.' oleh ketua ekskul. Hubungi ketua jika ini kurang tepat.',
                'ditolak',
            ],
            default => [
                'Status Anggota Diaktifkan Kembali',
                $namaSiswa.', status anggota kamu di ekskul '.$pendaftaran->ekskul->nama_ekskul.' telah diaktifkan kembali. Selamat beraktivitas!',
                'diterima',
            ],
        };

        NotifikasiService::statusAnggotaDiubah($pendaftaran, $targetStatus, $judul, $pesan, $tipe);

        return back()->with('success', 'Status '.$namaSiswa.' berhasil diubah menjadi '.$this->labelStatus($targetStatus).'.');
    }

    private function labelStatus(string $status): string
    {
        return match ($status) {
            Pendaftaran::STATUS_DITERIMA => 'Aktif',
            Pendaftaran::STATUS_PERINGATAN => 'Peringatan',
            Pendaftaran::STATUS_NONAKTIF => 'Nonaktif',
            default => $status,
        };
    }
}
