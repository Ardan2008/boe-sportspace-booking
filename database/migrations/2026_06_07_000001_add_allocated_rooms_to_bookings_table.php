<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // JSON array of allocated room numbers e.g. ["A-01", "A-02"]
            $table->json('allocated_rooms')->nullable()->after('nomor_kamar');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('allocated_rooms');
        });
    }
};
