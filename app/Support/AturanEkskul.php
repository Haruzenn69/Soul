<?php

namespace App\Support;

/**
 * Aturan lintas-ekskul untuk seorang siswa.
 *
 * Dua batasan utama:
 *  1. Seorang siswa maksimal mengikuti {@see self::MAKS_EKSKUL} ekskul sekaligus.
 *  2. Kelompok 4P1I (Paskibra, Polsis, Pramuka, PMR, IRMA) hanya boleh dipilih
 *     satu. Identifikasi dilakukan dari nama ekskul karena tidak ada kolom
 *     khusus di database.
 */
final class AturanEkskul
{
    /** Jumlah maksimal ekskul aktif per siswa. */
    public const MAKS_EKSKUL = 2;

    /** Jumlah maksimal ekskul wajib per siswa. */
    public const MAKS_EKSKUL_WAJIB = 1;

    /** Nama kategori ekskul wajib. */
    public const KATEGORI_WAJIB = 'Wajib';

    /**
     * Kategori fallback (default) untuk ekskul yang kategorinya dihapus.
     * Kategori ini dilindungi dan tidak bisa dihapus oleh kesiswaan.
     */
    public const KATEGORI_FALLBACK = 'Lainnya';

    /** Default daftar seluruh kategori ekskul di sistem. */
    public const DEFAULT_KATEGORI = [
        'Wajib',
        'Olahraga',
        'Seni',
        'Bahasa',
        'Bela Diri',
        'Lainnya',
    ];

    /** Fallback konstanta kategori ekskul. */
    public const KATEGORI_LIST = self::DEFAULT_KATEGORI;

    /**
     * Dapatkan daftar kategori ekskul yang aktif di database.
     *
     * @return list<string>
     */
    public static function kategoriList(): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('kategori_ekskuls')) {
                $dbKats = \App\Models\KategoriEkskul::orderBy('id')->pluck('nama')->all();
                if (! empty($dbKats)) {
                    return $dbKats;
                }
            }
        } catch (\Throwable) {
            // Fallback
        }

        return self::DEFAULT_KATEGORI;
    }

    /** Label kelompok yang tidak boleh dirangkap. */
    public const LABEL_P1I = '4P1I (Paskibra, Polsis, Pramuka, PMR, IRMA)';

    /**
     * Cek apakah sebuah kategori termasuk kategori Wajib.
     */
    public static function isWajib(?string $kategori): bool
    {
        $kat = mb_strtolower(trim((string) $kategori));

        return in_array($kat, ['wajib', 'ekskul wajib'], true);
    }

    /**
     * Cek apakah kategori adalah kategori fallback sistem (Lainnya).
     * Kategori ini tidak bisa dihapus karena menjadi tempat penampungan
     * ekskul yang kategorinya dihapus.
     */
    public static function isFallback(?string $kategori): bool
    {
        return mb_strtolower(trim((string) $kategori)) === mb_strtolower(self::KATEGORI_FALLBACK);
    }

    /**
     * Cek apakah kategori adalah kategori sistem (Wajib atau Lainnya) yang tidak bisa dihapus.
     */
    public static function isSistem(?string $kategori): bool
    {
        return self::isWajib($kategori) || self::isFallback($kategori);
    }

    /**
     * Kata kunci penanda ekskul 4P1I.
     *
     * Nilai boolean menandakan apakah pencocokan boleh dilakukan pada versi
     * teks yang dipadatkan (tanpa spasi). Kata kunci pendek seperti "pmr" dan
     * "irma" dibatasi pencocokan utuh agar tidak menangkap kata lain.
     *
     * @var array<string, bool>
     */
    private const P1I = [
        'paskibra' => true,
        'polsis' => true,
        'polisi siswa' => true,
        'pramuka' => true,
        'pmr' => false,
        'palang merah' => true,
        'irma' => false,
    ];

    public static function isP1i(?string $namaEkskul): bool
    {
        $nama = trim((string) $namaEkskul);

        if ($nama === '') {
            return false;
        }

        $terpisah = mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $nama) ?? '');
        $terpisah = trim(preg_replace('/\s+/', ' ', $terpisah) ?? '');
        $rapat = str_replace(' ', '', $terpisah);

        foreach (self::P1I as $kata => $bolehRapat) {
            if (preg_match('/\b'.preg_quote($kata, '/').'\b/u', $terpisah) === 1) {
                return true;
            }

            if ($bolehRapat && str_contains($rapat, str_replace(' ', '', $kata))) {
                return true;
            }
        }

        return false;
    }
}
