<?php
$rooms = [
    ['tipe' => [], 'panjang' => '20', 'lebar' => '10', 'harga_harian' => 150000, 'harga_mingguan' => 900000, 'harga_bulanan' => 1500000, 'harga_tahunan' => 15000000],
    ['tipe' => [], 'panjang' => '20', 'lebar' => '10', 'harga_harian' => 150000, 'harga_mingguan' => 900000, 'harga_bulanan' => 1500000, 'harga_tahunan' => 15000000],
    ['tipe' => [], 'panjang' => '20', 'lebar' => '10', 'harga_harian' => 150000, 'harga_mingguan' => 900000, 'harga_bulanan' => 1500000, 'harga_tahunan' => 15000000],
];

echo "Test 1 (all same): ";
if (count($rooms) <= 1) {
    echo 'true';
} else {
    $first = $rooms[0];
    $same = true;
    foreach (array_slice($rooms, 1) as $r) {
        if (($r['tipe'] ?? '') !== ($first['tipe'] ?? '')
            || ($r['panjang'] ?? '') !== ($first['panjang'] ?? '')
            || ($r['lebar'] ?? '') !== ($first['lebar'] ?? '')
            || ($r['harga_harian'] ?? 0) != ($first['harga_harian'] ?? 0)
            || ($r['harga_mingguan'] ?? 0) != ($first['harga_mingguan'] ?? 0)
            || ($r['harga_bulanan'] ?? 0) != ($first['harga_bulanan'] ?? 0)
            || ($r['harga_tahunan'] ?? 0) != ($first['harga_tahunan'] ?? 0)) {
            $same = false;
            break;
        }
    }
    echo $same ? 'true' : 'false';
}

echo "\n";

// Test with different rooms
$rooms2 = [
    ['tipe' => ['Futsal'], 'panjang' => '20', 'lebar' => '10', 'harga_harian' => 150000, 'harga_mingguan' => 900000, 'harga_bulanan' => 1500000, 'harga_tahunan' => 15000000],
    ['tipe' => ['Basket'], 'panjang' => '25', 'lebar' => '12', 'harga_harian' => 200000, 'harga_mingguan' => 1200000, 'harga_bulanan' => 2000000, 'harga_tahunan' => 20000000],
];

echo "Test 2 (different): ";
if (count($rooms2) <= 1) {
    echo 'true';
} else {
    $first = $rooms2[0];
    $same = true;
    foreach (array_slice($rooms2, 1) as $r) {
        if (($r['tipe'] ?? '') !== ($first['tipe'] ?? '')
            || ($r['panjang'] ?? '') !== ($first['panjang'] ?? '')
            || ($r['lebar'] ?? '') !== ($first['lebar'] ?? '')
            || ($r['harga_harian'] ?? 0) != ($first['harga_harian'] ?? 0)
            || ($r['harga_mingguan'] ?? 0) != ($first['harga_mingguan'] ?? 0)
            || ($r['harga_bulanan'] ?? 0) != ($first['harga_bulanan'] ?? 0)
            || ($r['harga_tahunan'] ?? 0) != ($first['harga_tahunan'] ?? 0)) {
            $same = false;
            break;
        }
    }
    echo $same ? 'true' : 'false';
}

echo "\n";

// Test case: tipe is empty array vs tipe is string (edge case from DB)
$rooms3 = [
    ['tipe' => [], 'panjang' => '', 'lebar' => '', 'harga_harian' => 100000, 'harga_mingguan' => 0, 'harga_bulanan' => 0, 'harga_tahunan' => 0],
    ['tipe' => [], 'panjang' => '', 'lebar' => '', 'harga_harian' => 100000, 'harga_mingguan' => 0, 'harga_bulanan' => 0, 'harga_tahunan' => 0],
];
echo "Test 3 (same, empty tipe/dims): ";
if (count($rooms3) <= 1) {
    echo 'true';
} else {
    $first = $rooms3[0];
    $same = true;
    foreach (array_slice($rooms3, 1) as $r) {
        if (($r['tipe'] ?? '') !== ($first['tipe'] ?? '')
            || ($r['panjang'] ?? '') !== ($first['panjang'] ?? '')
            || ($r['lebar'] ?? '') !== ($first['lebar'] ?? '')
            || ($r['harga_harian'] ?? 0) != ($first['harga_harian'] ?? 0)
            || ($r['harga_mingguan'] ?? 0) != ($first['harga_mingguan'] ?? 0)
            || ($r['harga_bulanan'] ?? 0) != ($first['harga_bulanan'] ?? 0)
            || ($r['harga_tahunan'] ?? 0) != ($first['harga_tahunan'] ?? 0)) {
            $same = false;
            break;
        }
    }
    echo $same ? 'true' : 'false';
}

echo "\n";

// Test: numeric 0 vs missing key (null)
$rooms4 = [
    ['tipe' => [], 'panjang' => '', 'lebar' => '', 'harga_harian' => 100000, 'harga_mingguan' => 0, 'harga_bulanan' => 0, 'harga_tahunan' => 0],
    ['tipe' => [], 'panjang' => '', 'lebar' => '', 'harga_harian' => 100000],
];
echo "Test 4 (missing price keys): ";
if (count($rooms4) <= 1) {
    echo 'true';
} else {
    $first = $rooms4[0];
    $same = true;
    foreach (array_slice($rooms4, 1) as $r) {
        if (($r['tipe'] ?? '') !== ($first['tipe'] ?? '')
            || ($r['panjang'] ?? '') !== ($first['panjang'] ?? '')
            || ($r['lebar'] ?? '') !== ($first['lebar'] ?? '')
            || ($r['harga_harian'] ?? 0) != ($first['harga_harian'] ?? 0)
            || ($r['harga_mingguan'] ?? 0) != ($first['harga_mingguan'] ?? 0)
            || ($r['harga_bulanan'] ?? 0) != ($first['harga_bulanan'] ?? 0)
            || ($r['harga_tahunan'] ?? 0) != ($first['harga_tahunan'] ?? 0)) {
            $same = false;
            break;
        }
    }
    echo $same ? 'true' : 'false';
}
echo "\n";
