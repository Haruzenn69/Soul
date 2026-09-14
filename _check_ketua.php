<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ketuas = \App\Models\Siswa::where('jabatan', 'ketua')->with(['user', 'pendaftarans.ekskul'])->get();
foreach ($ketuas as $k) {
    $pendaftaran = $k->pendaftarans->where('status', 'diterima')->first();
    $ekskul = $pendaftaran ? $pendaftaran->ekskul->nama_ekskul : 'TIDAK ADA';
    echo "Ketua: {$k->nama} (NIS: {$k->nis}) | User: {$k->user->email} | Ekskul: {$ekskul}" . PHP_EOL;
}