<?php

use Illuminate\Support\Facades\Route;
// --- TAMBAHKAN IMPORT CONTROLLER DI SINI ---
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\KontrolJadwalController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoomAvailabilityController;
use App\Http\Controllers\ValidationController;

// --- ROUTE ASLI KAMU (TIDAK DIUBAH) ---

Route::get('/', function () {
    $facilities = \App\Models\Fasilitas::all();
    return view('home', compact('facilities'));
})->name('home');

Route::get('/formBooking', function (\Illuminate\Http\Request $request) {
    $facilities = \App\Models\Fasilitas::all();
    
    foreach ($facilities as $fasilitas) {
        if ($fasilitas->tipe === 'asrama' && is_array($fasilitas->paket_harian)) {
            $today = now()->toDateString();
            $activeBookings = \App\Models\Booking::where('fasilitas_id', $fasilitas->id)
                ->whereIn('status', ['pending', 'confirmed', 'booked'])
                ->where('tgl_mulai', '<=', $today)
                ->where('tgl_selesai', '>=', $today)
                ->whereNotNull('tipe_kamar_id')
                ->get();

            $globalTypes = \App\Models\GlobalRoomType::whereIn('id', $activeBookings->pluck('tipe_kamar_id')->unique())->get()->keyBy('id');

            $bookedPerType = [];
            foreach ($activeBookings as $b) {
                $typeName = strtolower(trim($globalTypes[$b->tipe_kamar_id]->name ?? ''));
                if (!isset($bookedPerType[$typeName])) {
                    $bookedPerType[$typeName] = [];
                }
                if (!empty($b->allocated_rooms)) {
                    foreach ($b->allocated_rooms as $room) {
                        $bookedPerType[$typeName][] = $room;
                    }
                } else {
                    $packages = json_decode($b->selected_packages ?? '{}', true);
                    $roomsBooked = (int) ($packages['rooms'] ?? 1);
                    for ($i = 0; $i < $roomsBooked; $i++) {
                        $bookedPerType[$typeName][] = '__placeholder__';
                    }
                }
            }

            $paket = $fasilitas->paket_harian;
            foreach ($paket as &$rt) {
                $typeName = strtolower(trim($rt['tipe'] ?? ''));
                $allRooms = $rt['nomor_kamar'] ?? [];
                $totalRooms = count($allRooms) > 0 ? count($allRooms) : (int) ($rt['jumlah'] ?? 0);
                $typeBookedList = $bookedPerType[$typeName] ?? [];

                if (!empty($allRooms)) {
                    $bookedRooms = array_filter($typeBookedList, fn($r) => $r !== '__placeholder__');
                    $placeholderCount = count(array_filter($typeBookedList, fn($r) => $r === '__placeholder__'));
                    $available = count(array_diff($allRooms, array_values($bookedRooms))) - $placeholderCount;
                } else {
                    $placeholderCount = count(array_filter($typeBookedList, fn($r) => $r === '__placeholder__'));
                    $namedCount = count(array_filter($typeBookedList, fn($r) => $r !== '__placeholder__'));
                    $available = max(0, $totalRooms - $placeholderCount - $namedCount);
                }

                $rt['jumlah'] = max(0, $available);
            }
            unset($rt);
            $fasilitas->paket_harian = $paket;
        }
    }

    $selectedId = $request->query('id', '');
    return view('formBooking', compact('facilities', 'selectedId'));
})->name('formBooking');

Route::get('/fasilitas/{id}/detail', function ($id) {
    $fasilitas = \App\Models\Fasilitas::findOrFail($id);

    if ($fasilitas->tipe === 'asrama' && is_array($fasilitas->paket_harian)) {
        $today = now()->toDateString();
        $activeBookings = \App\Models\Booking::where('fasilitas_id', $fasilitas->id)
            ->whereIn('status', ['pending', 'confirmed', 'booked'])
            ->where('tgl_mulai', '<=', $today)
            ->where('tgl_selesai', '>=', $today)
            ->whereNotNull('tipe_kamar_id')
            ->get();

        $globalTypes = \App\Models\GlobalRoomType::whereIn('id', $activeBookings->pluck('tipe_kamar_id')->unique())->get()->keyBy('id');

        $bookedPerType = [];
        foreach ($activeBookings as $b) {
            $typeName = strtolower(trim($globalTypes[$b->tipe_kamar_id]->name ?? ''));
            if (!isset($bookedPerType[$typeName])) {
                $bookedPerType[$typeName] = [];
            }
            if (!empty($b->allocated_rooms)) {
                foreach ($b->allocated_rooms as $room) {
                    $bookedPerType[$typeName][] = $room;
                }
            } else {
                $packages = json_decode($b->selected_packages ?? '{}', true);
                $roomsBooked = (int) ($packages['rooms'] ?? 1);
                for ($i = 0; $i < $roomsBooked; $i++) {
                    $bookedPerType[$typeName][] = '__placeholder__';
                }
            }
        }

        $paket = $fasilitas->paket_harian;
        foreach ($paket as &$rt) {
            $typeName = strtolower(trim($rt['tipe'] ?? ''));
            $allRooms = $rt['nomor_kamar'] ?? [];
            $totalRooms = count($allRooms) > 0 ? count($allRooms) : (int) ($rt['jumlah'] ?? 0);
            $typeBookedList = $bookedPerType[$typeName] ?? [];

            if (!empty($allRooms)) {
                $bookedRooms = array_filter($typeBookedList, fn($r) => $r !== '__placeholder__');
                $placeholderCount = count(array_filter($typeBookedList, fn($r) => $r === '__placeholder__'));
                $available = count(array_diff($allRooms, array_values($bookedRooms))) - $placeholderCount;
            } else {
                $placeholderCount = count(array_filter($typeBookedList, fn($r) => $r === '__placeholder__'));
                $namedCount = count(array_filter($typeBookedList, fn($r) => $r !== '__placeholder__'));
                $available = max(0, $totalRooms - $placeholderCount - $namedCount);
            }

            $rt['jumlah'] = max(0, $available);
        }
        unset($rt);
        $fasilitas->paket_harian = $paket;
    }

    return view('detailFasilitas', compact('fasilitas'));
})->name('fasilitas.detail');

Route::post('/bookings/store', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/receipt/public/{id}', [BookingController::class, 'publicReceipt'])->name('public.receipt');

Route::get('/schedule_booking', [ScheduleController::class, 'index'])->name('schedule_booking');
Route::get('/schedule_booking/data', [KontrolJadwalController::class, 'publicCalendarData'])->name('schedule_booking.data');

Route::get('/api/check-room-availability', [RoomAvailabilityController::class, 'checkAvailability'])->name('api.check-room-availability');
Route::get('/api/validate-whatsapp', [ValidationController::class, 'whatsapp'])->name('api.validate.whatsapp');
Route::get('/api/validate-email', [ValidationController::class, 'email'])->name('api.validate.email');

// Bagian Admin
// -- form login
// ambil kode rahasia dari config (yang terhubung ke .env)
$secretUrl = config('services.admin.secret');

// mendaftarkan route langsung menggunakan kode rahasia tersebut
if ($secretUrl) {
    Route::get('/' . $secretUrl, function () {
        return view('admin.formLogin');
    })->name('formLogin');
}

// Auth Admin
Route::post('/admin/login', [AdminsController::class, 'login'])->name('admin.login');
Route::get('/admin/logout', [AdminsController::class, 'logout'])->name('admin.logout');

Route::get('/admin/dashboard/blokir-internal', [KontrolJadwalController::class, 'showFormBlokir'])->name('kontrolJadwal.formBlokir');

Route::middleware(['admin.access'])->group(function () {
    // Route Khusus Owner
    Route::middleware(['admin.access:owner'])->group(function () {
        Route::get('/admin/dashboard/management/add_new_admin', function (\Illuminate\Http\Request $request) {
            return view('admin.dashboard.management.add_new_admin', [
                'from' => $request->query('from', '')
            ]);
        })->name('dashboardAddNewAdmin');
        
        Route::post('/admin/store', [AdminsController::class, 'store'])->name('admin.store');
        Route::get('/admin/dashboard/management/admin_active_control', [AdminsController::class, 'adminActiveControl'])->name('admin.active.control');
        Route::get('/admin/dashboard/management/active-list', [AdminsController::class, 'adminActiveControl'])->name('admin.active.list');

        // Role Management Methods
        Route::put('/admin/permissions/{id_log}', [AdminsController::class, 'updatePermissions'])->name('admin.updatePermissions');
        Route::post('/admin/promote/{id_log}', [AdminsController::class, 'promoteToOwner'])->name('admin.promote');
        Route::post('/admin/force-logout/{id_log}', [AdminsController::class, 'forceLogoutAdmin'])->name('admin.forceLogout');
        Route::delete('/admin/delete/{id_log}', [AdminsController::class, 'destroyAdmin'])->name('admin.destroyAdmin');
        Route::put('/admin/update-credentials/{id_log}', [AdminsController::class, 'updateAdminCredentials'])->name('admin.updateCredentials');
        
        // Admin detail for owner to view
        Route::get('/admin/view/{id_log}', [AdminsController::class, 'view'])->name('admin.view');
        Route::get('/admin/dashboard/management/view_admin', function () {
            return view('admin.dashboard.management.view_admin');
        })->name('dashboardViewAdmin');

        // Audit Log (Owner Only)
        Route::get('/admin/dashboard/auditLog', [AuditLogController::class, 'index'])->name('kontrolJadwal.auditLog');
        Route::delete('/admin/dashboard/auditLog/batch', [AuditLogController::class, 'destroyBatch'])->name('auditLog.batchDestroy');
        Route::delete('/admin/dashboard/auditLog/{id}', [AuditLogController::class, 'destroy'])->name('auditLog.destroy');
    });

    // Routing umum Admin
    Route::get('/admin/dashboard/master', [AdminDashboardController::class, 'index'])->name('dashboardMaster');

    Route::get('/admin/dashboard/layouts/sidebar', function () {
        return view('admin.dashboard.layouts.sidebar');
    })->name('dashboardSidebar');

    Route::get('/admin/dashboard/dataFasilitas', [FasilitasController::class, 'index'])->name('fasilitas.index');
    Route::post('/admin/fasilitas/{id}/maintenance', [FasilitasController::class, 'storeMaintenance'])->name('fasilitas.maintenance');
    Route::post('/admin/fasilitas/{id}/cancel-maintenance', [FasilitasController::class, 'cancelMaintenance'])->name('fasilitas.cancelMaintenance');

    Route::get('/admin/dashboard/historyBooking', [RiwayatController::class, 'index'])->name('dashboardhistoryBooking');
    Route::delete('/admin/dashboard/historyBooking/batch', [RiwayatController::class, 'destroyBatch'])->name('admin.history.batchDestroy');
    Route::delete('/admin/dashboard/historyBooking/{id}', [RiwayatController::class, 'destroy'])->name('admin.history.destroy');
    Route::get('/admin/dashboard/managementBooking', [BookingController::class, 'management'])->name('dashboardManagementBooking');

    // ── Kontrol Jadwal ──
    Route::get('/admin/dashboard/kontrolJadwal', [KontrolJadwalController::class, 'index'])->name('kontrolJadwal.index');
    Route::get('/admin/dashboard/kontrolJadwal/data', [KontrolJadwalController::class, 'calendarData'])->name('kontrolJadwal.data');
    Route::post('/admin/jadwal/blokir', [KontrolJadwalController::class, 'storeBlokir'])->name('kontrolJadwal.blokir');
    Route::delete('/admin/jadwal/blokir/{id}', [KontrolJadwalController::class, 'destroyBlokir'])->name('kontrolJadwal.destroyBlokir');
    Route::get('/admin/bookings/{id}/receipt', [KontrolJadwalController::class, 'downloadReceipt'])->name('admin.bookings.receipt');

    // Book approve / reject / detail (AJAX)
    Route::get('/admin/bookings/{id}/detail', [BookingController::class, 'show'])->name('admin.bookings.detail');
    Route::post('/admin/bookings/{id}/approve', [BookingController::class, 'approve'])->name('admin.bookings.approve');
    Route::post('/admin/bookings/{id}/reject', [BookingController::class, 'reject'])->name('admin.bookings.reject');
    Route::post('/admin/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('admin.bookings.cancel');
    Route::post('/admin/bookings/{id}/extend', [BookingController::class, 'extend'])->name('admin.bookings.extend');
    Route::post('/admin/bookings/{id}/checkin', [BookingController::class, 'checkIn'])->name('admin.bookings.checkin');
    Route::post('/admin/bookings/{id}/checkout', [BookingController::class, 'checkOut'])->name('admin.bookings.checkout');
    Route::post('/admin/bookings/{id}/extend-stay', [BookingController::class, 'extendStay'])->name('admin.bookings.extendStay');

    // Notifications
    Route::get('/admin/notifications/count', [NotificationController::class, 'getPendingCount'])->name('admin.notifications.count');

    Route::get('/admin/dashboard/search/searchBar', function () {
        return view('admin.dashboard.search.searchBar');
    })->name('dashboardSearchBar');

    Route::get('/admin/dashboard/detail/detailBooking', function () {
        return view('admin.dashboard.detail.detailBooking');
    })->name('dashboarddetailBooking');

    Route::get('/admin/dashboard/stats', [AdminsController::class, 'index'])->name('admin.stats');

    // Route edit/update/create perlu can_edit (readonly check)
    Route::middleware(['admin.access:can_edit'])->group(function () {
        Route::get('/admin/dashboard/create/createFasilitas', function () {
            $roomTypes = \App\Models\GlobalRoomType::orderBy('name')->get(['id', 'name']);
            return view('admin.dashboard.create.createFasilitas', compact('roomTypes'));
        })->name('dashboardcreateFasilitas');
        
        Route::post('/admin/fasilitas/store', [FasilitasController::class, 'store'])->name('fasilitas.store');
        Route::get('/admin/dashboard/edit/{id}', [FasilitasController::class, 'edit'])->name('fasilitas.edit');
        Route::put('/admin/dashboard/update/{id}', [FasilitasController::class, 'update'])->name('fasilitas.update');
        Route::put('/admin/fasilitas/paket-harian/{id}', [FasilitasController::class, 'updatePaketHarian'])->name('fasilitas.updatePaketHarian');
        Route::delete('/admin/fasilitas/delete/{id}', [FasilitasController::class, 'destroy'])->name('fasilitas.destroy');
        Route::put('/admin/update/{id_log}', [AdminsController::class, 'update'])->name('admin.update');

        // Global Room Types CRUD (AJAX)
        Route::get('/admin/room-types', [\App\Http\Controllers\GlobalRoomTypeController::class, 'index'])->name('roomTypes.index');
        Route::post('/admin/room-types', [\App\Http\Controllers\GlobalRoomTypeController::class, 'store'])->name('roomTypes.store');
        Route::put('/admin/room-types/{id}', [\App\Http\Controllers\GlobalRoomTypeController::class, 'update'])->name('roomTypes.update');
        Route::delete('/admin/room-types/{id}', [\App\Http\Controllers\GlobalRoomTypeController::class, 'destroy'])->name('roomTypes.destroy');
    });

    Route::get('/admin/manage/{id_log}', [AdminsController::class, 'manage'])->name('admin.manage');
});