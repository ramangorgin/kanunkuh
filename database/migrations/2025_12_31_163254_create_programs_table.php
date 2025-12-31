<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();

            // اطلاعات پایه
            $table->string('name');
            $table->integer('peak_height')->nullable(); // متر
            $table->enum('program_type', [
                'کوهنوردی',
                'طبیعت‌گردی',
                'سنگ‌نوردی',
                'یخ‌نوردی',
                'غارنوردی',
                'فرهنگی'
            ]);
            $table->string('region_name')->nullable();

            // زمان اجرا
            $table->dateTime('execution_date');

            // حرکت
            $table->string('move_from_karaj')->nullable();
            $table->string('move_from_tehran')->nullable();

            // هزینه
            $table->bigInteger('cost_member')->nullable(); // ریال
            $table->bigInteger('cost_guest')->nullable();  // ریال

            // اطلاعات پرداخت
            $table->json('payment_info')->nullable();

            // لوازم، غذا، شرایط
            $table->json('equipments')->nullable();
            $table->json('meals')->nullable();
            $table->json('conditions')->nullable();

            // ثبت‌نام
            $table->dateTime('register_deadline')->nullable();

            // قوانین و توضیحات
            $table->longText('rules')->nullable();

            // وضعیت برنامه
            $table->enum('status', [
                'draft',
                'open',
                'closed',
                'done'
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
