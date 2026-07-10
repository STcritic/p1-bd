<?php

namespace App\Modules\Collaborator\Opportunity\ViewModels;

use App\Modules\Collaborator\Opportunity\Domain\Opportunity;

/**
 * Wraps an Opportunity for Blade consumption.
 * Computes display values, never touches DB.
 */
final class OpportunityViewModel
{
    public function __construct(
        private readonly Opportunity $opportunity
    ) {}

    public function __get(string $name): mixed
    {
        return $this->opportunity->$name;
    }

    // ── Identity ──────────────────────────────────────────────────────────────

    public function id(): int         { return $this->opportunity->id; }
    public function ref(): string     { return $this->opportunity->reference ?? '—'; }
    public function clientName(): string { return $this->opportunity->client_name; }
    public function serviceTitle(): string { return $this->opportunity->service_title; }

    // ── Status ────────────────────────────────────────────────────────────────

    public function statusLabel(string $lang = 'pt'): string { return $this->opportunity->statusLabel($lang); }
    public function statusColor(): string { return $this->opportunity->statusColor(); }
    public function progressPct(): int    { return $this->opportunity->progressPct(); }

    public function currentStep(string $lang = 'pt'): array
    {
        $step = $this->opportunity->currentStep();
        if ($lang === 'en') {
            if (isset($step['action_en'])) $step['action'] = $step['action_en'];
            if (isset($step['guide_en']))  $step['guide']  = $step['guide_en'];
        }
        return $step;
    }

    public function allowedTransitions(): array { return $this->opportunity->allowedTransitions(); }

    public function transitionOptions(string $lang = 'pt'): array
    {
        $states = config('opportunity_workflow.states', []);
        $labelKey = $lang === 'en' ? 'label_en' : 'label';
        return array_map(
            fn (string $s) => ['state' => $s, 'label' => $states[$s][$labelKey] ?? $states[$s]['label'] ?? $s],
            $this->opportunity->allowedTransitions()
        );
    }

    // ── Score ─────────────────────────────────────────────────────────────────

    public function hasScore(): bool
    {
        return ! empty($this->opportunity->score_data);
    }

    public function totalScore(): int
    {
        return $this->opportunity->score_data['total'] ?? 0;
    }

    public function riskLevel(): string
    {
        return $this->opportunity->score_data['risk_level'] ?? '—';
    }

    public function riskFlags(): array
    {
        return $this->opportunity->score_data['risk_flags'] ?? [];
    }

    public function scoreDimensions(): array
    {
        return $this->opportunity->score_data['dimensions'] ?? [];
    }

    public function decisionArguments(): array
    {
        return $this->opportunity->score_data['arguments'] ?? [];
    }

    // ── Diagnostic ────────────────────────────────────────────────────────────

    public function hasDiagnosis(): bool { return $this->opportunity->hasCompletedDiagnosis(); }

    public function latestPortalUrl(): ?string
    {
        $session = $this->opportunity->latestSession;
        return $session?->isOpen() ? $session->portalUrl() : null;
    }

    public function latestSessionExpiry(): ?string
    {
        return $this->opportunity->latestSession?->expires_at?->format('d/m/Y');
    }

    // ── Context ───────────────────────────────────────────────────────────────

    public function hasContext(): bool
    {
        return ! empty($this->opportunity->context_snapshot);
    }

    public function contextValue(string $key, mixed $default = null): mixed
    {
        return $this->opportunity->context_snapshot[$key] ?? $default;
    }

    // ── Timeline ──────────────────────────────────────────────────────────────

    public function timeline(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->opportunity->events()->orderByDesc('occurred_at')->get();
    }

    // ── Documents ─────────────────────────────────────────────────────────────

    public function documents(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->opportunity->documents;
    }

    // ── Tags ─────────────────────────────────────────────────────────────────

    public function tags(): array { return $this->opportunity->tags ?? []; }

    // ── Fee suggestion (recruitment) ──────────────────────────────────────────

    public function suggestedFee(): ?float
    {
        $ctx  = $this->opportunity->context_snapshot ?? [];
        $slug = $this->opportunity->service_slug;

        if ($slug !== 'recrutamento-seleccao') return null;

        $monthly = (float) ($ctx['salario_max'] ?? $ctx['salario_min'] ?? 0);
        if ($monthly <= 0) return null;

        $annual = $monthly * 12;
        $isExec = ($ctx['nivel_hierarquico'] ?? '') === 'executivo';
        $bands  = config('proposal_commercial_policy.recrutamento-seleccao.bands', []);

        if ($isExec) {
            foreach ($bands as $band) {
                if (isset($band['rate_min'])) {
                    return round($annual * $band['rate_min'] / 100, 2);
                }
            }
        }

        $rate = $annual <= 1_000_000 ? ($bands[0]['rate'] ?? 10)
              : ($annual <= 2_000_000 ? ($bands[1]['rate'] ?? 12.5) : ($bands[2]['rate'] ?? 15));

        return round($annual * $rate / 100, 2);
    }

    public function suggestedFeeExplanation(): string
    {
        $ctx    = $this->opportunity->context_snapshot ?? [];
        $monthly = (float) ($ctx['salario_max'] ?? $ctx['salario_min'] ?? 0);
        if ($monthly <= 0) return '';

        $annual  = $monthly * 12;
        $isExec  = ($ctx['nivel_hierarquico'] ?? '') === 'executivo';
        $bands   = config('proposal_commercial_policy.recrutamento-seleccao.bands', []);

        if ($isExec) {
            foreach ($bands as $band) {
                if (isset($band['rate_min'])) {
                    return "MZN {$monthly}/mês × 12 = " . number_format($annual, 0, ',', ' ') . " anual × {$band['rate_min']}% (headhunting)";
                }
            }
        }

        $rate = $annual <= 1_000_000 ? ($bands[0]['rate'] ?? 10)
              : ($annual <= 2_000_000 ? ($bands[1]['rate'] ?? 12.5) : ($bands[2]['rate'] ?? 15));

        return 'MZN ' . number_format($monthly, 0, ',', ' ') . '/mês × 12 = ' .
               number_format($annual, 0, ',', ' ') . ' anual × ' . $rate . '%';
    }
}
