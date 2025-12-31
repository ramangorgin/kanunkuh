<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('program_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->enum('file_type', ['poster','image','pdf','gps','other']);
            $table->string('file_path');
            $table->string('caption')->nullable(); // توضیح یا عنوان فایل
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('program_files');
    }
};
