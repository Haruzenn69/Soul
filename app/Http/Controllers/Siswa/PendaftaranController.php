<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Rules\AlasanValid;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function daftar(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if ($siswa && ! $siswa->isProfileComplete()) {
            return redirect()->route('siswa.profile.edit')->with('error', 'Lengkapi data diri (nama, kelas, jenis kelamin) terlebih dahulu sebelum mendaftar ekskul.');
        }

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;
        if ($pendaftaran) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah terdaftar di ekskul.');
        }

        $ekskuls = Ekskul::with('pembina')
            ->where('status', true)
            ->where('is_open_recruitment', true)
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

        if ($siswa && ! $siswa->isProfileComplete()) {
            return redirect()->route('siswa.profile.edit')->with('error', 'Lengkapi data diri (nama, kelas, jenis kelamin) terlebih dahulu sebelum mendaftar ekskul.');
        }

        if ($siswa?->activePendaftaran()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah terdaftar di ekskul.');
        }

        if ($siswa && $siswa->pendingPendaftaran()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah mengajukan pendaftaran. Tunggu verifikasi dari ketua ekskul.');
        }

        abort_unless($ekskul->status && $ekskul->is_open_recruitment, 403, 'Pendaftaran untuk ekskul ini sedang ditutup.');

        $ekskul->load('pembina');

        return view('siswa.form-daftar', compact('ekskul', 'siswa'));
    }

    public function store(Request $request)
    {
        $siswaId = auth()->user()->siswa?->id;
        abort_unless($siswaId, 403);

        $siswa = Siswa::find($siswaId);

        if (! $siswa?->isProfileComplete()) {
            return redirect()->route('siswa.profile.edit')->with('error', 'Lengkapi data diri (nama, kelas, jenis kelamin) terlebih dahulu sebelum mendaftar ekskul.');
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

        $pendaftaran = DB::transaction(function () use ($siswaId, $validated) {
            $siswa = Siswa::query()->lockForUpdate()->findOrFail($siswaId);
            $ekskul = Ekskul::query()
                ->whereKey($validated['ekskul_id'])
                ->where('status', true)
                ->where('is_open_recruitment', true)
                ->first();

            if (! $ekskul) {
                abort(422, 'Ekskul tidak tersedia atau pendaftarannya telah ditutup.');
            }

            if ($siswa->activePendaftaran()) {
                abort(422, 'Kamu sudah terdaftar di ekskul.');
            }

            if ($siswa->pendingPendaftaran()) {
                abort(422, 'Kamu sudah mengajukan pendaftaran. Tunggu verifikasi dari ketua ekskul.');
            }

            $pendaftaran = Pendaftaran::create([
                'siswa_id' => $siswa->id,
                'ekskul_id' => $ekskul->id,
                'tanggal_daftar' => now()->toDateString(),
                'status' => Pendaftaran::STATUS_PENDING,
                'alasan' => $validated['alasan'],
            ]);

            NotifikasiService::pendaftaranMasuk($pendaftaran);

            return $pendaftaran;
        });

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran kamu telah terkirim kepada ketua ekskul. Silakan menunggu konfirmasi dari ketua ekskul.');
    }
}
