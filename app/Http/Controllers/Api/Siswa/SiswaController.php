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
use App\Models\SiswaProfileHistory;
use App\Rules\AlasanValid;
use App\Support\AturanEkskul;
use App\Services\NotifikasiService;
use App\Services\RekapAbsensiService;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SiswaController extends ApiController
{
    private function siswa(): Siswa
    {
        $siswa = auth()->user()->siswa;

        abort_unless($siswa, 404, 'Profil siswa belum tersedia. Hubungi kesiswaan.');

        return $siswa;
    }

    public function dashboard(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $activePendaftarans = $siswa->activePendaftarans()
            ->with('ekskul.pembina')
            ->latest('id')
            ->get();
        $requestedEkskulId = $request->integer('ekskul_id');
        $pendaftaran = $requestedEkskulId
            ? $activePendaftarans->firstWhere('ekskul_id', $requestedEkskulId)
            : $activePendaftarans->first();

        return $this->ok($this->dashboardPayload($siswa, $pendaftaran, $activePendaftarans));
    }

    private function dashboardPayload(Siswa $siswa, $pendaftaran, $activePendaftarans): array
    {
        $ekskul = $pendaftaran?->ekskul;

        $statusTerakhir = $siswa->pendaftarans()->with('ekskul')->latest('tanggal_daftar')->first();
        $nonaktifStatus = null;

        if (! $pendaftaran && $statusTerakhir) {
            $nonaktifStatus = in_array($statusTerakhir->status, ['nonaktif', 'keluar'])
                ? $statusTerakhir->status
                : null;
        }

        $activeSummaries = $activePendaftarans
            ->filter(fn ($active) => $active->ekskul)
            ->map(function ($active) {
                $activeEkskul = $active->ekskul;
                $totalHadir = Presensi::where('pendaftaran_id', $active->id)
                    ->where('status', Presensi::STATUS_HADIR)
                    ->count();
                $upcoming = $activeEkskul->kegiatans()
                    ->whereDate('tanggal_kegiatan', '>=', today())
                    ->orderBy('tanggal_kegiatan')
                    ->get();
                $hasSubmittedTestimoni = $activeEkskul->testimoniss()
                    ->where('user_id', auth()->id())
                    ->whereIn('status', ['pending', 'approved'])
                    ->exists();

                return [
                    'id' => $activeEkskul->id,
                    'ekskul' => (new EkskulResource($activeEkskul))->resolve(),
                    'pendaftaran' => (new PendaftaranResource($active))->resolve(),
                    'status' => $active->status,
                    'total_hadir' => $totalHadir,
                    'kegiatan_mendatang' => $upcoming->map(fn ($kegiatan) => array_merge(
                        (new KegiatanResource($kegiatan))->resolve(),
                        [
                            'ekskul_id' => $activeEkskul->id,
                            'ekskul_nama' => $activeEkskul->nama_ekskul,
                        ],
                    ))->values(),
                    'has_submitted_testimoni' => $hasSubmittedTestimoni,
                ];
            })->values();

        $kegiatanMendatang = $activeSummaries
            ->flatMap(fn ($summary) => $summary['kegiatan_mendatang'])
            ->sortBy(fn ($kegiatan) => $kegiatan['tanggal_kegiatan'] ?? '')
            ->values();
        $totalHadir = $activeSummaries->sum('total_hadir');
        $selectedSummary = $activeSummaries->firstWhere('id', $ekskul?->id);
        $isWarned = ($selectedSummary['status'] ?? null) === 'peringatan';

        $unreadNotifCount = $siswa->notifikasis()->where('is_read', false)->count();

        $hasSubmittedTestimoni = $selectedSummary['has_submitted_testimoni'] ?? false;

        return [
            'siswa' => (new SiswaResource($siswa->loadMissing('kelas')))->resolve(),
            'pendaftaran' => $pendaftaran ? (new PendaftaranResource($pendaftaran))->resolve() : null,
            'is_warned' => $isWarned,
            'warning_count' => $activeSummaries->where('status', 'peringatan')->count(),
            'status_terakhir' => $statusTerakhir ? (new PendaftaranResource($statusTerakhir))->resolve() : null,
            'nonaktif_status' => $nonaktifStatus,
            'ekskul' => $ekskul ? (new EkskulResource($ekskul))->resolve() : null,
            'active_ekskuls' => EkskulResource::collection($activePendaftarans->pluck('ekskul')->filter()->values())->resolve(),
            'active_summaries' => $activeSummaries,
            'kegiatan_mendatang' => $kegiatanMendatang,
            'total_hadir' => $totalHadir,
            'total_hadir_selected' => $selectedSummary['total_hadir'] ?? 0,
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
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telp' => ['nullable', 'string', 'max:25'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
            'angkatan' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'medsos' => ['nullable', 'string', 'max:255'],
        ], [
            'username.unique' => 'Username sudah dipakai akun lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $oldEmail = $user->email;
        $oldUsername = $user->username;
        $newEmail = array_key_exists('email', $validated) ? $validated['email'] : $oldEmail;
        $newUsername = array_key_exists('username', $validated) ? $validated['username'] : $oldUsername;

        $oldFoto = $siswa->foto;
        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('profile-photos', 'public')
            : $oldFoto;

        $siswaFields = [
            'no_telp',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'angkatan',
            'alamat',
            'medsos',
        ];

        DB::transaction(function () use ($user, $siswa, $oldEmail, $newEmail, $oldUsername, $newUsername, $oldFoto, $fotoPath, $validated, $siswaFields) {
            $userUpdate = [];
            if ($newEmail !== $oldEmail) {
                $userUpdate['email'] = $newEmail;
                $userUpdate['email_verified_at'] = null;
            }
            if ($newUsername !== $oldUsername) {
                $userUpdate['username'] = $newUsername;
            }
            if (!empty($userUpdate)) {
                $user->update($userUpdate);
            }

            $siswaUpdate = [];
            if (array_key_exists('email', $validated)) {
                $siswaUpdate['email'] = $newEmail;
            }
            if ($fotoPath !== $oldFoto) {
                $siswaUpdate['foto'] = $fotoPath;
            }
            foreach ($siswaFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $siswaUpdate[$field] = $validated[$field];
                }
            }

            $changes = [];
            if (array_key_exists('email', $validated) && $oldEmail !== $newEmail) {
                $changes['email'] = [$oldEmail, $newEmail];
            }
            if (array_key_exists('no_telp', $validated) && $siswa->no_telp !== ($validated['no_telp'] ?? null)) {
                $changes['no_telp'] = [$siswa->no_telp, $validated['no_telp'] ?? null];
            }
            if ($oldFoto !== $fotoPath) {
                $changes['foto'] = [$oldFoto, $fotoPath];
            }

            if (!empty($siswaUpdate)) {
                $siswa->update($siswaUpdate);
            }

            $changedFields = [];
            foreach ($changes as $field => [$oldValue, $newValue]) {
                if ($oldValue !== $newValue) {
                    $changedFields[] = $field;
                    SiswaProfileHistory::create([
                        'siswa_id' => $siswa->id,
                        'changed_by_user_id' => $user->id,
                        'field' => $field,
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                    ]);
                }
            }

            if (!empty($changedFields)) {
                NotifikasiService::profilSiswaDiubah($siswa, $changedFields);
            }
        });

        return $this->ok([], 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak cocok dengan akun Anda.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->ok([], 'Password berhasil diperbarui.');
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
        $activePendaftarans = $siswa?->activePendaftarans()->with('ekskul')->get() ?? collect();
        $pendingPendaftarans = $siswa?->pendingPendaftarans()->with('ekskul')->get() ?? collect();

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
            'active_pendaftarans' => PendaftaranResource::collection($activePendaftarans)->resolve(),
            'pending_pendaftarans' => PendaftaranResource::collection($pendingPendaftarans)->resolve(),
            'active_ekskuls' => $siswa ? EkskulResource::collection($siswa->activeEkskuls())->resolve() : [],
            'bisa_daftar' => $siswa ? $siswa->alasanTidakBisaDaftar() === null : false,
            'alasan_per_ekskul' => $siswa
                ? $joinable->mapWithKeys(fn (Ekskul $ekskul) => [
                    $ekskul->id => $siswa->alasanTidakBisaDaftar($ekskul),
                ])
                : [],
            'joinable' => EkskulResource::collection($joinable)->resolve(),
        ]);
    }

    public function daftar(Request $request): JsonResponse
    {
        $siswa = $this->siswa();

        if (! $siswa->isProfileComplete()) {
            return $this->error('Lengkapi data diri (nama, kelas, jenis kelamin) terlebih dahulu sebelum mendaftar ekskul.', 422);
        }

        $terpakai = $siswa->activePendaftarans()->count() + $siswa->pendingPendaftarans()->count();

        if ($terpakai >= AturanEkskul::MAKS_EKSKUL) {
            return $this->error('Kamu sudah terdaftar di '.AturanEkskul::MAKS_EKSKUL.' ekskul.', 422);
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

        if (! $siswa->isProfileComplete()) {
            return $this->error('Lengkapi data diri terlebih dahulu sebelum mendaftar ekskul.', 422);
        }

        $target = Ekskul::findOrFail($validated['ekskul_id']);

        if ($alasan = $siswa->alasanTidakBisaDaftar($target)) {
            return $this->error($alasan, 422);
        }

        $pendaftaran = DB::transaction(function () use ($siswa, $validated) {
            $siswa = Siswa::whereKey($siswa->id)->lockForUpdate()->firstOrFail();
            $ekskul = Ekskul::findOrFail($validated['ekskul_id']);

            if (! $ekskul->status || ! $ekskul->is_open_recruitment) {
                abort(422, 'Pendaftaran ekskul ini sedang ditutup.');
            }

            if ($siswa->alasanTidakBisaDaftar($ekskul)) {
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
        $targetEkskulId = $request->input('ekskul_id') ?? $request->input('ekskul');
        $pendaftaran = $targetEkskulId ? $siswa->activePendaftaranUntuk((int) $targetEkskulId) : $siswa->activePendaftaran();

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
            'active_ekskuls' => EkskulResource::collection($siswa->activeEkskuls())->resolve(),
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
        $targetEkskulId = $request->input('ekskul_id') ?? $request->input('ekskul');
        $pendaftaran = $targetEkskulId ? $siswa->activePendaftaranUntuk((int) $targetEkskulId) : $siswa->activePendaftaran();
        $ekskul = $pendaftaran?->ekskul;

        $bulan = (new RekapAbsensiService)->normalizeBulan($request->input('bulan'));

        if (! $ekskul) {
            return $this->ok([
                'ekskul' => null,
                'bulan' => $bulan,
                'rekap' => null,
                'active_ekskuls' => EkskulResource::collection($siswa->activeEkskuls())->resolve(),
            ]);
        }

        $data = (new RekapAbsensiService)->rekap($ekskul, $bulan);

        $rekapSiswa = $data['rows']->firstWhere('pendaftaran.id', $pendaftaran->id);

        return $this->ok(array_merge(
            $this->serializeRekap($data, $rekapSiswa),
            ['active_ekskuls' => EkskulResource::collection($siswa->activeEkskuls())->resolve()]
        ));
    }

    public function nilai(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $targetEkskulId = $request->input('ekskul_id') ?? $request->input('ekskul');
        $pendaftaran = $targetEkskulId ? $siswa->activePendaftaranUntuk((int) $targetEkskulId) : $siswa->activePendaftaran();
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
            'active_ekskuls' => EkskulResource::collection($siswa->activeEkskuls())->resolve(),
            'periode' => $periode,
            'penilaian' => $penilaian ? (new PenilaianResource($penilaian))->resolve() : null,
            'rekap' => $pendaftaran ? Penilaian::kehadiran($pendaftaran, $periode) : null,
        ]);
    }

    public function pengajuanIndex(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $targetEkskulId = $request->input('ekskul_id') ?? $request->input('ekskul');
        $pendaftaran = $targetEkskulId ? $siswa->activePendaftaranUntuk((int) $targetEkskulId) : $siswa->activePendaftaran();
        $pendaftaran?->loadMissing('ekskul');
        $pengajuans = $siswa->pengajuanKeluars()->with('ekskul')->latest('tanggal_pengajuan')->get();

        return $this->ok([
            'siswa' => (new SiswaResource($siswa->loadMissing('kelas')))->resolve(),
            'ekskul' => $pendaftaran?->ekskul ? (new EkskulResource($pendaftaran->ekskul))->resolve() : null,
            'active_ekskuls' => EkskulResource::collection($siswa->activeEkskuls())->resolve(),
            'pengajuans' => $pengajuans->map(fn ($p) => [
                'id' => $p->id,
                'ekskul' => $p->ekskul ? (new EkskulResource($p->ekskul))->resolve() : null,
                'alasan' => $p->alasan,
                'status' => $p->status,
                'tanggal_pengajuan' => $p->tanggal_pengajuan?->toDateString(),
            ])->values(),
        ]);
    }

    public function storePengajuan(Request $request): JsonResponse
    {
        $siswa = $this->siswa();
        $targetEkskulId = $request->input('ekskul_id') ?? $request->input('ekskul');
        $pendaftaran = $targetEkskulId ? $siswa->activePendaftaranUntuk((int) $targetEkskulId) : $siswa->activePendaftaran();

        if (! $pendaftaran) {
            return $this->error('Kamu belum terdaftar di ekskul manapun.', 422);
        }

        $existsPending = $siswa->pengajuanKeluars()
            ->where('status', PengajuanKeluar::STATUS_PENDING)
            ->where('ekskul_id', $pendaftaran->ekskul_id)
            ->exists();

        if ($existsPending) {
            return $this->error('Kamu masih memiliki permohonan keluar yang berstatus pending untuk ekskul ini.', 422);
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

    public function hapusNotifikasi(Notifikasi $notifikasi): JsonResponse
    {
        $siswa = $this->siswa();

        abort_unless($notifikasi->siswa_id === $siswa->id, 403, 'Akses ditolak.');

        $notifikasi->delete();

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
            'foto' => MediaUrl::for($r->pendaftaran->siswa?->foto),
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
