<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\KtpOcrService;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\ApproveBookingMail;
use App\Mail\RejectBookingMail;
use App\Models\AuditLog;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // Verifikasi reCAPTCHA (hanya di production)
        if (app()->environment('production')) {
            $response = Http::asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret'   => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ]
            );

            if (!$response->json('success')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi captcha gagal. Silakan coba lagi.'
                ], 422);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s.\x27\x2D]+$/u',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'tgl_mulai' => 'required|date',
            'package_type' => 'required|in:harian,mingguan,bulanan,tahunan',
            'selected_days' => 'nullable|string|regex:/^[1-7](,[1-7])*$/',
            'duration' => 'required|integer|min:1',
            'rooms_count' => 'required|integer|min:1',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'foto_identitas' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fasilitas = \App\Models\Fasilitas::findOrFail($request->fasilitas_id);
        $totalPrice = 0;
        $tgl_selesai = null;
        $duration = (int)$request->duration;
        $tgl_mulai = $request->tgl_mulai;

        $selectedDays = $request->selected_days
            ? array_map('intval', explode(',', $request->selected_days))
            : [1,2,3,4,5,6,7];
        $sc = count($selectedDays);

        if ($request->package_type === 'harian') {
            $totalPrice = $duration * $fasilitas->harga;
            if ($fasilitas->tipe === 'lapangan') {
                $tgl_selesai = $tgl_mulai;
            } else {
                $tgl_selesai = \Carbon\Carbon::parse($tgl_mulai)->addDays($duration - 1)->format('Y-m-d');
            }
        } elseif ($request->package_type === 'mingguan') {
            $firstRoom = is_array($fasilitas->paket_harian) ? ($fasilitas->paket_harian[0] ?? []) : [];
            $hargaMingguan = isset($firstRoom['harga_mingguan']) && (float)$firstRoom['harga_mingguan'] > 0
                ? (float)$firstRoom['harga_mingguan']
                : $fasilitas->harga * 7;
            $totalPrice = $duration * $hargaMingguan * ($sc / 7);
            $tgl_selesai = \Carbon\Carbon::parse($tgl_mulai)->addWeeks($duration)->subDay()->format('Y-m-d');
        } elseif ($request->package_type === 'bulanan') {
            if (!$fasilitas->harga_bulanan) {
                return response()->json(['success' => false, 'message' => 'Fasilitas ini tidak mendukung paket bulanan.'], 422);
            }
            $totalPrice = $duration * $fasilitas->harga_bulanan * ($sc / 7);
            $tgl_selesai = \Carbon\Carbon::parse($tgl_mulai)->addMonths($duration)->subDay()->format('Y-m-d');
        } elseif ($request->package_type === 'tahunan') {
            $firstRoom = is_array($fasilitas->paket_harian) ? ($fasilitas->paket_harian[0] ?? []) : [];
            $hargaTahunan = isset($firstRoom['harga_tahunan']) && (float)$firstRoom['harga_tahunan'] > 0
                ? (float)$firstRoom['harga_tahunan']
                : ($fasilitas->harga_bulanan ? $fasilitas->harga_bulanan * 12 : $fasilitas->harga * 365);
            $totalPrice = $duration * $hargaTahunan * ($sc / 7);
            $tgl_selesai = \Carbon\Carbon::parse($tgl_mulai)->addYears($duration)->subDay()->format('Y-m-d');
        }

        // --- VALIDASI OVERLAP (per selected day) ---
        $isOverlapping = false;
        $existingBookings = \App\Models\Booking::where('fasilitas_id', $request->fasilitas_id)
            ->whereIn('status', ['pending', 'confirmed', 'booked'])
            ->where(function ($q) use ($tgl_mulai, $tgl_selesai) {
                $q->whereBetween('tgl_mulai', [$tgl_mulai, $tgl_selesai])
                  ->orWhereBetween('tgl_selesai', [$tgl_mulai, $tgl_selesai])
                  ->orWhere(function ($q2) use ($tgl_mulai, $tgl_selesai) {
                      $q2->where('tgl_mulai', '<=', $tgl_mulai)
                         ->where('tgl_selesai', '>=', $tgl_selesai);
                  });
            })
            ->get();

        $startDate = \Carbon\Carbon::parse($tgl_mulai);
        $endDate = \Carbon\Carbon::parse($tgl_selesai);

        foreach ($existingBookings as $eb) {
            $ebDays = $eb->selected_days
                ? array_map('intval', explode(',', $eb->selected_days))
                : [1,2,3,4,5,6,7];
            $ebStart = \Carbon\Carbon::parse($eb->tgl_mulai);
            $ebEnd = \Carbon\Carbon::parse($eb->tgl_selesai);

            $check = $startDate->copy()->max($ebStart);
            $overlapEnd = $endDate->copy()->min($ebEnd);

            while ($check <= $overlapEnd) {
                if (in_array($check->dayOfWeekIso(), $selectedDays)
                    && in_array($check->dayOfWeekIso(), $ebDays)) {
                    $isOverlapping = true;
                    break 2;
                }
                $check->addDay();
            }
        }

        $blockedRecords = \App\Models\JadwalBlokir::where('fasilitas_id', $request->fasilitas_id)
            ->where(function ($q) use ($tgl_mulai, $tgl_selesai) {
                $q->whereBetween('tgl_mulai', [$tgl_mulai, $tgl_selesai])
                  ->orWhereBetween('tgl_selesai', [$tgl_mulai, $tgl_selesai])
                  ->orWhere(function ($q2) use ($tgl_mulai, $tgl_selesai) {
                      $q2->where('tgl_mulai', '<=', $tgl_mulai)
                         ->where('tgl_selesai', '>=', $tgl_selesai);
                  });
            })
            ->get();

        $isBlocked = false;
        $requestedRooms = $request->input('allocated_rooms', []);
        foreach ($blockedRecords as $br) {
            $brRooms = $br->nomor_kamar;
            if (empty($brRooms)) {
                $isBlocked = true;
                break;
            }
            if (!empty($requestedRooms)) {
                $conflict = array_intersect($brRooms, $requestedRooms);
                if (!empty($conflict)) {
                    $isBlocked = true;
                    break;
                }
            } else {
                $isBlocked = true;
                break;
            }
        }

        if ($isOverlapping || $isBlocked) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, rentang tanggal yang Anda pilih sudah tidak tersedia atau telah digunakan oleh pemesan lain.'
            ], 422);
        }

        $identitasPath = null;
        if ($request->hasFile('foto_identitas')) {
            $file = $request->file('foto_identitas');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $identitasPath = $file->storeAs('ktp', $filename, 'public');

            // Hanya verifikasi OCR di production
            if (app()->environment('production')) {
                $ocr = new KtpOcrService();
                $result = $ocr->verifyName($identitasPath, $request->name);

                if (!$result['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 422);
                }
            }
        }

        // --- AUTO-ALLOCATE ROOMS at submission time ---
        $allocatedRooms = [];
        $frontendAllocated = $request->input('allocated_rooms', []);

        if (!empty($frontendAllocated) && is_array($frontendAllocated)) {
            $roomsNeeded    = (int) $request->rooms_count;
            $allocatedRooms = array_slice($frontendAllocated, 0, $roomsNeeded);
        } elseif ($fasilitas->paket_harian) {
            $allRoomNumbers = [];
            foreach ($fasilitas->paket_harian as $item) {
                $rooms = $item['nomor_kamar'] ?? [];
                foreach ($rooms as $r) {
                    $allRoomNumbers[] = $r;
                }
            }
            $allRoomNumbers = array_unique($allRoomNumbers);

            if (!empty($allRoomNumbers)) {
                $alreadyAllocated = \App\Models\Booking::where('fasilitas_id', $request->fasilitas_id)
                    ->whereIn('status', ['pending', 'confirmed', 'booked'])
                    ->where('tgl_mulai', '<', $tgl_selesai)
                    ->where('tgl_selesai', '>', $tgl_mulai)
                    ->whereNotNull('allocated_rooms')
                    ->get()
                    ->flatMap(fn ($b) => $b->allocated_rooms ?? [])
                    ->unique()
                    ->values()
                    ->toArray();

                $available      = array_values(array_diff($allRoomNumbers, $alreadyAllocated));
                $roomsNeeded    = (int) $request->rooms_count;
                $allocatedRooms = array_slice($available, 0, $roomsNeeded);
            }
        }

        // Create renter
        $penyewa = \App\Models\Penyewa::create([
            'nama' => $request->name,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'foto_identitas' => $identitasPath,
        ]);

        $booking = \App\Models\Booking::create([
            'penyewa_id'       => $penyewa->id,
            'fasilitas_id'     => $request->fasilitas_id,
            'nomor_kamar'      => !empty($allocatedRooms) ? $allocatedRooms : null,
            'allocated_rooms'  => !empty($allocatedRooms) ? $allocatedRooms : null,
            'tgl_mulai'        => $request->tgl_mulai,
            'tgl_selesai'      => $tgl_selesai,
            'package_type'     => $request->package_type,
            'selected_days'    => $request->package_type !== 'harian' ? $request->selected_days : null,
            'selected_packages' => [
                'duration'    => $duration,
                'adults'      => 1,
                'rooms'       => $request->rooms_count,
                'start_hour'  => $request->start_hour,
                'kode_blok'   => $request->selected_kode_blok ?? null,
                'tipe_id'     => $request->tipe_id !== null ? (int)$request->tipe_id : null,
            ],
            'total_harga' => $totalPrice,
            'status'      => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservasi Anda telah berhasil dikirim! Silakan tunggu konfirmasi admin.'
        ]);
    }


    public function approve($id)
    {
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);

        // --- AUTO-ALLOCATE ROOMS on approval if not yet allocated ---
        $updateData = [];
        if (empty($booking->allocated_rooms) && $booking->fasilitas) {
            $fasilitas = $booking->fasilitas;

            $allRoomNumbers = [];
            foreach (($fasilitas->paket_harian ?: []) as $item) {
                $rooms = $item['nomor_kamar'] ?? [];
                foreach ($rooms as $r) {
                    $allRoomNumbers[] = $r;
                }
            }
            $allRoomNumbers = array_unique($allRoomNumbers);

            if (!empty($allRoomNumbers)) {
                $alreadyAllocated = \App\Models\Booking::where('fasilitas_id', $booking->fasilitas_id)
                    ->where('id', '!=', $booking->id)
                    ->whereIn('status', ['pending', 'confirmed', 'booked'])
                    ->where('tgl_mulai', '<', $booking->tgl_selesai)
                    ->where('tgl_selesai', '>', $booking->tgl_mulai)
                    ->whereNotNull('allocated_rooms')
                    ->get()
                    ->flatMap(fn ($b) => $b->allocated_rooms ?? [])
                    ->unique()
                    ->values()
                    ->toArray();

                $available = array_values(array_diff($allRoomNumbers, $alreadyAllocated));

                $selectedPackages = ($booking->selected_packages ?? []);
                $roomsNeeded = (int) ($selectedPackages['rooms'] ?? 1);
                $allocated   = array_slice($available, 0, $roomsNeeded);

                if (!empty($allocated)) {
                    $updateData['allocated_rooms'] = $allocated;
                    $updateData['nomor_kamar']     = $allocated;
                }
            }
        }

        // Calculate expiration based on province (JAWA TIMUR = 1 day, others = 3 days)
        $isJatim   = strtoupper($booking->penyewa->provinsi ?? '') === 'JAWA TIMUR';
        $expiredAt = $isJatim ? now()->addDays(1) : now()->addDays(3);

        $booking->update(array_merge($updateData, [
            'status'     => 'confirmed',
            'expired_at' => $expiredAt,
        ]));

        $actionDate = \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';

        $penyewaEmail = $booking->penyewa->email ?? null;
        $penyewaNama  = $booking->penyewa->nama ?? 'Unknown';
        
        // Send Email real-time (not queued)
        if ($penyewaEmail) {
            try {
                Mail::to($penyewaEmail)->send(new ApproveBookingMail($booking, $actionDate));
            } catch (\Exception $e) {
                \Log::error("Failed to send approval email for booking #{$id}: " . $e->getMessage());
            }
        }

        $publicReceiptUrl = route('public.receipt', $booking->id);
        $fasilitasNama = $booking->fasilitas->nama ?? '-';

        // Audit Log
        AuditLog::catat(
            'Approve Booking',
            "Menyetujui reservasi #{$id} atas nama {$penyewaNama}.",
            [
                'target_tipe'    => 'booking',
                'target_id'      => $id,
                'fasilitas_nama' => $fasilitasNama,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking #' . $id . ' telah disetujui! Email telah dikirim.',
            'name' => $penyewaNama,
            'phone' => $booking->penyewa->whatsapp ?? '-',
            'booking_id' => $id,
            'public_receipt_url' => $publicReceiptUrl
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $booking = \App\Models\Booking::with('penyewa')->findOrFail($id);
        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);

        $actionDate = \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';

        $penyewaEmail = $booking->penyewa->email ?? null;
        $penyewaNama = $booking->penyewa->nama ?? 'Unknown';

        // Send Email using Mail facade safely
        if ($penyewaEmail) {
            try {
                Mail::to($penyewaEmail)->send(new RejectBookingMail($booking, $request->reason, $actionDate));
            } catch (\Exception $e) {
                \Log::error("Failed to send rejection email for booking #{$id}: " . $e->getMessage());
            }
        }

        // Audit Log
        AuditLog::catat(
            'Reject Booking',
            "Menolak reservasi #{$id} ({$penyewaNama}) Alasan: {$request->reason}",
            [
                'target_tipe'    => 'booking',
                'target_id'      => $id,
                'fasilitas_nama' => $booking->fasilitas->nama ?? '-',
                'reason'         => $request->reason,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking #' . $id . ' telah ditolak dengan alasan: ' . $request->reason,
            'name' => $penyewaNama,
            'phone' => $booking->penyewa->whatsapp ?? '-',
            'reason' => $request->reason
        ]);
    }

    public function publicReceipt($id)
    {
        // Public method to stream the receipt for sharing via WA link
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);

        if ($booking->status !== 'confirmed') {
            abort(403, 'Kwitansi ini belum valid untuk diunduh karena belum disetujui.');
        }

        $pdf = Pdf::loadView('pdf.receipt', compact('booking'));
        
        return $pdf->stream('Kwitansi_BOE_' . $booking->id . '.pdf');
    }

    public function show($id)
    {
        try {
            $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);

            $foto_identitas_url = null;
            if ($booking->penyewa && $booking->penyewa->foto_identitas) {
                $foto_identitas_url = asset('storage/' . $booking->penyewa->foto_identitas);
            }

            $rooms_data = null;
            if ($booking->fasilitas && $booking->fasilitas->paket_harian) {
                $allRooms       = $booking->fasilitas->paket_harian;
                $allocatedRooms = $booking->allocated_rooms ?? [];
                $allSame        = (bool) ($booking->fasilitas->all_same ?? false);

                if (!empty($allocatedRooms) && !$allSame) {
                    // Filter hanya rooms yang nomor_kamarnya bersinggungan dengan allocated_rooms
                    $rooms_data = array_values(array_filter($allRooms, function ($room) use ($allocatedRooms) {
                        $nomorKamar = $room['nomor_kamar'] ?? [];
                        if (empty($nomorKamar)) return false;
                        return count(array_intersect((array)$nomorKamar, (array)$allocatedRooms)) > 0;
                    }));
                    // Kalau tidak ada yang match (nomor_kamar belum diisi), fallback semua
                    if (empty($rooms_data)) {
                        $rooms_data = $allRooms;
                    }
                } else {
                    // allSame atau tidak ada allocated_rooms → tampilkan room[0] saja sebagai representasi
                    $rooms_data = $allSame ? [$allRooms[0]] : $allRooms;
                }
            }

            return response()->json([
                'success' => true,
                'id_raw' => $booking->id,
                'id' => '#BOE-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT),
                'nama' => $booking->penyewa?->nama ?? 'Data Hilang',
                'email' => $booking->penyewa?->email ?? '-',
                'whatsapp' => $booking->penyewa?->whatsapp ?? '-',
                'provinsi' => $booking->penyewa?->provinsi ?? 'Belum Diatur',
                'kabupaten' => $booking->penyewa?->kabupaten ?? 'Belum Diatur',
                'tgl_mulai' => $booking->tgl_mulai ? \Carbon\Carbon::parse($booking->tgl_mulai)->format('Y-m-d') : null,
                'tgl_selesai' => $booking->tgl_selesai ? \Carbon\Carbon::parse($booking->tgl_selesai)->format('Y-m-d') : null,
                'fasilitas' => $booking->fasilitas?->nama ?? 'Fasilitas Hilang',
                'fasilitas_tipe' => $booking->fasilitas?->tipe ?? '-',
                'package' => $booking->package_type,
                'status' => $booking->status,
                'total' => 'Rp ' . number_format($booking->total_harga, 0, ',', '.'),
                'details' => $booking->selected_packages ?? [],
                'rooms_data' => $rooms_data,
                'nomor_kamar' => is_array($booking->nomor_kamar)
                    ? implode(', ', $booking->nomor_kamar)
                    : ($booking->nomor_kamar ?? '-'),
                'allocated_rooms' => $booking->allocated_rooms ?? [],
                'created_at' => $booking->created_at ? $booking->created_at->format('d F Y, H:i') . ' WIB' : '-',
                'checkin_at' => $booking->checkin_at ? $booking->checkin_at->format('d F Y, H:i') . ' WIB' : null,
                'foto_identitas' => $foto_identitas_url
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data reservasi tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function management()
    {
        $pendingBookings = \App\Models\Booking::with(['penyewa', 'fasilitas'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $confirmedBookings = \App\Models\Booking::with(['penyewa', 'fasilitas'])
            ->where('status', 'confirmed')
            ->orderBy('updated_at', 'desc')
            ->get();

        $bookedBookings = \App\Models\Booking::with(['penyewa', 'fasilitas'])
            ->where('status', 'booked')
            ->orderBy('checkin_at', 'desc')
            ->get();
            
        return view('admin.dashboard.managementBooking', compact('pendingBookings', 'confirmedBookings', 'bookedBookings'));
    }

    public function cancel($id)
    {
        $booking = \App\Models\Booking::with('penyewa')->findOrFail($id);
        
        if ($booking->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya booking yang sudah disetujui yang dapat dibatalkan.'
            ]);
        }
        
        $booking->update([
            'status' => 'cancelled',
            'rejection_reason' => 'Dibatalkan/ditarik secara manual oleh Admin'
        ]);
        
        $fasilitasNama = $booking->fasilitas->nama ?? '-';
        $penyewaNama = $booking->penyewa->nama ?? 'Unknown';

        // Audit Log
        AuditLog::catat(
            'Cancel Booking',
            "Membatalkan reservasi #{$id} atas nama {$penyewaNama}.",
            [
                'target_tipe'    => 'booking',
                'target_id'      => $id,
                'fasilitas_nama' => $fasilitasNama,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Reservasi berhasil dibatalkan sepihak. Tanggal kembali ke status Ready.'
        ]);
    }

    public function extend($id)
    {
        $booking = \App\Models\Booking::with('penyewa')->findOrFail($id);
        
        if ($booking->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya reservasi Confirmed yang bisa diperpanjang.'
            ]);
        }

        // Add 1 day to the current expired_at, or if null, from now
        $newExpiry = $booking->expired_at ? $booking->expired_at->addDays(1) : now()->addDays(1);

        $booking->update([
            'expired_at' => $newExpiry
        ]);

        AuditLog::catat(
            'Extend Deadline',
            "Memperpanjang batas waktu pembayaran reservasi #{$id} sebanyak 1 Hari.",
            [
                'target_tipe' => 'booking',
                'target_id'   => $id
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Batas kadaluarsa berhasil diperpanjang 1 Hari.'
        ]);
    }

    public function checkIn($id)
    {
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);
        
        if ($booking->status !== 'confirmed') {
            return response()->json(['success' => false, 'message' => 'Hanya booking berstatus Confirmed yang bisa Check-In.']);
        }

        $booking->update([
            'status' => 'booked',
            'checkin_at' => now()
        ]);

        AuditLog::catat(
            'Confirm Check-In',
            "Check-In berhasil untuk reservasi #{$id} atas nama {$booking->penyewa->nama}.",
            ['target_tipe' => 'booking', 'target_id' => $id, 'fasilitas_nama' => $booking->fasilitas->nama]
        );

        return response()->json(['success' => true, 'message' => 'Check-In berhasil! Status beralih ke Booked.']);
    }

    public function checkOut($id)
    {
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);
        
        if ($booking->status !== 'booked') {
            return response()->json(['success' => false, 'message' => 'Hanya booking berstatus Booked yang bisa Check-Out.']);
        }

        $booking->update(['status' => 'completed']);

        AuditLog::catat(
            'Confirm Check-Out',
            "Check-Out berhasil untuk reservasi #{$id} atas nama {$booking->penyewa->nama}.",
            ['target_tipe' => 'booking', 'target_id' => $id, 'fasilitas_nama' => $booking->fasilitas->nama]
        );

        return response()->json(['success' => true, 'message' => 'Check-Out berhasil! Data telah diarsipkan ke Riwayat.']);
    }

    public function extendStay(Request $request, $id)
    {
        $booking = \App\Models\Booking::with(['penyewa', 'fasilitas'])->findOrFail($id);
        
        if ($booking->status !== 'booked') {
            return response()->json(['success' => false, 'message' => 'Hanya tamu aktif (Booked) yang bisa diperpanjang masa sewanya.']);
        }

        $days = (int) $request->days;
        if ($days < 1) {
            return response()->json(['success' => false, 'message' => 'Durasi perpanjangan minimal 1 hari.']);
        }

        $currentEnd = \Carbon\Carbon::parse($booking->tgl_selesai);
        $newEnd = $currentEnd->addDays($days);
        
        // Simple logic for cost update
        // Use daily rate for extensions
        $extraCost = $days * $booking->fasilitas->harga;

        $booking->update([
            'tgl_selesai' => $newEnd->format('Y-m-d'),
            'total_harga' => $booking->total_harga + $extraCost
        ]);

        AuditLog::catat(
            'Extend Stay',
            "Memperpanjang masa sewa #{$id} sebanyak {$days} hari. Total biaya diperbarui.",
            ['target_tipe' => 'booking', 'target_id' => $id]
        );

        return response()->json(['success' => true, 'message' => "Masa sewa berhasil diperpanjang {$days} hari."]);
    }
}
