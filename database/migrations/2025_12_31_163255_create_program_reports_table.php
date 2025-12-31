<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')
                ->constrained()
                ->onDelete('cascade');

            // شرح گزارش
            $table->longText('report_description')->nullable();
            $table->longText('important_notes')->nullable();

            // کروکی و نقشه (فایل‌ها در program_files)
            $table->string('map_author')->nullable();
            $table->string('map_scale')->nullable();
            $table->string('map_source')->nullable();

            // مشخصات فنی مسیر
            $table->json('technical_equipments')->nullable();
            $table->enum('route_difficulty', ['آسان','متوسط','سخت','بسیار سخت'])->nullable();
            $table->string('slope')->nullable();
            $table->enum('rock_engagement', ['کم','متوسط','زیاد'])->nullable();
            $table->enum('ice_engagement', ['ندارد','کم','زیاد'])->nullable();
            $table->decimal('avg_backpack_weight', 4, 1)->nullable(); // kg
            $table->text('prerequisites')->nullable();

            // مشخصات طبیعی
            $table->text('vegetation')->nullable();
            $table->text('wildlife')->nullable();
            $table->text('weather')->nullable();
            $table->integer('wind_speed')->nullable(); // km/h
            $table->decimal('temperature', 4, 1)->nullable(); // °C
            $table->string('local_language')->nullable();
            $table->text('attractions')->nullable();
            $table->enum('food_supply', ['دارد','ندارد','محدود'])->nullable();

            // اطلاعات جغرافیایی و مسیر
            $table->integer('start_altitude')->nullable(); // متر
            $table->integer('target_altitude')->nullable(); // متر
            $table->string('start_location_name')->nullable();
            $table->integer('distance_from_tehran')->nullable(); // km
            $table->enum('road_type', ['آسفالت','خاکی','ترکیبی'])->nullable();
            $table->json('transport_types')->nullable();

            // امکانات رفاهی
            $table->json('facilities')->nullable();

            // نقاط و زمان‌بندی
            $table->json('geo_points')->nullable();
            $table->json('timeline')->nullable();

            // شرکت‌کنندگان
            $table->json('participants')->nullable();
            $table->integer('participants_count')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_reports');
    }
};
