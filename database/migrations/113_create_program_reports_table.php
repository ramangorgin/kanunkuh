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

            // 1. اطلاعات کلی گزارش
            $table->datetime('report_date')->nullable()->after('program_id');
            
            // 4. مشخصات گزارشگر و سرپرست
            $table->foreignId('reporter_id')->nullable()->after('program_id')->constrained('users')->onDelete('set null');
            $table->string('reporter_name')->nullable()->after('reporter_id'); // برای وارد کردن دستی
            $table->foreignId('leader_id')->nullable()->after('reporter_name')->constrained('users')->onDelete('set null');
            $table->string('leader_name')->nullable()->after('leader_id'); // برای وارد کردن دستی
            
            // 7. اطلاعات کلی برنامه (در گزارش)
            $table->enum('report_program_type', ['کوهنوردی', 'کوهپیمایی', 'صخره‌نوردی', 'یخ‌نوردی', 'غارنوردی', 'دیگر'])->nullable()->after('program_id');
            $table->string('report_program_name')->nullable()->after('report_program_type');
            $table->string('report_region_route')->nullable()->after('report_program_name');
            $table->date('report_start_date')->nullable()->after('report_region_route');
            $table->date('report_end_date')->nullable()->after('report_start_date');
            $table->string('report_duration')->nullable()->after('report_end_date');
            
            // 8. عوامل اجرایی برنامه - در program_user_roles ذخیره می‌شود
            
            // 9. ویژگی فنی برنامه
            $table->enum('technical_feature', ['عمومی', 'تخصصی', 'خانوادگی'])->nullable()->after('report_duration');
            
            // 11. نوع جاده - road_type قبلاً وجود دارد
            // transport_types قبلاً وجود دارد
            
            // 12. مشخصات رفاهی منطقه - facilities قبلاً وجود دارد اما باید فیلدهای جدید اضافه کنیم
            // facilities به صورت JSON ذخیره می‌شود: ['piped_water', 'seasonal_spring', 'permanent_spring', 'school', 'phone', 'electricity', 'post', 'mobile_coverage']
            
            // 13. اطلاعات محلی
            $table->string('local_village_name')->nullable()->after('start_location_name');
            $table->text('local_guide_info')->nullable()->after('local_village_name');
            $table->text('shelters_info')->nullable()->after('local_guide_info');
            $table->json('shelters')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_reports');
    }
};
