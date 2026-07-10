<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunity_events', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('opportunity_id')
                ->constrained('opportunities')
                ->cascadeOnDelete();

            // Event classification
            $table->string('event_type', 60);       // state_changed | note_added | document_uploaded | ocr_processed | diagnostic_sent | diagnostic_received | proposal_generated
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40)->nullable();

            // Actor
            $table->string('actor_type', 30)->default('collaborator'); // collaborator | client | system
            $table->unsignedBigInteger('actor_id')->nullable();

            // Content
            $table->text('description')->nullable();
            $table->json('payload')->nullable();     // extra structured data

            $table->timestamp('occurred_at')->useCurrent();

            $table->index(['opportunity_id', 'occurred_at']);
            $table->index(['opportunity_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_events');
    }
};
