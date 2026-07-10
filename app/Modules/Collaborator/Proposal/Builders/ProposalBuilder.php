<?php

namespace App\Modules\Collaborator\Proposal\Builders;

use App\Modules\Collaborator\Proposal\DTO\ProposalData;
use App\Modules\Collaborator\Proposal\Services\ContentGeneratorService;
use App\Modules\Collaborator\Proposal\Services\FinancialCalculator;
use App\Modules\Collaborator\Proposal\Support\TextCleaner;
use Illuminate\Support\Carbon;

class ProposalBuilder
{
    public function __construct(
        private readonly ContentGeneratorService $content,
        private readonly FinancialCalculator     $financial,
    ) {}

    public function build(array $data, array $service): ProposalData
    {
        $slug      = $data['service_slug'];
        $packages  = config('proposal_presets.packages', []);
        $complexity= config('proposal_presets.complexity', []);
        $preset    = config("proposal_presets.services.{$slug}", []);
        $profiles  = config('proposal_presets.profiles', []);
        $defaults  = config('proposals.defaults', []);

        $selectedApproaches   = $this->selectedFrom($preset['approaches']  ?? [], $data['selected_approaches']  ?? null);
        $selectedModules      = $this->selectedFrom($preset['modules']     ?? [], $data['selected_modules']     ?? null);
        $selectedDeliverables = $this->selectedFrom(
            $preset['deliverables'] ?? ($service['deliverables'] ?? []),
            $data['selected_deliverables'] ?? null
        );

        $selectedProfileKeys    = $data['selected_profiles'] ?? ($preset['profiles'] ?? []);
        $selectedProfilesText   = collect($selectedProfileKeys)
            ->filter(fn (string $key): bool => isset($profiles[$key]))
            ->map(fn (string $key): string => $profiles[$key])
            ->values()
            ->all();

        if ($selectedProfilesText === []) {
            $selectedProfilesText = $this->selectedFrom($profiles, $preset['profiles'] ?? []);
        }

        $package        = $packages[$data['pricing_package']] ?? reset($packages);
        $complexityLabel= $complexity[$data['complexity_level']] ?? 'Média';
        $pricing        = $preset['pricing'] ?? [];
        $challenge      = TextCleaner::clean($data['challenge']);

        $rawItems     = $data['expense_items'] ?? [];
        $expenseItems = array_values(array_filter(
            $rawItems,
            fn ($item) => !empty($item['label']) || (float) ($item['amount'] ?? 0) > 0,
        ));

        $candidateSalary = isset($data['candidate_salary']) && $data['candidate_salary'] !== ''
            ? (float) $data['candidate_salary']
            : null;

        $recruitType = in_array($data['recruit_type'] ?? '', ['standard', 'headhunting'])
            ? $data['recruit_type']
            : 'standard';

        // Build effective recruitment policy: config defaults + per-proposal overrides
        $policyConfig    = config("proposal_commercial_policy.{$slug}", []);
        $effectiveBands  = [];
        foreach ($policyConfig['bands'] ?? [] as $i => $band) {
            $b = $band;
            if (isset($band['rate'])) {
                $rateKey = "policy_band_{$i}_rate";
                if (isset($data[$rateKey]) && $data[$rateKey] !== '') {
                    $b['rate'] = (float) $data[$rateKey];
                }
            } else {
                if (isset($data["policy_band_{$i}_rate_min"]) && $data["policy_band_{$i}_rate_min"] !== '') {
                    $b['rate_min'] = (float) $data["policy_band_{$i}_rate_min"];
                }
                if (isset($data["policy_band_{$i}_rate_max"]) && $data["policy_band_{$i}_rate_max"] !== '') {
                    $b['rate_max'] = (float) $data["policy_band_{$i}_rate_max"];
                }
            }
            $daysKey = "policy_band_{$i}_days";
            if (isset($data[$daysKey]) && $data[$daysKey] !== '') {
                $b['guarantee_days'] = (int) $data[$daysKey];
            }
            $effectiveBands[] = $b;
        }
        $recruitmentPolicy = $policyConfig;
        if ($effectiveBands) {
            $recruitmentPolicy['bands'] = $effectiveBands;
        }
        if (!empty($data['policy_mass_note'])) {
            $recruitmentPolicy['mass_note'] = $data['policy_mass_note'];
        }
        if (!empty($data['policy_guarantee_note'])) {
            $recruitmentPolicy['guarantee']['note'] = $data['policy_guarantee_note'];
        }

        $fin = $this->financial->calculate(
            (float) ($data['fee']      ?? 0),
            (float) ($data['expenses'] ?? 0),
            (float) ($data['vat_rate'] ?? $defaults['vat_rate'] ?? 16),
        );

        $reference     = $data['proposal_reference'] ?: ($defaults['reference_prefix'] ?? 'BD-PROP-').now()->format('Ymd');
        $validUntil    = $data['valid_until'] ?: Carbon::parse($data['proposal_date'])->addDays((int) ($defaults['validity_days'] ?? 15))->format('Y-m-d');
        $preparedBy    = $data['prepared_by']   ?: ($defaults['prepared_by']   ?? config('proposal_identity.team_members.sandra.name', 'Equipa técnica BD'));
        $preparedRole  = $data['prepared_role'] ?: ($defaults['prepared_role'] ?? config('proposal_identity.team_members.sandra.role', 'Consultoria Empresarial'));
        $coverImages   = config('proposals.cover_images', []);
        $coverImageUrl = $data['cover_image_url'] ?: asset($coverImages[$slug] ?? $coverImages['_default'] ?? 'assets/images/hero-consulting-team.png');

        return new ProposalData(
            serviceSlug:          $slug,
            serviceTitle:         $service['title'],
            serviceValue:         $service['value'] ?? '',
            reference:            $reference,
            proposalDate:         $data['proposal_date'],
            validUntil:           $validUntil,
            clientName:           $data['client_name'],
            clientContact:        $data['client_contact']  ?? null,
            clientPosition:       $data['client_position'] ?? null,
            clientEmail:          $data['client_email']    ?? null,
            clientLocation:       $data['client_location'] ?? null,
            clientIndustry:       $data['client_industry'] ?? null,
            preparedBy:           $preparedBy,
            preparedRole:         $preparedRole,
            coverImageUrl:        $coverImageUrl,
            challenge:            $challenge,
            objectives:           $data['objectives']  ?: $this->content->defaultObjectives($data['client_name'], $service['title'], $selectedApproaches),
            scope:                $data['scope']       ?: $this->content->defaultScope($service, $selectedModules),
            methodology:          $data['methodology'] ?: $this->content->defaultMethodology($slug),
            deliverables:         $data['deliverables']?: implode("\n", $selectedDeliverables),
            timeline:             $data['timeline']    ?: $this->defaultTimeline($slug, $recruitType, $defaults),
            team:                 $data['team']        ?: implode("\n", $selectedProfilesText),
            assumptions:          $this->buildAssumptions($slug, $data, $defaults),
            outOfScope:           $data['out_of_scope']?: ($defaults['out_of_scope']?? ''),
            currency:             $data['currency'],
            fee:                  $fin['fee'],
            expenses:             $fin['expenses'],
            vatRate:              $fin['vat_rate'],
            subtotal:             $fin['subtotal'],
            vat:                  $fin['vat'],
            total:                $fin['total'],
            hasInvestment:        $fin['has_investment'],
            paymentTerms:         $data['payment_terms']   ?: ($defaults['payment_terms'] ?? ''),
            financialNotes:       $data['financial_notes'] ?: $this->content->defaultFinancialNotes($package, $complexityLabel, $pricing),
            pricingPackage:       $package,
            complexityLabel:      $complexityLabel,
            pricingPolicy:        $pricing,
            expenseItems:         $expenseItems,
            candidateSalary:      $candidateSalary,
            recruitmentPolicy:    $recruitmentPolicy,
            recruitType:          $recruitType,
            selectedApproaches:   $selectedApproaches,
            selectedModules:      $selectedModules,
            selectedDeliverables: $selectedDeliverables,
            selectedProfiles:     $selectedProfilesText,
            personalLetter:       $this->content->personalLetter($data, $service, $challenge),
            contextualSummary:    $this->content->contextualSummary($data, $service, $challenge),
            clientContext:        $this->content->clientContext($data, $service, $challenge),
            positioningStatement: $this->content->positioningStatement($slug, $service['title']),
            bdSignature:          $this->content->bdSignature($slug),
            criticalCase:         $this->content->criticalCase($slug, $service['title']),
            featuredCase:         $this->content->featuredCase($slug),
            processFlow:          $this->content->processFlow($slug),
            timelinePlan:         $this->content->timelinePlan($slug),
            successMetrics:       $this->content->successMetrics($slug),
            practicalOutputs:     $this->content->practicalOutputs($slug, $selectedDeliverables),
            technicalTools:       $this->content->technicalTools($slug),
            differentiators:      $this->content->differentiators($slug),
            teamMembers:          $this->content->teamMembers($selectedProfileKeys),
            faqs:                 $this->content->faqs($slug),
            nextSteps:            $this->content->nextSteps($slug),
            closingNote:          $this->content->closingNote($data, $service),
            roadmap:              $this->content->defaultRoadmap($slug, $selectedModules),
            lang:                 $data['lang'] ?? 'pt',
        );
    }

    private function defaultTimeline(string $slug, string $recruitType, array $defaults): string
    {
        if ($slug === 'recrutamento-seleccao') {
            return $recruitType === 'headhunting'
                ? '8 a 12 semanas após adjudicação e definição do perfil executivo.'
                : '4 a 6 semanas após adjudicação e alinhamento do perfil.';
        }

        return $defaults['timeline'] ?? '';
    }

    private function buildAssumptions(string $slug, array $data, array $defaults): string
    {
        $base = $data['assumptions'] ?: ($defaults['assumptions'] ?? '');

        if ($slug === 'recrutamento-seleccao' && ! str_contains($base, 'INEP')) {
            $inep = 'Comunicação prévia ao INEP: nos termos da Lei do Trabalho (Lei n.º 23/2007), o empregador deve notificar o Instituto Nacional do Emprego e Profissional com mínimo de 7 dias úteis antes do início do processo. Responsabilidade do cliente.';
            $base = trim($inep . ($base !== '' ? "\n" . $base : ''));
        }

        return $base;
    }

    private function selectedFrom(array $options, ?array $selected): array
    {
        if ($selected === null || $selected === []) {
            return array_values($options);
        }

        return collect($selected)
            ->filter(fn (string $value): bool => in_array($value, $options, true) || array_key_exists($value, $options))
            ->map(fn (string $value): string => $options[$value] ?? $value)
            ->values()
            ->all();
    }
}
