<?php

namespace App\Models;

use App\Support\AturanEkskul;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    /**
     * Semua pendaftaran yang sedang aktif (diterima atau peringatan).
     */
    public function activePendaftarans(): HasMany
    {
        return $this->pendaftarans()
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN]);
    }

    /**
     * Semua ekskul yang sedang diikuti. Sejak aturan multi-ekskul, seorang
     * siswa bisa punya lebih dari satu.
     *
     * @return Collection<int, Ekskul>
     */
    public function activeEkskuls(): Collection
    {
        return $this->activePendaftarans()
            ->with('ekskul')
            ->get()
            ->pluck('ekskul')
            ->filter()
            ->values();
    }

    public function activeEkskulCount(): int
    {
        return $this->activePendaftarans()->count();
    }

    /**
     * Ambil pendaftaran aktif pada satu ekskul tertentu (opsional). Tanpa
     * parameter, mengembalikan yang paling baru untuk kompatibilitas lama.
     */
    public function activePendaftaranUntuk(Ekskul|int|null $ekskul = null): ?Pendaftaran
    {
        $query = $this->activePendaftarans()->with('ekskul');

        if ($ekskul !== null) {
            $query->where('ekskul_id', $ekskul instanceof Ekskul ? $ekskul->id : $ekskul);
        }

        return $query->latest('id')->first();
    }

    public function activePendaftaran(): ?Pendaftaran
    {
        return $this->resolveActivePendaftaran();
    }

    public function activeEkskul(): ?Ekskul
    {
        return $this->activePendaftaran()?->ekskul;
    }

    /**
     * Dapatkan ekskul yang sedang aktif dipilih dalam session (atau requested),
     * dengan fallback cerdas: jika siswa adalah ketua di satu ekskul dan anggota di ekskul lain,
     * secara otomatis default ke ekskul anggota saat berada di area siswa.
     */
    public function resolveActiveEkskul(?int $requestedEkskulId = null): ?Ekskul
    {
        $aktif = $this->activeEkskuls();
        if ($aktif->isEmpty()) {
            return null;
        }

        if (! $requestedEkskulId && request()->filled('ekskul')) {
            $candidate = (int) request('ekskul');
            if ($aktif->contains('id', $candidate)) {
                $requestedEkskulId = $candidate;
            }
        }

        if ($requestedEkskulId && $aktif->contains('id', (int) $requestedEkskulId)) {
            session(['siswa_dashboard_ekskul_id' => (int) $requestedEkskulId]);
            return $aktif->firstWhere('id', (int) $requestedEkskulId);
        }

        $sessionEkskulId = session('siswa_dashboard_ekskul_id');
        if ($sessionEkskulId && $aktif->contains('id', (int) $sessionEkskulId)) {
            return $aktif->firstWhere('id', (int) $sessionEkskulId);
        }

        // Default: jika user adalah ketua di salah satu ekskul, defaultkan ke ekskul anggota biasa
        $ledEkskul = $this->ekskulDipimpin();
        $memberEkskul = $ledEkskul
            ? $aktif->firstWhere('id', '!=', $ledEkskul->id)
            : null;

        $chosen = $memberEkskul ?? $aktif->first();
        if ($chosen) {
            session(['siswa_dashboard_ekskul_id' => $chosen->id]);
        }

        return $chosen;
    }

    /**
     * Dapatkan pendaftaran aktif sesuai ekskul yang sedang dipilih.
     */
    public function resolveActivePendaftaran(?int $requestedEkskulId = null): ?Pendaftaran
    {
        $ekskul = $this->resolveActiveEkskul($requestedEkskulId);
        if (! $ekskul) {
            return $this->activePendaftaranUntuk();
        }

        return $this->activePendaftaranUntuk($ekskul);
    }

    public function pendingPendaftarans(): HasMany
    {
        return $this->pendaftarans()->where('status', Pendaftaran::STATUS_PENDING);
    }

    public function pendingPendaftaran(): ?Pendaftaran
    {
        return $this->pendingPendaftarans()->latest('id')->first();
    }

    /**
     * Ekskul yang benar-benar dipimpin (jabatan ketua).
     *
     * Sumber utama adalah riwayat jabatan yang periodenya masih berjalan.
     * Kalau data lama belum punya riwayat, jatuh ke ekskul aktif terakhir
     * supaya tetap kompatibel.
     */
    public function ekskulDipimpin(): ?Ekskul
    {
        $periode = $this->riwayatJabatans()
            ->where('jabatan', RiwayatJabatan::JABATAN_KETUA)
            ->whereNull('selesai')
            ->with('ekskul')
            ->orderByDesc('mulai')
            ->orderByDesc('id')
            ->first();

        if ($periode?->ekskul) {
            return $periode->ekskul;
        }

        return $this->isKetua() ? $this->activeEkskul() : null;
    }

    /**
     * Kenapa siswa ini belum/tidak boleh mendaftar (opsional ke ekskul
     * tertentu). Mengembalikan null kalau boleh mendaftar.
     */
    public function alasanTidakBisaDaftar(?Ekskul $target = null): ?string
    {
        $aktif = $this->activeEkskuls();
        $pending = $this->pendingPendaftarans()->with('ekskul')->get();

        if ($target) {
            $sudah = $aktif->contains(fn (Ekskul $e) => $e->id === $target->id)
                || $pending->contains(fn (Pendaftaran $p) => $p->ekskul_id === $target->id);

            if ($sudah) {
                return 'Kamu sudah terdaftar atau mengajukan pendaftaran di ekskul ini.';
            }
        }

        $terpakai = $aktif->count() + $pending->count();

        if ($terpakai >= AturanEkskul::MAKS_EKSKUL) {
            return 'Kamu sudah terdaftar di '.$terpakai.' ekskul. Maksimal '.AturanEkskul::MAKS_EKSKUL.' ekskul per siswa.';
        }

        if ($target && AturanEkskul::isP1i($target->nama_ekskul)) {
            $bentrokAktif = $aktif->first(fn (Ekskul $e) => AturanEkskul::isP1i($e->nama_ekskul));

            if ($bentrokAktif) {
                return 'Kamu sudah mengikuti '.$bentrokAktif->nama_ekskul.'. Ekskul '.AturanEkskul::LABEL_P1I.' hanya boleh dipilih satu.';
            }

            $bentrokPending = $pending->first(fn (Pendaftaran $p) => $p->ekskul && AturanEkskul::isP1i($p->ekskul->nama_ekskul));

            if ($bentrokPending) {
                return 'Kamu sedang mengajukan '.$bentrokPending->ekskul->nama_ekskul.'. Ekskul '.AturanEkskul::LABEL_P1I.' hanya boleh dipilih satu.';
            }
        }

        return null;
    }

    /**
     * Kenapa ketua tidak boleh menerima pendaftaran ini (maksimal ekskul atau
     * bentrok sesama 4P1I). Mengembalikan null kalau aman diterima.
     */
    public function alasanTidakDapatMenerima(Pendaftaran $target): ?string
    {
        $lain = $this->activePendaftarans()
            ->where('id', '!=', $target->id)
            ->with('ekskul')
            ->get();

        if ($lain->count() >= AturanEkskul::MAKS_EKSKUL) {
            return 'Siswa ini sudah aktif di '.AturanEkskul::MAKS_EKSKUL.' ekskul.';
        }

        if ($target->ekskul && AturanEkskul::isP1i($target->ekskul->nama_ekskul)) {
            $bentrok = $lain->first(fn (Pendaftaran $p) => $p->ekskul && AturanEkskul::isP1i($p->ekskul->nama_ekskul));

            if ($bentrok) {
                return 'Siswa ini sudah aktif di ekskul '.AturanEkskul::LABEL_P1I.' ('.$bentrok->ekskul->nama_ekskul.').';
            }
        }

        return null;
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
