# FEATURE: ADD APPROVAL/REJECTION TIMESTAMP TO EMAIL & PDF RECEIPT
Target Files: BookingController.php, ApproveBookingMail.php, RejectBookingMail.php, resources/views/emails/approve.blade.php, resources/views/emails/reject.blade.php, resources/views/pdf/kuitansi.blade.php

## 1. BACKEND LOGIC UPDATE (BookingController.php)
- **Capture Time:** Pada method `approve` dan `reject`, tangkap waktu eksekusi saat ini menggunakan `now()` atau `\Carbon\Carbon::now()`.
- **Formatting:** Format waktu tersebut menggunakan standar Indonesia, contoh: `now()->translatedFormat('d F Y, H:i') . ' WIB'`.
- **Passing Data:** Kirimkan string waktu eksekusi ini sebagai variabel baru (misal: `$actionDate`) ke dalam constructor class `ApproveBookingMail` dan `RejectBookingMail`.

## 2. MAILABLE CLASSES UPDATE
- **ApproveBookingMail.php & RejectBookingMail.php:**
    - Perbarui constructor `__construct` agar menerima parameter baru `$actionDate`.
    - Jadikan properti tersebut sebagai variabel public (misal: `public $actionDate;`) agar otomatis bisa dibaca oleh file Blade template.
    - Di dalam `ApproveBookingMail`, pastikan variabel `$actionDate` ini juga ikut di-pass ke dalam pengiriman data view PDF kuitansi (`pdf.kuitansi`).

## 3. BLADE VIEWS UPDATE (FRONTEND DISPLAY)
- **resources/views/emails/approve.blade.php:**
    - Tambahkan baris baru di dalam tabel detail atau bagian atas informasi: "Tanggal Disetujui: [variabel actionDate]".
- **resources/views/emails/reject.blade.php:**
    - Tambahkan baris baru di area informasi penolakan: "Tanggal Ditolak: [variabel actionDate]".
- **resources/views/pdf/kuitansi.blade.php:**
    - Di dalam dokumen cetak PDF kuitansi, tambahkan baris waktu cetak/validasi kuitansi di bagian detail transaksi atau di dekat area tanda tangan/barcode menggunakan variabel `$actionDate`.