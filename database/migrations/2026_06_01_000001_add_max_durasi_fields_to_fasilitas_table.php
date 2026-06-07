<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->integer('max_durasi_hari')->nullable()->after('max_durasi_harian');
            $table->integer('max_durasi_minggu')->nullable()->after('max_durasi_hari');
            $table->integer('max_durasi_bulan')->nullable()->after('max_durasi_minggu');
            $table->integer('max_durasi_tahun')->nullable()->after('max_durasi_bulan');
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropColumn([
                'max_durasi_hari',
                'max_durasi_minggu',
                'max_durasi_bulan',
                'max_durasi_tahun',
            ]);
        });
    }
};
