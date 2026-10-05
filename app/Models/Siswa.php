<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $fillable = [
        'user_id',
        'nis',
        'foto',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'kelas_id',
        'angkatan',
        'status',
        'jenis_kelamin',
        'email',
        'no_telp',
        'alamat',
        'medsos',
        'jabatan',
    ];

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function pengajuanKeluars(): HasMany
    {
        return $this->hasMany(PengajuanKeluar::class);
    }

    public function riwayatJabatans(): HasMany
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    /**
     * Riwayat jabatan ketua pada satu ekskul, termasuk periode yang sedang berjalan.
     */
    public function riwayatKetua(Ekskul|int $ekskul): HasMany
    {
        return $this->riwayatJabatans()
            ->where('jabatan', RiwayatJabatan::JABATAN_KETUA)
            ->where('ekskul_id', $ekskul instanceof Ekskul ? $ekskul->id : $ekskul);
    }

    /**
     * Periode ketua yang sudah selesai pada ekskul tertentu (data arsip).
     */
    public function selesaiSebagaiKetua(Ekskul|int $ekskul): ?RiwayatJabatan
    {
        return $this->riwayatKetua($ekskul)
            ->whereNotNull('selesai')
            ->orderByDesc('selesai')
            ->orderByDesc('id')
            ->first();
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function profileHistories(): HasMany
    {
        return $this->hasMany(SiswaProfileHistory::class)->latest();
    }

    public function classHistories(): HasMany
    {
        return $this->hasMany(SiswaRiwayatKelas::class)->orderByDesc('diproses_pada');
    }

    public function pengajuanKeluar(): HasMany
    {
        return $this->pengajuanKeluars();
    }

    public function activePendaftaran(): ?Pendaftaran
    {
        return $this->pendaftarans()
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->latest('id')
            ->first();
    }

    public function activeEkskul(): ?Ekskul
    {
        return $this->activePendaftaran()?->ekskul;
    }

    public function pendingPendaftaran(): ?Pendaftaran
    {
        return $this->pendaftarans()
            ->where('status', Pendaftaran::STATUS_PENDING)
            ->latest('id')
            ->first();
    }

    public function isKetua(): bool
    {
        return $this->jabatan === 'ketua';
    }

    public function isProfileComplete(): bool
    {
        return filled($this->nama) && filled($this->kelas_id) && filled($this->jenis_kelamin);
    }
}
