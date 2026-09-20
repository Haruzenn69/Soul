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
            $ekskul = auth()->user()->siswa?->activeEkskul();

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
        abort_unless($model->ekskul_id === $this->ekskul()->id, 403, 'Data ini bukan bagian dari ekskul Anda.');
    }
}
