<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('meeting_setting_id')->nullable()->constrained('meeting_settings')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 80)->nullable();
            $table->string('organization', 190)->nullable();
            $table->string('position', 190)->nullable();
            $table->string('subject', 190)->nullable();
            $table->text('message')->nullable();
            $table->timestamp('scheduled_for');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('timezone', 80)->default('Africa/Johannesburg');
            $table->string('status', 40)->default('scheduled');
            $table->string('meeting_platform')->nullable();
            $table->string('meeting_url', 1000)->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->text('location_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['scheduled_for', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
