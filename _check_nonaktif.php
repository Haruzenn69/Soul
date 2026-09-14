<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pendaftaran;
use App\Models\User;

$pembinaUser = User::where('email', 'pembina_paskibra@soul.test')->first();
$pembina = $pembinaUser->pembina;
$ekskulIds = $pembina->ekskuls()->pluck('id');

$nonaktif = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
    ->where('status', 'nonaktif')
    ->with(['siswa', 'siswa.kelas'])
    ->latest('tanggal_daftar')
    ->get();

echo "Nonaktif di Paskibra (Bu Siti): " . $nonaktif->count() . PHP_EOL;
foreach ($nonaktif as $n) {
    echo "  - {$n->siswa->nama} ({$n->siswa->jabatan})" . PHP_EOL;
}

// Cek Pak Ahmad
$pakAhmadUser = User::where('email', 'pembina_karate@soul.test')->first();
$pakAhmad = $pakAhmadUser->pembina;
$ekskulIdsAhmad = $pakAhmad->ekskuls()->pluck('id');

$nonaktifAhmad = Pendaftaran::whereIn('ekskul_id', $ekskulIdsAhmad)
    ->where('status', 'nonaktif')
    ->with(['siswa', 'siswa.kelas'])
    ->latest('tanggal_daftar')
    ->get();

echo "\nNonaktif di Karate/FUTSAL (Pak Ahmad): " . $nonaktifAhmad->count() . PHP_EOL;
foreach ($nonaktifAhmad as $n) {
    echo "  - {$n->siswa->nama} ({$n->siswa->jabatan}) | Ekskul: {$n->ekskul->nama_ekskul}" . PHP_EOL;
}