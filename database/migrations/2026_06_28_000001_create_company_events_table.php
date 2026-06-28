<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('announcement_admin_id')->nullable()->constrained('announcement_admins')->nullOnDelete();
            $table->foreignId('announcement_id')->nullable()->constrained('announcements')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('audience', 255)->nullable();
            $table->string('format', 40)->default('presencial');
            $table->string('location', 255)->nullable();
            $table->string('image_url', 1000)->nullable();
            $table->string('external_url', 1000)->nullable();
            $table->unsignedInteger('seats_total')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('registration_deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_events');
    }
};
