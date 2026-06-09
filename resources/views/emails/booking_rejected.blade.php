<!DOCTYPE html>
<html>
<head>
    <title>Booking Ditolak</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Halo, {{ $booking->penyewa->nama }},</h2>
    
    <p>Terima kasih atas permohonan reservasi Anda di <strong>BOE Sport Space</strong>.</p>

    <p>Mohon maaf, permohonan booking Anda untuk fasilitas <strong>{{ $booking->fasilitas->nama }}</strong> pada tanggal {{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d M Y') }} terpaksa kami <strong>TOLAK</strong>.</p>
    
    <h3>Alasan Penolakan:</h3>
    <p style="padding: 10px; background-color: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb; border-radius: 4px;">
        {{ $reason }}
    </p>

    <p>Kami memohon maaf atas ketidaknyamanan ini. Silakan mengajukan permohonan jadwal baru pada tanggal yang berbeda, atau hubungi Admin kami untuk informasi lebih lanjut.</p>

    <p>Terima kasih atas pengertiannya,<br>
    Tim BOE Sport Space</p>
</body>
</html>
