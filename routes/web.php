<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\EkskulCatalogController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\Kesiswaan\DashboardController as KesiswaanDashboardController;
use App\Http\Controllers\Kesiswaan\EkskulController;
use App\Http\Controllers\Kesiswaan\KelasController;
use App\Http\Controllers\Kesiswaan\LaporanPenilaianController as KesiswaanLaporanPenilaianController;
use App\Http\Controllers\Kesiswaan\NotifikasiController as KesiswaanNotifikasiController;
use App\Http\Controllers\Kesiswaan\PelatihController as KesiswaanPelatihController;
use App\Http\Controllers\Kesiswaan\PembinaController as KesiswaanPembinaController;
use App\Http\Controllers\Kesiswaan\SiswaController;
use App\Http\Controllers\Kesiswaan\UserController;
use App\Http\Controllers\Ketua\DashboardController as KetuaDashboardController;
use App\Http\Controllers\Ketua\NotifikasiController as KetuaNotifikasiController;
use App\Http\Controllers\LaporanBulananController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Pembina\FaqController as PembinaFaqController;
use App\Http\Controllers\Pembina\NotifikasiController as PembinaNotifikasiController;
use App\Http\Controllers\Pembina\PelatihController as PembinaPelatihController;
use App\Http\Controllers\Pembina\PembinaController;
use App\Http\Controllers\Pembina\PenilaianController as PembinaPenilaianController;
use App\Http\Controllers\Pembina\TestimoniController as PembinaTestimoniController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PengajuanKeluarController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilEkskulController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\KatalogController;
use App\Http\Controllers\Siswa\NilaiController as SiswaNilaiController;
use App\Http\Controllers\Siswa\PendaftaranController as SiswaPendaftaranController;
use App\Http\Controllers\Siswa\PengajuanController;
use App\Http\Controllers\Siswa\PresensiController as SiswaPresensiController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;
use App\Http\Controllers\TestimoniController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/storage/{path}', function (string $path) {
    $storageRoot = realpath(Storage::disk('public')->path(''));
    $filePath = realpath(Storage::disk('public')->path($path));

    abort_unless(
        $storageRoot &&
        $filePath &&
        str_starts_with(
            strtolower($filePath),
            strtolower($storageRoot.DIRECTORY_SEPARATOR)
        ) &&
        is_file($filePath),
        404
    );

    return response()->file($filePath);
})->where('path', '.*')->name('storage.public');

Route::middleware(['auth', 'role:kesiswaan,admin'])->get('/maintenance/clear-cache', function () {
    $secret = env('MAINTENANCE_SECRET', '0192837465');

    abort_unless(hash_equals($secret, request('token', '')), 403, 'Token tidak valid.');

    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return response('Cache berhasil dibersihkan: '.implode(', ', [
        'config', 'cache', 'view', 'route',
    ]));
})->name('maintenance.clear-cache');

Route::get('/', [HomeController::class, 'landing'])->name('siswa.landing');

Route::get('/ekskul/{ekskul}', [EkskulCatalogController::class, 'show'])->name('ekskul.detail');

Route::middleware(['auth', 'throttle:5,1'])->group(function () {
    Route::post('/ekskul/{ekskul}/testimoni', [EkskulCatalogController::class, 'storeTestimoni'])->name('ekskul.testimoni.store');
    Route::post('/ekskul/{ekskul}/faq', [EkskulCatalogController::class, 'storeFaq'])->name('ekskul.faq.store');
});

Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware('auth')->name('dashboard');

// Profile (default untuk semua role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/onboarding/setup', [OnboardingController::class, 'setup'])->name('onboarding.setup');
    Route::post('/onboarding/login', [OnboardingController::class, 'returnToLogin'])->name('onboarding.login');
});

Route::middleware(['auth', 'role:kesiswaan,admin'])->prefix('kesiswaan')->name('kesiswaan.')->group(function () {
    Route::get('/dashboard', [KesiswaanDashboardController::class, 'index'])->name('dashboard');

    Route::get('users/import', [UserController::class, 'importPage'])->name('users.import-area');
    Route::get('users/template-siswa', [UserController::class, 'templateSiswa'])->name('users.template-siswa');
    Route::get('users/template-pembina', [UserController::class, 'templatePembina'])->name('users.template-pembina');
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');

    Route::resource('users', UserController::class)->except(['show']);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    Route::resource('ekskuls', EkskulController::class)->except([]);
    Route::post('pembina/{pembina}/assign-ekskul', [KesiswaanPembinaController::class, 'assignEkskul'])->name('pembina.assign-ekskul');
    Route::delete('pembina/{pembina}/remove-ekskul/{ekskul}', [KesiswaanPembinaController::class, 'removeEkskul'])->name('pembina.remove-ekskul');

    Route::resource('kelas', KelasController::class)->parameters([
        'kelas' => 'kela',
    ]);

    Route::get('siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('siswa/{siswa}/riwayat-profil', [SiswaController::class, 'riwayatProfil'])->name('siswa.riwayat-profil');

    Route::get('pembina', [KesiswaanPembinaController::class, 'index'])->name('pembina.index');
    Route::get('pembina/{pembina}/riwayat-profil', [KesiswaanPembinaController::class, 'riwayatProfil'])->name('pembina.riwayat-profil');

    Route::get('pelatih', [KesiswaanPelatihController::class, 'index'])->name('pelatih.index');
    Route::post('pelatih/{pelatih}/verifikasi', [KesiswaanPelatihController::class, 'verifikasi'])->name('pelatih.verifikasi');
    Route::post('pelatih/{pelatih}/tolak', [KesiswaanPelatihController::class, 'tolak'])->name('pelatih.tolak');

    Route::get('/profile', function () {
        return view('kesiswaan.profile', ['user' => auth()->user()]);
    })->name('profile');

    Route::get('/notifikasi', [KesiswaanNotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notifikasi}/read', [KesiswaanNotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [KesiswaanNotifikasiController::class, 'readAll'])->name('notifikasi.read-all');

    Route::get('/laporan-penilaian', [KesiswaanLaporanPenilaianController::class, 'index'])->name('laporan-penilaian.index');
    Route::get('/laporan-penilaian/{ekskul}', [KesiswaanLaporanPenilaianController::class, 'show'])->name('laporan-penilaian.show');
    Route::get('/laporan-penilaian/{ekskul}/download-pdf', [KesiswaanLaporanPenilaianController::class, 'downloadPdf'])->name('laporan-penilaian.download-pdf');
});

// ============================================================
// ROUTE SISWA
// ============================================================
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

    // 2. KATALOG EKSKUL
    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');

    // 3. PRESENSI & KEGIATAN
    Route::get('/presensi', [SiswaPresensiController::class, 'index'])->name('presensi');

    // 3b. REKAP ABSENSI PER BULAN
    Route::get('/rekap-absensi', [SiswaPresensiController::class, 'rekap'])->name('rekap');

    // 4. PROFILE SISWA
    Route::get('/profile', [SiswaProfilController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update-data', [SiswaProfilController::class, 'update'])->name('profile.update-data');

    // 5. HALAMAN DAFTAR EKSKUL (LIST CARD)
    Route::get('/daftar-ekskul', [SiswaPendaftaranController::class, 'daftar'])->name('daftar-ekskul');

    // 6. FORM DAFTAR EKSKUL (HALAMAN FORM)
    Route::get('/form-daftar/{ekskul}', [SiswaPendaftaranController::class, 'formDaftar'])->name('form-daftar');

    // 7. PROSES DAFTAR EKSKUL (STORE)
    Route::post('/daftar-ekskul', [SiswaPendaftaranController::class, 'store'])->name('daftar-ekskul.store');

    // 7b. NOTIFIKASI
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notifikasi}/read', [NotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'readAll'])->name('notifikasi.read-all');

    // 8. PENGAJUAN KELUAR
    Route::get('/pengajuan-keluar', [PengajuanController::class, 'index'])->name('pengajuan-keluar');
    Route::post('/pengajuan-keluar', [PengajuanController::class, 'store'])->name('pengajuan-keluar.store');

    // 9. NILAI EKSKUL
    Route::get('/nilai', [SiswaNilaiController::class, 'index'])->name('nilai');
});

// ============================================================
// ROUTE PEMBINA
// ============================================================
Route::middleware(['auth', 'role:pembina'])->prefix('pembina')->name('pembina.')->group(function () {
    Route::get('/dashboard', [PembinaController::class, 'dashboard'])->name('dashboard');
    Route::patch('/ekskuls/{ekskul}/pelatih', [PembinaController::class, 'updatePelatih'])->name('ekskul.pelatih');
    Route::get('/pelatih', [PembinaPelatihController::class, 'index'])->name('pelatih.index');
    Route::get('/pelatih/create', [PembinaPelatihController::class, 'create'])->name('pelatih.create');
    Route::post('/pelatih', [PembinaPelatihController::class, 'store'])->name('pelatih.store');
    Route::get('/anggota', [PembinaController::class, 'anggota'])->name('anggota');
    Route::get('/anggota/ekskul/{ekskul}', [PembinaController::class, 'anggotaByEkskul'])->name('anggota.ekskul');
    Route::get('/anggota/{pendaftaran}', [PembinaController::class, 'anggotaShow'])->name('anggota.show');
    Route::post('/ekskuls/{ekskul}/pilih-ketua/{siswa}', [PembinaController::class, 'pilihKetua'])->name('ekskul.pilih-ketua');
    Route::post('/ekskuls/{ekskul}/copot-ketua/{siswa}', [PembinaController::class, 'copotKetua'])->name('ekskul.copot-ketua');
    Route::patch('/arsip/periode-ketua/{riwayat}', [PembinaController::class, 'updatePeriodeKetua'])->name('arsip.periode-ketua');
    Route::get('/pendaftaran', [PembinaController::class, 'pendaftaran'])->name('pendaftaran');
    Route::get('/laporan', [PembinaController::class, 'laporan'])->name('laporan.index');
    Route::get('/laporan/{laporanBulanan}/detail', [PembinaController::class, 'laporanShow'])->name('laporan.show');
    Route::get('/laporan/{laporanBulanan}/download', [PembinaController::class, 'laporanDownload'])->name('laporan.download');
    Route::post('/laporan/{laporanBulanan}/approve', [PembinaController::class, 'laporanApprove'])->name('laporan.approve');
    Route::post('/laporan/{laporanBulanan}/reject', [PembinaController::class, 'laporanReject'])->name('laporan.reject');
    Route::get('/presensi', [PembinaController::class, 'presensi'])->name('presensi');
    Route::get('/rekap-absensi', [PembinaController::class, 'rekap'])->name('rekap');
    Route::get('/rekap-absensi/download', [PembinaController::class, 'rekapPdf'])->name('rekap.download');
    Route::get('/testimoni', [PembinaTestimoniController::class, 'index'])->name('testimoni.index');
    Route::post('/testimoni', [PembinaTestimoniController::class, 'store'])->name('testimoni.store');
    Route::patch('/testimoni/{testimoni}/approve', [PembinaTestimoniController::class, 'approve'])->name('testimoni.approve');
    Route::patch('/testimoni/{testimoni}/reject', [PembinaTestimoniController::class, 'reject'])->name('testimoni.reject');
    Route::delete('/testimoni/{testimoni}', [PembinaTestimoniController::class, 'destroy'])->name('testimoni.destroy');
    Route::get('/faq', [PembinaFaqController::class, 'index'])->name('faq.index');
    Route::post('/faq', [PembinaFaqController::class, 'store'])->name('faq.store');
    Route::patch('/faq/{faq}/answer', [PembinaFaqController::class, 'answer'])->name('faq.answer');
    Route::delete('/faq/{faq}', [PembinaFaqController::class, 'destroy'])->name('faq.destroy');
    Route::get('/profile', [PembinaController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [PembinaController::class, 'updateProfile'])->name('profile.update');

    Route::get('/penilaian', [PembinaPenilaianController::class, 'index'])->name('penilaian');
    Route::post('/penilaian', [PembinaPenilaianController::class, 'store'])->name('penilaian.store');
    Route::post('/penilaian/kirim', [PembinaPenilaianController::class, 'kirim'])->name('penilaian.kirim');
    Route::get('/penilaian/download-pdf', [PembinaPenilaianController::class, 'downloadPdf'])->name('penilaian.download-pdf');

    Route::get('/notifikasi', [PembinaNotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notifikasi}/read', [PembinaNotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [PembinaNotifikasiController::class, 'readAll'])->name('notifikasi.read-all');
});

// ============================================================
// ROUTE KETUA (hanya siswa dengan jabatan 'ketua')
// ============================================================
Route::middleware(['auth', 'role:siswa', 'ketua_ekskul'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/dashboard', [KetuaDashboardController::class, 'index'])->name('dashboard');

    Route::get('/nilai', [SiswaNilaiController::class, 'index'])->name('nilai');

    Route::resource('kegiatan', KegiatanController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('kegiatan/{kegiatan}/presensi', [PresensiController::class, 'create'])->name('presensi.create');
    Route::post('kegiatan/{kegiatan}/presensi', [PresensiController::class, 'store'])->name('presensi.store');
    Route::get('rekap-absensi/print', [PresensiController::class, 'rekapPdf'])->name('presensi.rekap-pdf');
    Route::get('rekap-absensi', [PresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::resource('pendaftaran', PendaftaranController::class)->only(['index', 'show', 'update']);
    Route::resource('pengajuan-keluar', PengajuanKeluarController::class)->only(['index', 'show', 'update']);
    Route::get('anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::patch('anggota/{pendaftaran}/status', [AnggotaController::class, 'updateStatus'])->name('anggota.update-status');
    Route::get('profil-ekskul', [ProfilEkskulController::class, 'edit'])->name('profil-ekskul.edit');
    Route::patch('profil-ekskul', [ProfilEkskulController::class, 'update'])->name('profil-ekskul.update');
    Route::patch('profil-ekskul/toggle-recruitment', [ProfilEkskulController::class, 'toggleRecruitment'])->name('profil-ekskul.toggle-recruitment');
    Route::post('profil-ekskul/galeri', [ProfilEkskulController::class, 'storeGaleri'])->name('profil-ekskul.galeri-store');
    Route::delete('profil-ekskul/galeri/{galeri}', [ProfilEkskulController::class, 'destroyGaleri'])->name('profil-ekskul.galeri-destroy');
    Route::get('prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');
    Route::post('prestasi', [PrestasiController::class, 'store'])->name('prestasi.store');
    Route::delete('prestasi/{prestasi}', [PrestasiController::class, 'destroy'])->name('prestasi.destroy');
    Route::get('testimoni', [TestimoniController::class, 'index'])->name('testimoni.index');
    Route::post('testimoni', [TestimoniController::class, 'store'])->name('testimoni.store');
    Route::patch('testimoni/{testimoni}/approve', [TestimoniController::class, 'approve'])->name('testimoni.approve');
    Route::patch('testimoni/{testimoni}/reject', [TestimoniController::class, 'reject'])->name('testimoni.reject');
    Route::delete('testimoni/{testimoni}', [TestimoniController::class, 'destroy'])->name('testimoni.destroy');
    Route::get('faq', [FaqController::class, 'index'])->name('faq.index');
    Route::post('faq', [FaqController::class, 'store'])->name('faq.store');
    Route::patch('faq/{faq}/answer', [FaqController::class, 'answer'])->name('faq.answer');
    Route::delete('faq/{faq}', [FaqController::class, 'destroy'])->name('faq.destroy');
    Route::resource('laporan-bulanan', LaporanBulananController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::get('laporan-bulanan/{laporan_bulanan}/download-pdf', [LaporanBulananController::class, 'downloadPdf'])->name('laporan-bulanan.download-pdf');
    Route::post('laporan-bulanan/{laporan_bulanan}/serahkan', [LaporanBulananController::class, 'submitToPembina'])->name('laporan-bulanan.submit');

    // Notifikasi Ketua
    Route::get('/notifikasi', [KetuaNotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notifikasi}/read', [KetuaNotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [KetuaNotifikasiController::class, 'readAll'])->name('notifikasi.read-all');
});

require __DIR__.'/auth.php';
