<?php

namespace App\Modules\Collaborator\Opportunity\Domain;

use App\Models\AnnouncementAdmin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'opportunities';

    protected $fillable = [
        'announcement_admin_id', 'reference', 'service_slug', 'service_title',
        'client_name', 'client_contact', 'client_email', 'client_company', 'client_industry',
        'status', 'previous_status', 'status_changed_at',
        'score_data', 'context_snapshot', 'tags', 'proposal_id',
        'internal_notes', 'meta', 'expected_close_at',
    ];

    protected $casts = [
        'score_data'       => 'array',
        'context_snapshot' => 'array',
        'tags'             => 'array',
        'meta'             => 'array',
        'status_changed_at'=> 'datetime',
        'expected_close_at'=> 'date',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function admin()
    {
        return $this->belongsTo(AnnouncementAdmin::class, 'announcement_admin_id');
    }

    public function events()
    {
        return $this->hasMany(OpportunityEvent::class)->orderBy('occurred_at');
    }

    public function diagnosticSessions()
    {
        return $this->hasMany(DiagnosticSession::class)->orderByDesc('created_at');
    }

    public function latestSession()
    {
        return $this->hasOne(DiagnosticSession::class)->latestOfMany();
    }

    public function documents()
    {
        return $this->hasMany(OpportunityDocument::class)->orderByDesc('created_at');
    }

    public function ocrResults()
    {
        return $this->hasMany(OcrResult::class)->orderByDesc('processed_at');
    }

    // ── State helpers ─────────────────────────────────────────────────────────

    public function statusLabel(string $lang = 'pt'): string
    {
        $key = $lang === 'en' ? 'label_en' : 'label';
        return config("opportunity_workflow.states.{$this->status}.{$key}",
               config("opportunity_workflow.states.{$this->status}.label", $this->status));
    }

    public function statusColor(): string
    {
        return config("opportunity_workflow.states.{$this->status}.color", 'gray');
    }

    public function progressPct(): int
    {
        return (int) config("opportunity_workflow.progress.{$this->status}", 0);
    }

    public function currentStep(): array
    {
        return config("opportunity_workflow.steps.{$this->status}", [
            'action' => '', 'guide' => '', 'minutes' => 0, 'next' => null,
        ]);
    }

    public function allowedTransitions(): array
    {
        return config("opportunity_workflow.transitions.{$this->status}", []);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, config('opportunity_workflow.terminal', []));
    }

    public function canTransitionTo(string $state): bool
    {
        return in_array($state, $this->allowedTransitions());
    }

    public function hasTags(string ...$tags): bool
    {
        $current = $this->tags ?? [];
        foreach ($tags as $tag) {
            if (in_array($tag, $current)) return true;
        }
        return false;
    }

    public function hasCompletedDiagnosis(): bool
    {
        return $this->diagnosticSessions()
            ->whereNotNull('submitted_at')
            ->exists();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForAdmin($query, int $adminId)
    {
        return $query->where('announcement_admin_id', $adminId);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', config('opportunity_workflow.terminal', []));
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
