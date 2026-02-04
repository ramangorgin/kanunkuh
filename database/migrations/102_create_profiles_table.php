<?php

/**
 * Database migration for creating the profiles table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates and drops the profiles table.
 */
return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('membership_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('membership_id')->unique(); 
            $table->string('membership_type')->nullable();       
            $table->date('membership_start')->nullable();      
            $table->date('membership_expiry')->nullable(); 
            $table->date('leave_date')->nullable(); 
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('father_name', 50)->nullable();
            $table->string('id_number', 20)->nullable();
            $table->string('id_place', 50)->nullable();
            $table->date('birth_date')->nullable();            
            $table->string('national_id', 10);       
            $table->string('photo');                          
            $table->string('national_card');
            $table->enum('marital_status', ['مجرد', 'متاهل'])->nullable();
            $table->string('emergency_phone', 15)->nullable();
            $table->string('referrer', 100)->nullable();
            $table->string('education', 100)->nullable();
            $table->string('job', 100)->nullable();
            $table->text('home_address')->nullable();
            $table->text('work_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
