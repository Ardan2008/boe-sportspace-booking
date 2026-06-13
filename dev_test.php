<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Fasilitas;

$ids = [37, 39, 42, 45];

foreach ($ids as $id) {
    $f = Fasilitas::find($id);
    if (!$f) { echo "ID $id: not found\n"; continue; }
    $fotoData = array_map(fn($r) => $r['foto'] ?? [], $f->paket_harian ?? []);
    echo "ID: {$f->id} | {$f->nama} | all_same:" . ($f->all_same ? 'true' : 'false') . " | jml:{$f->jumlah_lapangan} | fotos:" . json_encode($fotoData) . "\n";
}
