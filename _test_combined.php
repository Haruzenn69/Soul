<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pendaftaran;
use App\Models\User;

$pembinaUser = User::where('email', 'pembina_paskibra@soul.test')->first();
$pembina = $pembinaUser->pembina;
$ekskulIds = $pembina->ekskuls()->pluck('id');

$anggota = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
    ->whereIn('status', ['diterima', 'nonaktif'])
    ->with(['siswa', 'siswa.kelas'])
    ->latest('tanggal_daftar')
    ->get();

echo "Gabungan (diterima + nonaktif) di Paskibra (Bu Siti): " . $anggota->count() . PHP_EOL;
foreach ($anggota as $a) {
    $statusLabel = $a->status === 'diterima' ? 'Aktif' : 'Nonaktif';
    echo "  - {$a->siswa->nama} ({$a->siswa->jabatan}) | Status: {$statusLabel} ({$a->status})" . PHP_EOL;
}