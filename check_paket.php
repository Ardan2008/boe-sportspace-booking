<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = \DB::table('fasilitas')->select('id','nama','all_same','paket_harian')->get();
foreach ($rows as $r) {
    $rooms = json_decode($r->paket_harian, true) ?? [];
    echo "\n=== ID:{$r->id} | {$r->nama} | all_same:{$r->all_same} ===\n";
    foreach ($rooms as $i => $room) {
        echo "  Room[$i]:\n";
        $keys = ['tipe','kode_blok','panjang','lebar','harga_harian','harga_mingguan','harga_bulanan','harga_tahunan','keunggulan','foto','nomor_kamar'];
        foreach ($keys as $k) {
            $v = $room[$k] ?? 'NOT SET';
            if (is_array($v)) $v = json_encode($v);
            echo "    $k: $v\n";
        }
        $fas = $room['fasilitas'] ?? [];
        echo "    fasilitas: " . json_encode($fas) . "\n";
    }
}
