<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\Siswa;

// 1. Fix Karate pembina_id → Pak Ahmad (id: 1)
$karate = Ekskul::where('nama_ekskul', 'Karate')->first();
if ($karate) {
    $karate->update(['pembina_id' => 1]); // Pak Ahmad
    echo "✅ Karate pembina_id diupdate ke 1 (Pak Ahmad)" . PHP_EOL;
}

// 2. Fix Rizki Pratama pendaftaran status → diterima
$rizki = Siswa::where('nis', '2406510001')->first();
if ($rizki) {
    $pendaftaran = $rizki->pendaftarans()->where('ekskul_id', $karate->id)->first();
    if ($pendaftaran) {
        $pendaftaran->update(['status' => 'diterima']);
        echo "✅ Rizki Pratama status diupdate ke 'diterima'" . PHP_EOL;
    }
}

// 3. Fix Andi Wijaya pendaftaran status → pending
$andi = Siswa::where('nis', '2406510004')->first();
if ($andi) {
    $pendaftaran = $andi->pendaftarans()->where('status', 'nonaktif')->first();
    if ($pendaftaran) {
        $pendaftaran->update(['status' => 'pending']);
        echo "✅ Andi Wijaya status diupdate ke 'pending'" . PHP_EOL;
    }
}

echo "Selesai." . PHP_EOL;