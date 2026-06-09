# AUTO-EMAIL NOTIFICATION SYSTEM WITH PDF RECEIPT & VERIFICATION BARCODE
Target Files: BookingController.php, App/Mail/ApproveBookingMail.php, App/Mail/RejectBookingMail.php, resources/views/emails/approve.blade.php, resources/views/emails/reject.blade.php, resources/views/pdf/kuitansi.blade.php

## 1. COMPONENT INSTALLATION CHECK
- Pastikan kodingan menggunakan package `barryvdh/laravel-dompdf` untuk menangani pembuatan dokumen PDF.
- Untuk barcode gratisan di PDF, gunakan standar generator inline HTML/CSS Barcode atau library generator lokal (seperti `milon/barcode` tipe DNS1D) yang bekerja 100% offline tanpa API pihak ketiga.

## 2. MAILABLE CLASS CREATION
- **ApproveBookingMail:**
    - Menerima data objek `$booking`.
    - Di dalam fungsi `build()`, generate file PDF menggunakan view `pdf.kuitansi` dengan menyertakan data booking dan string barcode unik (berdasarkan `booking_id`).
    - Lampirkan file PDF tersebut ke dalam email menggunakan fungsi `attachData($pdf->output(), 'Kuitansi-Booking-'. $booking->id .'.pdf')`.
- **RejectBookingMail:**
    - Menerima data objek `$booking` dan string `$alasan_reject` yang di-input manual oleh admin dari form backend.

## 3. CONTROLLER TRIGGER LOGIC (BookingController.php)
- **On Method Approve:**
    - Ubah status reservasi menjadi disetujui.
    - Panggil kelas `ApproveBookingMail` dan kirimkan secara realtime ke `Mail::to($booking->email)`.
- **On Method Reject:**
    - Tangkap input manual `alasan_reject` dari request admin form.
    - Ubah status reservasi menjadi ditolak dan simpan alasan tersebut ke kolom database (jika ada) atau langsung kirimkan sebagai variabel.
    - Panggil kelas `RejectBookingMail` dengan menyertakan variabel alasan tersebut, lalu kirimkan ke `Mail::to($booking->email)`.

## 4. BLADE TEMPLATES DESIGN
- **emails.approve (Tampilan Email Setuju):**
    - Desain layout bersih menyontek struktur pop-up Detail Reservasi milik Admin.
    - Cantumkan pesan ucapan terima kasih yang hangat di bagian atas.
    - Tampilkan ringkasan detail transaksi di bagian badan email.
    - Bagian kaki email (Footer): Tambahkan baris informasi kontak pengelola penginapan jika terjadi kekeliruan data (Contoh: "Jika ada kesalahan data, hubungi admin di support@boesportspace.com").
- **emails.reject (Tampilan Email Tolak):**
    - Tampilkan detail reservasi asal sebagai referensi pengguna.
    - Tampilkan kotak peringatan berwarna merah/abu-abu terang yang memuat alasan penolakan dari admin secara jelas.
    - Sertakan footer kontak pengelola yang sama seperti di email setuju.
- **pdf.kuitansi (Tampilan Cetak PDF Kuitansi):**
    - Struktur isi disamakan persis dengan isi pop-up Detail Reservasi.
    - Berikan pembatas garis putus-putus yang rapi khas kuitansi pembayaran resmi.
    - Di bagian paling bawah kuitansi, render gambar barcode 1D unik dari ID booking tersebut agar siap di-scan menggunakan kamera di kemudian hari.