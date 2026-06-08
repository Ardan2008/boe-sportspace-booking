<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal_blokir', function (Blueprint $table) {
            $table->json('nomor_kamar')->nullable()->after('tujuan');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_blokir', function (Blueprint $table) {
            $table->dropColumn('nomor_kamar');
        });
    }
};
