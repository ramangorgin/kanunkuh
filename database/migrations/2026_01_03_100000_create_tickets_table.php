<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->string('status')->default('open'); // open, waiting_admin, waiting_user, closed
            $table->string('priority')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('last_reply_by')->nullable(); // user|admin
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['last_reply_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
