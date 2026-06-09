<!DOCTYPE html>
<html>
<head>
    <title>Booking Disetujui</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Halo, {{ $booking->penyewa->nama }}!</h2>
    
    <p>Terima kasih telah melakukan reservasi di <strong>BOE Sport Space</strong>.</p>

    <p>Kami sangat senang memberitahukan bahwa permohonan booking Anda untuk fasilitas <strong>{{ $booking->fasilitas->nama }}</strong> telah <strong>DISETUJUI</strong>.</p>
    
    <h3>Detail Booking:</h3>
    <ul>
        <li><strong>Fasilitas:</strong> {{ $booking->fasilitas->nama }}</li>
        <li><strong>Mulai:</strong> {{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d M Y') }}</li>
        <li><strong>Selesai:</strong> {{ \Carbon\Carbon::parse($booking->tgl_selesai)->format('d M Y') }}</li>
        <li><strong>Total Estimasi Harga:</strong> Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</li>
    </ul>

    <p>Bersama email ini kami telah melampirkan <strong>Kwitansi (PDF)</strong> bukti persetujuan booking Anda.</p>
    
    <p>Silakan unduh dan simpan kwitansi tersebut sebagai bukti pemesanan yang sah.</p>

    <p>Terima kasih,<br>
    Tim BOE Sport Space</p>
</body>
</html>
