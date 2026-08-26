<?php

use App\Http\Controllers\Kesiswaan\EkskulController;
use App\Http\Controllers\Kesiswaan\KelasController;
use App\Http\Controllers\Kesiswaan\UserController;
use App\Http\Controllers\Ketua\KegiatanController;
use App\Http\Controllers\Ketua\LaporanBulananController;
use App\Http\Controllers\Ketua\PendaftaranController;
use App\Http\Controllers\Ketua\PengajuanKeluarController;
use App\Http\Controllers\Ketua\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $ekskuls = Ekskul::with('pembina')->get();
    return view('welcome', compact('ekskuls'));
})->name('siswa.landing');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'kesiswaan' || $user->role === 'admin') {
        return redirect()->route('kesiswaan.dashboard');
    }

    if ($user->role === 'pembina') {
        return redirect()->route('pembina.dashboard');
    }

    if ($user->siswa && $user->siswa->jabatan === 'ketua') {
        return redirect()->route('ketua.dashboard');
    }

    return redirect()->route('siswa.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:kesiswaan,admin'])->prefix('kesiswaan')->name('kesiswaan.')->group(function () {
    Route::get('/dashboard', function () {
        return view('kesiswaan.dashboard', [
            'totalUsers'  => User::count(),
            'totalSiswa'  => Siswa::count(),
            'totalEkskul' => Ekskul::count(),
            'ekskulBuka'  => Ekskul::where('is_open_recruitment', true)->count(),
            'totalKelas'  => Kelas::count(),
        ]);
    })->name('dashboard');

    Route::resource('users', UserController::class)->except(['show']);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    Route::resource('ekskuls', EkskulController::class)->except(['show']);

    Route::resource('kelas', KelasController::class)->except(['show'])->parameters([
        'kelas' => 'kela',
    ]);
});

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', function () {
        $user  = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->with('ekskul')->first() : null;
        $ekskul      = $pendaftaran ? $pendaftaran->ekskul : null;

        $kegiatanMendatang = $ekskul 
        ? $ekskul->kegiatans()->where('tanggal_kegiatan', '>=', now())->orderBy('tanggal_kegiatan', 'asc')->get() 
        : collect();

        // ✅ DIPERBAIKI: pakai pendaftaran_id
        $totalHadir = $siswa && $pendaftaran 
            ? Presensi::where('pendaftaran_id', $pendaftaran->id)->where('status', 'hadir')->count() 
            : 0;

        return view('siswa.dashboard', compact('siswa', 'ekskul', 'kegiatanMendatang', 'totalHadir'));
    })->name('dashboard');
});

Route::middleware(['auth', 'role:pembina'])->prefix('pembina')->name('pembina.')->group(function () {
    Route::get('/dashboard', function () {
        $user    = auth()->user();
        $pembina = $user->pembina;
        return view('pembina.dashboard', compact('pembina'));
    })->name('dashboard');
});

Route::middleware(['auth', 'role:siswa'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/dashboard', function () {
        return view('ketua.dashboard');
    })->name('dashboard');

    Route::resource('kegiatan', KegiatanController::class)->except(['edit', 'update', 'destroy']);
    Route::get('kegiatan/{kegiatan}/presensi', [PresensiController::class, 'create'])->name('presensi.create');
    Route::post('kegiatan/{kegiatan}/presensi', [PresensiController::class, 'store'])->name('presensi.store');
    Route::resource('pendaftaran', PendaftaranController::class)->only(['index', 'show', 'update']);
    Route::resource('pengajuan-keluar', PengajuanKeluarController::class)->only(['index', 'show', 'update']);
    Route::resource('laporan-bulanan', LaporanBulananController::class)->only(['index', 'create', 'store', 'show']);
});

require __DIR__.'/auth.php';