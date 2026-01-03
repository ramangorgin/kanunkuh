<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('program_registrations', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
        
            // عضو یا مهمان
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        
            // اطلاعات مهمان
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_national_id')->nullable();
        
            // حمل و نقل
            $table->string('pickup_location')->nullable(); // تهران، کرج، ...
            $table->boolean('needs_transport')->default(false);
        
            // پرداخت
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
        
            // وضعیت ثبت‌نام
            $table->enum('status', [
                'pending',     // ثبت اولیه
                'paid',        // پرداخت شده
                'approved',    // تایید شده
                'rejected',    // رد شده
                'cancelled'    // لغو شده
            ])->default('pending');
        
            $table->timestamps();
        });
        
        
    }

    public function down()
    {
        Schema::dropIfExists('program_registrations');
    }
};
