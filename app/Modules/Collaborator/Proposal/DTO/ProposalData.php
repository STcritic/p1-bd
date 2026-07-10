<?php

namespace App\Modules\Collaborator\Proposal\DTO;

readonly class ProposalData
{
    public function __construct(
        // Identification
        public string  $serviceSlug,
        public string  $serviceTitle,
        public string  $serviceValue,
        public string  $reference,
        public string  $proposalDate,
        public string  $validUntil,

        // Client
        public string  $clientName,
        public ?string $clientContact,
        public ?string $clientPosition,
        public ?string $clientEmail,
        public ?string $clientLocation,
        public ?string $clientIndustry,

        // Authorship
        public string  $preparedBy,
        public string  $preparedRole,
        public string  $coverImageUrl,

        // Brief
        public string  $challenge,

        // Scope
        public string  $objectives,
        public string  $scope,
        public string  $methodology,
        public string  $deliverables,
        public string  $timeline,
        public string  $team,
        public string  $assumptions,
        public string  $outOfScope,

        // Financial
        public string  $currency,
        public float   $fee,
        public float   $expenses,
        public float   $vatRate,
        public float   $subtotal,
        public float   $vat,
        public float   $total,
        public bool    $hasInvestment,
        public string  $paymentTerms,
        public string  $financialNotes,
        public array   $pricingPackage,
        public string  $complexityLabel,
        public array   $pricingPolicy,
        public array   $expenseItems,
        public ?float  $candidateSalary,
        public array   $recruitmentPolicy,
        public string  $recruitType,

        // Selections
        public array   $selectedApproaches,
        public array   $selectedModules,
        public array   $selectedDeliverables,
        public array   $selectedProfiles,

        // Generated content
        public string  $personalLetter,
        public string  $contextualSummary,
        public array   $clientContext,
        public string  $positioningStatement,
        public array   $bdSignature,
        public array   $criticalCase,
        public array   $featuredCase,
        public array   $processFlow,
        public array   $timelinePlan,
        public array   $successMetrics,
        public array   $practicalOutputs,
        public array   $technicalTools,
        public array   $differentiators,
        public array   $teamMembers,
        public array   $faqs,
        public array   $nextSteps,
        public string  $closingNote,
        public array   $roadmap,

        // Language
        public string  $lang = 'pt',
    ) {}
}
