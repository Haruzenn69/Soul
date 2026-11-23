<?php

namespace App\Models;

use App\Support\AturanEkskul;
use App\Support\EkskulInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekskul extends Model
{
    public const TIPE_EKSKUL = 'ekskul';
    public const TIPE_KOMUNITAS = 'komunitas';

    protected $fillable = [
        'pembina_id',
        'pelatih_id',
        'parent_id',
        'tipe',
        'nama_ekskul',
        'kategori',
        'deskripsi',
        'tagline',
        'tujuan',
        'logo',
        'cover',
        'jadwal',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subdivisis(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function isKomunitas(): bool
    {
        return ($this->tipe ?? self::TIPE_EKSKUL) === self::TIPE_KOMUNITAS;
    }

    public function isEkskul(): bool
    {
        return ! $this->isKomunitas();
    }

    public function isSubdivisi(): bool
    {
        return ! is_null($this->parent_id);
    }

    public function hasSubdivisi(): bool
    {
        return $this->subdivisis()->exists();
    }

    public function ketuaSubdivisi(): ?Siswa
    {
        if ($this->isSubdivisi() && $this->parent_id) {
            $pengurus = Pengurus::where('ekskul_id', $this->parent_id)
                ->where('subdivisi_id', $this->id)
                ->where('jabatan', Pengurus::JABATAN_WAKIL_KETUA)
                ->where('status', Pengurus::STATUS_AKTIF)
                ->first();

            if ($pengurus?->siswa) {
                return $pengurus->siswa;
            }
        }

        return $this->ketua();
    }

    public function wakilKetuaSubdivisi(int|self $subdivisi): ?Siswa
    {
        $subId = is_numeric($subdivisi) ? $subdivisi : $subdivisi->id;
        $pengurus = $this->penguruses()
            ->where('subdivisi_id', $subId)
            ->where('jabatan', Pengurus::JABATAN_WAKIL_KETUA)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->first();

        return $pengurus?->siswa;
    }

    public function labelTipe(): string
    {
        return $this->isKomunitas() ? 'Komunitas' : 'Ekstrakurikuler';
    }

    public function pembina(): BelongsTo
    {
        return $this->belongsTo(Pembina::class);
    }

    public function pelatih(): BelongsTo
    {
        return $this->belongsTo(Pelatih::class);
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class)->orderBy('tanggal_kegiatan');
    }

    public function pengajuanKeluars(): HasMany
    {
        return $this->hasMany(PengajuanKeluar::class);
    }

    public function laporanBulanans(): HasMany
    {
        return $this->hasMany(LaporanBulanan::class);
    }

    public function prestasis(): HasMany
    {
        return $this->hasMany(Prestasi::class);
    }

    public function testimoniss(): HasMany
    {
        return $this->hasMany(Testimoni::class);
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
                return $this->logo;
            }
            if (str_starts_with($this->logo, 'images/')) {
                return asset($this->logo);
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo)) {
                return asset('storage/'.$this->logo);
            }
        }

        return asset('images/komunita.jpg');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function galeris(): HasMany
    {
        return $this->hasMany(EkskulGaleri::class)->latest();
    }

    public function penguruses(): HasMany
    {
        return $this->hasMany(Pengurus::class);
    }

    public function pengurusAktif(): HasMany
    {
        return $this->penguruses()->where('status', Pengurus::STATUS_AKTIF);
    }

    public function riwayatJabatans(): HasMany
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function riwayatKetua(): HasMany
    {
        return $this->riwayatJabatans()->where('jabatan', RiwayatJabatan::JABATAN_KETUA);
    }

    /**
     * Periode ketua yang masih berjalan, bila ada.
     */
    public function periodeKetuaAktif(): ?RiwayatJabatan
    {
        return $this->riwayatKetua()
            ->whereNull('selesai')
            ->orderByDesc('mulai')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Semua periode ketua yang sudah selesai (data arsip).
     */
    public function periodeKetuaSelesai(): HasMany
    {
        return $this->riwayatKetua()
            ->whereNotNull('selesai')
            ->orderByDesc('selesai');
    }

    public function ketua(): ?Siswa
    {
        $aktif = $this->periodeKetuaAktif()?->siswa;
        if ($aktif) {
            return $aktif;
        }

        return $this->pendaftarans()
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->whereHas('siswa', function ($q) {
                $q->where('jabatan', 'ketua')
                    ->whereDoesntHave('riwayatJabatans', function ($rq) {
                        $rq->where('ekskul_id', '!=', $this->id)
                            ->where('jabatan', RiwayatJabatan::JABATAN_KETUA)
                            ->whereNull('selesai');
                    });
            })
            ->first()
            ?->siswa;
    }

    public function getBidangAttribute(): string
    {
        return EkskulInfo::bidang($this->nama_ekskul);
    }

    public function isWajib(): bool
    {
        return AturanEkskul::isWajib($this->kategori);
    }
}
