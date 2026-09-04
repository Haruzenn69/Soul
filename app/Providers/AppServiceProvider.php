<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // 1. Tambahkan import ini

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
    }
}