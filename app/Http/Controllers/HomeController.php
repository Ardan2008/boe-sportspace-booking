<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Booking;
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
                ->where('tgl_selesai', '>', $today)
                ->count();

            $totalKamar = $item->jumlah_kamar ?? 0;
            $availableStok[$item->id] = max(0, $totalKamar - $bookedCount);
        }

        return view('home', compact('facilities', 'availableStok'));
    }
}