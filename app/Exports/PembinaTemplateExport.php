<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembinaTemplateExport implements FromArray, WithColumnFormatting, WithColumnWidths, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return ['NIP', 'Username', 'Nama Lengkap', 'Email', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'No. Telp', 'Alamat', 'Media Sosial'];
    }

    public function array(): array
    {
        return [];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 26,
            'B' => 24,
            'C' => 30,
            'D' => 30,
            'E' => 20,
            'F' => 22,
            'G' => 18,
            'H' => 16,
            'I' => 18,
            'J' => 32,
            'K' => 28,
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']]],
        ];
    }
}
