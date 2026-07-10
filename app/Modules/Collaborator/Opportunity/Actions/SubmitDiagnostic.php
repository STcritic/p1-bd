<?php

namespace App\Modules\Collaborator\Opportunity\Actions;

use App\Jobs\ProcessDocumentOcr;
use App\Modules\Collaborator\Opportunity\Context\ContextEngine;
use App\Modules\Collaborator\Opportunity\Decision\DecisionEngine;
use App\Modules\Collaborator\Opportunity\Domain\DiagnosticResponse;
use App\Modules\Collaborator\Opportunity\Domain\DiagnosticSession;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityDocument;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent;
use App\Modules\Collaborator\Opportunity\Ocr\OcrService;
use App\Modules\Collaborator\Opportunity\Workflow\WorkflowEngine;
use App\Services\WebsiteNotificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Processes a complete diagnostic form submission from the client portal.
 * Runs inside a transaction:
 *  1. Persist all responses
 *  2. Mark session as submitted
 *  3. Queue OCR for eligible documents
 *  4. Rebuild context snapshot
 *  5. Run decision engine
 *  6. Advance opportunity to diagnosis_received
 */
final class SubmitDiagnostic
{
    public function __construct(
        private readonly WorkflowEngine $workflow,
        private readonly ContextEngine  $context,
        private readonly DecisionEngine $decision,
        private readonly OcrService     $ocr,
        private readonly WebsiteNotificationService $notifications,
    ) {}

    public function execute(DiagnosticSession $session, array $answers, array $files = []): void
    {
        $opportunity = $session->opportunity;
        $guide       = config("diagnostic_guides.{$session->service_slug}", config('diagnostic_guides._default'));

        DB::transaction(function () use ($session, $opportunity, $answers, $files, $guide): void {

            // 1. Persist responses
            foreach ($guide['groups'] as $group) {
                if (! $this->groupVisible($group['conditions'] ?? [], $answers)) continue;

                foreach ($group['questions'] as $question) {
                    $key = $question['key'];
                    if ($question['type'] === 'file') continue; // handled separately
                    if (! isset($answers[$key])) continue;

                    DiagnosticResponse::updateOrCreate(
                        ['diagnostic_session_id' => $session->id, 'question_key' => $key],
                        [
                            'opportunity_id' => $opportunity->id,
                            'group_key'      => $group['key'],
                            'question_label' => $question['label'],
                            'answer_value'   => (array) $answers[$key],
                        ]
                    );
                }
            }

            // 2. Process uploaded files
            foreach ($files as $questionKey => $file) {
                if (! ($file instanceof UploadedFile)) continue;

                $path = $file->store("opportunities/{$opportunity->id}/diagnostic", 'local');

                $doc = OpportunityDocument::create([
                    'opportunity_id'       => $opportunity->id,
                    'diagnostic_session_id'=> $session->id,
                    'original_name'        => $file->getClientOriginalName(),
                    'stored_path'          => $path,
                    'disk'                 => 'local',
                    'mime_type'            => $file->getMimeType(),
                    'file_size'            => $file->getSize(),
                    'question_key'         => $questionKey,
                    'uploaded_by'          => 'client',
                    'ocr_eligible'         => false,
                ]);

                $doc->update(['ocr_eligible' => $this->ocr->isEligible($doc)]);

                if ($doc->ocr_eligible) {
                    $doc->update(['ocr_queued_at' => now()]);
                    ProcessDocumentOcr::dispatch($doc->id);

                    OpportunityEvent::create([
                        'opportunity_id' => $opportunity->id,
                        'event_type'     => OpportunityEvent::DOCUMENT_UPLOADED,
                        'actor_type'     => 'client',
                        'description'    => 'Documento "' . $file->getClientOriginalName() . '" recebido e enviado para OCR.',
                        'payload'        => ['document_id' => $doc->id],
                        'occurred_at'    => now(),
                    ]);
                }
            }

            // 3. Mark session submitted
            $session->update(['submitted_at' => now(), 'draft_answers' => null]);

            OpportunityEvent::create([
                'opportunity_id' => $opportunity->id,
                'event_type'     => OpportunityEvent::DIAGNOSTIC_RECEIVED,
                'actor_type'     => 'client',
                'description'    => 'Diagnóstico submetido pelo cliente.',
                'occurred_at'    => now(),
            ]);

        });

        // 4. Rebuild context (outside transaction for performance)
        $this->context->refresh($opportunity->fresh());

        // 5. Decision engine
        $this->decision->evaluate($opportunity->fresh());

        // 6. Advance workflow
        $opportunity->refresh();
        if ($opportunity->canTransitionTo('diagnosis_received')) {
            $this->workflow->transition(
                $opportunity, 'diagnosis_received', 'system', null,
                'Diagnóstico recebido e contexto consolidado automaticamente.'
            );
        }

        $this->notifications->diagnosticSubmitted($session->fresh(['opportunity.admin']));
    }

    private function groupVisible(array $conditions, array $answers): bool
    {
        foreach ($conditions as $cond) {
            $val = $answers[$cond['field']] ?? null;
            $expected = $cond['value'];
            $match = match ($cond['operator']) {
                'eq'  => $val == $expected,
                'in'  => in_array($val, (array) $expected),
                default => true,
            };
            if (! $match) return false;
        }
        return true;
    }
}
