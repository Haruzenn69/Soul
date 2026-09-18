<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AkunTemplateExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    public function __construct(protected string $role = 'siswa')
    {
    }

    public function headings(): array
    {
        if ($this->role === 'pembina') {
            return ['email', 'nip', 'nama', 'jenis_kelamin', 'password'];
        }

        return ['email', 'nis', 'nama', 'kelas', 'jenis_kelamin', 'jabatan', 'password'];
    }

    public function array(): array
    {
        if ($this->role === 'pembina') {
            return [
                ['pembina1@soul.test', '197801012005011001', 'Budi Santoso', 'laki-laki', 'password'],
                ['', '198202102010021002', 'Siti Aminah', 'perempuan', 'password'],
            ];
        }

        $contohKelas = Kelas::orderBy('nama')->value('nama') ?? '';

        return [
            ['siswa1@soul.test', '2023001', 'Andi Pratama', $contohKelas, 'laki-laki', 'siswa', 'password'],
            ['', '2023002', 'Bela Safitri', '', 'perempuan', 'siswa', 'password'],
            ['', '2023003', 'Citra Ayu', '', 'perempuan', 'anggota', 'password'],
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF3B82F6']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = 500;

                if ($this->role === 'pembina') {
                    $this->addListDropdown($sheet, 'D', 2, $lastRow, ['laki-laki', 'perempuan']);
                } else {
                    $this->addKelasDropdown($sheet, 'D', $lastRow);
                    $this->addListDropdown($sheet, 'E', 2, $lastRow, ['laki-laki', 'perempuan']);
                    $this->addListDropdown($sheet, 'F', 2, $lastRow, ['siswa', 'anggota', 'ketua']);
                }
            },
        ];
    }

    /**
     * Dropdown kelas dari database via hidden sheet (bisa >255 karakter total).
     */
    protected function addKelasDropdown(Worksheet $sheet, string $column, int $lastRow): void
    {
        $kelas = Kelas::orderBy('nama')->pluck('nama');

        $spreadsheet = $sheet->getParent();
        if (!$spreadsheet) {
            return;
        }

        try {
            $refSheet = $spreadsheet->getSheetByName('Referensi');
        } catch (\Throwable) {
            $refSheet = null;
        }
        if (!$refSheet) {
            $refSheet = $spreadsheet->createSheet();
            $refSheet->setTitle('Referensi');
        }

        $refSheet->getCell('A1')->setValue('DAFTAR KELAS (dari database, pilih di kolom kelas)');
        $i = 2;
        foreach ($kelas as $nama) {
            if ($nama === '') {
                continue;
            }
            $refSheet->getCell('A'.$i)->setValue($nama);
            $i++;
        }
        $refSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        $range = '$A$2:$A$'.max(2, $i - 1);
        $spreadsheet->addNamedRange(new NamedRange('daftar_kelas', $refSheet, $range));

        $this->addListDropdown($sheet, $column, 2, $lastRow, [], '=daftar_kelas');
    }

    /**
     * Dropdown tipe list. Jika $formula disediakan, gunakan itu (misal named range),
     * selain itu gunakan daftar inline.
     */
    protected function addListDropdown(Worksheet $sheet, string $column, int $fromRow, int $toRow, array $options, ?string $formula = null): void
    {
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setFormula1($formula ?? '"'.implode(',', $options).'"');
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setPromptTitle('Pilih dari daftar');
        $validation->setPrompt('Pilih salah satu nilai dari dropdown.');
        $validation->setErrorTitle('Nilai tidak valid');
        $validation->setError('Pilih nilai dari dropdown yang tersedia.');

        $sheet->setDataValidation($column.$fromRow.':'.$column.$toRow, $validation);
    }
}