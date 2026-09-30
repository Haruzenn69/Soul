<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\Ketua\DashboardController as KetuaDashboardController;
use App\Http\Controllers\Api\Ketua\KegiatanController as KetuaKegiatanController;
use App\Http\Controllers\Api\Ketua\LaporanBulananController as KetuaLaporanBulananController;
use App\Http\Controllers\Api\Ketua\MembershipController as KetuaMembershipController;
use App\Http\Controllers\Api\Ketua\NotifikasiController as KetuaNotifikasiController;
use App\Http\Controllers\Api\Ketua\ProfilEkskulController as KetuaProfilEkskulController;
use App\Http\Controllers\Api\Siswa\SiswaController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/catalog', [CatalogController::class, 'index']);
Route::get('/catalog/{ekskul}', [CatalogController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::post('/catalog/{ekskul}/testimoni', [CatalogController::class, 'storeTestimoni'])
        ->middleware('throttle:5,1');
    Route::post('/catalog/{ekskul}/faq', [CatalogController::class, 'storeFaq'])
        ->middleware('throttle:5,1');

    // ============================================================
    // API SISWA
    // ============================================================
    Route::prefix('siswa')->middleware('role:siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/katalog', [SiswaController::class, 'katalog'])->name('katalog');
        Route::get('/daftar-exkul', [SiswaController::class, 'daftar'])->name('daftar');
        Route::post('/daftar-ekskul', [SiswaController::class, 'storeDaftar'])->name('daftar.store');
        Route::get('/presensi', [SiswaController::class, 'presensi'])->name('presensi');
        Route::get('/rekap', [SiswaController::class, 'rekap'])->name('rekap');
        Route::get('/nilai', [SiswaController::class, 'nilai'])->name('nilai');
        Route::get('/pengajuan-keluar', [SiswaController::class, 'pengajuanIndex'])->name('pengajuan.index');
        Route::post('/pengajuan-keluar', [SiswaController::class, 'storePengajuan'])->name('pengajuan.store');
        Route::get('/notifikasi', [SiswaController::class, 'notifikasiIndex'])->name('notifikasi');
        Route::post('/notifikasi/{notifikasi}/read', [SiswaController::class, 'bacaNotifikasi'])->name('notifikasi.read');
        Route::post('/notifikasi/read-all', [SiswaController::class, 'bacaSemuaNotifikasi'])->name('notifikasi.read-all');
        Route::get('/profile', [SiswaController::class, 'profil'])->name('profile');
        Route::post('/profile', [SiswaController::class, 'updateProfil'])->name('profile.update');
        Route::get('/kelas', [SiswaController::class, 'kelas'])->name('kelas');
        Route::post('/onboarding/complete', [SiswaController::class, 'onboardingComplete'])->name('onboarding.complete');
    });

    // ============================================================
    // API KETUA EKSKUL (siswa dengan jabatan ketua)
    // ============================================================
    Route::prefix('ketua')->middleware(['role:siswa', 'ketua_ekskul'])->name('ketua.')->group(function () {
        Route::get('/dashboard', [KetuaDashboardController::class, 'index'])->name('dashboard');

        Route::get('/kegiatan', [KetuaKegiatanController::class, 'index'])->name('kegiatan.index');
        Route::post('/kegiatan', [KetuaKegiatanController::class, 'store'])->name('kegiatan.store');
        Route::get('/kegiatan/{kegiatan}', [KetuaKegiatanController::class, 'show'])->name('kegiatan.show');
        Route::post('/kegiatan/{kegiatan}/update', [KetuaKegiatanController::class, 'update'])->name('kegiatan.update');
        Route::delete('/kegiatan/{kegiatan}', [KetuaKegiatanController::class, 'destroy'])->name('kegiatan.destroy');
        Route::get('/kegiatan/{kegiatan}/presensi', [KetuaKegiatanController::class, 'presensiForm'])->name('kegiatan.presensi-form');
        Route::post('/kegiatan/{kegiatan}/presensi', [KetuaKegiatanController::class, 'storePresensi'])->name('kegiatan.presensi');
        Route::get('/rekap', [KetuaKegiatanController::class, 'rekap'])->name('rekap');

        Route::get('/anggota', [KetuaMembershipController::class, 'anggota'])->name('anggota');
        Route::post('/anggota/{pendaftaran}/status', [KetuaMembershipController::class, 'updateStatusAnggota'])->name('anggota.status');
        Route::get('/pendaftaran', [KetuaMembershipController::class, 'pendaftaran'])->name('pendaftaran');
        Route::post('/pendaftaran/{pendaftaran}', [KetuaMembershipController::class, 'prosesPendaftaran'])->name('pendaftaran.proses');
        Route::get('/pengajuan-keluar', [KetuaMembershipController::class, 'pengajuanKeluar'])->name('pengajuan-keluar');
        Route::post('/pengajuan-keluar/{pengajuanKeluar}', [KetuaMembershipController::class, 'prosesPengajuanKeluar'])->name('pengajuan-keluar.proses');

        Route::get('/laporan-bulanan', [KetuaLaporanBulananController::class, 'index'])->name('laporan-bulanan.index');
        Route::post('/laporan-bulanan', [KetuaLaporanBulananController::class, 'store'])->name('laporan-bulanan.store');
        Route::get('/laporan-bulanan/{laporan_bulanan}', [KetuaLaporanBulananController::class, 'show'])->name('laporan-bulanan.show');
        Route::post('/laporan-bulanan/{laporan_bulanan}/update', [KetuaLaporanBulananController::class, 'update'])->name('laporan-bulanan.update');
        Route::post('/laporan-bulanan/{laporan_bulanan}/serahkan', [KetuaLaporanBulananController::class, 'submitToPembina'])->name('laporan-bulanan.submit');

        Route::get('/profil-ekskul', [KetuaProfilEkskulController::class, 'show'])->name('profil-ekskul.show');
        Route::post('/profil-ekskul/update', [KetuaProfilEkskulController::class, 'update'])->name('profil-ekskul.update');
        Route::post('/profil-ekskul/toggle-recruitment', [KetuaProfilEkskulController::class, 'toggleRecruitment'])->name('profil-ekskul.toggle');
        Route::post('/profil-ekskul/galeri', [KetuaProfilEkskulController::class, 'storeGaleri'])->name('profil-ekskul.galeri');
        Route::delete('/profil-ekskul/galeri/{galeri}', [KetuaProfilEkskulController::class, 'destroyGaleri'])->name('profil-ekskul.galeri-destroy');
        Route::get('/prestasi', [KetuaProfilEkskulController::class, 'prestasi'])->name('prestasi');
        Route::post('/prestasi', [KetuaProfilEkskulController::class, 'storePrestasi'])->name('prestasi.store');
        Route::delete('/prestasi/{prestasi}', [KetuaProfilEkskulController::class, 'destroyPrestasi'])->name('prestasi.destroy');

        Route::get('/notifikasi', [KetuaNotifikasiController::class, 'index'])->name('notifikasi');
        Route::post('/notifikasi/{notifikasi}/read', [KetuaNotifikasiController::class, 'read'])->name('notifikasi.read');
        Route::post('/notifikasi/read-all', [KetuaNotifikasiController::class, 'readAll'])->name('notifikasi.read-all');
    });
});