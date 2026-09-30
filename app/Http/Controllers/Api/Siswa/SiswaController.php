<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\EkskulResource;
use App\Http\Resources\KegiatanResource;
use App\Http\Resources\NotifikasiResource;
use App\Http\Resources\PendaftaranResource;
use App\Http\Resources\PenilaianResource;
use App\Http\Resources\SiswaResource;
use App\Models\Kelas;
use App\Models\Notifikasi;
use App\Models\PengajuanKeluar;
use App\Models\Penilaian;
use App\Models\Presensi;
use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Rules\AlasanValid;
use App\Services\NotifikasiService;
use App\Services\RekapAbsensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SiswaController extends ApiController
{
    private function siswa(): Siswa
    {
        $siswa = auth()->user()->siswa;

        abort_unless($siswa, 404, 'Profil siswa belum tersedia. Hubungi kesiswaan.');

        return $siswa;
    }

    public function dashboard(): JsonResponse
    {
        $siswa = $this->siswa();
        $pendaftaran = $siswa->pendaftarans()
            ->whereIn('status', ['diterima', 'peringatan'])
            ->with('ekskul.pembina')
            ->latest('id')
            ->first();

        return $this->ok($this->dashboardPayload($siswa, $pendaftaran));
    }

    private function dashboardPayload(Siswa $siswa, $pendaftaran): array
    {
        $ekskul = $pendaftaran?->ekskul;
        $isWarned = $pendaftaran && $pendaftaran->status === 'peringatan';

        $statusTerakhir = $siswa->pendaftarans()->with('ekskul')->latest('tanggal_daftar')->first();
        $nonaktifStatus = null;

        if (! $pendaftaran && $statusTerakhir) {
            $nonaktifStatus = in_array($statusTerakhir->status, ['nonaktif', 'keluar'])
                ? $statusTerakhir->status
                : null;
        }

        $kegiatanMendatang = $ekskul
            ? $ekskul->kegiatans()->whereDate('tanggal_kegiatan', '>=', today())->orderBy('tanggal_kegiatan', 'asc')->get()
            : collect();

        $totalHadir = $pendaftaran
            ? Presensi::where('pendaftaran_id', $pendaftaran->id)->where('status', Presensi::STATUS_HADIR)->count()
            : 0;

        $unreadNotifCount = $siswa->notifikasis()->where('is_read', false)->count();

        $hasSubmittedTestimoni = $ekskul
            ? $ekskul->testimoniss()->where('user_id', auth()->id())->whereIn('status', ['pending', 'approved'])->exists()
            : false;

        return [
            'siswa' => (new SiswaResource($siswa->loadMissing('kelas')))->resolve(),
            'pendaftaran' => $pendaftaran ? (new PendaftaranResource($pendaftaran))->resolve() : null,
            'is_warned' => $isWarned,
            'status_terakhir' => $statusTerakhir ? (new PendaftaranResource($statusTerakhir))->resolve() : null,
            'nonaktif_status' => $nonaktifStatus,
            'ekskul' => $ekskul ? (new EkskulResource($ekskul))->resolve() : null,
            'kegiatan_mendatang' => KegiatanResource::collection($kegiatanMendatang)->resolve(),
            'total_hadir' => $totalHadir,
            'unread_notif_count' => $unreadNotifCount,
            'has_submitted_testimoni' => $hasSubmittedTestimoni,
        ];
    }

    public function kelas(): JsonResponse
    {
        $kelas = Kelas::with('tahunAjaran')->orderBy('nama')->get();

        return $this->ok([
            'kelas' => $kelas->map(fn ($k) => [
                'id' => $k->id,
                'nama' => $k->nama,
                'tingkat' => $k->tingkat,
                'jurusan' => $k->jurusan,
                'jurusan_label' => $k->jurusan_label,
                'rombel' => $k->rombel,
            ])->values(),
        ]);
    }

    public function profil(): JsonResponse
    {
        $siswa = $this->siswa()->loadMissing('kelas');

        return $this->ok([
            'siswa' => (new SiswaResource($siswa))->resolve(),
        ]);
    }

    public function updateProfil(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $user = $request->user();

        $validated = $request->validate([
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
        ]);

        DB::transaction(function () use ($user, $siswa, $validated) {
            $siswa->update(['jenis_kelamin' => $validated['jenis_kelamin']]);

            if (filled($validated['email'] ?? null) && $validated['email'] !== $user->email) {
                $user->update([
                    'email' => $validated['email'],
                    'email_verified_at' => null,
                ]);
            }

            if ($siswa->isProfileComplete()) {
                $user->update(['onboarding_completed_at' => $user->onboarding_completed_at ?? now()]);
            }
        });

        return $this->ok([], 'Profil berhasil diperbarui.');
    }

    public function onboardingComplete(Request $request): JsonResponse
    {
        $request->user()->update(['onboarding_completed_at' => now()]);

        return $this->noContent();
    }

    public function katalog(): JsonResponse
    {
        $siswa = auth()->user()->siswa;
        $pendaftaran = $siswa?->activePendaftaran();
        $pending = $siswa?->pendingPendaftaran();

        $joinable = Ekskul::with('pembina')
            ->where('status', true)
            ->where('is_open_recruitment', true)
            ->withCount([
                'pendaftarans as anggota_count' => fn ($q) => $q->whereIn('status', ['diterima', 'peringatan']),
            ])
            ->get();

        return $this->ok([
            'siswa' => $siswa ? (new SiswaResource($siswa))->resolve() : null,
            'profile_complete' => $siswa ? $siswa->isProfileComplete() : false,
            'pendaftaran' => $pendaftaran ? (new PendaftaranResource($pendaftaran->loadMissing('ekskul')))->resolve() : null,
            'pending' => $pending ? (new PendaftaranResource($pending->loadMissing('ekskul')))->resolve() : null,
            'joinable' => EkskulResource::collection($joinable)->resolve(),
        ]);
    }

    public function daftar(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $pendaftaran = $siswa->activePendaftaran();

        if ($pendaftaran) {
            return $this->error('Kamu sudah terdaftar di ekskul.', 422);
        }

        $pending = $siswa->pendingPendaftaran();

        if ($pending) {
            return $this->error('Kamu sudah terdaftar di ekskul.', 422);
        }

        if (! $siswa->isProfileComplete()) {
            return $this->error('Lengkapi data diri (nama, kelas, jenis kelamin) terlebih dahulu sebelum mendaftar ekskul.', 422);
        }

        return $this->ok([
            'ekskuls' => EkskulResource::collection(
                Ekskul::with('pembina')->where('status', true)->where('is_open_recruitment', true)->get()
            )->resolve(),
        ]);
    }

    public function storeDaftar(Request $request): JsonResponse
    {
        $siswa = $this->siswa();

        $validated = $request->validate([
            'ekskul_id' => ['required', 'exists:ekskuls,id'],
            'alasan' => ['required', 'string', 'max:1000', new AlasanValid],
        ], [
            'ekskul_id.required' => 'Pilih ekskul terlebih dahulu.',
            'ekskul_id.exists' => 'Ekskul yang dipilih tidak valid.',
            'alasan.required' => 'Alasan bergabung wajib diisi.',
            'alasan.max' => 'Alasan bergabung maksimal 1000 karakter.',
        ]);

        if ($siswa->activePendaftaran() || $siswa->pendingPendaftaran()) {
            return $this->error('Kamu sudah terdaftar di ekskul.', 422);
        }

        if (! $siswa->isProfileComplete()) {
            return $this->error('Lengkapi data diri terlebih dahulu sebelum mendaftar ekskul.', 422);
        }

        $pendaftaran = DB::transaction(function () use ($siswa, $validated) {
            $siswa = Siswa::whereKey($siswa->id)->lockForUpdate()->firstOrFail();
            $ekskul = Ekskul::findOrFail($validated['ekskul_id']);

            if (! $ekskul->status || ! $ekskul->is_open_recruitment) {
                abort(422, 'Pendaftaran ekskul ini sedang ditutup.');
            }

            if ($siswa->activePendaftaran() || $siswa->pendingPendaftaran()) {
                return null;
            }

            return $siswa->pendaftarans()->create([
                'ekskul_id' => $ekskul->id,
                'tanggal_daftar' => now()->toDateString(),
                'status' => \App\Models\Pendaftaran::STATUS_PENDING,
                'alasan' => $validated['alasan'],
            ]);
        });

        if (! $pendaftaran) {
            return $this->error('Kamu sudah mengajukan pendaftaran atau terdaftar di ekskul.', 422);
        }

        NotifikasiService::pendaftaranMasuk($pendaftaran);

        return $this->created(
            ['pendaftaran' => (new PendaftaranResource($pendaftaran->loadMissing('ekskul')))->resolve()],
            'Pendaftaran kamu telah terkirim kepada ketua ekskul. Silakan menunggu konfirmasi dari ketua ekskul.',
        );
    }

    public function presensi(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $pendaftaran = $siswa->activePendaftaran();

        $presensis = collect();

        if ($pendaftaran) {
            $query = Presensi::where('pendaftaran_id', $pendaftaran->id)->with('kegiatan');

            if ($cari = $request->input('cari')) {
                $query->whereHas('kegiatan', fn ($k) => $k->where('materi', 'like', "%{$cari}%"));
            }

            if ($bulan = $request->input('bulan')) {
                [$tahun, $bulanNum] = explode('-', $bulan);
                $query->whereHas('kegiatan', fn ($k) => $k
                    ->whereYear('tanggal_kegiatan', $tahun)
                    ->whereMonth('tanggal_kegiatan', $bulanNum));
            }

            $presensis = $query->get();
        }

        return $this->ok([
            'pendaftaran' => $pendaftaran ? (new PendaftaranResource($pendaftaran->loadMissing('ekskul')))->resolve() : null,
            'presensi' => $presensis->map(fn ($p) => [
                'id' => $p->id,
                'status' => $p->status,
                'kegiatan' => $p->kegiatan ? (new KegiatanResource($p->kegiatan))->resolve() : null,
            ])->values(),
        ]);
    }

    public function rekap(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $pendaftaran = $siswa->activePendaftaran();
        $ekskul = $pendaftaran?->ekskul;

        $bulan = (new RekapAbsensiService)->normalizeBulan($request->input('bulan'));

        if (! $ekskul) {
            return $this->ok([
                'ekskul' => null,
                'bulan' => $bulan,
                'rekap' => null,
            ]);
        }

        $data = (new RekapAbsensiService)->rekap($ekskul, $bulan);

        $rekapSiswa = $data['rows']->firstWhere('pendaftaran.id', $pendaftaran->id);

        return $this->ok($this->serializeRekap($data, $rekapSiswa));
    }

    public function nilai(): JsonResponse
    {
        $siswa = $this->siswa();
        $pendaftaran = $siswa->activePendaftaran();
        $ekskul = $pendaftaran?->ekskul;
        $periode = Penilaian::periodeSekarang();

        $penilaian = null;

        if ($pendaftaran) {
            $penilaian = Penilaian::where('pendaftaran_id', $pendaftaran->id)
                ->where('periode', $periode['label'])
                ->where('status', Penilaian::STATUS_TERKIRIM)
                ->with('penilai')
                ->latest('updated_at')
                ->first();
        }

        return $this->ok([
            'pendaftaran' => $pendaftaran ? (new PendaftaranResource($pendaftaran->loadMissing('ekskul')))->resolve() : null,
            'ekskul' => $ekskul ? (new EkskulResource($ekskul))->resolve() : null,
            'periode' => $periode,
            'penilaian' => $penilaian ? (new PenilaianResource($penilaian))->resolve() : null,
        ]);
    }

    public function pengajuanIndex(): JsonResponse
    {
        $siswa = $this->siswa();
        $pendaftaran = $siswa->activePendaftaran()?->loadMissing('ekskul');
        $pengajuans = $siswa->pengajuanKeluars()->latest('tanggal_pengajuan')->get();

        return $this->ok([
            'siswa' => (new SiswaResource($siswa->loadMissing('kelas')))->resolve(),
            'ekskul' => $pendaftaran?->ekskul ? (new EkskulResource($pendaftaran->ekskul))->resolve() : null,
            'pengajuans' => $pengajuans->map(fn ($p) => [
                'id' => $p->id,
                'alasan' => $p->alasan,
                'status' => $p->status,
                'tanggal_pengajuan' => $p->tanggal_pengajuan?->toDateString(),
            ])->values(),
        ]);
    }

    public function storePengajuan(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $pendaftaran = $siswa->activePendaftaran();

        if (! $pendaftaran) {
            return $this->error('Kamu belum terdaftar di ekskul manapun.', 422);
        }

        $existsPending = $siswa->pengajuanKeluars()
            ->where('status', PengajuanKeluar::STATUS_PENDING)
            ->exists();

        if ($existsPending) {
            return $this->error('Kamu masih memiliki permohonan keluar yang berstatus pending.', 422);
        }

        $validated = $request->validate([
            'alasan' => ['required', 'string', 'max:1000', new AlasanValid],
        ], [
            'alasan.required' => 'Alasan keluar wajib diisi.',
            'alasan.max' => 'Alasan keluar maksimal 1000 karakter.',
        ]);

        $pengajuan = $siswa->pengajuanKeluars()->create([
            'ekskul_id' => $pendaftaran->ekskul_id,
            'alasan' => $validated['alasan'],
            'status' => PengajuanKeluar::STATUS_PENDING,
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        NotifikasiService::pengajuanKeluarMasuk($pengajuan);

        return $this->created([], 'Pengajuan keluar berhasil dikirim dan sedang menunggu keputusan ketua ekskul.');
    }

    public function notifikasiIndex(): JsonResponse
    {
        $siswa = $this->siswa();
        $notifikasis = $siswa->notifikasis()->orderByDesc('created_at')->get();

        return $this->ok([
            'unread_count' => $notifikasis->where('is_read', false)->count(),
            'notifikasis' => NotifikasiResource::collection($notifikasis)->resolve(),
        ]);
    }

    public function bacaNotifikasi(Notifikasi $notifikasi): JsonResponse
    {
        $siswa = $this->siswa();

        abort_unless($notifikasi->siswa_id === $siswa->id, 403, 'Akses ditolak.');

        $notifikasi->update(['is_read' => true]);

        return $this->noContent();
    }

    public function bacaSemuaNotifikasi(): JsonResponse
    {
        $siswa = $this->siswa();
        $siswa->notifikasis()->where('is_read', false)->update(['is_read' => true]);

        return $this->noContent();
    }

    private function serializeRekap(array $data, ?object $rekapSiswa): array
    {
        $row = fn (object $r) => [
            'pendaftaran_id' => $r->pendaftaran->id,
            'nama' => $r->pendaftaran->siswa->nama ?? '-',
            'kelas' => $r->pendaftaran->siswa->kelas->nama ?? '-',
            'sel' => $r->sel,
            'hadir' => $r->hadir,
            'izin' => $r->izin,
            'sakit' => $r->sakit,
            'alpha' => $r->alpha,
            'total' => $r->total,
            'persentase_kehadiran' => $r->persentaseKehadiran,
        ];

        return [
            'ekskul' => $data['ekskul'] ? (new EkskulResource($data['ekskul']))->resolve() : null,
            'bulan' => $data['bulan'],
            'kegiatans' => KegiatanResource::collection($data['kegiatans'])->resolve(),
            'event_kegiatans' => KegiatanResource::collection($data['eventKegiatans'])->resolve(),
            'total_hadir' => $data['totalHadir'],
            'total_izin' => $data['totalIzin'],
            'total_sakit' => $data['totalSakit'],
            'total_alpha' => $data['totalAlpha'],
            'available_months' => $data['availableMonths']->values()->all(),
            'rows' => $data['rows']->map($row)->values(),
            'rekap_siswa' => $rekapSiswa ? $row($rekapSiswa) : null,
        ];
    }
}