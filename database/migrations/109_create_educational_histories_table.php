<?php

/**
 * Database migration for creating the educational_histories table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates and drops the educational_histories table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('educational_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('federation_course_id')->nullable();

            $table->string('custom_course_title')->nullable();
            
            $table->date('issue_date')->nullable();

            $table->string('certificate_file')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('federation_course_id')
                ->references('id')
                ->on('federation_courses')
                ->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('educational_histories');
    }
};