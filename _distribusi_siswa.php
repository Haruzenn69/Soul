<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Siswa;
use App\Models\Pendaftaran;
use App\Models\Ekskul;
use Carbon\Carbon;

echo "=== MULAI DISTRIBUSI ===" . PHP_EOL . PHP_EOL;

// 1. Fix Andi Wijaya: pending Karate → diterima Karate
$andi = Siswa::where('nis', '2406510004')->first();
if ($andi) {
    $pendaftaran = $andi->pendaftarans()->where('status', 'pending')->first();
    if ($pendaftaran) {
        $pendaftaran->update(['status' => 'diterima']);
        echo "✅ Andi Wijaya: pending → diterima (Karate)" . PHP_EOL;
    }
}

// 2. Hapus duplikat Nazwa (NIS 240651000 dan 24065100) - keduanya invalid NIS
// Mungkin dari SiswaEkskulSeeder yang tidak create Siswa record? Cek user mereka
$duplikat = Siswa::whereIn('nis', ['240651000', '24065100'])->get();
foreach ($duplikat as $d) {
    echo "🗑️ Hapus duplikat: {$d->nama} (NIS: {$d->nis})" . PHP_EOL;
    $d->pendaftarans()->delete();
    $d->delete();
}

// 3. Ambil siswa yang belum punya pendaftaran diterima
$siswaTanpaEkskul = Siswa::whereDoesntHave('pendaftarans', function($q) {
    $q->where('status', 'diterima');
})->get();

echo "\nSiswa tanpa ekskul diterima: " . $siswaTanpaEkskul->count() . PHP_EOL;
foreach ($siswaTanpaEkskul as $s) {
    echo "  - {$s->nama} (NIS: {$s->nis})" . PHP_EOL;
}

// 4. Distribusi ke ekskul yang perlu anggota
// Karate: butuh lebih banyak (hanya 2)
// Paskibra: sudah 4 (2 ketua + 2 anggota)
// FUTSAL: 0, butuh diisi

$karate = Ekskul::where('nama_ekskul', 'Karate')->first();
$paskibra = Ekskul::where('nama_ekskul', 'Paskibra')->first();
$futsal = Ekskul::where('nama_ekskul', 'FUTSAL')->first();

$targetKarate = 4; // ingin 4 anggota
$targetPaskibra = 4; // sudah 4, biarkan
$targetFutsal = 2; // ingin 2 anggota

$currentKarate = $karate->pendaftarans()->where('status', 'diterima')->count();
$currentFutsal = $futsal->pendaftarans()->where('status', 'diterima')->count();

echo "\nTarget Karate: {$targetKarate} (skrg: {$currentKarate})" . PHP_EOL;
echo "Target Futsal: {$targetFutsal} (skrg: {$currentFutsal})" . PHP_EOL;

// Distribusi
$assignedKarate = 0;
$assignedFutsal = 0;

foreach ($siswaTanpaEkskul as $s) {
    // Cek apakah sudah punya pending di mana
    $pending = $s->pendaftarans()->where('status', 'pending')->first();
    if ($pending) {
        // Hapus pending lama
        $pending->delete();
    }
    
    if ($assignedKarate < ($targetKarate - $currentKarate)) {
        // Masukkan ke Karate
        Pendaftaran::create([
            'siswa_id' => $s->id,
            'ekskul_id' => $karate->id,
            'tanggal_daftar' => Carbon::now()->subDays(rand(1, 5))->toDateString(),
            'status' => 'diterima',
            'alasan' => 'Dimasukkan otomatis untuk testing distribusi rata.',
        ]);
        echo "➡️ {$s->nama} → Karate (diterima)" . PHP_EOL;
        $assignedKarate++;
    } elseif ($assignedFutsal < ($targetFutsal - $currentFutsal)) {
        // Masukkan ke Futsal
        Pendaftaran::create([
            'siswa_id' => $s->id,
            'ekskul_id' => $futsal->id,
            'tanggal_daftar' => Carbon::now()->subDays(rand(1, 5))->toDateString(),
            'status' => 'diterima',
            'alasan' => 'Dimasukkan otomatis untuk testing distribusi rata.',
        ]);
        echo "➡️ {$s->nama} → FUTSAL (diterima)" . PHP_EOL;
        $assignedFutsal++;
    } else {
        // Default ke Karate
        Pendaftaran::create([
            'siswa_id' => $s->id,
            'ekskul_id' => $karate->id,
            'tanggal_daftar' => Carbon::now()->subDays(rand(1, 5))->toDateString(),
            'status' => 'diterima',
            'alasan' => 'Dimasukkan otomatis untuk testing distribusi rata.',
        ]);
        echo "➡️ {$s->nama} → Karate (diterima, default)" . PHP_EOL;
    }
}

echo "\n=== SELESAI ===" . PHP_EOL;