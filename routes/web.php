<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;

Route::get('/', [SiswaController::class, 'index'])->name('siswa.landing');