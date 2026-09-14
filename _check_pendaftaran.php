<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pendaftarans = \App\Models\Pendaftaran::with(['siswa.user', 'ekskul'])->get();
foreach ($pendaftarans as $p) {
    echo "Pendaftaran: Siswa={$p->siswa->nama} ({$p->siswa->jabatan}) | Ekskul={$p->ekskul->nama_ekskul} | Status={$p->status}" . PHP_EOL;
}