<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PengajuanKeluarController;
use App\Http\Controllers\LaporanBulananController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'siswa' && $user->siswa && $user->siswa->jabatan === 'ketua') {
        return redirect()->route('ketua.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============ KETUA EKSKUL ============
Route::middleware(['auth', 'role:siswa', 'ketua_ekskul'])->prefix('ketua')->name('ketua.')->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();
        $siswa = $user->siswa;
        $pendaftaran = $siswa->pendaftarans()->where('status', 'diterima')->first();

        if (!$pendaftaran) {
            return view('ketua.dashboard', [
                'ekskul'         => null,
                'totalAnggota'   => 0,
                'pendingCount'   => 0,
                'pengajuanCount' => 0,
            ]);
        }

        $ekskul = $pendaftaran->ekskul;

        return view('ketua.dashboard', [
            'ekskul'         => $ekskul,
            'totalAnggota'   => $ekskul->pendaftarans()->where('status', 'diterima')->count(),
            'pendingCount'   => $ekskul->pendaftarans()->where('status', 'pending')->count(),
            'pengajuanCount' => $ekskul->pengajuanKeluars()->where('status', 'pending')->count(),
        ]);
    })->name('dashboard');

    Route::resource('kegiatan', KegiatanController::class)->except(['edit', 'update', 'destroy']);
    Route::get('kegiatan/{kegiatan}/presensi', [PresensiController::class, 'create'])->name('presensi.create');
    Route::post('kegiatan/{kegiatan}/presensi', [PresensiController::class, 'store'])->name('presensi.store');
    Route::resource('pendaftaran', PendaftaranController::class)->only(['index', 'show', 'update']);
    Route::resource('pengajuan-keluar', PengajuanKeluarController::class)->only(['index', 'show', 'update']);
    Route::resource('laporan-bulanan', LaporanBulananController::class)->only(['index', 'create', 'store', 'show']);
});

require __DIR__.'/auth.php';
