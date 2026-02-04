<?php

/**
 * Database migration for creating the courses table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates and drops the courses table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('federation_course_id')->nullable();
            $table->foreign('federation_course_id')
                ->references('id')->on('federation_courses')
                ->onDelete('cascade');

            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')
                ->references('id')->on('teachers')
                ->onDelete('set null');

            $table->string('title')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration')->nullable();

            $table->longText('description')->nullable();

            $table->string('place')->nullable();
            $table->string('place_address')->nullable();
            $table->decimal('place_lat', 10, 7)->nullable();
            $table->decimal('place_lon', 10, 7)->nullable();
            
            $table->integer('capacity')->nullable();

            $table->boolean('is_free')->default(false);
            $table->integer('member_cost')->nullable();
            $table->integer('guest_cost')->nullable();

            $table->string('card_number')->nullable();
            $table->string('sheba_number')->nullable();
            $table->string('card_holder')->nullable();
            $table->string('bank_name')->nullable();

            $table->boolean('is_registration_open')->default(true);
            $table->date('registration_deadline')->nullable();

            $table->longText('report')->nullable();

            $table->enum('status', ['draft','published','completed','canceled'])->default('draft');
            
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
