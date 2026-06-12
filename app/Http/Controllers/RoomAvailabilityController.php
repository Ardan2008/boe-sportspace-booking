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
        ]);

        $fasilitas = Fasilitas::findOrFail($request->fasilitas_id);

        $checkIn  = $request->check_in_date;
        $checkOut = $request->check_out_date;

        $allRoomNumbers = [];
        $paket = $fasilitas->paket_harian ?: [];
        foreach ($paket as $item) {
            $rooms = $item['nomor_kamar'] ?? [];
            foreach ($rooms as $r) {
                $allRoomNumbers[] = $r;
            }
        }
        $allRoomNumbers = array_unique($allRoomNumbers);

        $bookedRoomNumbers = Booking::where('fasilitas_id', $request->fasilitas_id)
            ->whereIn('status', ['pending', 'confirmed', 'booked'])
            ->where('tgl_mulai', '<=', $checkOut)
            ->where('tgl_selesai', '>=', $checkIn)
            ->whereNotNull('allocated_rooms')
            ->get()
            ->filter(function ($b) use ($fasilitas, $allRoomNumbers) {
                $totalKamar = count($allRoomNumbers) > 0 ? count($allRoomNumbers) : ($fasilitas->jumlah_kamar ?: 1);
                $isMultipleSameSpec = ($fasilitas->tipe === 'lapangan' && $fasilitas->all_same && $totalKamar > 1);
                
                if ($isMultipleSameSpec && $b->package_type === 'harian') {
                    return false;
                }
                return true;
            })
            ->flatMap(fn ($b) => $b->allocated_rooms ?? [])
            ->unique()
            ->values()
            ->toArray();

        $maintenanceRooms = JadwalBlokir::where('fasilitas_id', $request->fasilitas_id)
            ->where('tipe', 'maintenance')
            ->where('tgl_mulai', '<=', $checkOut)
            ->where('tgl_selesai', '>=', $checkIn)
            ->get()
            ->flatMap(fn ($m) => $m->nomor_kamar ?? [])
            ->unique()
            ->values()
            ->toArray();

        $hasFullMaintenance = JadwalBlokir::where('fasilitas_id', $request->fasilitas_id)
            ->where('tipe', 'maintenance')
            ->where('tgl_mulai', '<=', $checkOut)
            ->where('tgl_selesai', '>=', $checkIn)
            ->whereNull('nomor_kamar')
            ->exists();

        $excludedRooms = array_unique(array_merge($bookedRoomNumbers, $maintenanceRooms));

        if ($hasFullMaintenance) {
            $availableRooms = [];
        } else {
            $availableRooms = array_values(array_diff($allRoomNumbers, $excludedRooms));
        }

        return response()->json([
            'success'               => true,
            'total_kamar_tersedia'  => count($availableRooms),
            'nomor_kamar_tersedia'  => $availableRooms,
        ]);
    }
}
