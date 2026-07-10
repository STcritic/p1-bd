<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostic_sessions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('opportunity_id')
                ->constrained('opportunities')
                ->cascadeOnDelete();

            // Token-based access — no login required
            $table->string('token', 80)->unique();
            $table->string('service_slug', 80);

            // Lifecycle
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('opened_at')->nullable();   // first client access
            $table->timestamp('last_saved_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            // Questionnaire version pinned at send time
            $table->string('guide_version', 20)->default('1.0');

            // Partial save support — client can continue later
            $table->json('draft_answers')->nullable();

            // Notification tracking
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();

            $table->timestamps();

            $table->index(['token']);
            $table->index(['opportunity_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_sessions');
    }
};
