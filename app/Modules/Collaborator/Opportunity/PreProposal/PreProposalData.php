<?php

namespace App\Modules\Collaborator\Opportunity\PreProposal;

/**
 * PreProposalData — data structure for the executive pre-proposal document.
 *
 * Sent BEFORE the diagnostic. No price. 3-4 pages.
 * Purpose: demonstrate professionalism, contextualise the challenge,
 * request client participation in the diagnostic.
 */
readonly class PreProposalData
{
    public function __construct(
        // Identity
        public string  $serviceSlug,
        public string  $serviceTitle,
        public string  $clientName,
        public ?string $clientContact,
        public ?string $clientPosition,
        public string  $reference,
        public string  $date,
        public string  $preparedBy,
        public string  $preparedRole,
        public string  $coverImageUrl,

        // Page 2 — Context & Challenge
        public string  $challengeStatement,     // what we know so far about their challenge
        public string  $positioningStatement,   // BD's value for this service
        public array   $contextSignals,         // [ ['label' => ..., 'text' => ...] ]
        public string  $clientContextIntro,     // "Compreendemos que a organização opera em..."

        // Page 3 — Approach & Methodology
        public string  $approachIntro,          // "Como trabalhamos"
        public array   $methodologySteps,       // simplified steps for this service
        public array   $differentiators,        // 2-3 BD key differentiators
        public string  $teamBrief,              // brief team description
        public string  $timelineEstimate,       // "Estimamos X semanas após adjudicação"

        // Page 4 — Next Steps & Diagnostic Call
        public string  $diagnosticIntro,        // why the diagnostic matters
        public array   $diagnosticBenefits,     // what it produces
        public string  $diagnosticCallToAction, // "Partilhe o link de diagnóstico com..."
        public string  $closingStatement,       // final commitment line
        public ?string $portalUrl,              // if session already created
        public ?int    $opportunityId,
        public string  $lang = 'pt',
    ) {}
}
