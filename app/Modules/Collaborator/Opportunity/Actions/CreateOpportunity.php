<?php

namespace App\Modules\Collaborator\Opportunity\Actions;

use App\Modules\Collaborator\Opportunity\Domain\Opportunity;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent;
use Illuminate\Support\Str;

/**
 * Creates a new opportunity in 'draft' state.
 */
final class CreateOpportunity
{
    public function execute(array $data, int $adminId): Opportunity
    {
        $serviceSlug  = $data['service_slug'];
        $serviceTitle = $this->resolveTitle($serviceSlug);

        $opportunity = Opportunity::create([
            'announcement_admin_id' => $adminId,
            'reference'             => $this->generateReference(),
            'service_slug'          => $serviceSlug,
            'service_title'         => $serviceTitle,
            'client_name'           => $data['client_name'],
            'client_contact'        => $data['client_contact']  ?? null,
            'client_email'          => $data['client_email']    ?? null,
            'client_company'        => $data['client_company']  ?? null,
            'client_industry'       => $data['client_industry'] ?? null,
            'status'                => 'draft',
            'internal_notes'        => $data['internal_notes']  ?? null,
            'expected_close_at'     => $data['expected_close_at'] ?? null,
        ]);

        OpportunityEvent::create([
            'opportunity_id' => $opportunity->id,
            'event_type'     => OpportunityEvent::STATE_CHANGED,
            'to_status'      => 'draft',
            'actor_type'     => 'collaborator',
            'actor_id'       => $adminId,
            'description'    => "Oportunidade criada para {$opportunity->client_name}.",
            'occurred_at'    => now(),
        ]);

        return $opportunity;
    }

    private function generateReference(): string
    {
        return 'OPP-' . strtoupper(Str::random(6)) . '-' . date('Y');
    }

    private function resolveTitle(string $slug): string
    {
        $guides = config('service_guides.pt', []);
        foreach ($guides as $guide) {
            if (($guide['slug'] ?? '') === $slug) return $guide['title'];
        }
        return $slug;
    }
}
