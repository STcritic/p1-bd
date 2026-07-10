<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_results', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('document_id')
                ->constrained('opportunity_documents')
                ->cascadeOnDelete();

            $table->foreignId('opportunity_id')
                ->constrained('opportunities')
                ->cascadeOnDelete();

            // Raw OCR output — never overwritten, append-only
            $table->longText('raw_text');
            $table->json('parsed_pages')->nullable();    // per-page text array from OCR API
            $table->json('extracted_data')->nullable();  // structured extraction if applicable

            // API metadata
            $table->tinyInteger('engine_used')->default(1);   // OCR.space engine 1/2/3
            $table->float('confidence_pct')->nullable();
            $table->boolean('has_errors')->default(false);
            $table->text('error_message')->nullable();

            $table->timestamp('processed_at')->useCurrent();

            $table->index('opportunity_id');
            $table->index('document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_results');
    }
};
