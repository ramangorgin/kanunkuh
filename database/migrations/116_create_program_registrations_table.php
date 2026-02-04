<?php

/**
 * Database migration for creating the program_registrations table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates and drops the program_registrations table.
 */
return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('program_registrations', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
        
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_national_id')->nullable();
        
            $table->string('pickup_location')->nullable();
            $table->boolean('needs_transport')->default(false);
        
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
        
            $table->enum('status', [
                'pending',
                'paid',
                'approved',
                'rejected',
                'cancelled'
            ])->default('pending');
        
            $table->timestamps();
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('program_registrations');
    }
};
