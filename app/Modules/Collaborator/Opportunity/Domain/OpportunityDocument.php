<?php

namespace App\Modules\Collaborator\Opportunity\Domain;

use Illuminate\Database\Eloquent\Model;

class OpportunityDocument extends Model
{
    protected $table = 'opportunity_documents';

    protected $fillable = [
        'opportunity_id', 'diagnostic_session_id', 'original_name',
        'stored_path', 'disk', 'mime_type', 'file_size',
        'question_key', 'uploaded_by',
        'ocr_eligible', 'ocr_processed', 'ocr_queued_at',
    ];

    protected $casts = [
        'ocr_eligible'  => 'boolean',
        'ocr_processed' => 'boolean',
        'ocr_queued_at' => 'datetime',
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function session()
    {
        return $this->belongsTo(DiagnosticSession::class, 'diagnostic_session_id');
    }

    public function ocrResult()
    {
        return $this->hasOne(OcrResult::class, 'document_id');
    }

    public function isOcrEligible(): bool
    {
        return $this->ocr_eligible && ! $this->ocr_processed;
    }

    public function fileSizeFormatted(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}
