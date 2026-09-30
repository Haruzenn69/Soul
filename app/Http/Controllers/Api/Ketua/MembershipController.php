<?php

namespace App\Http\Controllers\Api\Ketua;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Http\Resources\PendaftaranResource;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MembershipController extends ApiController
{
    use KetuaEkskul;

    public function anggota(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $query = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', ['diterima', 'nonaktif', 'peringatan', 'keluar']);

        $total = (clone $query)->count();

        if ($cari = $request->input('cari')) {
            $query->whereHas('siswa', fn ($s) => $s
                ->where('nama', 'like', "%{$cari}%")
                ->orWhere('nis', 'like', "%{$cari}%"));
        }

        if ($status = $request->input('status')) {
            if ($status !== 'semua') {
                $query->where('status', $status);
            }
        }

        $peringatanCount = (clone $query)->where('status', 'peringatan')->count();
        $nonaktifCount = (clone $query)->where('status', 'nonaktif')->count();
        $keluarCount = (clone $query)->where('status', 'keluar')->count();

        $anggotas = $query->with('siswa.kelas')
            ->orderBy('tanggal_daftar', 'desc')
            ->paginate(10);

        return $this->ok([
            'total' => $total,
            'peringatan_count' => $peringatanCount,
            'nonaktif_count' => $nonaktifCount,
            'keluar_count' => $keluarCount,
            'anggotas' => PendaftaranResource::collection($anggotas)->resolve(),
            'pagination' => [
                'current' => $anggotas->currentPage(),
                'last' => $anggotas->lastPage(),
                'per_page' => $anggotas->perPage(),
            ],
        ]);
    }

    public function updateStatusAnggota(Request $request, Pendaftaran $pendaftaran): JsonResponse
    {
        $this->ensureEkskul($pendaftaran);

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,peringatan,nonaktif'],
        ], [
            'status.required' => 'Status anggota wajib dipilih.',
            'status.in' => 'Status anggota tidak valid.',
        ]);

        $target = $validated['status'];
        $namaSiswa = $pendaftaran->siswa?->nama ?? 'Siswa';

        $labels = [
            'diterima' => 'Aktif',
            'peringatan' => 'Peringatan',
            'nonaktif' => 'Nonaktif',
        ];

        if ($pendaftaran->siswa_id === auth()->user()->siswa?->id
            || $pendaftaran->siswa?->jabatan === 'ketua') {
            if (in_array($target, ['peringatan', 'nonaktif'], true)) {
                return $this->error('Kamu (ketua) tidak bisa memberi peringatan atau menonaktifkan dirimu sendiri.', 422);
            }
        }

        if ($pendaftaran->status === $target) {
            return $this->error('Status anggota sudah '.($labels[$target] ?? $target).'.', 422);
        }

        $pendaftaran->update(['status' => $target]);

        [$judul, $pesan, $tipe] = match ($target) {
            'peringatan' => [
                'Peringatan dari Ketua Ekskul',
                "{$namaSiswa}, kamu mendapatkan peringatan dari ketua {$pendaftaran->ekskul->nama_ekskul}. Tingkatkan keaktifan dan kehadiranmu, ya!",
                'ditolak',
            ],
            'nonaktif' => [
                'Dinonaktifkan dari Ekskul',
                "{$namaSiswa}, kamu telah dinonaktifkan dari ekskul {$pendaftaran->ekskul->nama_ekskul} oleh ketua ekskul. Hubungi ketua jika ini kurang tepat.",
                'ditolak',
            ],
            default => [
                'Status Anggota Diaktifkan Kembali',
                "{$namaSiswa}, status anggota kamu di ekskul {$pendaftaran->ekskul->nama_ekskul} telah diaktifkan kembali. Selamat bergabung!",
                'diterima',
            ],
        };

        NotifikasiService::statusAnggotaDiubah($pendaftaran, $target, $judul, $pesan, $tipe);

        return $this->noContent('Status '.$namaSiswa.' berhasil diubah menjadi '.($labels[$target] ?? $target).'.');
    }

    public function pendaftaran(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $query = Pendaftaran::where('ekskul_id', $ekskul->id);

        $total = (clone $query)->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $diterimaCount = (clone $query)->where('status', 'diterima')->count();
        $ditolakCount = (clone $query)->where('status', 'ditolak')->count();

        if ($cari = $request->input('cari')) {
            $query->whereHas('siswa', fn ($s) => $s
                ->where('nama', 'like', "%{$cari}%")
                ->orWhere('nis', 'like', "%{$cari}%"));
        }

        if ($status = $request->input('status')) {
            if ($status !== 'semua') {
                $query->where('status', $status);
            }
        }

        $pendaftarans = $query->with('siswa.kelas')
            ->orderBy('tanggal_daftar', 'desc')
            ->paginate(10);

        return $this->ok([
            'total' => $total,
            'pending_count' => $pendingCount,
            'diterima_count' => $diterimaCount,
            'ditolak_count' => $ditolakCount,
            'pendaftarans' => PendaftaranResource::collection($pendaftarans)->resolve(),
            'pagination' => [
                'current' => $pendaftarans->currentPage(),
                'last' => $pendaftarans->lastPage(),
                'per_page' => $pendaftarans->perPage(),
            ],
        ]);
    }

    public function prosesPendaftaran(Request $request, Pendaftaran $pendaftaran): JsonResponse
    {
        $this->ensureEkskul($pendaftaran);

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,ditolak'],
        ]);

        if ($pendaftaran->status !== Pendaftaran::STATUS_PENDING) {
            return $this->error('Hanya pendaftaran berstatus pending yang dapat diproses.', 422);
        }

        DB::transaction(function () use ($pendaftaran, $validated) {
            $pendaftaran = Pendaftaran::lockForUpdate()->findOrFail($pendaftaran->id);
            $siswa = \App\Models\Siswa::lockForUpdate()->findOrFail($pendaftaran->siswa_id);

            abort_unless($pendaftaran->status === Pendaftaran::STATUS_PENDING, 422, 'Pendaftaran ini sudah diproses.');

            if ($validated['status'] === Pendaftaran::STATUS_DITERIMA) {
                $exists = Pendaftaran::where('siswa_id', $pendaftaran->siswa_id)
                    ->where('id', '!=', $pendaftaran->id)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->exists();

                abort_if($exists, 422, 'Siswa ini sudah aktif di ekskul lain.');
            }

            $pendaftaran->update(['status' => $validated['status']]);

            if ($validated['status'] === Pendaftaran::STATUS_DITERIMA) {
                $siswa->update(['jabatan' => 'anggota']);
                NotifikasiService::pendaftaranDiterima($pendaftaran);
            } else {
                NotifikasiService::pendaftaranDitolak($pendaftaran);
            }
        });

        return $this->noContent('Status pendaftaran berhasil diupdate.');
    }

    public function pengajuanKeluar(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $query = PengajuanKeluar::where('ekskul_id', $ekskul->id);

        $total = (clone $query)->count();

        if ($cari = $request->input('cari')) {
            $query->where(fn ($q) => $q
                ->where('alasan', 'like', "%{$cari}%")
                ->orWhereHas('siswa', fn ($s) => $s->where('nama', 'like', "%{$cari}%")));
        }

        if ($status = $request->input('status')) {
            if ($status !== 'semua') {
                $query->where('status', $status);
            }
        }

        $pengajuans = $query->with('siswa')->orderBy('tanggal_pengajuan', 'desc')->paginate(10);

        return $this->ok([
            'total' => $total,
            'pengajuans' => $pengajuans->map(fn ($p) => [
                'id' => $p->id,
                'nama' => $p->siswa->nama ?? '-',
                'alasan' => $p->alasan,
                'status' => $p->status,
                'tanggal_pengajuan' => $p->tanggal_pengajuan?->toDateString(),
            ])->values(),
            'pagination' => [
                'current' => $pengajuans->currentPage(),
                'last' => $pengajuans->lastPage(),
                'per_page' => $pengajuans->perPage(),
            ],
        ]);
    }

    public function prosesPengajuanKeluar(Request $request, PengajuanKeluar $pengajuanKeluar): JsonResponse
    {
        $this->ensureEkskul($pengajuanKeluar);

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,ditolak'],
        ]);

        if ($pengajuanKeluar->status !== PengajuanKeluar::STATUS_PENDING) {
            return $this->error('Hanya pengajuan berstatus pending yang dapat diproses.', 422);
        }

        DB::transaction(function () use ($pengajuanKeluar, $validated) {
            $pengajuanKeluar = PengajuanKeluar::lockForUpdate()->findOrFail($pengajuanKeluar->id);

            abort_unless($pengajuanKeluar->status === PengajuanKeluar::STATUS_PENDING, 422, 'Pengajuan ini sudah diproses.');

            $pengajuanKeluar->update(['status' => $validated['status']]);

            if ($validated['status'] === PengajuanKeluar::STATUS_DITERIMA) {
                $pengajuanKeluar->siswa->update(['jabatan' => 'siswa']);

                Pendaftaran::where('siswa_id', $pengajuanKeluar->siswa_id)
                    ->where('ekskul_id', $pengajuanKeluar->ekskul_id)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->update(['status' => Pendaftaran::STATUS_KELUAR]);

                NotifikasiService::pengajuanKeluarDiterima($pengajuanKeluar);
            } else {
                NotifikasiService::pengajuanKeluarDitolak($pengajuanKeluar);
            }
        });

        return $this->noContent('Pengajuan keluar berhasil diupdate.');
    }
}