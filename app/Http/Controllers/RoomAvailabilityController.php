<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Fasilitas;
use App\Models\GlobalRoomType;

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

        if ($request->has('tipe_kamar_id')) {
            $request->validate(['tipe_kamar_id' => 'required|exists:global_room_types,id']);
            $roomType = GlobalRoomType::findOrFail($request->tipe_kamar_id);
            $typeName = $roomType->name;
            $tipeKamarId = $request->tipe_kamar_id;
        } elseif ($request->has('tipe_kamar_nama')) {
            $typeName = $request->tipe_kamar_nama;
            $roomType = GlobalRoomType::where('name', $typeName)->first();
            $tipeKamarId = $roomType ? $roomType->id : null;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tipe_kamar_id atau tipe_kamar_nama diperlukan.',
            ], 422);
        }

        $checkIn  = $request->check_in_date;
        $checkOut = $request->check_out_date;

        $paket = $fasilitas->paket_harian ?: [];
        $matchingType = null;
        foreach ($paket as $item) {
            if (strtolower(trim($item['tipe'] ?? '')) === strtolower(trim($typeName))) {
                $matchingType = $item;
                break;
            }
        }

        if (!$matchingType) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe kamar tidak ditemukan pada fasilitas ini.',
            ], 404);
        }

        $allRoomNumbers = $matchingType['nomor_kamar'] ?? [];

        $bookedRoomNumbers = Booking::where('fasilitas_id', $request->fasilitas_id)
            ->where('tipe_kamar_id', $tipeKamarId)
            ->whereIn('status', ['pending', 'confirmed', 'booked'])
            ->where('tgl_mulai', '<', $checkOut)
            ->where('tgl_selesai', '>', $checkIn)
            ->whereNotNull('allocated_rooms')
            ->get()
            ->flatMap(fn ($b) => $b->allocated_rooms ?? [])
            ->unique()
            ->values()
            ->toArray();

        $availableRooms = array_values(array_diff($allRoomNumbers, $bookedRoomNumbers));

        return response()->json([
            'success'               => true,
            'total_kamar_tersedia'  => count($availableRooms),
            'nomor_kamar_tersedia'  => $availableRooms,
        ]);
    }
}
