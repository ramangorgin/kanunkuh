<?php

/**
 * Database migration for updating the programs.program_type column.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Alters program_type to allow custom values and provides rollback.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE programs MODIFY program_type VARCHAR(255)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE programs MODIFY program_type ENUM('کوهنوردی','طبیعت‌گردی','سنگ‌نوردی','یخ‌نوردی','غارنوردی','فرهنگی')");
    }
};
