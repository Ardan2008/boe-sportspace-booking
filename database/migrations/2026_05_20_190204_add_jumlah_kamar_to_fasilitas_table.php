<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_kamar')->default(1)->after('max_anak');
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropColumn('jumlah_kamar');
        });
    }
};