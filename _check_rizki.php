<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pendaftaran;
use App\Models\Siswa;

$rizki = Siswa::where('nis', '2406510001')->first();
if ($rizki) {
    echo "Siswa: {$rizki->nama} (id: {$rizki->id})" . PHP_EOL;
    foreach ($rizki->pendaftarans as $p) {
        echo "  Pendaftaran id: {$p->id} | Ekskul: {$p->ekskul->nama_ekskul} | Status: {$p->status} | Pembina: " . ($p->ekskul->pembina->nama ?? 'NULL') . PHP_EOL;
    }
}

// Also check pendaftaran yang mungkin tertukar
echo "\n--- Semua pendaftaran diterima untuk Paskibra ---" . PHP_EOL;
$paskibraDiterima = Pendaftaran::where('status', 'diterima')->whereHas('ekskul', fn($q) => $q->where('nama_ekskul', 'Paskibra'))->with('siswa')->get();
foreach ($paskibraDiterima as $p) {
    echo "  {$p->siswa->nama} ({$p->siswa->jabatan}) - Status: {$p->status}" . PHP_EOL;
}

echo "\n--- Semua pendaftaran diterima untuk Karate ---" . PHP_EOL;
$karateDiterima = Pendaftaran::where('status', 'diterima')->whereHas('ekskul', fn($q) => $q->where('nama_ekskul', 'Karate'))->with('siswa')->get();
foreach ($karateDiterima as $p) {
    echo "  {$p->siswa->nama} ({$p->siswa->jabatan}) - Status: {$p->status}" . PHP_EOL;
}