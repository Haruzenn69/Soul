<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKeluar;
use App\Rules\AlasanValid;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran()->load('ekskul') : null;
        $ekskul = $pendaftaran ? $pendaftaran->ekskul : null;
        $pengajuan = $siswa ? $siswa->pengajuanKeluars()->latest('tanggal_pengajuan')->get() : collect();

        return view('siswa.pengajuan-keluar', compact('siswa', 'ekskul', 'pengajuan'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;
        if (! $pendaftaran) {
            return redirect()->back()->with('error', 'Kamu belum terdaftar di ekskul manapun.');
        }

        $pending = $siswa->pengajuanKeluars()->where('status', PengajuanKeluar::STATUS_PENDING)->first();
        if ($pending) {
            return redirect()->back()->with('error', 'Kamu masih memiliki permohonan keluar yang berstatus pending.');
        }

        $validated = $request->validate([
            'alasan' => ['required', 'string', 'max:1000', new AlasanValid],
        ], [
            'alasan.required' => 'Alasan keluar wajib diisi.',
            'alasan.max' => 'Alasan keluar maksimal 1000 karakter.',
        ]);

        $pengajuanKeluar = PengajuanKeluar::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $pendaftaran->ekskul_id,
            'alasan' => $validated['alasan'],
            'status' => PengajuanKeluar::STATUS_PENDING,
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        NotifikasiService::pengajuanKeluarMasuk($pengajuanKeluar);

        return redirect()->back()->with('success', 'Pengajuan keluar berhasil dikirim dan sedang menunggu keputusan ketua ekskul.');
    }
}
