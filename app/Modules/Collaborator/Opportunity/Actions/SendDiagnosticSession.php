<?php

namespace App\Modules\Collaborator\Opportunity\Actions;

use App\Modules\Collaborator\Opportunity\Domain\DiagnosticSession;
use App\Modules\Collaborator\Opportunity\Domain\Opportunity;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent;
use App\Modules\Collaborator\Opportunity\Workflow\WorkflowEngine;
use App\Services\WebsiteNotificationService;

/**
 * Creates a diagnostic session (client portal link) for an opportunity.
 * If the opportunity has a client email, the link is sent automatically.
 */
final class SendDiagnosticSession
{
    public function __construct(
        private readonly WorkflowEngine $workflow,
        private readonly WebsiteNotificationService $notifications,
    ) {}

    public function execute(Opportunity $opportunity, int $adminId, ?int $daysValid = 14): DiagnosticSession
    {
        // Invalidate any previous open session for this opportunity
        DiagnosticSession::where('opportunity_id', $opportunity->id)
            ->whereNull('submitted_at')
            ->whereNull('expires_at')
            ->orWhere(fn ($q) => $q->where('opportunity_id', $opportunity->id)
                ->whereNull('submitted_at')
                ->where('expires_at', '>', now()))
            ->update(['expires_at' => now()]);

        $session = DiagnosticSession::create([
            'opportunity_id' => $opportunity->id,
            'token'          => DiagnosticSession::generateToken($opportunity),
            'service_slug'   => $opportunity->service_slug,
            'expires_at'     => $daysValid ? now()->addDays($daysValid) : null,
            'guide_version'  => '1.0',
        ]);

        OpportunityEvent::create([
            'opportunity_id' => $opportunity->id,
            'event_type'     => OpportunityEvent::DIAGNOSTIC_SENT,
            'actor_type'     => 'collaborator',
            'actor_id'       => $adminId,
            'description'    => "Link de diagnóstico gerado. Válido por {$daysValid} dias.",
            'payload'        => [
                'session_id'  => $session->id,
                'expires_at'  => $session->expires_at?->toDateString(),
                'portal_url'  => $session->portalUrl(),
            ],
            'occurred_at'    => now(),
        ]);

        // Advance to awaiting_client if currently in diagnosis
        if ($opportunity->canTransitionTo('awaiting_client')) {
            $this->workflow->transition(
                $opportunity, 'awaiting_client', 'collaborator', $adminId,
                'Link de diagnóstico enviado ao cliente.'
            );
        }

        $this->notifications->diagnosticLinkGenerated($session);

        return $session;
    }
}
