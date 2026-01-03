<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * اجرای مایگریشن
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // ارتباط با کاربر
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // نوع پرداخت
            $table->enum('type', ['membership', 'program', 'course']);

            // ارتباط با آیتم مربوطه (nullable برای حق عضویت)
            $table->unsignedBigInteger('related_id')->nullable();

            // سال حق عضویت
            $table->integer('year')->nullable();

            // مبلغ پرداخت (تومان)
            $table->integer('amount')->nullable();

            // تاریخ پرداخت (در صورت نیاز بعداً توسط ادمین پر می‌شود)
            $table->date('payment_date')->nullable();

            // شناسه عضویت کاربر (اختیاری، ممکن است null باشد)
            $table->string('membership_code', 20)->nullable();

            // شناسه ده‌رقمی پرداخت (توسط سیستم ساخته می‌شود)
            $table->string('transaction_code', 20)->unique();

            // وضعیت پرداخت
            // pending = در انتظار بررسی
            // approved = تایید شده
            // rejected = رد شده
            $table->string('status', 20)->default('pending');

            // تایید نهایی توسط ادمین (برای استفاده در فیلترها)
            $table->boolean('approved')->default(false);

            // توضیحات اختیاری (در صورت نیاز در آینده)
            $table->text('description')->nullable();

            $table->text('metadata')->nullable();
            

            $table->timestamps();
        });
    }

    /**
     * بازگرداندن تغییرات
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
