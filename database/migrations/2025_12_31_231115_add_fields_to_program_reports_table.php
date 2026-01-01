<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('program_reports', function (Blueprint $table) {
            // 1. اطلاعات کلی گزارش
            $table->datetime('report_date')->nullable()->after('program_id');
            
            // 2. کروکی و نقشه - فایل در program_files ذخیره می‌شود
            // map_author, map_scale, map_source قبلاً وجود دارد
            
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
        });
    }

    public function down(): void
    {
        Schema::table('program_reports', function (Blueprint $table) {
            $table->dropForeign(['reporter_id']);
            $table->dropForeign(['leader_id']);
            $table->dropColumn([
                'report_date',
                'reporter_id',
                'reporter_name',
                'leader_id',
                'leader_name',
                'report_program_type',
                'report_program_name',
                'report_region_route',
                'report_start_date',
                'report_end_date',
                'report_duration',
                'technical_feature',
                'local_village_name',
                'local_guide_info',
                'shelters_info',
            ]);
        });
    }
};
