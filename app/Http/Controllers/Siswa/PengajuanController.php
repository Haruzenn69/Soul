<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKeluar;
use App\Rules\AlasanValid;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftarans = $siswa ? $siswa->activePendaftarans()->with('ekskul')->orderByDesc('id')->get() : collect();
        $ekskulId = $request->input('ekskul');
        $pendaftaran = $ekskulId
            ? ($pendaftarans->firstWhere('ekskul_id', (int) $ekskulId) ?? $pendaftarans->first())
            : $pendaftarans->first();
        $ekskul = $pendaftaran ? $pendaftaran->ekskul : null;
        $pengajuan = $siswa ? $siswa->pengajuanKeluars()->with('ekskul')->latest('tanggal_pengajuan')->get() : collect();

        return view('siswa.pengajuan-keluar', compact('siswa', 'ekskul', 'pendaftaran', 'pendaftarans', 'pengajuan'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if (! $siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $pendaftarans = $siswa->activePendaftarans()->with('ekskul')->orderByDesc('id')->get();

        if ($pendaftarans->isEmpty()) {
            return redirect()->back()->with('error', 'Kamu belum terdaftar di ekskul manapun.');
        }

        $validated = $request->validate([
            'ekskul_id' => ['required', 'integer', 'exists:ekskuls,id'],
            'alasan' => ['required', 'string', 'max:1000', new AlasanValid],
        ], [
            'ekskul_id.required' => 'Silakan pilih ekskul yang ingin kamu ajukan untuk keluar.',
            'ekskul_id.exists' => 'Ekskul yang dipilih tidak valid.',
            'alasan.required' => 'Alasan keluar wajib diisi.',
            'alasan.max' => 'Alasan keluar maksimal 1000 karakter.',
        ]);

        $pendaftaran = $pendaftarans->firstWhere('ekskul_id', (int) $validated['ekskul_id']);
        if (! $pendaftaran) {
            return redirect()->back()->with('error', 'Kamu tidak terdaftar aktif di ekskul yang dipilih.');
        }

        $pending = $siswa->pengajuanKeluars()
            ->where('status', PengajuanKeluar::STATUS_PENDING)
            ->where('ekskul_id', $pendaftaran->ekskul_id)
            ->exists();
        if ($pending) {
            return redirect()->back()->with('error', 'Kamu masih memiliki permohonan keluar yang berstatus pending untuk ekskul ' . $pendaftaran->ekskul->nama_ekskul . '.');
        }

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
