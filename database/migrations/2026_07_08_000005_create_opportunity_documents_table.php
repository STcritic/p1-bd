<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunity_documents', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('opportunity_id')
                ->constrained('opportunities')
                ->cascadeOnDelete();

            $table->foreignId('diagnostic_session_id')
                ->nullable()
                ->constrained('diagnostic_sessions')
                ->nullOnDelete();

            // File info
            $table->string('original_name', 255);
            $table->string('stored_path', 500);
            $table->string('disk', 30)->default('local');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();   // bytes

            // Source context
            $table->string('question_key', 80)->nullable();        // which question triggered upload
            $table->string('uploaded_by', 30)->default('client');  // client | collaborator

            // OCR
            $table->boolean('ocr_eligible')->default(false);
            $table->boolean('ocr_processed')->default(false);
            $table->timestamp('ocr_queued_at')->nullable();

            $table->timestamps();

            $table->index(['opportunity_id', 'ocr_processed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_documents');
    }
};
