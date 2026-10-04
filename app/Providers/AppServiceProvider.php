<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL; // 1. Tambahkan import ini
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. Paksa semua URL asset() dan route() menggunakan HTTPS saat pakai tunnel
        if (request()->hasHeader('x-forwarded-proto') || env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        \Carbon\Carbon::setLocale(config('app.locale', 'id'));

        // Auto-migrate: jalankan migration tertunda dari request web pertama
        // agar update via upload file di DirectAdmin langsung aktif tanpa terminal.
        if (filter_var(config('app.auto_migrate'), FILTER_VALIDATE_BOOL)
            && ! $this->app->runningInConsole()) {
            $this->runPendingMigrations();
        }

        // Data bersama untuk seluruh view pembina (pembina + unread notifikasi)
        View::composer('pembina.*', function (\Illuminate\View\View $view) {
            $pembina = auth()->user()?->pembina;
            $unreadNotifCount = $pembina ? $pembina->notifikasis()->where('is_read', false)->count() : 0;
            $view->with('pembina', $pembina)->with('unreadNotifCount', $unreadNotifCount);
        });

        // Data bersama untuk seluruh view ketua (unread notifikasi ketua)
        View::composer('ketua.*', function (\Illuminate\View\View $view) {
            $siswa = auth()->user()?->siswa;
            $ekskul = $siswa?->pendaftarans()->where('status', 'diterima')->first()?->ekskul;
            $unreadNotifCount = 0;
            if ($ekskul) {
                $unreadNotifCount = \App\Models\Notifikasi::whereHas('pendaftaran', function ($q) use ($ekskul) {
                    $q->where('ekskul_id', $ekskul->id);
                })->where('is_read', false)->count();
                $unreadNotifCount += \App\Models\Notifikasi::whereHas('pengajuanKeluar', function ($q) use ($ekskul) {
                    $q->where('ekskul_id', $ekskul->id);
                })->where('is_read', false)->count();
                $unreadNotifCount += \App\Models\Notifikasi::whereHas('laporanBulanan', function ($q) use ($ekskul) {
                    $q->where('ekskul_id', $ekskul->id);
                })->where('is_read', false)->count();
            }
            $view->with('unreadNotifCount', $unreadNotifCount);
        });

        // Data bersama untuk seluruh view kesiswaan (unread notifikasi kesiswaan)
        View::composer('kesiswaan.*', function (\Illuminate\View\View $view) {
            $user = auth()->user();
            $unreadNotifCount = $user ? \App\Models\Notifikasi::where('user_id', $user->id)->where('is_read', false)->count() : 0;
            $view->with('unreadNotifCount', $unreadNotifCount);
        });
    }

    private function runPendingMigrations(): void
    {
        $migrator = $this->app['migrator'];
        $migrator->setConnection($this->app['db']->getDefaultConnection());
        $repository = $migrator->getRepository();
        $files = $migrator->getMigrationFiles([$this->app->databasePath('migrations')]);

        if ($repository->repositoryExists()
            && array_diff(array_keys($files), $repository->getRan()) === []) {
            return;
        }

        $lock = @fopen(storage_path('framework/auto-migrate.lock'), 'c');

        if ($lock === false) {
            throw new \RuntimeException('Unable to open the automatic migration lock file.');
        }

        try {
            if (! flock($lock, LOCK_EX)) {
                throw new \RuntimeException('Unable to acquire the automatic migration lock.');
            }

            if (! $repository->repositoryExists()) {
                $repository->createRepository();
            }

            $ran = $repository->getRan();
            $pending = array_diff(array_keys($files), $ran);

            if ($pending !== []) {
                $exitCode = Artisan::call('migrate', ['--force' => true]);

                if ($exitCode !== 0) {
                    $output = trim(Artisan::output());
                    throw new \RuntimeException(
                        'Automatic database migration failed.'
                        .($output !== '' ? "\n".$output : ''),
                    );
                }
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
