<?php

namespace App\Modules\Collaborator\Opportunity\Ocr;

use App\Modules\Collaborator\Opportunity\Domain\OcrResult;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityDocument;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * OcrService — integrates OCR.space API.
 *
 * Key: config('services.ocr_space.key')
 * Never overwrites existing OcrResult for a document.
 * Both raw text and original file are always preserved.
 */
final class OcrService
{
    private string $endpoint;
    private string $apiKey;
    private int    $engine;
    private string $language;

    // MIME types eligible for OCR
    private const ELIGIBLE_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/tiff',
        'image/bmp',
        'image/gif',
        'image/webp',
    ];

    public function __construct()
    {
        $this->endpoint = config('services.ocr_space.endpoint', 'https://api.ocr.space/parse/image');
        $this->apiKey   = config('services.ocr_space.key', '');
        $this->engine   = (int) config('services.ocr_space.engine', 2);
        $this->language = config('services.ocr_space.language', 'por');
    }

    public function isEligible(OpportunityDocument $document): bool
    {
        return in_array($document->mime_type, self::ELIGIBLE_TYPES, true);
    }

    /**
     * Process a document through OCR.space and persist the result.
     * Returns null if the document is not eligible or already processed.
     */
    public function process(OpportunityDocument $document): ?OcrResult
    {
        if (! $this->isEligible($document)) {
            return null;
        }

        // Already processed — never overwrite
        if ($document->ocr_processed) {
            return $document->ocrResult;
        }

        if (! $this->apiKey) {
            Log::warning('OcrService: OCR_SPACE_API_KEY not configured.');
            return null;
        }

        try {
            $fileContents = Storage::disk($document->disk)->get($document->stored_path);

            if (! $fileContents) {
                Log::error("OcrService: could not read file {$document->stored_path}");
                return null;
            }

            $response = Http::timeout(60)
                ->asMultipart()
                ->post($this->endpoint, [
                    ['name' => 'apikey',       'contents' => $this->apiKey],
                    ['name' => 'OCREngine',    'contents' => (string) $this->engine],
                    ['name' => 'language',     'contents' => $this->language],
                    ['name' => 'isOverlayRequired', 'contents' => 'false'],
                    ['name' => 'file',
                     'contents' => $fileContents,
                     'filename' => $document->original_name,
                     'headers'  => ['Content-Type' => $document->mime_type ?? 'application/octet-stream'],
                    ],
                ]);

            $data = $response->json();

            return $this->persist($document, $data);

        } catch (RequestException $e) {
            Log::error('OcrService HTTP error', ['doc' => $document->id, 'error' => $e->getMessage()]);
            return $this->persistError($document, $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('OcrService unexpected error', ['doc' => $document->id, 'error' => $e->getMessage()]);
            return $this->persistError($document, $e->getMessage());
        }
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function persist(OpportunityDocument $document, array $data): OcrResult
    {
        $hasErrors  = (bool) ($data['IsErroredOnProcessing'] ?? false);
        $errorMsg   = $data['ErrorMessage'][0] ?? ($data['ErrorDetails'] ?? null);

        $pages      = $data['ParsedResults'] ?? [];
        $rawText    = implode("\n", array_column($pages, 'ParsedText'));
        $confidence = isset($pages[0]['TextOverlay']) ? null : null; // API doesn't expose per-call confidence directly

        $result = OcrResult::create([
            'document_id'    => $document->id,
            'opportunity_id' => $document->opportunity_id,
            'raw_text'       => $rawText,
            'parsed_pages'   => $pages,
            'extracted_data' => null,
            'engine_used'    => $this->engine,
            'confidence_pct' => $confidence,
            'has_errors'     => $hasErrors,
            'error_message'  => $errorMsg,
            'processed_at'   => now(),
        ]);

        $document->update(['ocr_processed' => true]);

        return $result;
    }

    private function persistError(OpportunityDocument $document, string $message): OcrResult
    {
        $result = OcrResult::create([
            'document_id'    => $document->id,
            'opportunity_id' => $document->opportunity_id,
            'raw_text'       => '',
            'has_errors'     => true,
            'error_message'  => $message,
            'engine_used'    => $this->engine,
            'processed_at'   => now(),
        ]);

        $document->update(['ocr_processed' => true]);

        return $result;
    }
}
