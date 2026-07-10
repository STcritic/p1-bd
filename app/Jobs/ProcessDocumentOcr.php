<?php

namespace App\Jobs;

use App\Modules\Collaborator\Opportunity\Context\ContextEngine;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityDocument;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent;
use App\Modules\Collaborator\Opportunity\Ocr\OcrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDocumentOcr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly int $documentId
    ) {}

    public function handle(OcrService $ocr, ContextEngine $context): void
    {
        $document = OpportunityDocument::with('opportunity')->find($this->documentId);

        if (! $document) return;

        $result = $ocr->process($document);

        if (! $result) return;

        $opportunity = $document->opportunity;

        // Log OCR completion on the timeline
        OpportunityEvent::create([
            'opportunity_id' => $opportunity->id,
            'event_type'     => OpportunityEvent::OCR_PROCESSED,
            'actor_type'     => 'system',
            'description'    => $result->has_errors
                ? 'OCR falhou para "' . $document->original_name . '": ' . $result->error_message
                : 'OCR concluído para "' . $document->original_name . '" — ' . mb_strlen($result->raw_text) . ' caracteres extraídos.',
            'payload'        => [
                'document_id'  => $document->id,
                'has_errors'   => $result->has_errors,
                'char_count'   => mb_strlen($result->raw_text),
            ],
            'occurred_at'    => now(),
        ]);

        // Refresh consolidated context so the new OCR text is immediately available
        if (! $result->has_errors) {
            $context->refresh($opportunity);
        }
    }
}
