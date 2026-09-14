<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Siswa;
use App\Models\Pendaftaran;
use App\Models\Ekskul;

$futsal = Ekskul::where('nama_ekskul', 'FUTSAL')->first();
$karate = Ekskul::where('nama_ekskul', 'Karate')->first();
$paskibra = Ekskul::where('nama_ekskul', 'Paskibra')->first();

// Pindahkan Ahmad Fauzi dari Paskibra ke FUTSAL
$ahmad = Siswa::where('nis', '2406510005')->first();
if ($ahmad) {
    $p = $ahmad->pendaftarans()->where('status', 'diterima')->first();
    if ($p && $p->ekskul_id === $paskibra->id) {
        $p->update(['ekskul_id' => $futsal->id]);
        echo "✅ Ahmad Fauzi: Paskibra → FUTSAL" . PHP_EOL;
    }
}

// Pindahkan Nazwa Nurhafiza dari Karate ke FUTSAL
$nazwa = Siswa::where('nis', '2406510002')->first();
if ($nazwa) {
    $p = $nazwa->pendaftarans()->where('status', 'diterima')->first();
    if ($p && $p->ekskul_id === $karate->id) {
        $p->update(['ekskul_id' => $futsal->id]);
        echo "✅ Nazwa Nurhafiza: Karate → FUTSAL" . PHP_EOL;
    }
}

echo "\n=== HASIL AKHIR ===" . PHP_EOL;
foreach (['Karate', 'Paskibra', 'FUTSAL'] as $nama) {
    $e = Ekskul::where('nama_ekskul', $nama)->first();
    $count = $e->pendaftarans()->where('status', 'diterima')->count();
    $anggota = $e->pendaftarans()->where('status', 'diterima')->with('siswa')->get();
    echo "{$nama}: {$count} anggota" . PHP_EOL;
    foreach ($anggota as $a) {
        echo "  - {$a->siswa->nama} ({$a->siswa->jabatan})" . PHP_EOL;
    }
}