<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembinaTemplateExport implements FromArray, WithColumnFormatting, WithColumnWidths, WithEvents, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return ['NIP', 'Nama Lengkap', 'Email', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'No. Telp', 'Alamat', 'Media Sosial'];
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
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 26,
            'B' => 30,
            'C' => 30,
            'D' => 20,
            'E' => 22,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 32,
            'J' => 28,
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Dropdown Jenis Kelamin (Kolom D)
                $genderValidation = new DataValidation();
                $genderValidation->setType(DataValidation::TYPE_LIST);
                $genderValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $genderValidation->setAllowBlank(false);
                $genderValidation->setShowInputMessage(true);
                $genderValidation->setShowErrorMessage(true);
                $genderValidation->setShowDropDown(true);
                $genderValidation->setErrorTitle('Pilihan Tidak Valid');
                $genderValidation->setError('Pilih jenis kelamin dari daftar yang tersedia.');
                $genderValidation->setPromptTitle('Pilih Jenis Kelamin');
                $genderValidation->setPrompt('Silakan pilih Laki-laki atau Perempuan');
                $genderValidation->setFormula1('"Laki-laki,Perempuan"');
                $sheet->setDataValidation('D2:D500', $genderValidation);

                // Prompt Tempat Lahir (Kolom E)
                $birthPlacePrompt = new DataValidation();
                $birthPlacePrompt->setType(DataValidation::TYPE_CUSTOM);
                $birthPlacePrompt->setShowInputMessage(true);
                $birthPlacePrompt->setShowErrorMessage(false);
                $birthPlacePrompt->setPromptTitle('Petunjuk Tempat Lahir');
                $birthPlacePrompt->setPrompt('Tuliskan nama Kota/Kabupaten (contoh: Kota Bandung, Kabupaten Bogor, atau Bandung).');
                $sheet->setDataValidation('E2:E500', $birthPlacePrompt);

                // Petunjuk & Komentar Tanggal Lahir (Kolom F)
                $sheet->getComment('F1')->getText()->createTextRun("Petunjuk Format Tanggal Lahir:\n- Gunakan format DD-MM-YYYY (contoh: 26-06-2008)\n- Atau format YYYY-MM-DD (contoh: 2008-06-26)\n- Pastikan hari, bulan, dan tahun valid.");

                $birthDatePrompt = new DataValidation();
                $birthDatePrompt->setType(DataValidation::TYPE_CUSTOM);
                $birthDatePrompt->setShowInputMessage(true);
                $birthDatePrompt->setShowErrorMessage(false);
                $birthDatePrompt->setPromptTitle('Petunjuk Tanggal Lahir');
                $birthDatePrompt->setPrompt("Gunakan format tanggal: DD-MM-YYYY (contoh: 26-06-2008) atau YYYY-MM-DD (contoh: 2008-06-26).");
                $sheet->setDataValidation('F2:F500', $birthDatePrompt);

                // Dropdown Agama (Kolom G)
                $agamaValidation = new DataValidation();
                $agamaValidation->setType(DataValidation::TYPE_LIST);
                $agamaValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $agamaValidation->setAllowBlank(false);
                $agamaValidation->setShowInputMessage(true);
                $agamaValidation->setShowErrorMessage(true);
                $agamaValidation->setShowDropDown(true);
                $agamaValidation->setErrorTitle('Pilihan Tidak Valid');
                $agamaValidation->setError('Pilih agama dari daftar yang tersedia.');
                $agamaValidation->setPromptTitle('Pilih Agama');
                $agamaValidation->setPrompt('Pilih salah satu: Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu');
                $agamaValidation->setFormula1('"Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu"');
                $sheet->setDataValidation('G2:G500', $agamaValidation);
            },
        ];
    }
}
