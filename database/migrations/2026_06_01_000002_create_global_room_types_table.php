<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed the default types that were previously hardcoded in Alpine
        \DB::table('global_room_types')->insert([
            ['name' => 'Standard',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deluxe',    'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suite',     'created_at' => now(), 'updated_at' => now()],
            ['name' => 'VIP',       'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('global_room_types');
    }
};
