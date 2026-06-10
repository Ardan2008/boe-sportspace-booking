<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('selected_days', 50)->nullable()->after('package_type')
                ->comment('Comma-separated ISO day numbers (1=Monday..7=Sunday); null = all days');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('selected_days');
        });
    }
};
