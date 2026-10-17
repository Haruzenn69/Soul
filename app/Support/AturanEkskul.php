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

    /** Label kelompok yang tidak boleh dirangkap. */
    public const LABEL_P1I = '4P1I (Paskibra, Polsis, Pramuka, PMR, IRMA)';

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
