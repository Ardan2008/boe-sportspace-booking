# MASTER CONFIGURATION: ROOM DETAILS, DYNAMIC HEADERS, REALTIME STOCK, AND DISABLED BUTTONS

## 1. DETAIL ADMIN, EMAIL & KUITANSI
- **Pop-up Detail Admin:** Pada komponen modal/pop-up detail reservasi di dashboard admin, cari bagian "Fasilitas Kamar". Tambahkan baris baru untuk menampilkan nomor kamar yang dipilih. Karena kolom `nomor_kamar` di database bertipe JSON/Array, tampilkan dengan cara di-implode: 
  `{{ is_array($booking->nomor_kamar) ? implode(', ', $booking->nomor_kamar) : $booking->nomor_kamar }}`
- **Template Email & Kuitansi PDF:** Terapkan kode implode yang sama pada file:
  * `resources/views/emails/approve.blade.php`
  * `resources/views/emails/reject.blade.php`
  * `resources/views/pdf/kuitansi.blade.php`
  Sehingga saat user memesan 2 kamar Standar, otomatis tertulis jelas: **"Nomor Kamar: A-01, A-02"**.

## 2. ALPINE.JS FIX UNTUK INDIKATOR SLOT KAMAR
- Buka file view `form-step-2.blade.php`.
- Pastikan state Alpine.js menangkap properti array nomor kamar dari response API (misal: `response.data.nomor_kamar_tersedia`). Simpan ke dalam variabel Alpine, contoh: `availableRoomNumbers`.
- Cari elemen judul di dalam perulangan (`x-for`) slot kamar (yang berisi icon dewasa & anak).
- Ubah tag judul teks statis `KAMAR 1` atau `KAMAR {{ $index }}` menggunakan `x-text` milik Alpine.js dengan logika:
  `x-text="availableRoomNumbers && availableRoomNumbers[index] ? 'KAMAR ' + availableRoomNumbers[index] : 'KAMAR ' + (index + 1)"`
- Dengan cara ini, jika user memilih 2 kamar Standar, judul kotak slot otomatis berubah realtime dari "KAMAR 1" dan "KAMAR 2" menjadi **"KAMAR A-01"** dan **"KAMAR A-02"**.

## 3. LOGIKA STOK REALTIME & TOMBOL NONAKTIF
- **Perbaikan Query Stok Backend:** Pastikan fungsi hitung sisa kamar di backend mengurangi total kapasitas kamar asli dengan jumlah kamar dari booking yang statusnya sudah 'approved' atau 'pending' pada rentang tanggal tersebut. (Jika Standar ada 3 kamar, dipesan 2, maka query HARUS mengembalikan angka 1).
- **Badge Kamar Penuh:** Jika sisa kamar == 0, ubah teks badge di halaman detail cards dan form 2 secara otomatis menjadi `"Kamar Penuh"` dengan background warna merah.
- **Disable Button & Alert:** On cards Tipe Kamar (baik di Halaman Detail maupun Form 2), pasang kondisi pada tombol "Booking Now" / "Booking Sekarang":
  * Jika sisa kamar <= 0, ubah class tombol menjadi warna abu-abu (`bg-gray-400`), tambahkan atribut `disabled` atau `pointer-events-none`.
  * Tambahkan trigger `@click` atau javascript alert sederhana: jika tombol abu-abu tersebut dipaksa diklik, munculkan pesan peringatan: `"Maaf, semua kamar pada tipe ini sudah penuh untuk tanggal yang Anda pilih."`