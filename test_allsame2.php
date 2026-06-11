<?php
// Simulate what FasilitasController::edit() returns for $rooms
// when allSame was used during create — rooms are all identical clones

// Typical "allSame=true" saved data:
$rooms = [
    [
        'tipe' => [],
        'jumlah' => 1,
        'kode_blok' => '',
        'foto' => [],
        'harga_harian' => 100000,
        'harga_mingguan' => 600000,
        'harga_bulanan' => 1500000,
        'harga_tahunan' => 15000000,
        'keunggulan' => '',
        'panjang' => '',
        'lebar' => '',
        'nomor_kamar' => [],
        'fasilitas' => ['lampu' => 0, 'parkir' => 0, 'toilet' => 0],
    ],
    [
        'tipe' => [],
        'jumlah' => 1,
        'kode_blok' => '',
        'foto' => [],
        'harga_harian' => 100000,
        'harga_mingguan' => 600000,
        'harga_bulanan' => 1500000,
        'harga_tahunan' => 15000000,
        'keunggulan' => '',
        'panjang' => '',
        'lebar' => '',
        'nomor_kamar' => [],
        'fasilitas' => ['lampu' => 0, 'parkir' => 0, 'toilet' => 0],
    ],
    [
        'tipe' => [],
        'jumlah' => 1,
        'kode_blok' => '',
        'foto' => [],
        'harga_harian' => 100000,
        'harga_mingguan' => 600000,
        'harga_bulanan' => 1500000,
        'harga_tahunan' => 15000000,
        'keunggulan' => '',
        'panjang' => '',
        'lebar' => '',
        'nomor_kamar' => [],
        'fasilitas' => ['lampu' => 0, 'parkir' => 0, 'toilet' => 0],
    ],
];

// Add nomor_kamar/temp_input as edit() controller does
foreach ($rooms as &$room) {
    if (!isset($room['nomor_kamar'])) $room['nomor_kamar'] = [];
    if (!isset($room['temp_input']))   $room['temp_input']  = '';
}
unset($room);

// My allSame detection
$result = null;
if (count($rooms) <= 1) {
    $result = 'true';
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
    $result = $same ? 'true' : 'false';
}

echo "allSame result: $result\n";

// Now test with "Beda" case — different tipe tags, different prices
$rooms2 = [
    [
        'tipe' => ['Futsal'],
        'harga_harian' => 100000,
        'harga_mingguan' => 600000,
        'harga_bulanan' => 1500000,
        'harga_tahunan' => 15000000,
        'panjang' => '20',
        'lebar' => '10',
    ],
    [
        'tipe' => ['Basket'],
        'harga_harian' => 150000,
        'harga_mingguan' => 800000,
        'harga_bulanan' => 2000000,
        'harga_tahunan' => 20000000,
        'panjang' => '25',
        'lebar' => '14',
    ],
];

$result2 = null;
if (count($rooms2) <= 1) {
    $result2 = 'true';
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
    $result2 = $same ? 'true' : 'false';
}
echo "Different rooms result: $result2\n";

// Edge: harga_mingguan/bulanan/tahunan are 0 (not filled) but harga_harian is set
$rooms3 = [
    ['tipe' => [], 'panjang' => '', 'lebar' => '', 'harga_harian' => 150000, 'harga_mingguan' => 0, 'harga_bulanan' => 0, 'harga_tahunan' => 0],
    ['tipe' => [], 'panjang' => '', 'lebar' => '', 'harga_harian' => 150000, 'harga_mingguan' => 0, 'harga_bulanan' => 0, 'harga_tahunan' => 0],
];

$result3 = null;
if (count($rooms3) <= 1) {
    $result3 = 'true';
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
    $result3 = $same ? 'true' : 'false';
}
echo "Zero prices result: $result3\n";
