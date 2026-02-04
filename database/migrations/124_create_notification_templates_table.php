<?php

/**
 * Database migration for creating the notification_templates table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates and drops the notification_templates table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event_key');
            $table->enum('channel', ['site', 'sms']);
            $table->string('title_template')->nullable();
            $table->text('body_template');
            $table->unsignedBigInteger('sms_template_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['event_key', 'channel']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
