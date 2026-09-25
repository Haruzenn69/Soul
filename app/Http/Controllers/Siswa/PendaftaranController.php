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

        if (! $ekskul->status || ! $ekskul->is_open_recruitment) {
            return redirect()->route('siswa.daftar-ekskul')->with('error', 'Pendaftaran ekskul ini sedang ditutup.');
        }

        $ekskul->load('pembina');

        return view('siswa.form-daftar', compact('ekskul', 'siswa'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        abort_unless($siswa, 403, 'Profil siswa belum tersedia. Hubungi kesiswaan.');

        if ($siswa->activePendaftaran()) {
            return redirect()->back()->with('error', 'Kamu sudah terdaftar di ekskul.');
        }

        if ($siswa->pendingPendaftaran()) {
            return redirect()->back()->with('error', 'Kamu sudah mengajukan pendaftaran. Tunggu verifikasi dari ketua ekskul.');
        }

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

        $pendaftaran = DB::transaction(function () use ($siswa, $validated) {
            // Serialize submissions for this student so concurrent requests
            // cannot both pass the active/pending enrollment checks.
            Siswa::whereKey($siswa->id)->lockForUpdate()->firstOrFail();

            $ekskul = Ekskul::query()->whereKey($validated['ekskul_id'])->firstOrFail();
            if (! $ekskul->status || ! $ekskul->is_open_recruitment) {
                abort(422, 'Pendaftaran ekskul ini sedang ditutup.');
            }

            if ($siswa->activePendaftaran() || $siswa->pendingPendaftaran()) {
                return false;
            }

            return Pendaftaran::create([
                'siswa_id' => $siswa->id,
                'ekskul_id' => $ekskul->id,
                'tanggal_daftar' => now()->toDateString(),
                'status' => Pendaftaran::STATUS_PENDING,
                'alasan' => $validated['alasan'],
            ]);
        });

        if ($pendaftaran === null) {
            return redirect()->back()->with('error', 'Pendaftaran ekskul ini sedang ditutup.');
        }

        if ($pendaftaran === false) {
            return redirect()->back()->with('error', 'Kamu sudah mengajukan pendaftaran atau terdaftar di ekskul.');
        }

        NotifikasiService::pendaftaranMasuk($pendaftaran);

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran kamu telah terkirim kepada ketua ekskul. Silakan menunggu konfirmasi dari ketua ekskul.');
    }
}
