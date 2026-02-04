<?php

/**
 * Database migration for creating the course_prerequisites table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates and drops the course_prerequisites table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('prerequisite_id');
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')->on('federation_courses')
                ->onDelete('cascade');

            $table->foreign('prerequisite_id')
                ->references('id')->on('federation_courses')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('course_prerequisites');
    }
};
