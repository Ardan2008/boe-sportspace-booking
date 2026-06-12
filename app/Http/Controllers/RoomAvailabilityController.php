<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Fasilitas;
use App\Models\JadwalBlokir;

class RoomAvailabilityController extends Controller
{
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'fasilitas_id'   => 'required|exists:fasilitas,id',
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'start_hour'     => 'nullable|integer|min:0|max:23',
            'duration'       => 'nullable|integer|min:1',
        ]);

        $fasilitas = Fasilitas::findOrFail($request->fasilitas_id);
        $reqStartHour = $request->filled('start_hour') ? (int) $request->start_hour : null;
        $reqDuration = (int) ($request->input('duration', 1));

        $checkIn  = $request->check_in_date;
        $checkOut = $request->check_out_date;

        $allRoomNumbers = [];
        $paket = $fasilitas->paket_harian ?: [];
        foreach ($paket as $item) {
            $rooms = $item['nomor_lapangan'] ?? [];
            foreach ($rooms as $r) {
                $allRoomNumbers[] = $r;
            }
        }
        $allRoomNumbers = array_unique($allRoomNumbers);

        // Fallback ketika nomor_lapangan tidak didefinisikan di paket_harian
        if (empty($allRoomNumbers)) {
            $total = $fasilitas->jumlah_lapangan ?: 1;
            $allRoomNumbers = range(1, $total);
        }

        $overlappingBookings = Booking::where('fasilitas_id', $request->fasilitas_id)
            ->whereIn('status', ['pending', 'confirmed', 'booked'])
            ->where('tgl_mulai', '<=', $checkOut)
            ->where('tgl_selesai', '>=', $checkIn)
            ->get()
            ->filter(function ($b) use ($fasilitas, $allRoomNumbers, $reqStartHour, $reqDuration) {
                $totalKamar = count($allRoomNumbers) > 0 ? count($allRoomNumbers) : ($fasilitas->jumlah_lapangan ?: 1);
                $isMultipleSameSpec = ($fasilitas->tipe === 'lapangan' && $fasilitas->all_same && $totalKamar > 1);
                
                if ($isMultipleSameSpec && $b->package_type === 'harian') {
                    if ($reqStartHour === null) return false;
                    $reqEndH = (int) $reqStartHour + (int) $reqDuration;
                    $ebStartH = (int) ($b->selected_packages['start_hour'] ?? 0);
                    $ebEndH = $ebStartH + (int) ($b->selected_packages['duration'] ?? 1);
                    if ($reqEndH <= $ebStartH || $reqStartHour >= $ebEndH) return false;
                }
                return true;
            });

        // Booking dengan allocated_rooms → room-nya sudah pasti terpakai
        $bookedRoomNumbers = $overlappingBookings
            ->filter(fn ($b) => !empty($b->allocated_rooms))
            ->flatMap(fn ($b) => $b->allocated_rooms)
            ->unique()
            ->values()
            ->toArray();

        // Booking tanpa allocated_rooms → kurangi kapasitas (placeholder)
        $placeholderCount = $overlappingBookings
            ->filter(fn ($b) => empty($b->allocated_rooms))
            ->sum(function ($b) {
                return (int) ($b->selected_packages['rooms'] ?? 1);
            });

        $maintenanceRooms = JadwalBlokir::where('fasilitas_id', $request->fasilitas_id)
            ->where('tipe', 'maintenance')
            ->where('tgl_mulai', '<=', $checkOut)
            ->where('tgl_selesai', '>=', $checkIn)
            ->get()
            ->flatMap(fn ($m) => $m->nomor_lapangan ?? [])
            ->unique()
            ->values()
            ->toArray();

        $hasFullMaintenance = JadwalBlokir::where('fasilitas_id', $request->fasilitas_id)
            ->where('tipe', 'maintenance')
            ->where('tgl_mulai', '<=', $checkOut)
            ->where('tgl_selesai', '>=', $checkIn)
            ->whereNull('nomor_lapangan')
            ->exists();

        $excludedRooms = array_unique(array_merge($bookedRoomNumbers, $maintenanceRooms));

        if ($hasFullMaintenance) {
            $availableRooms = [];
        } else {
            $available = array_values(array_diff($allRoomNumbers, $excludedRooms));
            $availableRooms = array_slice($available, $placeholderCount);
        }

        return response()->json([
            'success'                => true,
            'total_lapangan_tersedia' => count($availableRooms),
            'nomor_lapangan_tersedia' => $availableRooms,
        ]);
    }
}
