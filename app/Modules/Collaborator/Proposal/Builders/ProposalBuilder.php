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
            timeline:             $data['timeline']    ?: ($defaults['timeline']    ?? ''),
            team:                 $data['team']        ?: implode("\n", $selectedProfilesText),
            assumptions:          $data['assumptions'] ?: ($defaults['assumptions'] ?? ''),
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
        );
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
