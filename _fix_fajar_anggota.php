<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Siswa;

$fajar = Siswa::where('nis', '2406510099')->first();
if ($fajar) {
    $fajar->update(['jabatan' => 'anggota']);
    echo "✅ Fajar Ramadhan: jabatan → 'anggota'" . PHP_EOL;
} else {
    echo "Fajar tidak ditemukan" . PHP_EOL;
}