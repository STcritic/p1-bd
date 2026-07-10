<?php

namespace App\Modules\Collaborator\Proposal\ViewModels;

use App\Modules\Collaborator\Proposal\DTO\ProposalData;
use App\Modules\Collaborator\Proposal\Services\FinancialCalculator;
use App\Modules\Collaborator\Proposal\Support\TextCleaner;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProposalViewModel
{
    public function __construct(
        private readonly ProposalData      $proposal,
        private readonly array             $identity,
        private readonly FinancialCalculator $financial,
    ) {}

    // ─── Proxy — transparent access to ProposalData properties ──────────────

    public function __get(string $name): mixed
    {
        return $this->proposal->$name;
    }

    public function __isset(string $name): bool
    {
        return isset($this->proposal->$name);
    }

    // ─── Formatted dates ─────────────────────────────────────────────────────

    public function formattedDate(): string
    {
        return Carbon::parse($this->proposal->proposalDate)->format('d/m/Y');
    }

    public function formattedValidUntil(): string
    {
        return Carbon::parse($this->proposal->validUntil)->format('d/m/Y');
    }

    // ─── Financial formatting ─────────────────────────────────────────────────

    public function money(float $amount): string
    {
        return $this->financial->format($amount);
    }

    // ─── Identity helpers ─────────────────────────────────────────────────────

    public function company(): array
    {
        return $this->identity['company'] ?? [];
    }

    public function lang(): string
    {
        return $this->proposal->lang ?? 'pt';
    }

    public function bank(): array
    {
        return $this->identity['bank_details'] ?? [];
    }

    public function hasBank(): bool
    {
        $bank = $this->bank();

        return collect(['bank_name', 'account_holder', 'account_number', 'nib', 'swift'])
            ->contains(fn (string $key): bool => filled($bank[$key] ?? null));
    }

    public function clients(): Collection
    {
        return collect($this->identity['clients'] ?? [])->take(7);
    }

    public function credibilityMetrics(): Collection
    {
        return collect($this->identity['credibility_metrics'] ?? [])->take(4);
    }

    public function impactMetrics(): Collection
    {
        return collect($this->identity['impact_metrics'] ?? [])->take(4);
    }

    public function caseStudies(): Collection
    {
        return collect($this->identity['case_studies'] ?? [])->take(3);
    }

    public function valueProposition(): array
    {
        return $this->identity['value_proposition'] ?? [];
    }

    public function qualityPrinciples(): array
    {
        return $this->identity['quality_principles'] ?? [];
    }

    public function guarantees(): array
    {
        return $this->identity['guarantees'] ?? [];
    }

    public function commercialTerms(): array
    {
        return $this->identity['commercial_terms'] ?? [];
    }

    public function credentials(): array
    {
        return $this->identity['credentials'] ?? [];
    }

    public function recruitmentPolicy(): array
    {
        return $this->proposal->recruitmentPolicy;
    }

    // ─── Text helpers ─────────────────────────────────────────────────────────

    public function lines(?string $text): array
    {
        return TextCleaner::lines($text);
    }
}
