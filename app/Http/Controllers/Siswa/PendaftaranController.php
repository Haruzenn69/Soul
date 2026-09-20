<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Rules\AlasanValid;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function daftar(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;
        if ($pendaftaran) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah terdaftar di ekskul.');
        }

        $ekskuls = Ekskul::with('pembina')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(function ($sub) use ($cari) {
                    $sub->where('nama_ekskul', 'like', "%{$cari}%")
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$cari}%"));
                });
            })
            ->get();

        return view('siswa.daftar-ekskul', compact('ekskuls', 'siswa'));
    }

    public function formDaftar(Ekskul $ekskul)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if ($siswa?->activePendaftaran()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah terdaftar di ekskul.');
        }

        if ($siswa && $siswa->pendingPendaftaran()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah mengajukan pendaftaran. Tunggu verifikasi dari ketua ekskul.');
        }

        $ekskul->load('pembina');

        return view('siswa.form-daftar', compact('ekskul', 'siswa'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if ($siswa->activePendaftaran()) {
            return redirect()->back()->with('error', 'Kamu sudah terdaftar di ekskul.');
        }

        if ($siswa->pendingPendaftaran()) {
            return redirect()->back()->with('error', 'Kamu sudah mengajukan pendaftaran. Tunggu verifikasi dari ketua ekskul.');
        }

        $validated = $request->validate([
            'ekskul_id' => ['required', 'exists:ekskuls,id'],
            'alasan' => ['required', 'string', 'max:1000', new AlasanValid],
        ], [
            'ekskul_id.required' => 'Pilih ekskul terlebih dahulu.',
            'ekskul_id.exists' => 'Ekskul yang dipilih tidak valid.',
            'alasan.required' => 'Alasan bergabung wajib diisi.',
            'alasan.max' => 'Alasan bergabung maksimal 1000 karakter.',
        ]);

        $pendaftaran = Pendaftaran::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $validated['ekskul_id'],
            'tanggal_daftar' => now()->toDateString(),
            'status' => Pendaftaran::STATUS_PENDING,
            'alasan' => $validated['alasan'],
        ]);

        NotifikasiService::pendaftaranMasuk($pendaftaran);

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran kamu telah terkirim kepada ketua ekskul. Silakan menunggu konfirmasi dari ketua ekskul.');
    }
}
