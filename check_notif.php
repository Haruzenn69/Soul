<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Tahun Ajarans ===\n";
$tahunajarans = App\Models\TahunAjaran::all();
foreach ($tahunajarans as $ta) {
    echo "ID:{$ta->id} | {$ta->nama} | active:{$ta->is_active}\n";
}

echo "\n=== Kelas (with student count) ===\n";
$kelas = App\Models\Kelas::withCount('siswas')->with('tahunAjaran')->get();
foreach ($kelas as $k) {
    echo "ID:{$k->id} | {$k->nama} | tingkat:{$k->tingkat} | jurusan:{$k->jurusan} | rombel:{$k->rombel} | TA:{$k->tahunAjaran?->nama} | students:{$k->siswas_count}\n";
}

echo "\n=== Siswas ===\n";
$siswas = App\Models\Siswa::with('kelas')->get();
foreach ($siswas as $s) {
    echo "ID:{$s->id} | {$s->nama} | kelas:{$s->kelas?->nama} (id:{$s->kelas_id}) | status:{$s->status}\n";
}

echo "\n=== SiswaRiwayatKelas ===\n";
$riwayat = App\Models\SiswaRiwayatKelas::all();
foreach ($riwayat as $r) {
    echo "ID:{$r->id} | siswa_id:{$r->siswa_id} | TA:{$r->tahun_ajaran} | asal:{$r->kelas_asal} | tujuan:{$r->kelas_tujuan} | jenis:{$r->jenis}\n";
}
