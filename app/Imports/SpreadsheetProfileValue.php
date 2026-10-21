<?php

namespace App\Imports;

use Carbon\Carbon;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class SpreadsheetProfileValue
{
    public static function placeOfBirth(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (class_exists(\Laravolt\Indonesia\Models\City::class)) {
            $city = \Laravolt\Indonesia\Models\City::where('name', $value)->first();
            if ($city) {
                return ucwords(strtolower($city->name));
            }

            $city = \Laravolt\Indonesia\Models\City::where('name', 'KOTA ' . $value)->first();
            if ($city) {
                return ucwords(strtolower($city->name));
            }

            $city = \Laravolt\Indonesia\Models\City::where('name', 'KABUPATEN ' . $value)->first();
            if ($city) {
                return ucwords(strtolower($city->name));
            }

            $city = \Laravolt\Indonesia\Models\City::where('name', 'like', "%{$value}%")->first();
            if ($city) {
                return ucwords(strtolower($city->name));
            }
        }

        return ucwords(strtolower($value));
    }

    public static function religion(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $standardReligions = [
            'islam' => 'Islam',
            'kristen' => 'Kristen',
            'protestan' => 'Kristen',
            'katolik' => 'Katolik',
            'catholic' => 'Katolik',
            'hindu' => 'Hindu',
            'buddha' => 'Buddha',
            'budha' => 'Buddha',
            'khonghucu' => 'Khonghucu',
            'konghucu' => 'Khonghucu',
        ];

        $lower = mb_strtolower($value);
        if (isset($standardReligions[$lower])) {
            return $standardReligions[$lower];
        }

        return ucwords($value);
    }

    public static function gender(mixed $value): ?string
    {
        $value = mb_strtolower(trim((string) $value));

        return match ($value) {
            'l', 'laki-laki', 'laki laki', 'pria' => 'laki-laki',
            'p', 'perempuan', 'wanita' => 'perempuan',
            '' => null,
            default => $value,
        };
    }


    public static function date(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (is_numeric($value) && (float) $value >= 1 && (float) $value <= 100000) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (Throwable) {
                // Try the explicit date formats below.
            }
        }

        foreach (['!d-m-Y', '!d/m/Y', '!d.m.Y', '!d,m,Y', '!Y-m-d', '!Y/m/d', '!d-m-y', '!d/m/y'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date !== false && $date->format(substr($format, 1)) === $value) {
                    return $date->format('Y-m-d');
                }
            } catch (Throwable) {
                // Continue until a supported format matches.
            }
        }

        return $value;
    }
}
