<?php

namespace App\Support;

class TableKit
{
    /**
     * Membaca & memvalidasi parameter sort/direction dari request.
     *
     * @param  array<string>  $columns  whitelist kolom yang diizinkan di-sort
     * @return array{0: string, 1: string} [sort, direction]
     */
    public static function sort(array $columns, string $default, string $defaultDirection = 'asc'): array
    {
        $sort = (string) request()->input('sort', $default);
        $direction = strtolower((string) request()->input('direction', $defaultDirection));

        if (! in_array($sort, $columns, true)) {
            $sort = $default;
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = $defaultDirection;
        }

        return [$sort, $direction];
    }
}