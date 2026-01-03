<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('program_reports', function (Blueprint $table) {
            $table->json('shelters')->nullable()->after('shelters_info');
        });
    }

    public function down(): void
    {
        Schema::table('program_reports', function (Blueprint $table) {
            $table->dropColumn('shelters');
        });
    }
};
