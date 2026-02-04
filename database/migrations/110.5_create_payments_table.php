<?php

/**
 * Database migration for creating the payments table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates and drops the payments table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['membership', 'program', 'course']);
            $table->unsignedBigInteger('related_id')->nullable();
            $table->integer('year')->nullable();
            $table->integer('amount')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('membership_code', 20)->nullable();
            $table->string('transaction_code', 20)->unique();
            $table->string('status', 20)->default('pending');
            $table->boolean('approved')->default(false);
            $table->text('description')->nullable();

            $table->text('metadata')->nullable();
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
