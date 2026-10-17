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
    private const PROFIL_BELUM_LENGKAP = 'Lengkapi data diri (nama, kelas, jenis kelamin) terlebih dahulu sebelum mendaftar ekskul.';

    public function daftar(Request $request)
    {
        $siswa = auth()->user()->siswa;

        if ($siswa && ! $siswa->isProfileComplete()) {
            return redirect()->route('siswa.profile.edit')->with('error', self::PROFIL_BELUM_LENGKAP);
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

        $alasanDaftar = $siswa?->alasanTidakBisaDaftar();
        $ekskulAktifs = $siswa ? $siswa->activeEkskuls() : collect();
        $alasanPerEkskul = $siswa
            ? $ekskuls->mapWithKeys(fn (Ekskul $e) => [$e->id => $siswa->alasanTidakBisaDaftar($e)])
            : collect();

        return view('siswa.daftar-ekskul', compact('ekskuls', 'siswa', 'alasanDaftar', 'ekskulAktifs', 'alasanPerEkskul'));
    }

    public function formDaftar(Ekskul $ekskul)
    {
        $siswa = auth()->user()->siswa;

        if ($siswa && ! $siswa->isProfileComplete()) {
            return redirect()->route('siswa.profile.edit')->with('error', self::PROFIL_BELUM_LENGKAP);
        }

        if (! $ekskul->status || ! $ekskul->is_open_recruitment) {
            return redirect()->route('siswa.daftar-ekskul')->with('error', 'Pendaftaran ekskul ini sedang ditutup.');
        }

        if ($siswa && ($alasan = $siswa->alasanTidakBisaDaftar($ekskul))) {
            return redirect()->route('siswa.daftar-ekskul')->with('error', $alasan);
        }

        $ekskul->load('pembina');

        return view('siswa.form-daftar', compact('ekskul', 'siswa'));
    }

    public function store(Request $request)
    {
        $siswa = auth()->user()->siswa;

        abort_unless($siswa, 403, 'Profil siswa belum tersedia. Hubungi kesiswaan.');

        if (! $siswa->isProfileComplete()) {
            return redirect()->route('siswa.profile.edit')->with('error', self::PROFIL_BELUM_LENGKAP);
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

        $ekskul = Ekskul::findOrFail($validated['ekskul_id']);

        if ($alasan = $siswa->alasanTidakBisaDaftar($ekskul)) {
            return redirect()->back()->with('error', $alasan);
        }

        $pendaftaran = DB::transaction(function () use ($siswa, $ekskul, $validated) {
            // Serialize submissions for this student so concurrent requests
            // cannot both pass the enrollment rules.
            Siswa::whereKey($siswa->id)->lockForUpdate()->firstOrFail();

            $ekskul = Ekskul::query()->lockForUpdate()->findOrFail($ekskul->id);

            if (! $ekskul->status || ! $ekskul->is_open_recruitment) {
                abort(422, 'Pendaftaran ekskul ini sedang ditutup.');
            }

            if ($siswa->fresh()->alasanTidakBisaDaftar($ekskul)) {
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

        if ($pendaftaran === false) {
            return redirect()->back()->with('error', $siswa->fresh()->alasanTidakBisaDaftar($ekskul) ?? 'Kamu tidak dapat mendaftar ke ekskul ini.');
        }

        NotifikasiService::pendaftaranMasuk($pendaftaran);

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran kamu telah terkirim kepada ketua ekskul. Silakan menunggu konfirmasi dari ketua ekskul.');
    }
}
