# FEATURE: FRONTEND INTEGRATION FOR ROOM STOCK INDICATOR & ALPINE.JS DYNAMIC ROOM NUMBER
Target Files: resources/views/components/room-card.blade.php, resources/views/reservasi/form-step-2.blade.php (atau sesuaikan dengan nama file View Detail Cards dan Form 2 milikmu)

## 1. INDICATOR STOCK ON ROOM CARDS
- Pada halaman "Lihat Detail" atau list cards Tipe Kamar, tambahkan sebuah badge/teks indikator stok yang dinamis.
- Indikator ini membaca sisa kamar tipe tersebut (Contoh: "Tersisa 3 Kamar" atau jika habis "Kamar Penuh").
- Pastikan pada Form 2 tempat user memilih kamar, indikator stok ini juga nampak jelas sebelum user menekan tombol booking.

## 2. ALPINE.JS AJAX FETCH LOGIC
- Di dalam komponen Alpine.js yang mengurus form reservasi, tambahkan trigger `x-effect` atau watcher yang mengawasi perubahan input tanggal `check_in` dan `check_out`.
- Jika tanggal sudah terisi, lakukan `fetch()` atau `axios.get()` ke endpoint backend kita: `/api/check-room-availability` dengan mengirimkan parameter `tipe_kamar_id`, `check_in`, dan `check_out`.
- Simpan hasil response JSON (berupa data `total_kamar_tersedia` dan array `nomor_kamar_tersedia`) ke dalam variabel state Alpine.js (misal: `availableRooms: []`, `maxStock: 0`).

## 3. REAKTIVITAS UI CARD SLOTS
- Cari bagian layout template yang menampilkan kotak slot dinamis (Tamu Dewasa + Anak dengan icon) seperti pada foto referensi pengguna.
- Modifikasi teks judul statis yang awalnya `"KAMAR 1"`, `"KAMAR 2"`, dst. 
- Ubah menjadi dinamis menggunakan direktif Alpine.js, dengan aturan: Mengambil nama dari array `nomor_kamar_tersedia` berdasarkan index slot tersebut.
  *Contoh logika:* `x-text="availableRooms[index] ? 'KAMAR ' + availableRooms[index] : 'KAMAR ' + (index + 1)"`
- Dengan begitu, jika array berisi `["A-02", "A-03"]`, otomatis judul slot berubah menjadi `"KAMAR A-02"` dan `"KAMAR A-03"`.

## 4. DYNAMIC INPUT CONSTRAINT
- Pada input field atau tombol plus/minus untuk memilih jumlah kamar yang akan di-booking, pasang atribut `max` secara dinamis mengikuti nilai `maxStock` (total kamar tersedia hasil fetch API).
- Cegah user meningkatkan jumlah booking jika sudah mencapai batas maksimal stok yang tersedia pada tanggal tersebut.