# FEATURE: FIXING ROOM AVAILABILITY BADGE, ALPINE.JS BINDING, AND PENDING BOOKING QUERY
Target Files: app/Http/Controllers/BookingController.php (atau RoomApiController.php), resources/views/reservasi/form-step-2.blade.php (atau file template Form 2)

## 1. BACKEND QUERY FIX
- Buka fungsi query pengecekan ketersediaan kamar di Controller.
- Pastikan kriteria kamar yang dianggap **TERISI** adalah semua reservasi pada tanggal tersebut yang statusnya **BUKAN** 'rejected' dan **BUKAN** 'canceled'.
- Artinya, status 'pending' (baru submit) dan 'approved' HARUS dihitung sebagai kamar terisi agar stok langsung berkurang realtime setelah user klik submit booking.

## 2. FRONTEND ALPINE.JS FIX
- Di dalam tag input tanggal atau saat kartu tipe kamar dipilih, tambahkan validasi di Alpine.js sebelum melakukan `fetch` API:
  *Pastikan fetch HANYA berjalan jika `tipe_kamar_id`, `check_in`, dan `check_out` sudah terisi dengan benar (tidak null / tidak kosong).*
- Jika tanggal belum dipilih oleh user, buat badge menampilkan total kapasitas kamar bawaan (master data kamar asli) dari tipe tersebut, JANGAN langsung diset menjadi "Kamar Penuh".
- Pada bagian judul slot room dinamis, pastikan mapping index array dari `nomor_kamar_tersetia` sinkron dengan perulangan `x-for`. Gunakan format:
  `x-text="availableRooms[index] ? 'KAMAR ' + availableRooms[index] : 'KAMAR ' + (index + 1)"`
- Pastikan state array `availableRooms` tidak ter-reset menjadi kosong saat user menambah jumlah slot kamar.