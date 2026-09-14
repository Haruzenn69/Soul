<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AlasanValid implements ValidationRule
{
    private const MIN_KARAKTER = 10;

    private const MAX_KARAKTER = 1000;

    /**
     * Kata/frasa kosong atau iseng yang tidak menjadi alasan yang baik.
     */
    private array $filler = [
        'aaa', 'aaaa', 'aaa ', 'asd', 'asdf', 'qwe', 'qwerty', 'qwertyuiop', 'zzz',
        'xyz', 'testing', 'test', 'tes', 'dummy', 'coba', 'cobain', 'jajal',
        'random', 'terserah', 'bebas', 'pokoknya', 'semau gue', 'suka suka',
        'gatau', 'ga tau', 'gak tau', 'nggak tau', 'ngk tau', 'ngktau', 'nggatau',
        'tidak tau', 'tidak tahu', 'kurang tau', 'gatau aja', 'ga tau aja',
        'tidak mau', 'ga mau', 'gak mau', 'nggak mau', 'tidak ingin', 'ga ingin',
        'gak ingin', 'nggak ingin', 'tidak pengen', 'ga pengen', 'malas', 'ngmalas',
        'tidak ada alasan', 'ga ada alasan', 'gak ada alasan', 'tidak ada',
        'ga ada', 'gak ada', 'nggak ada', 'haha', 'hahaha', 'hehe', 'hihi', 'wkwk',
        'wkwkwk', 'lol', 'njir', 'anjir', 'bodo', 'apalagi', 'biasa aja', 'apaan',
        'oi', 'ehe', 'yaudah', 'lah', 'dah', 'ah',
        'ingin bergabung', 'mau bergabung', 'pengen bergabung', 'ingin gabung',
        'mau gabung', 'pengen gabung', 'ikut aja', 'sekedar ikut', 'sekadar ikut',
        'ingin ikut', 'mau ikut', 'tertarik aja', 'ingin mencoba', 'mau coba',
        'coba dulu', 'iseng', 'iseng aja', 'temen ngajakin', 'diajak teman', 'ikut teman',
        'mengisi waktu luang', 'mengisi waktu', 'isi waktu luang', 'buang waktu',
        'supaya sibuk', 'biar sibuk', 'biar ga gabut', 'biar gak gabut', 'biar nggak gabut',
        'karena teman', 'karena diajak', 'diajak teman', 'ikut misuh', 'masa gratis', 'gratis',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail(':attribute harus berupa teks.');
            return;
        }

        $alasan = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $value)));
        $panjang = mb_strlen($value);

        if ($alasan === '') {
            $fail(':attribute wajib diisi.');
            return;
        }

        if ($panjang < self::MIN_KARAKTER) {
            $fail(':attribute minimal ' . self::MIN_KARAKTER . ' karakter agar alasan jelas dan bermakna.');
            return;
        }

        if ($panjang > self::MAX_KARAKTER) {
            $fail(':attribute maksimal ' . self::MAX_KARAKTER . ' karakter.');
            return;
        }

        $kata = preg_split('/[\s,\-\.]+/u', $alasan, -1, PREG_SPLIT_NO_EMPTY);
        $kataBermakna = array_filter($kata, fn ($k) => mb_strlen($k) >= 3);

        if (count($kataBermakna) < 2) {
            $fail(':attribute harus berisi alasan yang jelas (minimal 2 kata bermakna).');
            return;
        }

        $hanyaHuruf = preg_replace('/[^a-z0-9]/u', '', $alasan);

        if (strlen(count_chars($hanyaHuruf, 3)) <= 3) {
            $fail(':attribute tidak boleh berupa huruf yang diulang-ulang (misal "aaaa", "asd").');
            return;
        }

        if (preg_match('/(.)\1{4,}/u', $hanyaHuruf)) {
            $fail(':attribute tidak boleh berupa huruf yang diulang-ulang (misal "aaaa").');
            return;
        }

        foreach ($this->filler as $frasa) {
            $frasa = trim($frasa);

            if ($alasan === $frasa) {
                $fail(':attribute harus berupa alasan yang baik dan masuk akal, bukan sekadar "' . $frasa . '".');
                return;
            }

            if (mb_strlen($alasan) <= 20 && str_starts_with($alasan, $frasa)) {
                $fail(':attribute harus berupa alasan yang baik dan masuk akal, bukan sekadar kata iseng.');
                return;
            }
        }
    }
}