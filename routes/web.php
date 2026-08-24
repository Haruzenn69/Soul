<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PengajuanKeluarController;
use App\Http\Controllers\LaporanBulananController;
use Illuminate\Support\Facades\Route;

// 1. LANDING PAGE
Route::get('/', function () {
    $ekskuls = \App\Models\Ekskul::with(['pembina', 'pelatih'])->get();
    return view('welcome', compact('ekskuls'));
})->name('siswa.landing');

// 2. REDIRECTOR DASHBOARD UTAMA
// Meneruskan user ke dashboard yang sesuai berdasarkan role/jabatan saat mengakses /dashboard
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'siswa') {
        if ($user->siswa && $user->siswa->jabatan === 'ketua') {
            return redirect()->route('ketua.dashboard');
        }
        return redirect()->route('siswa.dashboard');
    }

    if ($user->role === 'pembina') {
        return redirect()->route('pembina.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. PROFILE USER
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 4. ROUTE ROLE: SISWA (ANGGOTA REGULER)
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

// 5. ROUTE ROLE: PEMBINA
Route::middleware(['auth', 'role:pembina'])->prefix('pembina')->name('pembina.')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $pembina = $user->pembina;
        return view('pembina.dashboard', compact('pembina'));
    })->name('dashboard');
});

// 6. ROUTE ROLE: KETUA EKSKUL (SISWA DENGAN JABATAN KETUA)
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