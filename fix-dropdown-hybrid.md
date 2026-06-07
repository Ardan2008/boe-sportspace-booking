# CRITICAL FIX: PERMANENT DELETE & UI UNIFICATION
Target Files: FasilitasController.php, web.php, formFasilitas.blade.php

## 1. BACKEND: DELETE ROUTE & METHOD
- **Web.php:** Tambahkan route baru: `Route::delete('/admin/fasilitas/room-type/{id}', [FasilitasController::class, 'destroyRoomType'])->name('fasilitas.room-type.destroy');`.
- **FasilitasController.php:** Buat method `destroyRoomType($id)`. 
    - Logika: Cari data tipe kamar berdasarkan ID, hapus filenya (jika ada foto), lalu hapus record-nya dari database. 
    - Return: Response JSON success.

## 2. FRONTEND: AJAX SYNC & UI UNIFICATION
Perbaiki komponen Alpine.js pada formFasilitas.blade.php:
- **Style Unification:** JANGAN gunakan `confirm()` bawaan browser. Gunakan library atau komponen modal yang sama persis dengan yang ada di halaman Edit (misal: SweetAlert2).
- **Smart Delete Logic:**
    - Saat tombol hapus ditekan, cek apakah item memiliki atribut `id`.
    - Jika TIDAK ada ID (Data baru yang belum di-save): Langsung lakukan `.splice(index, 1)`.
    - Jika ADA ID (Data lama dari database):
        1. Tampilkan modal konfirmasi "Hapus Permanen?".
        2. Jika 'OK', kirim request `axios.delete` ke route yang baru dibuat.
        3. Setelah server merespon sukses, baru lakukan `.splice(index, 1)` agar baris hilang dari UI.
- **Persistence:** Pastikan setelah proses ini, data benar-benar hilang dari database sehingga tidak muncul lagi saat refresh.

## 3. FEEDBACK
- Tampilkan toast notification "Tipe Kamar berhasil dihapus permanen" setelah proses AJAX berhasil.