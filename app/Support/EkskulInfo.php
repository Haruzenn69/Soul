<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Helper tampilan untuk katalog ekskul.
 *
 * Jadwal disimpan sebagai teks bebas ("Selasa & Jumat, 15:30 - 17:30"), padahal
 * kapan ekskul bertemu adalah fakta yang paling menentukan seorang siswa bisa
 * bergabung atau tidak. Di sini teks bebas itu dipecah menjadi hari dan jam
 * supaya bisa digambar sebagai sumbu minggu, dengan fallback aman kalau formatnya
 * tidak dikenali.
 */
final class EkskulInfo
{
    /** Label hari untuk sumbu minggu. */
    public const HARI = [
        1 => 'Sen',
        2 => 'Sel',
        3 => 'Rab',
        4 => 'Kam',
        5 => 'Jum',
        6 => 'Sab',
        7 => 'Min',
    ];

    /** Nama hari penuh, untuk pembaca layar. */
    public const HARI_PANJANG = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    /** @var array<int, string> */
    private const NAMA_HARI = [
        1 => 'senin',
        2 => 'selasa',
        3 => 'rabu',
        4 => 'kamis',
        5 => 'jumat',
        6 => 'sabtu',
        7 => 'minggu',
    ];

    /**
     * @return array{hari: list<int>, waktu: ?string, mentah: ?string}
     */
    public static function jadwal(?string $jadwal): array
    {        $mentah = trim((string) $jadwal);

        if ($mentah === '') {
            return ['hari' => [], 'waktu' => null, 'mentah' => null];
        }

        // "Jum'at" dan "Jum'at" harus tetap dikenali sebagai Jumat.
        $teks = str_replace(["'", "\u{2019}"], '', mb_strtolower($mentah));

        $hari = [];
        foreach (self::NAMA_HARI as $angka => $nama) {
            if (str_contains($teks, $nama)) {
                $hari[] = $angka;
            }
        }

        return [
            'hari' => $hari,
            'waktu' => self::jam($teks),
            'mentah' => $mentah,
        ];
    }

    /**
     * @param  list<int>  $hari
     */
    public static function hariTerbaca(array $hari): string
    {
        $nama = array_values(array_filter(
            array_map(fn (int $angka) => self::HARI_PANJANG[$angka] ?? null, $hari)
        ));

        if ($nama === []) {
            return '';
        }

        if (count($nama) === 1) {
            return $nama[0];
        }

        $terakhir = array_pop($nama);

        return implode(', ', $nama).' dan '.$terakhir;
    }

    /**
     * URL logo yang benar-benar ada di disk. Kalau berkasnya hilang, kembalikan
     * null supaya kartu jatuh ke monogram, bukan gambar rusak.
     */
    public static function logo(?string $path): ?string
    {
        return self::berkas($path);
    }

    /**
     * URL cover yang benar-benar ada di disk.
     */
    public static function cover(?string $path): ?string
    {
        return self::berkas($path);
    }

    private static function berkas(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/'.$path);
    }

    private static function jam(string $teks): ?string
    {
        $polaRentang = '/(\d{1,2})[.:](\d{2})\s*(?:-|–|—|s\/d|s\.d|thru|to)\s*(\d{1,2})[.:](\d{2})/u';

        if (preg_match($polaRentang, $teks, $cocok)) {
            $mulai = self::format((int) $cocok[1], (int) $cocok[2]);
            $selesai = self::format((int) $cocok[3], (int) $cocok[4]);

            if ($mulai !== null && $selesai !== null) {
                return $mulai.'–'.$selesai;
            }
        }

        if (preg_match('/(\d{1,2})[.:](\d{2})/u', $teks, $cocok)) {
            return self::format((int) $cocok[1], (int) $cocok[2]);
        }

        return null;
    }

    private static function format(int $jam, int $menit): ?string
    {
        if ($jam > 23 || $menit > 59) {
            return null;
        }

        return sprintf('%02d.%02d', $jam, $menit);
    }
}
