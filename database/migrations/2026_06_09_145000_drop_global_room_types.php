<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['tipe_kamar_id']);
                $table->dropColumn('tipe_kamar_id');
            });
        }

        Schema::dropIfExists('global_room_types');
    }

    public function down(): void
    {
        Schema::create('global_room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        DB::table('global_room_types')->insert([
            ['name' => 'Standar', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'VIP', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('tipe_kamar_id')->nullable()->after('fasilitas_id')->constrained('global_room_types')->nullOnDelete();
        });
    }
};
