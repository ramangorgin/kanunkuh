<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['federation_course_id']);
            
            // Make the column nullable
            $table->unsignedInteger('federation_course_id')->nullable()->change();
            
            // Re-add the foreign key constraint (nullable foreign keys are allowed)
            $table->foreign('federation_course_id')
                ->references('id')
                ->on('federation_courses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['federation_course_id']);
            
            // Make the column NOT NULL again
            $table->unsignedInteger('federation_course_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('federation_course_id')
                ->references('id')
                ->on('federation_courses')
                ->onDelete('cascade');
        });
    }
};

