<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('announcement_admin_id')
                ->constrained('announcement_admins')
                ->cascadeOnDelete();

            // Core identity
            $table->string('reference', 80)->nullable();
            $table->string('service_slug', 80);
            $table->string('service_title', 190);
            $table->string('client_name', 190);
            $table->string('client_contact', 190)->nullable();
            $table->string('client_email', 190)->nullable();
            $table->string('client_company', 190)->nullable();
            $table->string('client_industry', 190)->nullable();

            // Workflow
            $table->string('status', 40)->default('draft');
            $table->string('previous_status', 40)->nullable();
            $table->timestamp('status_changed_at')->nullable();

            // Scoring & context (computed by engines, stored as snapshots)
            $table->json('score_data')->nullable();       // DecisionEngine output
            $table->json('context_snapshot')->nullable(); // ContextEngine consolidated output
            $table->json('tags')->nullable();             // Workflow tags (urgente, headhunting, etc.)

            // Linked proposal (once generated)
            $table->foreignId('proposal_id')->nullable()->constrained('proposals')->nullOnDelete();

            // Notes and meta
            $table->text('internal_notes')->nullable();
            $table->json('meta')->nullable();

            $table->date('expected_close_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['announcement_admin_id', 'status']);
            $table->index(['announcement_admin_id', 'created_at']);
            $table->index('service_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
