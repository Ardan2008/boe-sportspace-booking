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

            // Hitung jumlah kamar yang sedang dibooking (approved/pending) pada tanggal hari ini
            $bookedCount = Booking::where('fasilitas_id', $item->id)
                ->whereIn('status', ['approved', 'pending', 'confirmed', 'booked'])
                ->where('tgl_mulai', '<=', $today)
                ->where('tgl_selesai', '>=', $today)
                ->count();

            $totalKamar = $item->jumlah_kamar ?? 0;

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