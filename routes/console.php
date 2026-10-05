<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('school:rollover-academic-year')
    ->yearlyOn(7, 1, '00:05')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
