<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('tipe_kamar_id')->nullable()->after('fasilitas_id')->constrained('global_room_types')->nullOnDelete();
            $table->string('nomor_kamar')->nullable()->after('tipe_kamar_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipe_kamar_id');
            $table->dropColumn('nomor_kamar');
        });
    }
};
