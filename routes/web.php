<?php

use App\Http\Controllers\Kesiswaan\EkskulController;
use App\Http\Controllers\Kesiswaan\KelasController;
use App\Http\Controllers\Kesiswaan\UserController;
use App\Http\Controllers\ProfileController;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $ekskuls = \App\Models\Ekskul::with('pembina')->get();
    return view('welcome', compact('ekskuls'));
})->name('siswa.landing');

// Redirect otomatis ke dashboard sesuai role setelah login
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'kesiswaan' || $user->role === 'admin') {
        return redirect()->route('kesiswaan.dashboard');
    }

    if ($user->role === 'pembina') {
        return redirect()->route('pembina.dashboard');
    }

    // Siswa & ketua: role sama-sama "siswa", beda hanya jabatan
    if ($user->siswa && $user->siswa->jabatan === 'ketua') {
        return redirect()->route('ketua.dashboard');
    }

    return redirect()->route('siswa.dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/siswa/dashboard', function () {
    return view('siswa.dashboard');
})->middleware(['auth', 'role:siswa'])->name('siswa.dashboard');

Route::get('/pembina/dashboard', function () {
    return view('pembina.dashboard');
})->middleware(['auth', 'role:pembina'])->name('pembina.dashboard');

Route::middleware(['auth', 'role:kesiswaan,admin'])->prefix('kesiswaan')->name('kesiswaan.')->group(function () {

    Route::get('/dashboard', function () {
        return view('kesiswaan.dashboard', [
            'totalUsers' => User::count(),
            'totalSiswa' => Siswa::count(),
            'totalEkskul' => Ekskul::count(),
            'ekskulBuka' => Ekskul::where('is_open_recruitment', true)->count(),
            'totalKelas' => Kelas::count(),
        ]);
    })->name('dashboard');

    // CRUD Akun Pengguna
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    // CRUD Ekskul
    Route::resource('ekskuls', EkskulController::class)->except(['show']);

    // CRUD Kelas
    Route::resource('kelas', KelasController::class)->except(['show'])->parameters([
        'kelas' => 'kela',
    ]);
});

Route::middleware(['auth', 'role:siswa', 'ketua_ekskul'])->prefix('ketua')->name('ketua.')->group(function () {

    Route::get('/dashboard', function () {
        return view('ketua.dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
