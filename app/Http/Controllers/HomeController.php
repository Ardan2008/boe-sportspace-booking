<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Booking;
use App\Models\JadwalBlokir;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua data fasilitas dari database
        $facilities = Fasilitas::all();

        // Hitung stok kamar tersedia per fasilitas asrama (kurangi booking approved/pending aktif hari ini)
        $today = now()->toDateString();
        $availableStok = [];

        foreach ($facilities as $item) {
            if ($item->tipe !== 'asrama') {
                $availableStok[$item->id] = null; // bukan asrama, tidak perlu hitung
                continue;
            }

            // Hitung total kamar dari paket_harian (semua tipe)
            $totalKamar = 0;
            if (is_array($item->paket_harian)) {
                foreach ($item->paket_harian as $rt) {
                    $totalKamar += count($rt['nomor_kamar'] ?? []);
                }
            }
            if ($totalKamar === 0) $totalKamar = $item->jumlah_kamar ?? 0;

            // Hitung jumlah kamar yang sedang dibooking hari ini
            $activeBookings = Booking::where('fasilitas_id', $item->id)
                ->whereIn('status', ['pending', 'confirmed', 'booked'])
                ->where('tgl_mulai', '<=', $today)
                ->where('tgl_selesai', '>=', $today)
                ->get();

            $bookedRooms = [];
            $untypedRoomCount = 0;
            foreach ($activeBookings as $b) {
                $rooms = $b->allocated_rooms;
                if (!empty($rooms) && is_array($rooms)) {
                    foreach ($rooms as $r) {
                        $bookedRooms[strval($r)] = true;
                    }
                } else {
                    $packages = ($b->selected_packages ?? []);
                    $untypedRoomCount += (int) ($packages['rooms'] ?? 1);
                }
            }
            $bookedCount = count($bookedRooms) + $untypedRoomCount;

            $activeMaintenance = JadwalBlokir::where('fasilitas_id', $item->id)
                ->where('tipe', 'maintenance')
                ->where('tgl_mulai', '<=', $today)
                ->where('tgl_selesai', '>=', $today)
                ->get();

            $blockedRooms = [];
            $fullMaintenance = false;
            foreach ($activeMaintenance as $m) {
                $rooms = $m->nomor_kamar;
                if (empty($rooms)) {
                    $fullMaintenance = true;
                    break;
                }
                foreach ((array) $rooms as $nr) {
                    $blockedRooms[$nr] = true;
                }
            }

            if ($fullMaintenance) {
                $availableStok[$item->id] = 0;
            } else {
                $availableStok[$item->id] = max(0, $totalKamar - $bookedCount - count($blockedRooms));
            }
        }

        return view('home', compact('facilities', 'availableStok'));
    }
}