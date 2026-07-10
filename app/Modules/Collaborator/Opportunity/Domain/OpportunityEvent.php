<?php

namespace App\Modules\Collaborator\Opportunity\Domain;

use Illuminate\Database\Eloquent\Model;

class OpportunityEvent extends Model
{
    public $timestamps = false;

    protected $table = 'opportunity_events';

    protected $fillable = [
        'opportunity_id', 'event_type', 'from_status', 'to_status',
        'actor_type', 'actor_id', 'description', 'payload', 'occurred_at',
    ];

    protected $casts = [
        'payload'     => 'array',
        'occurred_at' => 'datetime',
    ];

    // ── Event type constants ──────────────────────────────────────────────────

    const STATE_CHANGED        = 'state_changed';
    const NOTE_ADDED           = 'note_added';
    const DOCUMENT_UPLOADED    = 'document_uploaded';
    const OCR_PROCESSED        = 'ocr_processed';
    const DIAGNOSTIC_SENT      = 'diagnostic_sent';
    const DIAGNOSTIC_OPENED    = 'diagnostic_opened';
    const DIAGNOSTIC_SAVED     = 'diagnostic_saved';
    const DIAGNOSTIC_RECEIVED  = 'diagnostic_received';
    const PROPOSAL_GENERATED   = 'proposal_generated';
    const CONTEXT_REFRESHED    = 'context_refreshed';

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function iconForType(): string
    {
        return match($this->event_type) {
            self::STATE_CHANGED       => '→',
            self::NOTE_ADDED          => '✎',
            self::DOCUMENT_UPLOADED   => '📎',
            self::OCR_PROCESSED       => '⊙',
            self::DIAGNOSTIC_SENT     => '✉',
            self::DIAGNOSTIC_OPENED   => '👁',
            self::DIAGNOSTIC_SAVED    => '◌',
            self::DIAGNOSTIC_RECEIVED => '✔',
            self::PROPOSAL_GENERATED  => '▤',
            self::CONTEXT_REFRESHED   => '↺',
            default                   => '·',
        };
    }
}
