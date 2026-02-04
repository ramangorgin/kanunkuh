<?php

/**
 * Database migration for creating the programs table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates and drops the programs table.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('peak_height')->nullable();
            $table->enum('program_type', [
                'کوهنوردی',
                'طبیعت‌گردی',
                'سنگ‌نوردی',
                'یخ‌نوردی',
                'غارنوردی',
                'فرهنگی'
            ]);
            $table->string('region_name')->nullable();
            $table->dateTime('execution_date');
            $table->string('move_from_karaj')->nullable();
            $table->string('move_from_tehran')->nullable();
            $table->bigInteger('cost_member')->nullable();
            $table->bigInteger('cost_guest')->nullable();
            $table->json('payment_info')->nullable();
            $table->json('equipments')->nullable();
            $table->json('meals')->nullable();
            $table->json('conditions')->nullable();
            $table->dateTime('register_deadline')->nullable();
            $table->longText('rules')->nullable();
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
