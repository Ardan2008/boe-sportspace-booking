<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = App\Models\Fasilitas::all();
foreach ($f as $item) {
    echo "ID: {$item->id} | Nama: {$item->nama} | Tipe: {$item->tipe}\n";
    $p = $item->paket_harian;
    if ($p) {
        foreach ($p as $i => $rt) {
            echo "  [$i] tipe=" . ($rt['tipe'] ?? '?') . " nomor_kamar=" . json_encode($rt['nomor_kamar'] ?? []) . "\n";
        }
    }
}
echo "--- Bookings ---\n";
$b = App\Models\Booking::all();
foreach ($b as $item) {
    echo "ID: {$item->id} | fasilitas_id: {$item->fasilitas_id} | tgl_mulai: {$item->tgl_mulai} | tgl_selesai: {$item->tgl_selesai} | status: {$item->status}\n";
}
