<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Disetujui</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f4f6f9;">
    <table cellpadding="0" cellspacing="0" width="100%" style="background:#f4f6f9;padding:30px 0;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
                    <tr>
                        <td style="background:#1265A8;padding:30px 40px;text-align:center;">
                            <h1 style="color:#fff;margin:0;font-size:24px;letter-spacing:1px;">BOE Sport Space</h1>
                            <p style="color:rgba(255,255,255,0.85);margin:6px 0 0;font-size:13px;">Konfirmasi Persetujuan Booking</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:35px 40px 20px;">
                            <h2 style="color:#333;margin:0 0 6px;font-size:20px;">Halo, <strong>{{ $booking->penyewa->nama }}</strong>!</h2>
                            <p style="color:#555;line-height:1.7;margin:0 0 20px;">
                                Terima kasih telah memilih <strong>BOE Sport Space</strong> untuk kebutuhan reservasi Anda.
                                Kami dengan senang hati memberitahukan bahwa permohonan booking Anda telah <strong style="color:#22c55e;">DISETUJUI</strong>.
                            </p>

                            <table cellpadding="0" cellspacing="0" style="width:100%;background:#f8fafc;border-radius:12px;padding:18px 22px;margin-bottom:22px;">
                                <tr>
                                    <td style="font-size:13px;color:#888;padding-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;" colspan="2">Detail Reservasi</td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px;color:#555;padding:5px 0;width:40%;">ID Booking</td>
                                    <td style="font-size:14px;color:#333;font-weight:700;padding:5px 0;">#BOE-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px;color:#555;padding:5px 0;">Fasilitas</td>
                                    <td style="font-size:14px;color:#333;font-weight:700;padding:5px 0;">{{ $booking->fasilitas->nama }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px;color:#555;padding:5px 0;">Tanggal Mulai</td>
                                    <td style="font-size:14px;color:#333;font-weight:700;padding:5px 0;">{{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px;color:#555;padding:5px 0;">Tanggal Selesai</td>
                                    <td style="font-size:14px;color:#333;font-weight:700;padding:5px 0;">{{ \Carbon\Carbon::parse($booking->tgl_selesai)->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px;color:#555;padding:5px 0;">Tipe Paket</td>
                                    <td style="font-size:14px;color:#333;font-weight:700;padding:5px 0;">{{ ucfirst($booking->package_type) }}</td>
                                </tr>
                                @if($booking->nomor_lapangan)
                                <tr>
                                    <td style="font-size:14px;color:#555;padding:5px 0;">Nomor Lapangan</td>
                                    <td style="font-size:14px;color:#333;font-weight:700;padding:5px 0;">
                                        {{ is_array($booking->nomor_lapangan) ? implode(', ', $booking->nomor_lapangan) : $booking->nomor_lapangan }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="font-size:14px;color:#555;padding:5px 0;">Total Biaya</td>
                                    <td style="font-size:16px;color:#1265A8;font-weight:800;padding:5px 0;">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px;color:#555;padding:5px 0;">Tanggal Disetujui</td>
                                    <td style="font-size:14px;color:#22c55e;font-weight:700;padding:5px 0;">{{ $actionDate }}</td>
                                </tr>
                            </table>

                            <p style="color:#555;line-height:1.7;margin:0 0 6px;">
                                Bersama email ini kami lampirkan <strong>Kuitansi (PDF)</strong> sebagai bukti persetujuan resmi.
                                Silakan unduh dan simpan kwitansi tersebut untuk keperluan Anda.
                            </p>
                            <p style="color:#555;line-height:1.7;margin:0 0 22px;">
                                Jika ada kesalahan data atau pertanyaan lebih lanjut, jangan ragu untuk menghubungi tim dukungan kami.
                            </p>

                            <p style="color:#333;line-height:1.7;margin:0;font-size:15px;">
                                Salam hangat,<br>
                                <strong style="color:#1265A8;">Tim Manajemen BOE Sport Space</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f1f5f9;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
                            <p style="margin:0;font-size:12px;color:#94a3b8;">
                                BOE Sport Space &mdash; Sistem Booking Lapangan Olahraga
                            </p>
                            <p style="margin:4px 0 0;font-size:12px;color:#94a3b8;">
                                Jika ada kesalahan data, hubungi admin di
                                <a href="mailto:{{ $booking->fasilitas->email ?? 'support@boesportspace.com' }}" style="color:#1265A8;text-decoration:none;">
                                    {{ $booking->fasilitas->email ?? 'support@boesportspace.com' }}
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
