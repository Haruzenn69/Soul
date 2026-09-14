<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Siswa;
use App\Models\Pendaftaran;
use App\Models\Ekskul;

$siswas = Siswa::with(['user', 'pendaftarans.ekskul', 'kelas'])->get();

echo "=== SEMUA SISWA DAN STATUS EKSKUL ===" . PHP_EOL;
foreach ($siswas as $s) {
    $diterima = $s->pendaftarans->where('status', 'diterima')->first();
    $pending = $s->pendaftarans->where('status', 'pending')->first();
    if ($diterima) {
        $status = "DITERIMA ({$diterima->ekskul->nama_ekskul})";
    } elseif ($pending) {
        $status = "PENDING ({$pending->ekskul->nama_ekskul})";
    } else {
        $status = "BELUM MASUK EKSKUL";
    }
    $kelasNama = $s->kelas ? $s->kelas->nama : '-';
    echo "{$s->nama} (NIS: {$s->nis}) | Jabatan: {$s->jabatan} | Kelas: {$kelasNama} | {$status}" . PHP_EOL;
}

// Ekskul tersedia
echo "\n=== EKSKUL TERSEDIA ===" . PHP_EOL;
$ekskuls = Ekskul::with('pembina')->get();
foreach ($ekskuls as $e) {
    $count = $e->pendaftarans()->where('status', 'diterima')->count();
    $pembinaNama = $e->pembina ? $e->pembina->nama : 'NULL';
    echo "{$e->nama_ekskul} (Pembina: {$pembinaNama}) | Anggota diterima: {$count}" . PHP_EOL;
}