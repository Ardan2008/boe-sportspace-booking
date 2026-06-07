# FINAL REPAIR: STATIC PHOTO CARDS & LIGHTBOX FIX
Target Files: formBooking.blade.php, detailFasilitas.blade.php, FasilitasController.php

## 1. PHOTO DISPLAY (NO AUTO-SLIDE)
- HAPUS semua logika animasi 'Auto-Slide' atau 'Carousel' yang berjalan otomatis.
- Tampilkan hanya foto pertama (`foto_kamar[0]`) sebagai thumbnail utama pada card tipe kamar.
- Pastikan gambar ter-render sempurna dengan `object-cover` agar tidak gepeng.

## 2. INTERACTIVE HOVER & LIGHTBOX
- Saat area foto di-hover:
    - Terapkan efek `blur-sm` dan `brightness-75`.
    - Munculkan ikon mata (👁️) di tengah foto secara transparan/halus.
- Saat area foto atau ikon mata diklik:
    - Picu Modal Lightbox (Pop-up Foto).
    - Lightbox harus menggunakan `fixed inset-0 z-[9999]` agar muncul di atas segalanya (mengingat detail asrama juga berupa pop-up).
    - Di dalam Lightbox, tampilkan ke-3 foto (`foto_kamar[0, 1, 2]`) dalam bentuk galeri yang bisa di-klik manual atau di-scroll.

## 3. DATA PERSISTENCE FIX
- Pastikan FasilitasController.php tidak menghapus data foto lama saat melakukan update jika tidak ada file baru yang diunggah.
- Pastikan data `foto_kamar` di-parse sebagai JSON array di sisi Alpine.js agar bisa dipanggil index-nya (0, 1, 2) tanpa error/glitch.

## 4. UI CLEANUP
- Gunakan warna badge fasilitas monokrom (Slate/Gray).
- Hapus border biru tebal pada harga, gunakan tipografi yang bersih.