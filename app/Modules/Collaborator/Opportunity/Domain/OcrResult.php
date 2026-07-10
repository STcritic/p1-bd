<?php

namespace App\Modules\Collaborator\Opportunity\Domain;

use Illuminate\Database\Eloquent\Model;

class OcrResult extends Model
{
    public $timestamps = false;

    protected $table = 'ocr_results';

    protected $fillable = [
        'document_id', 'opportunity_id',
        'raw_text', 'parsed_pages', 'extracted_data',
        'engine_used', 'confidence_pct',
        'has_errors', 'error_message', 'processed_at',
    ];

    protected $casts = [
        'parsed_pages'   => 'array',
        'extracted_data' => 'array',
        'has_errors'     => 'boolean',
        'processed_at'   => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(OpportunityDocument::class, 'document_id');
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function fullText(): string
    {
        return trim($this->raw_text ?? '');
    }
}
