<?php

namespace App\Modules\Collaborator\Opportunity\Workflow;

use App\Modules\Collaborator\Opportunity\Domain\Opportunity;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * WorkflowEngine — single entry point for all state transitions.
 *
 * Responsibilities:
 *   - Validate transition is allowed (config-driven)
 *   - Apply the transition atomically
 *   - Record an event in opportunity_events
 *   - Enforce pre-conditions (e.g., must have completed diagnosis before building)
 *
 * No business logic belongs here — only topology enforcement.
 */
final class WorkflowEngine
{
    public function __construct(
        private readonly array $workflowConfig
    ) {}

    /**
     * Transition an opportunity to a new state.
     *
     * @throws RuntimeException if transition is not allowed or pre-conditions fail
     */
    public function transition(
        Opportunity $opportunity,
        string $toStatus,
        string $actorType = 'collaborator',
        ?int $actorId = null,
        ?string $description = null,
        array $payload = []
    ): void {
        $fromStatus = $opportunity->status;

        $this->assertTransitionAllowed($opportunity, $toStatus);
        $this->assertPreConditions($opportunity, $toStatus);

        DB::transaction(function () use ($opportunity, $fromStatus, $toStatus, $actorType, $actorId, $description, $payload): void {
            $opportunity->update([
                'previous_status'  => $fromStatus,
                'status'           => $toStatus,
                'status_changed_at'=> now(),
            ]);

            OpportunityEvent::create([
                'opportunity_id' => $opportunity->id,
                'event_type'     => OpportunityEvent::STATE_CHANGED,
                'from_status'    => $fromStatus,
                'to_status'      => $toStatus,
                'actor_type'     => $actorType,
                'actor_id'       => $actorId,
                'description'    => $description ?? $this->defaultDescription($fromStatus, $toStatus),
                'payload'        => $payload,
                'occurred_at'    => now(),
            ]);
        });
    }

    /**
     * Log a non-transition event (note, upload, OCR, etc.)
     */
    public function logEvent(
        Opportunity $opportunity,
        string $eventType,
        string $description,
        string $actorType = 'system',
        ?int $actorId = null,
        array $payload = []
    ): void {
        OpportunityEvent::create([
            'opportunity_id' => $opportunity->id,
            'event_type'     => $eventType,
            'actor_type'     => $actorType,
            'actor_id'       => $actorId,
            'description'    => $description,
            'payload'        => $payload,
            'occurred_at'    => now(),
        ]);
    }

    /**
     * Return which transitions are currently available.
     */
    public function availableTransitions(Opportunity $opportunity): array
    {
        $allowed = $this->workflowConfig['transitions'][$opportunity->status] ?? [];

        return array_filter($allowed, function (string $state) use ($opportunity): bool {
            try {
                $this->assertPreConditions($opportunity, $state);
                return true;
            } catch (RuntimeException) {
                return false;
            }
        });
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function assertTransitionAllowed(Opportunity $opportunity, string $toStatus): void
    {
        $allowed = $this->workflowConfig['transitions'][$opportunity->status] ?? [];

        if (! in_array($toStatus, $allowed, true)) {
            throw new RuntimeException(
                "Transição inválida: '{$opportunity->status}' → '{$toStatus}'. " .
                "Transições permitidas: " . implode(', ', $allowed) . '.'
            );
        }
    }

    private function assertPreConditions(Opportunity $opportunity, string $toStatus): void
    {
        $requiresDiagnosis = $this->workflowConfig['requires_diagnosis'] ?? [];

        if (in_array($toStatus, $requiresDiagnosis, true) && ! $opportunity->hasCompletedDiagnosis()) {
            throw new RuntimeException(
                "Não é possível avançar para '{$toStatus}' sem diagnóstico submetido pelo cliente."
            );
        }
    }

    private function defaultDescription(string $from, string $to): string
    {
        $fromLabel = $this->workflowConfig['states'][$from]['label'] ?? $from;
        $toLabel   = $this->workflowConfig['states'][$to]['label']   ?? $to;
        return "Estado alterado de «{$fromLabel}» para «{$toLabel}».";
    }
}
