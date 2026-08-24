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

// 1. LANDING PAGE
Route::get('/', function () {
    $ekskuls = Ekskul::with('pembina')->get();
    return view('welcome', compact('ekskuls'));
})->name('siswa.landing');

// 2. REDIRECTOR DASHBOARD UTAMA
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

// 3. PROFILE USER
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 4. ROUTE ROLE: KESISWAAN & ADMIN
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

// 5. ROUTE ROLE: SISWA (ANGGOTA REGULER)
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', function () {
        $user  = auth()->user();
        $siswa = $user->siswa;

        // 1. Ambil pendaftaran ekskul yang statusnya diterima
        $pendaftaran = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->with('ekskul')->first() : null;
        $ekskul      = $pendaftaran ? $pendaftaran->ekskul : null;

        // 2. Ambil kegiatan mendatang khusus ekskul siswa ini
        $kegiatanMendatang = $ekskul 
            ? $ekskul->kegiatans()->where('tanggal', '>=', now())->orderBy('tanggal', 'asc')->get() 
            : collect();

        // 3. Ambil total kehadiran siswa
        $totalHadir = $siswa ? $siswa->presensis()->where('status', 'hadir')->count() : 0;

        return view('siswa.dashboard', compact('siswa', 'ekskul', 'kegiatanMendatang', 'totalHadir'));
    })->name('dashboard');
});

// 6. ROUTE ROLE: PEMBINA
Route::middleware(['auth', 'role:pembina'])->prefix('pembina')->name('pembina.')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $pembina = $user->pembina;
        return view('pembina.dashboard', compact('pembina'));
    })->name('dashboard');
});

// 7. ROUTE ROLE: KETUA EKSKUL (SISWA DENGAN JABATAN KETUA)
Route::middleware(['auth', 'role:siswa', 'ketua_ekskul'])->prefix('ketua')->name('ketua.')->group(function () {
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