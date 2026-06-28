<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_event_id')->constrained('company_events')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 80)->nullable();
            $table->string('organization', 190)->nullable();
            $table->string('position', 190)->nullable();
            $table->unsignedInteger('seats_requested')->default(1);
            $table->string('status', 40)->default('pending');
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('source', 40)->default('website');
            $table->timestamps();

            $table->index(['company_event_id', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
