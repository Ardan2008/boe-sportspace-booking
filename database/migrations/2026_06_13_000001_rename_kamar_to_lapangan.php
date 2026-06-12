<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename columns
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->renameColumn('jumlah_kamar', 'jumlah_lapangan');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('nomor_kamar', 'nomor_lapangan');
        });

        Schema::table('jadwal_blokir', function (Blueprint $table) {
            $table->renameColumn('nomor_kamar', 'nomor_lapangan');
        });

        // Update JSON key in fasilitas.paket_harian: nomor_kamar → nomor_lapangan
        $fasilitas = DB::table('fasilitas')->whereNotNull('paket_harian')->get();
        foreach ($fasilitas as $f) {
            $paket = json_decode($f->paket_harian, true);
            if (!is_array($paket)) continue;
            $changed = false;
            foreach ($paket as &$item) {
                if (isset($item['nomor_kamar'])) {
                    $item['nomor_lapangan'] = $item['nomor_kamar'];
                    unset($item['nomor_kamar']);
                    $changed = true;
                }
            }
            if ($changed) {
                DB::table('fasilitas')->where('id', $f->id)->update([
                    'paket_harian' => json_encode($paket),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->renameColumn('jumlah_lapangan', 'jumlah_kamar');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('nomor_lapangan', 'nomor_kamar');
        });

        Schema::table('jadwal_blokir', function (Blueprint $table) {
            $table->renameColumn('nomor_lapangan', 'nomor_kamar');
        });

        // Revert JSON key
        $fasilitas = DB::table('fasilitas')->whereNotNull('paket_harian')->get();
        foreach ($fasilitas as $f) {
            $paket = json_decode($f->paket_harian, true);
            if (!is_array($paket)) continue;
            $changed = false;
            foreach ($paket as &$item) {
                if (isset($item['nomor_lapangan'])) {
                    $item['nomor_kamar'] = $item['nomor_lapangan'];
                    unset($item['nomor_lapangan']);
                    $changed = true;
                }
            }
            if ($changed) {
                DB::table('fasilitas')->where('id', $f->id)->update([
                    'paket_harian' => json_encode($paket),
                ]);
            }
        }
    }
};
