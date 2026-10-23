<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ResetDatabase extends Command
{
    protected $signature = 'school:reset-data
        {--dry-run : Tampilkan jumlah data yang akan dihapus tanpa mengubah database}
        {--force : Lewati konfirmasi interaktif}';

    protected $description = 'Hapus tahun ajaran, siswa, dan seluruh data transaksi; pertahankan akun pembina, data kelas, dan data ekskul.';

    private const TABLES_TO_CLEAR = [
        'presensis',
        'presensi_pelatihs',
        'penilaians',
        'notifikasis',
        'pengajuan_keluars',
        'pendaftarans',
        'riwayat_jabatans',
        'siswa_profile_histories',
        'siswa_riwayat_kelas',
        'kegiatans',
        'laporan_bulanans',
        'prestasis',
        'testimonis',
        'faqs',
        'ekskul_galeris',
        'pembina_profile_histories',
        'siswas',
    ];

    private const TRANSIENT_TABLES = [
        'sessions',
        'password_reset_tokens',
        'personal_access_tokens',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
    ];

    public function handle(): int
    {
        $counts = [];

        foreach (self::TABLES_TO_CLEAR as $table) {
            $counts[$table] = Schema::hasTable($table) ? DB::table($table)->count() : 0;
        }

        $counts['users (selain pembina)'] = User::query()->where('role', '!=', 'pembina')->count();
        $counts['pelatihs'] = Schema::hasTable('pelatihs') ? DB::table('pelatihs')->count() : 0;
        $counts['tahun_ajarans'] = Schema::hasTable('tahun_ajarans') ? DB::table('tahun_ajarans')->count() : 0;

        foreach (self::TRANSIENT_TABLES as $table) {
            $counts[$table] = Schema::hasTable($table) ? DB::table($table)->count() : 0;
        }

        $this->table(
            ['Tabel/Data', 'Akan dihapus'],
            collect($counts)->map(fn (int $count, string $label) => [$label, $count])->values()->all()
        );

        $this->line('Dipertahankan: akun pembina, pembinas, kelas, ekskuls, kategori_ekskuls, dan data referensi wilayah.');

        if ($this->option('dry-run')) {
            $this->info('Simulasi saja; tidak ada perubahan database.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Hapus semua data di atas secara permanen?', false)) {
            $this->warn('Dibatalkan.');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function (): void {
                foreach (self::TABLES_TO_CLEAR as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)->delete();
                    }
                }

                if (Schema::hasTable('pelatihs')) {
                    DB::table('ekskuls')->update(['pelatih_id' => null]);
                    DB::table('pelatihs')->delete();
                }

                User::query()->where('role', '!=', 'pembina')->delete();

                DB::table('kelas')->update(['tahun_ajaran_id' => null]);
                DB::table('tahun_ajarans')->delete();

                foreach (self::TRANSIENT_TABLES as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)->delete();
                    }
                }
            });
        } catch (Throwable $exception) {
            $this->error('Gagal mereset data: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Data berhasil direset.');
        $this->line('Tersisa: akun pembina, data pembina, data kelas, dan data ekskul.');

        return self::SUCCESS;
    }
}
