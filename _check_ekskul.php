<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ekskuls = \App\Models\Ekskul::with('pembina.user')->get();
foreach ($ekskuls as $e) {
    echo "Ekskul: {$e->nama_ekskul} | Pembina: " . ($e->pembina->nama ?? 'NULL') . " (id: {$e->pembina_id}) | User: " . ($e->pembina->user->email ?? 'NULL') . PHP_EOL;
}