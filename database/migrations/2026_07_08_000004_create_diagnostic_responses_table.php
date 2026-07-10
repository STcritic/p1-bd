<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostic_responses', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('diagnostic_session_id')
                ->constrained('diagnostic_sessions')
                ->cascadeOnDelete();

            $table->foreignId('opportunity_id')
                ->constrained('opportunities')
                ->cascadeOnDelete();

            // Question identity
            $table->string('group_key', 80);
            $table->string('question_key', 80);
            $table->text('question_label');

            // Answer stored as JSON to handle all field types
            $table->json('answer_value');         // null|string|number|array|bool

            $table->timestamps();

            $table->unique(['diagnostic_session_id', 'question_key'], 'uniq_session_question');
            $table->index(['opportunity_id', 'question_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_responses');
    }
};
