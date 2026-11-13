<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Ekskul;

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
            $ekskul = $siswa?->ekskulDipimpin() ?? $siswa?->activeEkskul();

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

        abort_unless($model->ekskul_id === $this->ekskul()->id, 403, 'Data ini bukan bagian dari ekskul Anda.');
    }
}
