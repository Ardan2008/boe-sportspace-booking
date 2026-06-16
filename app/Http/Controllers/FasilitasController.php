<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas; 
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class FasilitasController extends Controller
{

    public function index()
    {
        // Mengambil semua data dari model Facility
        $facilities = Fasilitas::all(); 

        $today = now()->startOfDay();
        foreach ($facilities as $f) {
            $f->is_maintenance = \App\Models\JadwalBlokir::where('fasilitas_id', $f->id)
                ->where('tipe', 'maintenance')
                ->where('tgl_selesai', '>=', $today)
                ->exists();
        }

        // Pastikan nama variabel di compact('facilities') sesuai dengan @foreach($facilities as $item)
        return view('admin.dashboard.dataFasilitas', compact('facilities'));
    }

    public function update(Request $request, int $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:lapangan,kolam_renang',
            'deskripsi' => 'required',
            'detail' => 'nullable',
            'harga' => 'nullable|numeric',
            'harga_bulanan' => 'nullable|numeric',
            'max_dewasa' => 'nullable|integer',
            'max_anak' => 'nullable|integer',
            'max_durasi_harian' => 'nullable|integer',
            'max_durasi_hari' => 'nullable|integer|min:0',
            'max_durasi_minggu' => 'nullable|integer|min:0',
            'max_durasi_bulan' => 'nullable|integer|min:0',
            'max_durasi_tahun' => 'nullable|integer|min:0',
            'jam_operasional' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'room_fotos' => 'nullable|array',
            'room_fotos.*' => 'nullable|array',
            'room_fotos.*.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'paket_harian' => 'nullable|string',
            'rooms_data'   => 'nullable|string',
            'jumlah_lapangan' => 'nullable|integer|min:1',
            'labels' => 'nullable|array',
        ]);

        $oldHarga = $fasilitas->harga;

        // paket_harian contains the full rooms payload from the Alpine.js syncPaketHarian()
        $paket_harian = $request->paket_harian ? json_decode($request->paket_harian, true) : [];
        if (!is_array($paket_harian)) {
            $paket_harian = [];
        }

        // Derive canonical harga from first room's harga_harian (prices live in paket_harian now)
        $firstRoom = $paket_harian[0] ?? [];
        $h_harian  = isset($firstRoom['harga_harian'])  && (float) $firstRoom['harga_harian']  > 0
                        ? (float) $firstRoom['harga_harian']
                        : (float) ($request->harga ?? $oldHarga);
        $h_bulanan = isset($firstRoom['harga_bulanan']) && (float) $firstRoom['harga_bulanan'] > 0
                        ? (float) $firstRoom['harga_bulanan']
                        : (float) ($request->harga_bulanan ?? 0);

        $newHarga = $h_harian;

        // Record price history only when harga_harian actually changed
        if ($oldHarga != $newHarga && $newHarga > 0) {
            $diff = $newHarga - $oldHarga;
            $percent = ($oldHarga != 0) ? ($diff / $oldHarga) * 100 : 100;
            $percentFormatted = ($percent > 0 ? '+' : '') . round($percent) . '%';

            \App\Models\HargaSewaHistory::create([
                'fasilitas_id' => $fasilitas->id,
                'harga_lama' => $oldHarga,
                'harga_baru' => $newHarga,
                'persen_perubahan' => $percentFormatted,
            ]);
        }

        // Calculate thumbnail price range from all rooms' all price tiers
        $allPrices = [];
        foreach ($paket_harian as $room) {
            foreach (['harga_harian','harga_mingguan','harga_bulanan','harga_tahunan'] as $pKey) {
                $v = isset($room[$pKey]) ? (float) $room[$pKey] : 0;
                if ($v > 0) $allPrices[] = $v;
            }
        }
        if (empty($allPrices)) {
            $allPrices = [$h_harian];
            if ($h_bulanan > 0) $allPrices[] = $h_bulanan;
        }
        $minPrice = min($allPrices);
        $maxPrice = max($allPrices);

        $formatPrice = function($price) {
            if ($price >= 1000000) return round($price / 1000000, 1) . 'JT';
            if ($price >= 1000) return round($price / 1000) . 'K';
            return $price;
        };

        $harga_thumbnail = ($minPrice !== $maxPrice) 
            ? "Mulai " . $formatPrice($minPrice) . " - " . $formatPrice($maxPrice)
            : "Rp " . number_format($newHarga, 0, ',', '.');

        $data = [
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'detail' => $request->detail,
            'harga' => $newHarga,
            'harga_bulanan' => $h_bulanan > 0 ? $h_bulanan : null,
            'max_dewasa' => $request->max_dewasa,
            'max_anak' => $request->max_anak,
            'max_durasi_harian' => $request->max_durasi_harian,
            'max_durasi_hari' => $request->max_durasi_hari ? (int) $request->max_durasi_hari : null,
            'max_durasi_minggu' => $request->max_durasi_minggu ? (int) $request->max_durasi_minggu : null,
            'max_durasi_bulan' => $request->max_durasi_bulan ? (int) $request->max_durasi_bulan : null,
            'max_durasi_tahun' => $request->max_durasi_tahun ? (int) $request->max_durasi_tahun : null,
            'jam_operasional' => $request->jam_operasional,
            'jumlah_lapangan' => $request->jumlah_lapangan ? (int) $request->jumlah_lapangan : $fasilitas->jumlah_lapangan,
            'all_same' => filter_var($request->input('all_same', true), FILTER_VALIDATE_BOOLEAN),
            'labels' => $request->labels ?? [],
            'harga_thumbnail' => $harga_thumbnail,
        ];

        $roomFotoFiles = $request->file('room_fotos');
        if (is_array($roomFotoFiles)) {
            foreach ($roomFotoFiles as $roomIdx => $fotos) {
                if (isset($paket_harian[$roomIdx])) {
                    $existingFoto = isset($paket_harian[$roomIdx]['foto']) && is_array($paket_harian[$roomIdx]['foto'])
                        ? $paket_harian[$roomIdx]['foto']
                        : [];

                    foreach ($fotos as $fIdx => $file) {
                        if ($file && $file->isValid()) {
                            if (isset($existingFoto[$fIdx])) {
                                $oldPath = public_path('storage/fasilitas/rooms/' . $existingFoto[$fIdx]);
                                if (File::exists($oldPath)) File::delete($oldPath);
                            }
                            $name = time() . '_room_' . $roomIdx . '_' . $fIdx . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('storage/fasilitas/rooms'), $name);
                            $existingFoto[$fIdx] = $name;
                        }
                    }

                    $paket_harian[$roomIdx]['foto'] = array_values(array_filter($existingFoto));
                }
            }
        }

        $data['paket_harian'] = $paket_harian;

        if ($request->hasFile('image')) {
            $oldPath = public_path('storage/fasilitas/' . $fasilitas->image);
            if (File::exists($oldPath)) File::delete($oldPath);

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/fasilitas'), $imageName);
            $data['image'] = $imageName;
        }

        // Handle Gallery
        $gallery = $fasilitas->gallery ?? [];
        if ($request->hasFile('gallery')) {
            // Delete old gallery if new ones are uploaded (or just replace, but user said UX 3 boxes)
            // For simplicity, we replace if index matches or just append
            // User requested 3 boxes, so we'll expect array of files
            foreach ($request->file('gallery') as $index => $file) {
                if ($file) {
                    $name = time() . '_gallery_' . $index . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('storage/fasilitas/gallery'), $name);
                    
                    // Replace if exists at index
                    $gallery[$index] = $name;
                }
            }
        }
        $data['gallery'] = array_values(array_filter($gallery));

        $fasilitas->update($data);

        AuditLog::catat(
            'Update Fasilitas',
            "Mengubah data fasilitas: {$fasilitas->nama}",
            ['target_tipe' => 'fasilitas', 'target_id' => $fasilitas->id, 'fasilitas_nama' => $fasilitas->nama]
        );

        if (request()->expectsJson() || request()->header('Accept') === 'application/json') {
            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui!']);
        }

        return redirect()->route('fasilitas.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function edit(int $id) {
        $fasilitas = Fasilitas::findOrFail($id);

        $rooms = [];
        $paketHarian = $fasilitas->paket_harian;
        if (is_array($paketHarian) && !empty($paketHarian)) {
            $rooms = $paketHarian;
            foreach ($rooms as &$room) {
                if (!isset($room['nomor_lapangan'])) $room['nomor_lapangan'] = [];
                if (!isset($room['temp_input']))   $room['temp_input']  = '';
            }
            unset($room);
        } else {
            $count = $fasilitas->jumlah_lapangan ?? 1;
            for ($i = 0; $i < $count; $i++) {
                $rooms[] = [
                    'tipe' => '',
                    'jumlah' => 1,
                    'kode_blok' => '',
                    'nomor_lapangan' => [],
                    'temp_input' => '',
                    'max_dewasa' => $fasilitas->max_dewasa ?? 1,
                    'max_anak' => $fasilitas->max_anak ?? 0,
                    'harga_harian' => $i === 0 ? $fasilitas->harga : '',
                    'harga_mingguan' => '',
                    'harga_bulanan' => $i === 0 ? $fasilitas->harga_bulanan : '',
                    'harga_tahunan' => '',
                    'keunggulan' => '',
                    'panjang' => '',
                    'lebar' => '',
                    'fasilitas' => [
                        'lampu' => 0,
                        'parkir' => 0,
                        'toilet' => 0,
                        'mushola' => 0,
                        'kursi_tribun' => 0,
                        'ruang_ganti' => 0,
                        'papan_skor' => 0,
                        'sound_system' => 0,
                        'air_minum' => 0,
                        'wifi' => 0,
                    ],
                ];
            }
        }

        return view('admin.dashboard.edit.editFasilitas', compact('fasilitas', 'rooms'));
    }

    public function destroy(int $id) {
        $fasilitas = Fasilitas::findOrFail($id);
        if ($fasilitas->image) {
            Storage::delete('public/fasilitas/' . $fasilitas->image);
        }
        // Also delete gallery
        if ($fasilitas->gallery) {
            foreach ($fasilitas->gallery as $img) {
                Storage::delete('public/fasilitas/gallery/' . $img);
            }
        }
        // Also delete room spesifikasi images
        if ($fasilitas->paket_harian) {
            foreach ($fasilitas->paket_harian as $room) {
                if (isset($room['foto']) && is_array($room['foto'])) {
                    foreach ($room['foto'] as $foto) {
                        Storage::delete('public/fasilitas/rooms/' . $foto);
                    }
                }
            }
        }
        $fasilitas->delete();

        // Audit Log
        AuditLog::catat(
            'Hapus Fasilitas',
            "Menghapus fasilitas: {$fasilitas->nama}",
            ['target_tipe' => 'fasilitas', 'target_id' => $id, 'fasilitas_nama' => $fasilitas->nama]
        );

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'tipe' => 'required|in:lapangan,kolam_renang',
                'deskripsi' => 'required',
                'detail' => 'nullable',
                'max_dewasa_kolam_renang' => 'nullable|integer',
                'max_durasi_harian' => 'nullable|integer',
                'max_durasi_hari' => 'nullable|integer|min:0',
                'max_durasi_minggu' => 'nullable|integer|min:0',
                'max_durasi_bulan' => 'nullable|integer|min:0',
                'max_durasi_tahun' => 'nullable|integer|min:0',
                'jumlah_lapangan'     => 'required|integer|min:1',
                'jam_operasional' => 'nullable|string',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'room_fotos' => 'nullable|array',
                'room_fotos.*' => 'nullable|array',
                'room_fotos.*.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'paket_harian' => 'nullable|string',
                'rooms_data'   => 'nullable|string',
                'labels' => 'nullable|array',
            ]);

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('storage/fasilitas'), $imageName);
            }

            $gallery = [];
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $file) {
                    if ($file) {
                        $path = $file->store('fasilitas/gallery', 'public');
                        $gallery[$index] = basename($path);
                    }
                }
            }
            $gallery = array_values(array_filter($gallery));

            $paket_harian = $request->paket_harian ? json_decode($request->paket_harian, true) : [];
            if (!is_array($paket_harian)) $paket_harian = [];

            $roomFotoFiles = $request->file('room_fotos');
            if (is_array($roomFotoFiles)) {
                foreach ($roomFotoFiles as $roomIdx => $fotos) {
                    if (isset($paket_harian[$roomIdx])) {
                        $saved = [];
                        foreach ($fotos as $fIdx => $file) {
                            if ($file && $file->isValid()) {
                                $name = time() . '_room_' . $roomIdx . '_' . $fIdx . '.' . $file->getClientOriginalExtension();
                                $file->move(public_path('storage/fasilitas/rooms'), $name);
                                $saved[$fIdx] = $name;
                            }
                        }
                        $saved = array_values(array_filter($saved));
                        if (!empty($saved)) {
                            $paket_harian[$roomIdx]['foto'] = $saved;
                        }
                    }
                }
            }

            // Get prices from first room in paket_harian
            $firstRoom = $paket_harian[0] ?? [];
            $h_harian = (float) ($firstRoom['harga_harian'] ?? 0);
            $h_bulanan = !empty($firstRoom['harga_bulanan']) ? (float) $firstRoom['harga_bulanan'] : null;

            // Collect all price tiers across all rooms to build thumbnail range
            $allPrices = [];
            foreach ($paket_harian as $room) {
                foreach (['harga_harian','harga_mingguan','harga_bulanan','harga_tahunan'] as $pKey) {
                    $v = isset($room[$pKey]) ? (float) $room[$pKey] : 0;
                    if ($v > 0) $allPrices[] = $v;
                }
            }
            if (empty($allPrices)) {
                $allPrices = [$h_harian > 0 ? $h_harian : 0];
                if ($h_bulanan > 0) $allPrices[] = $h_bulanan;
            }

            $minPrice = min($allPrices);
            $maxPrice = max($allPrices);

            $formatPrice = function($price) {
                if ($price >= 1000000) return round($price / 1000000, 1) . 'JT';
                if ($price >= 1000) return round($price / 1000) . 'K';
                return $price;
            };

            $harga_thumbnail = ($minPrice !== $maxPrice)
                ? "Mulai " . $formatPrice($minPrice) . " - " . $formatPrice($maxPrice)
                : "Rp " . number_format($h_harian, 0, ',', '.');

            $newFasilitas = Fasilitas::create([
                'nama' => $request->nama,
                'tipe' => $request->tipe,
                'deskripsi' => $request->deskripsi,
                'detail' => $request->detail,
                'harga' => $h_harian,
                'harga_bulanan' => $h_bulanan,
                'max_dewasa' => $request->tipe === 'lapangan'
                    ? (int) ($firstRoom['max_dewasa'] ?? 1)
                    : (int) $request->max_dewasa_kolam_renang,
                'max_anak' => (int) ($firstRoom['max_anak'] ?? 0),
                'max_durasi_harian' => $request->max_durasi_harian,
                'max_durasi_hari' => $request->max_durasi_hari ? (int) $request->max_durasi_hari : null,
                'max_durasi_minggu' => $request->max_durasi_minggu ? (int) $request->max_durasi_minggu : null,
                'max_durasi_bulan' => $request->max_durasi_bulan ? (int) $request->max_durasi_bulan : null,
                'max_durasi_tahun' => $request->max_durasi_tahun ? (int) $request->max_durasi_tahun : null,
                'jumlah_lapangan'     => (int) $request->jumlah_lapangan,
                'all_same'         => filter_var($request->input('all_same', true), FILTER_VALIDATE_BOOLEAN),
                'jam_operasional' => $request->jam_operasional,
                'image' => $imageName, 
                'gallery' => $gallery,
                'paket_harian' => $paket_harian,
                'labels' => $request->labels ?? [],
                'harga_thumbnail' => $harga_thumbnail,
            ]);

            // Audit Log
            AuditLog::catat(
                'Tambah Fasilitas',
                "Menambahkan fasilitas baru: {$request->nama}",
                ['target_tipe' => 'fasilitas', 'target_id' => $newFasilitas->id, 'fasilitas_nama' => $request->nama]
            );

            return response()->json(['success' => 'Data fasilitas berhasil disimpan!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Fasilitas Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updatePaketHarian(Request $request, int $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        $request->validate([
            'paket_harian' => 'nullable|string',
        ]);

        $fasilitas->update([
            'paket_harian' => $request->paket_harian ? json_decode($request->paket_harian, true) : [],
        ]);

        return redirect()->back()->with('success', 'Paket harian berhasil diperbarui!');
    }

    public function storeMaintenance(Request $request, int $id)
    {
        try {
            $request->validate([
                "tgl_mulai" => "required|date|after_or_equal:today",
                "tgl_selesai" => "required|date|after_or_equal:tgl_mulai",
                "tujuan" => "required|string|max:255",
                "nomor_lapangan" => "nullable|array",
                "nomor_lapangan.*" => "string|max:50",
            ]);

            $fasilitas = Fasilitas::findOrFail($id);
            $start = \Carbon\Carbon::parse($request->tgl_mulai)->startOfDay();
            $end = \Carbon\Carbon::parse($request->tgl_selesai)->endOfDay();

            $nomorKamar = $request->nomor_lapangan;
            $blockingAll = empty($nomorKamar);

            $overlaps = \App\Models\Booking::where("fasilitas_id", $id)
                ->whereIn("status", ["pending", "confirmed", "booked"])
                ->where(function($q) use ($start, $end) {
                    $q->whereBetween("tgl_mulai", [$start, $end])
                      ->orWhereBetween("tgl_selesai", [$start, $end])
                      ->orWhere(function($q2) use ($start, $end) {
                          $q2->where("tgl_mulai", "<=", $start)
                             ->where("tgl_selesai", ">=", $end);
                      });
                });

            if (!$blockingAll) {
                $overlaps = $overlaps->where(function ($q) use ($nomorKamar) {
                    $q->whereNull("allocated_rooms")
                      ->orWhere(function ($q2) use ($nomorKamar) {
                          foreach ($nomorKamar as $nr) {
                              $q2->orWhereJsonContains("allocated_rooms", $nr);
                          }
                      });
                });
            }

            $overlaps = $overlaps->get();

            if ($overlaps->count() > 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Gagal! Terdapat " . $overlaps->count() . " reservasi aktif pada rentang tanggal tersebut."
                ], 422);
            }

            \App\Models\JadwalBlokir::create([
                "fasilitas_id" => $id,
                "tgl_mulai" => $start,
                "tgl_selesai" => $end,
                "tipe" => "maintenance",
                "tujuan" => $request->tujuan,
                "nomor_lapangan" => $blockingAll ? null : $nomorKamar,
                "created_by" => session("nama") ?? "System Admin",
            ]);

            $roomInfo = $blockingAll ? 'semua kamar' : implode(', ', $nomorKamar);
            \App\Models\AuditLog::catat(
                "Maintenance Facility",
                "Mengaktifkan mode perbaikan untuk: {$fasilitas->nama} - {$roomInfo} ({$request->tgl_mulai} s/d {$request->tgl_selesai})",
                ["target_tipe" => "fasilitas", "target_id" => $id, "reason" => $request->tujuan, "nomor_lapangan" => $nomorKamar]
            );

            return response()->json(["success" => true, "message" => "Mode perbaikan berhasil diaktifkan!"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()], 500);
        }
    }

    public function cancelMaintenance(int $id)
    {
        try {
            $fasilitas = Fasilitas::findOrFail($id);
            $today = now()->startOfDay();

            // Cari record maintenance yang aktif atau akan datang
            $deletedCount = \App\Models\JadwalBlokir::where('fasilitas_id', $id)
                ->where('tipe', 'maintenance')
                ->where('tgl_selesai', '>=', $today)
                ->delete();

            if ($deletedCount > 0) {
                \App\Models\AuditLog::catat(
                    "Cancel Maintenance",
                    "Membatalkan mode perbaikan untuk: {$fasilitas->nama}. Fasilitas sekarang siap digunakan kembali.",
                    ["target_tipe" => "fasilitas", "target_id" => $id, "fasilitas_nama" => $fasilitas->nama]
                );

                return response()->json([
                    "success" => true,
                    "message" => "Mode perbaikan berhasil dibatalkan! Fasilitas kini tersedia kembali."
                ]);
            }

            return response()->json([
                "success" => false,
                "message" => "Tidak ditemukan jadwal perbaikan aktif untuk fasilitas ini."
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Terjadi kesalahan sistem: " . $e->getMessage()
            ], 500);
        }
    }
}
