<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatJabatan extends Model
{
    public const JABATAN_KETUA = 'ketua';

    public const ALASAN_DIGANTI = 'diganti';

    public const ALASAN_DICOPOT = 'dicopot';

    public const ALASAN_KELUAR = 'keluar';

    public const ALASAN_DIEDIT = 'diedit';

    protected $fillable = [
        'siswa_id',
        'ekskul_id',
        'jabatan',
        'mulai',
        'selesai',
        'alasan_selesai',
    ];

    protected $casts = [
        'mulai' => 'date',
        'selesai' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }

    /**
     * Periode yang masih berjalan dibedakan dengan `selesai` bernilai null.
     */
    public function isActive(): bool
    {
        return $this->selesai === null;
    }

    /**
     * Periode yang sudah selesai = kandidat arsip "ketua sebelumnya".
     */
    public function isFinished(): bool
    {
        return ! $this->isActive();
    }

    public function periodeText(): string
    {
        $mulai = $this->mulai?->translatedFormat('M Y') ?? '-';

        return $this->selesai
            ? $mulai.' - '.$this->selesai->translatedFormat('M Y')
            : $mulai.' - Sekarang';
    }

    public function durasiHari(): ?int
    {
        $end = $this->selesai ?? now();

        return $this->mulai ? (int) abs($this->mulai->diffInDays($end)) : null;
    }
}
