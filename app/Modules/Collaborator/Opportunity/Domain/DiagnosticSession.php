<?php

namespace App\Modules\Collaborator\Opportunity\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DiagnosticSession extends Model
{
    protected $table = 'diagnostic_sessions';

    protected $fillable = [
        'opportunity_id', 'token', 'service_slug', 'expires_at',
        'opened_at', 'last_saved_at', 'submitted_at',
        'guide_version', 'draft_answers',
        'reminder_sent', 'reminder_sent_at',
    ];

    protected $casts = [
        'expires_at'       => 'datetime',
        'opened_at'        => 'datetime',
        'last_saved_at'    => 'datetime',
        'submitted_at'     => 'datetime',
        'reminder_sent_at' => 'datetime',
        'draft_answers'    => 'array',
        'reminder_sent'    => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function responses()
    {
        return $this->hasMany(DiagnosticResponse::class);
    }

    public function documents()
    {
        return $this->hasMany(OpportunityDocument::class);
    }

    // ── State ─────────────────────────────────────────────────────────────────

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isOpen(): bool
    {
        return ! $this->isSubmitted() && ! $this->isExpired();
    }

    public function portalUrl(): string
    {
        return route('diagnostic.portal', ['token' => $this->token]);
    }

    // ── Factory ───────────────────────────────────────────────────────────────

    public static function generateToken(?Opportunity $opportunity = null): string
    {
        if (! $opportunity) {
            return Str::lower(Str::random(32));
        }

        $client  = explode('-', Str::slug($opportunity->client_name))[0];
        $service = self::serviceCode($opportunity->service_slug);
        $year    = now()->format('Y');
        $suffix  = Str::lower(Str::random(8));

        return "{$client}-{$service}-{$year}-{$suffix}";
    }

    private static function serviceCode(string $slug): string
    {
        return match ($slug) {
            'gestao-desempenho'              => 'gd',
            'carreira-sucessao'              => 'cs',
            'avaliacao-classificacao-cargos' => 'acc',
            'perfil-comportamental'          => 'pc',
            'recrutamento-seleccao'          => 'rec',
            'politicas-procedimentos'        => 'pp',
            'remuneracao-beneficios'         => 'rb',
            'formacao-desenvolvimento'       => 'fd',
            'assessoria-outsourcing-rh'      => 'aor',
            'digitalizacao-rh-endomarketing' => 'drh',
            default                          => Str::limit(Str::slug($slug), 6, ''),
        };
    }
}
