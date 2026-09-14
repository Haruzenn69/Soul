<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pendaftaran;
use App\Models\User;

// Simulasikan persis seperti PembinaController::dashboard()
$pembinaUser = User::where('email', 'pembina_paskibra@soul.test')->first();
$pembina = $pembinaUser->pembina;

$ekskuls = $pembina->ekskuls()->get();
$ekskulIds = $ekskuls->pluck('id');

$anggota = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
    ->where('status', 'diterima')
    ->with(['siswa', 'siswa.kelas'])
    ->latest('tanggal_daftar')
    ->get();

echo "=== Dashboard Bu Siti (Paskibra) ===" . PHP_EOL;
echo "Total anggota diterima: " . $anggota->count() . PHP_EOL;
foreach ($anggota as $a) {
    echo "  - {$a->siswa->nama} | Jabatan: {$a->siswa->jabatan} | Ekskul: {$a->ekskul->nama_ekskul} | Status: {$a->status} | Ekskul ID: {$a->ekskul_id} | Siswa ID: {$a->siswa_id}" . PHP_EOL;
}

// Cek apakah ada Rizki Pratama di query ini
$rizkiAda = $anggota->filter(fn($a) => $a->siswa->nis === '2406510001')->first();
if ($rizkiAda) {
    echo "\n⚠️ RIZKI PRATAMA (Ketua Karate) MASIH ADA DI QUERY INI!" . PHP_EOL;
} else {
    echo "\n✅ Rizki Pratama TIDAK ADA di query ini." . PHP_EOL;
}

// Cross-check: Cek pembina_id dari Karate
$karate = \App\Models\Ekskul::where('nama_ekskul', 'Karate')->first();
echo "\nKarate pembina_id: " . $karate->pembina_id . " (seharusnya 1 = Pak Ahmad)" . PHP_EOL;