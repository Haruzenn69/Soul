<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Siswa;
use App\Models\Pendaftaran;

$rizki = Siswa::where('nis', '2406510001')->first();
if ($rizki) {
    echo "Rizki Pratama (id: {$rizki->id})" . PHP_EOL;
    echo "Semua pendaftaran:" . PHP_EOL;
    foreach ($rizki->pendaftarans as $p) {
        echo "  - Pendaftaran ID: {$p->id} | Ekskul: {$p->ekskul->nama_ekskul} | Status: {$p->status} | Ekskul ID: {$p->ekskul_id} | Pembina: " . ($p->ekskul->pembina->nama ?? 'NULL') . PHP_EOL;
    }
}

// Cek apakah ada pendaftaran Paskibra untuk Rizki
$rizkiPaskibra = Pendaftaran::where('siswa_id', $rizki->id)
    ->whereHas('ekskul', fn($q) => $q->where('nama_ekskul', 'Paskibra'))
    ->first();
if ($rizkiPaskibra) {
    echo "\n⚠️ RIZKI ADA PENDAFTARAN PASKIBRA: ID {$rizkiPaskibra->id} Status: {$rizkiPaskibra->status}" . PHP_EOL;
} else {
    echo "\n✅ Rizki TIDAK ADA pendaftaran ke Paskibra." . PHP_EOL;
}