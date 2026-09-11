<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    private function getEkskul()
    {
        $pendaftaran = auth()->user()->siswa?->pendaftarans()->whereIn('status', ['diterima', 'peringatan'])->first();
        abort_unless($pendaftaran, 404, 'Anda belum tergabung dalam ekskul mana pun.');
        return $pendaftaran->ekskul;
    }

    public function index()
    {
        $ekskul = $this->getEkskul();
        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', ['diterima', 'nonaktif', 'peringatan'])
            ->with('siswa.kelas')
            ->latest('tanggal_daftar')
            ->get();

        $peringatanCount = $anggotas->where('status', 'peringatan')->count();
        $nonaktifCount = $anggotas->where('status', 'nonaktif')->count();

        return view('ketua.anggota.index', compact('anggotas', 'peringatanCount', 'nonaktifCount'));
    }

    public function updateStatus(Request $request, Pendaftaran $pendaftaran)
    {
        abort_unless($pendaftaran->ekskul_id === $this->getEkskul()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,peringatan,nonaktif'],
        ], [
            'status.required' => 'Status anggota wajib dipilih.',
            'status.in' => 'Status anggota tidak valid.',
        ]);

        $targetStatus = $validated['status'];

        $protected = $pendaftaran->siswa_id === auth()->user()->siswa?->id
            || $pendaftaran->siswa?->jabatan === 'ketua';

        if ($protected && in_array($targetStatus, ['peringatan', 'nonaktif'])) {
            return back()->with('error', 'Kamu (ketua) tidak bisa memberi peringatan atau menonaktifkan dirimu sendiri.');
        }

        if ($pendaftaran->status === $targetStatus) {
            return back()->with('error', 'Status anggota sudah ' . $this->labelStatus($targetStatus) . '.');
        }

        $pendaftaran->update(['status' => $targetStatus]);

        $namaSiswa = $pendaftaran->siswa?->nama ?? 'Siswa';
        $judul = '';
        $pesan = '';
        $tipe = 'info';

        switch ($targetStatus) {
            case 'peringatan':
                $judul = 'Peringatan dari Ketua Ekskul';
                $pesan = $namaSiswa . ', kamu mendapatkan peringatan dari ketua ' . $pendaftaran->ekskul->nama_ekskul . '. Tingkatkan keaktifan dan kehadiranmu, ya!';
                $tipe = 'ditolak';
                break;
            case 'nonaktif':
                $judul = 'Dinonaktifkan dari Ekskul';
                $pesan = $namaSiswa . ', kamu telah dinonaktifkan dari ekskul ' . $pendaftaran->ekskul->nama_ekskul . ' oleh ketua ekskul. Hubungi ketua jika ini kurang tepat.';
                $tipe = 'ditolak';
                break;
            case 'diterima':
                $judul = 'Status Anggota Diaktifkan Kembali';
                $pesan = $namaSiswa . ', status anggota kamu di ekskul ' . $pendaftaran->ekskul->nama_ekskul . ' telah diaktifkan kembali. Selamat beraktivitas!';
                $tipe = 'diterima';
                break;
        }

        Notifikasi::create([
            'siswa_id' => $pendaftaran->siswa_id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
        ]);

        return back()->with('success', 'Status ' . $namaSiswa . ' berhasil diubah menjadi ' . $this->labelStatus($targetStatus) . '.');
    }

    private function labelStatus(string $status): string
    {
        return match ($status) {
            'diterima' => 'Aktif',
            'peringatan' => 'Peringatan',
            'nonaktif' => 'Nonaktif',
            default => $status,
        };
    }
}
