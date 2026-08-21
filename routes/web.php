<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $ekskuls = \App\Models\Ekskul::with('pembina')->get();
    return view('welcome', compact('ekskuls'));
})->name('siswa.landing');

Route::get('/siswa/dashboard', function () {
    return view('siswa.dashboard');
})->name('siswa.dashboard');

Route::get('/pembina/dashboard', function () {
    return view('pembina.dashboard');
})->name('pembina.dashboard');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'siswa' && $user->siswa && $user->siswa->jabatan === 'ketua') {
        return redirect()->route('ketua.dashboard');
    }

    return view('ketua.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:siswa', 'ketua_ekskul'])->prefix('ketua')->name('ketua.')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

require __DIR__.'/auth.php';