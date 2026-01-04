<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Allow any custom program type (was ENUM)
        DB::statement("ALTER TABLE programs MODIFY program_type VARCHAR(255)");
    }

    public function down(): void
    {
        // Revert to original ENUM options
        DB::statement("ALTER TABLE programs MODIFY program_type ENUM('کوهنوردی','طبیعت‌گردی','سنگ‌نوردی','یخ‌نوردی','غارنوردی','فرهنگی')");
    }
};
