# FEATURE: FINAL FIX FOR DYNAMIC ROOM HEADERS AND REALTIME STOCK DECREASE
Target Files: app/Http/Controllers/BookingController.php, resources/views/reservasi/form-step-2.blade.php, App/Models/Booking.php (dan file terkait model/relasi kamar)

## 1. BACKEND: SAVE ALLOCATED ROOMS TO DATABASE
- **Periksa Skema:** Jalankan inspeksi pada model `Booking` dan `Room`. Pastikan ada cara untuk menyimpan hubungan kamar yang dipesan (apakah via kolom `room_id`, kolom JSON `allocated_rooms`, atau tabel pivot `booking_room`).
- **Proses Menyimpan:** Saat proses reservasi dibuat (atau saat admin melakukan *Approve*), sistem HARUS secara otomatis mengambil nomor/ID kamar yang tersedia saat itu, lalu menyimpannya ke database sebagai kamar yang terkunci untuk booking tersebut.
- **Query Ketersediaan:** Perbarui fungsi `check-room-availability`. Query harus memeriksa kamar-kamar yang terikat pada booking aktif (status bukan 'rejected'/'canceled') di rentang tanggal yang bentrok, lalu mengecualikannya (`whereNotIn`) dari total kamar terdaftar. Dengan begitu, stok terbukti berkurang setelah reservasi sukses.

## 2. FRONTEND: ALPINE.JS HTML BINDING
- Buka file view Form 2 (`form-step-2.blade.php`) tempat slot dinamis Tamu Dewasa & Anak berada.
- Cari elemen teks/HTML yang menampilkan judul `KAMAR 1`, `KAMAR 2`, dst.
- Ganti teks statis tersebut menggunakan direktif reaktif Alpine.js `x-text`.
- Gunakan formula pengecekan index array: 
  `x-text="filteredRooms && filteredRooms[index] ? 'KAMAR ' + filteredRooms[index].room_number : 'KAMAR ' + (index + 1)"`
  *(Sesuaikan nama properti `room_number` atau `nomor_kamar` dengan struktur properti yang dikembalikan oleh JSON API kamu).*
- Pastikan setiap kali input tanggal berubah atau kartu dipilih, array penampung di Alpine.js langsung terupdate dengan sempurna tanpa ada yang ter-reset menjadi kosong.