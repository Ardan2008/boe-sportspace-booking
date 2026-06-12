<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Reservasi - BOE Sport Space</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 750px; margin: auto; padding: 20px; }
        .header { text-align: center; padding-bottom: 16px; margin-bottom: 20px; }
        .header h1 { color: #1265A8; margin: 0; font-size: 22px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 4px 0 0; font-size: 12px; color: #888; }
        .dashed-top { border-top: 2px dashed #ccc; margin-bottom: 18px; padding-top: 18px; }
        .dashed-divider { border-top: 1px dashed #ddd; margin: 16px 0; }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid th { text-align: left; padding: 10px 12px; background: #f8f9fa; border-bottom: 1px solid #e5e7eb; width: 35%; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #1265A8; }
        .info-grid td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 13px; font-weight: 600; }
        .total-row td { font-size: 16px; color: #1265A8; font-weight: 800; }
        .footer { margin-top: 30px; text-align: center; }
        .stamp { display: inline-block; padding: 10px 24px; border: 3px double #22c55e; color: #22c55e; font-weight: 800; transform: rotate(-5deg); text-transform: uppercase; font-size: 14px; letter-spacing: 2px; margin-bottom: 16px; }
        .barcode-wrapper { text-align: center; margin: 20px 0 10px; padding-top: 14px; border-top: 2px dashed #ccc; }
        .barcode-wrapper p { font-size: 11px; color: #999; margin: 6px 0 0; letter-spacing: 1px; }
        .barcode-wrapper table { margin: 0 auto; }
        .signature { margin-top: 24px; font-size: 11px; color: #aaa; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KUITANSI RESERVASI</h1>
            <p>BOE Sport Space &mdash; Sistem Booking Lapangan Olahraga</p>
        </div>

        <div class="dashed-top"></div>

        <table class="info-grid">
            <tr>
                <th>ID Reservasi</th>
                <td>#BOE-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <th>Nama Penyewa</th>
                <td>{{ $booking->penyewa->nama }}</td>
            </tr>
            <tr>
                <th>WhatsApp</th>
                <td>{{ $booking->penyewa->whatsapp }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $booking->penyewa->email }}</td>
            </tr>
            <tr>
                <th>Fasilitas</th>
                <td>{{ $booking->fasilitas->nama }}</td>
            </tr>
            <tr>
                <th>Tanggal Mulai</th>
                <td>{{ \Carbon\Carbon::parse($booking->tgl_mulai)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Tanggal Selesai</th>
                <td>{{ \Carbon\Carbon::parse($booking->tgl_selesai)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Tipe Paket</th>
                <td>{{ ucfirst($booking->package_type) }}</td>
            </tr>
            @if($booking->nomor_lapangan)
            <tr>
                <th>Nomor Lapangan</th>
                <td>{{ is_array($booking->nomor_lapangan) ? implode(', ', $booking->nomor_lapangan) : $booking->nomor_lapangan }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <th>Total Biaya</th>
                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Tanggal Disetujui</th>
                <td>{{ $actionDate ?? \Carbon\Carbon::now()->format('d F Y, H:i \W\I\B') }}</td>
            </tr>
        </table>

        <div class="dashed-divider"></div>

        <div class="footer">
            <div class="stamp">LUNAS / CONFIRMED</div>
        </div>

        <div class="barcode-wrapper">
            {!! \App\Helpers\BarcodeHelper::code128B((string) $booking->id) !!}
            <p>* {{ $booking->id }} *</p>
        </div>

        <div class="signature">
            Dokumen ini sah dikeluarkan melalui sistem elektronik.<br>
            BOE Sport Space &mdash; Sistem Booking Lapangan Olahraga
        </div>
    </div>
</body>
</html>
