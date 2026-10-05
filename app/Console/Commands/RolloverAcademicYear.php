<?php

namespace App\Console\Commands;

use App\Services\AcademicYearRolloverService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class RolloverAcademicYear extends Command
{
    protected $signature = 'school:rollover-academic-year {--date= : Simulate the date in YYYY-MM-DD format} {--dry-run : Preview the changes without updating data}';

    protected $description = 'Create the next academic year, promote grade XI, and archive grade XII students.';

    public function handle(AcademicYearRolloverService $rollover): int
    {
        try {
            $date = $this->option('date');
            $runAt = $date ? Carbon::createFromFormat('!Y-m-d', $date, 'Asia/Jakarta') : now('Asia/Jakarta');
            if (! $runAt || ($date && $runAt->format('Y-m-d') !== $date)) {
                throw new \InvalidArgumentException('Format tanggal harus YYYY-MM-DD, contoh 2027-07-01.');
            }

            $result = $this->option('dry-run')
                ? $rollover->preview($runAt)
                : $rollover->rollover($runAt);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if ($result['already_done']) {
            $this->info("Tahun ajaran {$result['to']} sudah aktif; kenaikan tidak dijalankan ulang.");

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("Simulasi saja; tidak ada perubahan database. Tahun ajaran {$result['from']} akan berganti menjadi {$result['to']}.");
            $this->line("Kelas baru yang akan dibuat: {$result['new_classes']}; naik ke kelas 12: {$result['promoted']}; menunggu penempatan kelas 11: {$result['pending']}; menjadi alumni/nonaktif: {$result['graduated']}.");

            return self::SUCCESS;
        }

        $this->info("Tahun ajaran {$result['from']} berhasil ditutup dan {$result['to']} diaktifkan.");
        $this->line("Naik ke kelas 12: {$result['promoted']} siswa; menunggu penempatan kelas 11: {$result['pending']} siswa; alumni/nonaktif: {$result['graduated']} siswa.");

        return self::SUCCESS;
    }
}
