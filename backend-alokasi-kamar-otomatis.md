# FEATURE: BACKEND ROOM AVAILABILITY CHECK & AUTOMATIC ROOM NUMBER ALLOCATION
Target Files: app/Http/Controllers/BookingController.php (atau RoomApiController.php), routes/web.php (atau routes/api.php), App/Models/Booking.php, App/Models/Room.php (sesuaikan dengan nama model Kamar & Booking milikmu)

## 1. LOGIKA QUERY BENTROKAN TANGGAL (AVAILABILITY LOGIC)
- Buat sebuah fungsi atau API endpoint internal `GET /api/check-room-availability`.
- Endpoint ini menerima parameter: `tipe_kamar_id`, `check_in_date`, dan `check_out_date`.
- Di dalam fungsi tersebut, lakukan query untuk mencari nomor-nomor kamar yang **SUDAH TERBOOKING** pada rentang tanggal tersebut dengan kondisi:
  `booking.check_in < input.check_out` DAN `booking.check_out > input.check_in`
  Serta pastikan status booking tersebut adalah aktif (bukan 'rejected' atau 'canceled').

## 2. AUTO-ALLOCATION & STOCK COUNT CALCULATOR
- Ambil semua daftar nomor kamar (master data) berdasarkan `tipe_kamar_id` yang dipilih.
- Lakukan eliminasi (`whereNotIn`) menggunakan daftar nomor kamar yang sudah terbooking dari Langkah 1.
- Hasil akhirnya adalah daftar nomor kamar yang **BENAR-BENAR BERSIH/TERSEDIA** untuk langsung ditempati.
- Kembalikan respon berupa JSON dengan struktur seperti ini:
  ```json
  {
    "success": true,
    "total_kamar_tersedia": 2, // Nilai ini untuk indikator Nomor 3
    "nomor_kamar_tersedia": ["A-02", "A-03"] // List ini untuk reaktivitas Form 2 Nomor 4
  }