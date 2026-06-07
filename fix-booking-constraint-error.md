# FEATURE: FIX INTERNAL SERVER ERROR 500 (MARIADB CONSTRAINT 4025) AND BIND ALPINE.JS
Target Files: app/Http/Controllers/BookingController.php, app/Models/Booking.php, resources/views/reservasi/form-step-2.blade.php

## 1. BACKEND CONTROLLER FIX (BookingController.php)
- Cari baris kode proses `insert` atau `Booking::create([...])` di dalam method store reservasi.
- Cari bagian pengisian field `nomor_kamar`. Ubah yang awalnya diisi string tunggal menjadi json string dari array kamar yang dialokasikan.
- *Contoh Perbaikan:* Jika ada `$allocatedRooms = ['A-01'];`, maka simpan ke database dengan cara:
  `'nomor_kamar' => json_encode($allocatedRooms)` atau disamakan dengan isi field `allocated_rooms`.
- Langkah ini akan membuat database menerima data dengan sukses (Lolos dari CONSTRAINT 4025) dan mendukung penyimpanan multi-kamar (Nomor 3 & 4).

## 2. MODEL CASTING FIX (Booking.php)
- Buka file model `Booking.php`.
- Pastikan properti `$casts` sudah mendaftarkan `nomor_kamar` dan `allocated_rooms` sebagai `array` atau `json`.
- *Contoh:*
  ```php
  protected $casts = [
      'nomor_kamar' => 'array',
      'allocated_rooms' => 'array',
  ];