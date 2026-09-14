<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pendaftaran;
use App\Models\User;

// Simulasikan login sebagai Bu Siti (pembina_paskibra@soul.test)
$pembinaUser = User::where('email', 'pembina_paskibra@soul.test')->first();
if (!$pembinaUser) {
    echo "User pembina_paskibra tidak ditemukan!" . PHP_EOL;
    exit;
}

$pembina = $pembinaUser->pembina;
if (!$pembina) {
    echo "Pembina record tidak ditemukan!" . PHP_EOL;
    exit;
}

echo "Pembina: {$pembina->nama} (id: {$pembina->id})" . PHP_EOL;
echo "User: {$pembinaUser->email}" . PHP_EOL;

$ekskuls = $pembina->ekskuls()->get();
echo "\nEkskul yang dibina:" . PHP_EOL;
foreach ($ekskuls as $e) {
    echo "  - {$e->nama_ekskul} (id: {$e->id}, pembina_id: {$e->pembina_id})" . PHP_EOL;
}

$ekskulIds = $ekskuls->pluck('id');

$anggota = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
    ->where('status', 'diterima')
    ->with(['siswa', 'siswa.kelas'])
    ->latest('tanggal_daftar')
    ->get();

echo "\nAnggota (diterima) di dashboard Bu Siti:" . PHP_EOL;
foreach ($anggota as $a) {
    echo "  - {$a->siswa->nama} ({$a->siswa->jabatan}) | Ekskul: {$a->ekskul->nama_ekskul} | Status: {$a->status}" . PHP_EOL;
}

$pending = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
    ->where('status', 'pending')
    ->with(['siswa', 'siswa.kelas'])
    ->latest('tanggal_daftar')
    ->get();

echo "\nPending di dashboard Bu Siti:" . PHP_EOL;
foreach ($pending as $p) {
    echo "  - {$p->siswa->nama} ({$p->siswa->jabatan}) | Ekskul: {$p->ekskul->nama_ekskul} | Status: {$p->status}" . PHP_EOL;
}