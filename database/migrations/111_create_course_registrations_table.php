<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');

            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_national_id')->nullable();

            $table->enum('status', [
                'pending',     // ثبت اولیه
                'paid',        // پرداخت شده
                'approved',    // تایید شده
                'rejected',    // رد شده
                'cancelled'    // لغو شده
            ])->default('pending');

            $table->string('certificate_file')->nullable();


            $table->boolean('approved')->default(false);

            $table->timestamps();
        });       
    }

    public function down()
    {
        Schema::dropIfExists('course_registrations');
    }
};
