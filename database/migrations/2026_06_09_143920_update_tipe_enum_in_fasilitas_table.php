<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('fasilitas')->where('tipe', 'asrama')->update(['tipe' => 'lapangan']);
        DB::table('fasilitas')->where('tipe', 'aula')->update(['tipe' => 'kolam_renang']);

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE fasilitas MODIFY COLUMN tipe ENUM('lapangan','kolam_renang') DEFAULT 'lapangan'");
        } else {
            Schema::table('fasilitas', function (Blueprint $table) {
                $table->string('tipe', 20)->change();
            });
        }
    }

    public function down(): void
    {
        DB::table('fasilitas')->where('tipe', 'lapangan')->update(['tipe' => 'asrama']);
        DB::table('fasilitas')->where('tipe', 'kolam_renang')->update(['tipe' => 'aula']);

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE fasilitas MODIFY COLUMN tipe ENUM('asrama','aula') DEFAULT 'asrama'");
        } else {
            Schema::table('fasilitas', function (Blueprint $table) {
                $table->string('tipe', 20)->change();
            });
        }
    }
};
