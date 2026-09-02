<?php

use App\Http\Controllers\EkskulCatalogController;
use App\Http\Controllers\Kesiswaan\EkskulController;
use App\Http\Controllers\Kesiswaan\KelasController;
use App\Http\Controllers\Kesiswaan\UserController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PengajuanKeluarController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\ProfilEkskulController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\LaporanBulananController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Pembina\NotifikasiController as PembinaNotifikasiController;
use App\Http\Controllers\Pembina\PembinaController;
use App\Http\Controllers\Ketua\NotifikasiController as KetuaNotifikasiController;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', function () {
    $ekskuls = \App\Models\Ekskul::with('pembina')->get();
    return view('welcome', compact('ekskuls'));
})->name('siswa.landing');

Route::get('/ekskul/{ekskul}', [EkskulCatalogController::class, 'show'])->name('ekskul.detail');

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

// Profile (default untuk semua role)
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


// ============================================================
// ROUTE SISWA - LENGKAP
// ============================================================
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', function () {
        $user  = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->with('ekskul')->first() : null;
        $ekskul      = $pendaftaran ? $pendaftaran->ekskul : null;

        $kegiatanMendatang = $ekskul 
        ? $ekskul->kegiatans()->whereDate('tanggal_kegiatan', '>=', today())->orderBy('tanggal_kegiatan', 'asc')->get() 
        : collect();

        $totalHadir = $siswa && $pendaftaran 
            ? Presensi::where('pendaftaran_id', $pendaftaran->id)->where('status', 'hadir')->count() 
            : 0;

        $unreadNotifCount = $siswa ? $siswa->notifikasis()->where('is_read', false)->count() : 0;

        return view('siswa.dashboard', compact('siswa', 'ekskul', 'kegiatanMendatang', 'totalHadir', 'unreadNotifCount'));
    })->name('dashboard');

    // 2. KATALOG EKSKUL
    Route::get('/katalog', function () {
        $user = auth()->user();
        $siswa = $user->siswa;
        
        $isRegistered = false;
        $isPending = false;
        
        if ($siswa) {
            $pendaftaran = $siswa->pendaftarans()->where('status', 'diterima')->first();
            $isRegistered = $pendaftaran ? true : false;
            
            $pending = $siswa->pendaftarans()->where('status', 'pending')->first();
            $isPending = $pending ? true : false;
        }
        
        $ekskuls = Ekskul::with('pembina')->get();
        return view('siswa.katalog', compact('ekskuls', 'siswa', 'isRegistered', 'isPending'));
    })->name('katalog');

    // 3. PRESENSI & KEGIATAN
    Route::get('/presensi', function () {
        $user = auth()->user();
        $siswa = $user->siswa;
        
        $pendaftaran = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->first() : null;
        $presensis = $pendaftaran ? Presensi::where('pendaftaran_id', $pendaftaran->id)->with('kegiatan')->get() : collect();
        
        return view('siswa.presensi', compact('presensis', 'siswa'));
    })->name('presensi');

    // 4. PROFILE SISWA
    Route::get('/profile', function () {
        $user = auth()->user();
        $siswa = $user->siswa;
        
        $pendaftaran = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->with('ekskul.pembina')->first() : null;
        $ekskul = $pendaftaran ? $pendaftaran->ekskul : null;
        $pengajuan = $siswa ? $siswa->pengajuanKeluars()->latest('tanggal_pengajuan')->get() : collect();
        
        return view('profile.edit', compact('siswa', 'ekskul', 'pengajuan'));
    })->name('profile.edit');

    // 5. HALAMAN DAFTAR EKSKUL (LIST CARD)
    Route::get('/daftar-ekskul', function () {
        $user = auth()->user();
        $siswa = $user->siswa;
        
        $pendaftaran = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->first() : null;
        if ($pendaftaran) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah terdaftar di ekskul.');
        }
        
        $ekskuls = Ekskul::with('pembina')->get();
        return view('siswa.daftar-ekskul', compact('ekskuls', 'siswa'));
    })->name('daftar-ekskul');

    // 6. FORM DAFTAR EKSKUL (HALAMAN FORM)
    Route::get('/form-daftar/{ekskul}', function ($ekskul) {
        $user = auth()->user();
        $siswa = $user->siswa;

        // Cek apakah sudah terdaftar (diterima)
        $diterima = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->first() : null;
        if ($diterima) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah terdaftar di ekskul.');
        }

        // Cek apakah sudah mendaftar (pending)
        $pending = $siswa ? $siswa->pendaftarans()->where('status', 'pending')->first() : null;
        if ($pending) {
            return redirect()->route('siswa.dashboard')->with('error', 'Kamu sudah mengajukan pendaftaran. Tunggu verifikasi dari ketua ekskul.');
        }

        $ekskul = Ekskul::with('pembina')->findOrFail($ekskul);
        return view('siswa.form-daftar', compact('ekskul', 'siswa'));
    })->name('form-daftar');

    // 7. PROSES DAFTAR EKSKUL (STORE)
    Route::post('/daftar-ekskul', function (Request $request) {
        $user = auth()->user();
        $siswa = $user->siswa;
        
        $existing = $siswa->pendaftarans()->where('status', 'diterima')->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Kamu sudah terdaftar di ekskul.');
        }
        
        $pending = $siswa->pendaftarans()->where('status', 'pending')->first();
        if ($pending) {
            return redirect()->back()->with('error', 'Kamu sudah mengajukan pendaftaran. Tunggu verifikasi dari ketua ekskul.');
        }
        
        $pendaftaran = Pendaftaran::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $request->ekskul_id,
            'tanggal_daftar' => now()->toDateString(),
            'status' => 'pending',
            'alasan' => $request->alasan,
        ]);

        \App\Models\Notifikasi::create([
            'siswa_id' => $siswa->id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => 'Pendaftaran Terkirim',
            'pesan' => 'Pendaftaran kamu ke ekskul berhasil dikirim. Menunggu verifikasi dari ketua ekskul.',
            'tipe' => 'info',
        ]);

        $pembina = $pendaftaran->ekskul->pembina;
        if ($pembina) {
            \App\Models\Notifikasi::create([
                'pembina_id' => $pembina->id,
                'pendaftaran_id' => $pendaftaran->id,
                'judul' => 'Pendaftaran Baru Masuk',
                'pesan' => $siswa->nama . ' dari ' . ($siswa->kelas->nama ?? '-') . ' baru saja mendaftar ke ekskul ' . $pendaftaran->ekskul->nama_ekskul . '.',
                'tipe' => 'info',
            ]);
        }

        // Notifikasi untuk Ketua Ekskul
        $ketua = \App\Models\Siswa::where('jabatan', 'ketua')
            ->whereHas('pendaftarans', function ($q) use ($pendaftaran) {
                $q->where('ekskul_id', $pendaftaran->ekskul_id)
                  ->where('status', 'diterima');
            })
            ->first();
        if ($ketua) {
            \App\Models\Notifikasi::create([
                'siswa_id' => $ketua->id,
                'pendaftaran_id' => $pendaftaran->id,
                'judul' => 'Pendaftaran Baru Masuk',
                'pesan' => $siswa->nama . ' dari ' . ($siswa->kelas->nama ?? '-') . ' baru saja mendaftar ke ekskul ' . $pendaftaran->ekskul->nama_ekskul . '. Silakan verifikasi.',
                'tipe' => 'info',
            ]);
        }
        
        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran berhasil dikirim!');
    })->name('daftar-ekskul.store');

    // 7b. NOTIFIKASI
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notifikasi}/read', [NotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'readAll'])->name('notifikasi.read-all');

    // 8. STORE PENGAJUAN KELUAR
    Route::post('/pengajuan-keluar', function (Request $request) {
        $user = auth()->user();
        $siswa = $user->siswa;
        
        $pendaftaran = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->first() : null;
        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Kamu belum terdaftar di ekskul manapun.');
        }
        
        $pending = $siswa->pengajuanKeluars()->where('status', 'pending')->first();
        if ($pending) {
            return redirect()->back()->with('error', 'Kamu masih memiliki permohonan keluar yang berstatus pending.');
        }

        $validated = $request->validate([
            'alasan' => ['required', 'string', 'min:5'],
        ], [
            'alasan.required' => 'Alasan keluar wajib diisi.',
            'alasan.min' => 'Alasan keluar minimal harus 5 karakter.',
        ]);
        
        PengajuanKeluar::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $pendaftaran->ekskul_id,
            'alasan' => $validated['alasan'],
            'status' => 'pending',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        $pembina = $pendaftaran->ekskul->pembina;
        if ($pembina) {
            \App\Models\Notifikasi::create([
                'pembina_id' => $pembina->id,
                'judul' => 'Pengajuan Keluar Masuk',
                'pesan' => $siswa->nama . ' mengajukan keluar dari ekskul ' . $pendaftaran->ekskul->nama_ekskul . '. Menunggu keputusan ketua ekskul.',
                'tipe' => 'info',
            ]);
        }

        // Notifikasi untuk Ketua Ekskul
        $ketua = \App\Models\Siswa::where('jabatan', 'ketua')
            ->whereHas('pendaftarans', function ($q) use ($pendaftaran) {
                $q->where('ekskul_id', $pendaftaran->ekskul_id)
                  ->where('status', 'diterima');
            })
            ->first();
        if ($ketua) {
            \App\Models\Notifikasi::create([
                'siswa_id' => $ketua->id,
                'pengajuan_keluar_id' => $pengajuanKeluar->id,
                'judul' => 'Pengajuan Keluar Masuk',
                'pesan' => $siswa->nama . ' mengajukan keluar dari ekskul ' . $pendaftaran->ekskul->nama_ekskul . '. Silakan keputusan.',
                'tipe' => 'info',
            ]);
        }
        
        return redirect()->back()->with('success', 'Pengajuan keluar berhasil dikirim dan sedang menunggu keputusan ketua ekskul.');
    })->name('pengajuan-keluar.store');
});


// ============================================================
// ROUTE PEMBINA
// ============================================================
Route::middleware(['auth', 'role:pembina'])->prefix('pembina')->name('pembina.')->group(function () {
    Route::get('/dashboard', [PembinaController::class, 'dashboard'])->name('dashboard');
    Route::get('/anggota', [PembinaController::class, 'anggota'])->name('anggota');
    Route::get('/pendaftaran', [PembinaController::class, 'pendaftaran'])->name('pendaftaran');
    Route::get('/laporan', [PembinaController::class, 'laporan'])->name('laporan.index');
    Route::get('/laporan/{laporanBulanan}/detail', [PembinaController::class, 'laporanShow'])->name('laporan.show');
    Route::get('/laporan/{laporanBulanan}/download', [PembinaController::class, 'laporanDownload'])->name('laporan.download');
    Route::get('/presensi', [PembinaController::class, 'presensi'])->name('presensi');
    Route::get('/profile', [PembinaController::class, 'profile'])->name('profile');

    Route::get('/notifikasi', [PembinaNotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notifikasi}/read', [PembinaNotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [PembinaNotifikasiController::class, 'readAll'])->name('notifikasi.read-all');
});


// ============================================================
// ROUTE KETUA
// ============================================================
Route::middleware(['auth', 'role:siswa'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $siswa = $user->siswa;
        $pendaftaran = $siswa ? $siswa->pendaftarans()->where('status', 'diterima')->first() : null;

        if (!$pendaftaran || !$pendaftaran->ekskul) {
            return view('ketua.dashboard', [
                'ekskul'         => null,
                'totalAnggota'   => 0,
                'pendingCount'   => 0,
                'pengajuanCount' => 0,
                'chartKegiatan'  => ['labels' => [], 'hadir' => [], 'izin' => [], 'sakit' => [], 'alpha' => []],
                'chartKelas'     => ['labels' => ['Kelas X', 'Kelas XI', 'Kelas XII'], 'data' => [0, 0, 0]],
            ]);
        }

        $ekskul = $pendaftaran->ekskul;

        // 1. Data Line Chart: Tren Kehadiran Kegiatan Terakhir (Maks 6)
        $kegiatanTerbaru = $ekskul->kegiatans()
            ->with('presensis')
            ->orderBy('tanggal_kegiatan', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $chartKegiatan = [
            'labels' => $kegiatanTerbaru->map(function ($k) {
                return $k->tanggal_kegiatan ? $k->tanggal_kegiatan->translatedFormat('d M') : 'Kegiatan #'.$k->id;
            })->toArray(),
            'hadir' => $kegiatanTerbaru->map(fn($k) => $k->presensis->where('status', 'hadir')->count())->toArray(),
            'izin'  => $kegiatanTerbaru->map(fn($k) => $k->presensis->where('status', 'izin')->count())->toArray(),
            'sakit' => $kegiatanTerbaru->map(fn($k) => $k->presensis->where('status', 'sakit')->count())->toArray(),
            'alpha' => $kegiatanTerbaru->map(fn($k) => $k->presensis->where('status', 'alpha')->count())->toArray(),
        ];

        // 2. Data Doughnut Chart: Distribusi Anggota per Tingkat Kelas
        $anggotaAktif = $ekskul->pendaftarans()
            ->where('status', 'diterima')
            ->with('siswa.kelas')
            ->get();

        $countX = $anggotaAktif->filter(fn($p) => strtolower($p->siswa?->kelas?->tingkat ?? '') === 'x')->count();
        $countXI = $anggotaAktif->filter(fn($p) => strtolower($p->siswa?->kelas?->tingkat ?? '') === 'xi')->count();
        $countXII = $anggotaAktif->filter(fn($p) => strtolower($p->siswa?->kelas?->tingkat ?? '') === 'xii')->count();

        $chartKelas = [
            'labels' => ['Kelas X', 'Kelas XI', 'Kelas XII'],
            'data'   => [$countX, $countXI, $countXII],
        ];

        return view('ketua.dashboard', [
            'ekskul'         => $ekskul,
            'totalAnggota'   => $anggotaAktif->count(),
            'pendingCount'   => $ekskul->pendaftarans()->where('status', 'pending')->count(),
            'pengajuanCount' => $ekskul->pengajuanKeluars()->where('status', 'pending')->count(),
            'chartKegiatan'  => $chartKegiatan,
            'chartKelas'     => $chartKelas,
        ]);
    })->name('dashboard');

    Route::resource('kegiatan', KegiatanController::class)->except(['edit', 'update', 'destroy']);
    Route::get('kegiatan/{kegiatan}/presensi', [PresensiController::class, 'create'])->name('presensi.create');
    Route::post('kegiatan/{kegiatan}/presensi', [PresensiController::class, 'store'])->name('presensi.store');
    Route::resource('pendaftaran', PendaftaranController::class)->only(['index', 'show', 'update']);
    Route::resource('pengajuan-keluar', PengajuanKeluarController::class)->only(['index', 'show', 'update']);
    Route::get('anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::patch('anggota/{pendaftaran}/toggle', [AnggotaController::class, 'toggle'])->name('anggota.toggle');
    Route::get('profil-ekskul', [ProfilEkskulController::class, 'edit'])->name('profil-ekskul.edit');
    Route::patch('profil-ekskul', [ProfilEkskulController::class, 'update'])->name('profil-ekskul.update');
    Route::match(['get', 'patch'], 'profil-ekskul/toggle-recruitment', [ProfilEkskulController::class, 'toggleRecruitment'])->name('profil-ekskul.toggle-recruitment');
    Route::get('prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');
    Route::post('prestasi', [PrestasiController::class, 'store'])->name('prestasi.store');
    Route::delete('prestasi/{prestasi}', [PrestasiController::class, 'destroy'])->name('prestasi.destroy');
    Route::get('testimoni', [TestimoniController::class, 'index'])->name('testimoni.index');
    Route::post('testimoni', [TestimoniController::class, 'store'])->name('testimoni.store');
    Route::delete('testimoni/{testimoni}', [TestimoniController::class, 'destroy'])->name('testimoni.destroy');
    Route::get('faq', [FaqController::class, 'index'])->name('faq.index');
    Route::post('faq', [FaqController::class, 'store'])->name('faq.store');
    Route::delete('faq/{faq}', [FaqController::class, 'destroy'])->name('faq.destroy');
    Route::resource('laporan-bulanan', LaporanBulananController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('laporan-bulanan/{laporan_bulanan}/download-pdf', [LaporanBulananController::class, 'downloadPdf'])->name('laporan-bulanan.download-pdf');

    // Notifikasi Ketua
    Route::get('/notifikasi', [KetuaNotifikasiController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notifikasi}/read', [KetuaNotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [KetuaNotifikasiController::class, 'readAll'])->name('notifikasi.read-all');
});

require __DIR__.'/auth.php';