<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Ekskul;
use App\Models\Pengurus;
use Illuminate\Http\Request;

/**
 * Membatasi seluruh operasi ketua hanya pada ekskul yang dipimpinnya,
 * sekaligus menjadi satu-satunya sumber "ekskul milik ketua yang login".
 */
trait KetuaEkskul
{
    private ?Ekskul $ketuaEkskulCache = null;

    protected function ekskul(): Ekskul
    {
        if (! $this->ketuaEkskulCache) {
            $user = auth()->user();

            // Jika diakses oleh Kesiswaan atau Admin: pilih ekskul aktif berdasarkan request, session, atau pertama
            if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
                $ekskulId = request('ekskul') ?? request('ekskul_id') ?? session('kesiswaan_active_ekskul_id');
                $ekskul = null;

                if ($ekskulId) {
                    $ekskul = Ekskul::find($ekskulId);
                }

                if (! $ekskul) {
                    $ekskul = Ekskul::orderBy('nama_ekskul')->first();
                }

                abort_unless($ekskul, 404, 'Belum ada data ekstrakurikuler yang terdaftar.');

                session(['kesiswaan_active_ekskul_id' => $ekskul->id]);
                $this->ketuaEkskulCache = $ekskul;

                return $this->ketuaEkskulCache;
            }

            $siswa = $user?->siswa;

            // Ketua bisa mengikuti lebih dari satu ekskul, jadi utamakan ekskul
            // yang benar-benar dipimpin, baru jatuh ke ekskul aktif terakhir.
            $ekskul = $siswa?->ekskulDipimpin();
            if (! $ekskul) {
                $pengurusSubdivisi = $siswa?->penguruses()
                    ->where('jabatan', Pengurus::JABATAN_WAKIL_KETUA)
                    ->where('status', Pengurus::STATUS_AKTIF)
                    ->whereNotNull('subdivisi_id')
                    ->with('subdivisi')
                    ->latest('id')
                    ->first();
                $ekskul = $pengurusSubdivisi?->subdivisi?->isSubdivisi()
                    ? $pengurusSubdivisi->subdivisi
                    : $siswa?->activeEkskul();
            }

            abort_unless($ekskul, 404, 'Anda belum tergabung dalam ekskul mana pun.');

            $this->ketuaEkskulCache = $ekskul;
        }

        return $this->ketuaEkskulCache;
    }

    /**
     * Pastikan sebuah record (Pendaftaran, Kegiatan, Prestasi, dll.)
     * merupakan bagian dari ekskul ketua yang sedang login.
     */
    protected function ensureEkskul(mixed $model): void
    {
        $user = auth()->user();

        // Kesiswaan dan Admin memiliki wewenang atas seluruh ekskul
        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return;
        }

        $ekskul = $this->ekskul();
        $ownsRecord = (int) $model->ekskul_id === (int) $ekskul->id;
        $monitorsChild = $ekskul->isSubdivisi() === false
            && $ekskul->subdivisis()->whereKey($model->ekskul_id)->exists()
            && auth()->user()?->siswa?->ekskulDipimpin()?->id === $ekskul->id;

        abort_unless($ownsRecord || $monitorsChild, 403, 'Data ini bukan bagian dari ekskul Anda.');
    }

    /**
     * Ekskul yang boleh dipakai untuk pendaftaran anggota pada halaman ketua.
     * Ketua pusat memilih subdivisi; ketua subdivisi selalu dibatasi ke unitnya.
     */
    protected function targetAnggotaEkskul(Request $request): Ekskul
    {
        $ekskul = $this->ekskul();
        $user = auth()->user();
        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            $requested = $request->input('subdivisi_id');
            if ($ekskul->hasSubdivisi()) {
                abort_unless($requested, 422, 'Pilih subdivisi tujuan anggota.');
                return $ekskul->subdivisis()->findOrFail($requested);
            }
            return $ekskul;
        }

        if ($ekskul->isSubdivisi()) {
            abort_if($request->filled('subdivisi_id') && (int) $request->input('subdivisi_id') !== $ekskul->id, 403, 'Ketua subdivisi hanya dapat memilih subdivisinya sendiri.');
            return $ekskul;
        }

        if ($ekskul->hasSubdivisi()) {
            $requested = $request->input('subdivisi_id');
            abort_unless($requested, 422, 'Pilih subdivisi tujuan anggota.');
            return $ekskul->subdivisis()->findOrFail($requested);
        }

        return $ekskul;
    }

    /** @return array<int> */
    protected function anggotaEkskulIds(Ekskul $ekskul): array
    {
        if ($ekskul->isSubdivisi()) {
            return [$ekskul->id];
        }

        if (auth()->user() && in_array(auth()->user()->role, ['kesiswaan', 'admin'], true)
            || auth()->user()?->siswa?->ekskulDipimpin()?->id === $ekskul->id) {
            return array_merge([$ekskul->id], $ekskul->subdivisis()->pluck('id')->all());
        }

        return [$ekskul->id];
    }

    protected function kegiatanEkskul(): Ekskul
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return $this->ekskul();
        }

        $siswa = $user?->siswa;
        $dipimpin = $siswa?->ekskulDipimpin();
        if ($dipimpin) {
            $requestedEkskulId = request()->integer('ekskul');
            if ($requestedEkskulId && $dipimpin->hasSubdivisi()) {
                $requestedEkskul = Ekskul::find($requestedEkskulId);
                if ($requestedEkskul?->parent_id === $dipimpin->id) {
                    return $requestedEkskul;
                }
            }

            return $dipimpin;
        }

        $pengurus = $siswa?->penguruses()
            ->where('jabatan', Pengurus::JABATAN_WAKIL_KETUA)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->whereNotNull('subdivisi_id')
            ->with('subdivisi')
            ->latest('id')
            ->first();

        abort_unless($pengurus?->subdivisi?->isSubdivisi(), 403, 'Anda tidak memimpin sub divisi.');

        return $pengurus->subdivisi;
    }

    protected function kegiatanEkskulIds(Ekskul $ekskul): array
    {
        if ($this->isKetuaUtamaMonitoring($ekskul)) {
            if ($ekskul->isSubdivisi()) {
                return [$ekskul->id];
            }

            return array_merge([$ekskul->id], $ekskul->subdivisis()->pluck('id')->all());
        }

        return [$ekskul->id];
    }

    protected function isKetuaUtamaMonitoring(Ekskul $ekskul): bool
    {
        $siswa = auth()->user()?->siswa;
        $dipimpin = $siswa?->ekskulDipimpin();

        return $dipimpin !== null
            && $dipimpin->subdivisis()->exists()
            && ((int) $dipimpin->id === (int) $ekskul->id || (int) $ekskul->parent_id === (int) $dipimpin->id);
    }

    protected function ensureKegiatanEkskul(mixed $record): void
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return;
        }

        $ekskul = $this->kegiatanEkskul();
        $allowedIds = $this->kegiatanEkskulIds($ekskul);
        abort_unless(in_array((int) $record->ekskul_id, array_map('intval', $allowedIds), true), 403, 'Data ini bukan bagian dari ekskul yang Anda pantau.');
    }

    protected function pastikanBolehKelolaKegiatan(?Ekskul $target = null): Ekskul
    {
        $ekskul = $target ?? $this->kegiatanEkskul();
        abort_if($this->isKetuaUtamaMonitoring($ekskul), 403, 'Ketua utama hanya dapat memantau kegiatan dan laporan sub divisi.');

        if (! in_array(auth()->user()?->role, ['kesiswaan', 'admin'], true)) {
            abort_unless($ekskul->id === $this->kegiatanEkskul()->id, 403, 'Anda tidak berwenang mengelola data ini.');
        }

        return $ekskul;
    }
}
