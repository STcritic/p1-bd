<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('platform_name')->nullable();
            $table->string('meeting_url', 1000)->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->text('location_notes')->nullable();
            $table->json('notification_emails')->nullable();
            $table->string('standard_subject')->default('Conversa de diagnóstico BD');
            $table->text('standard_message')->nullable();
            $table->unsignedSmallInteger('default_duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_settings');
    }
};
