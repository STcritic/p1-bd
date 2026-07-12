<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementAdmin;
use App\Modules\Collaborator\Opportunity\Actions\CreateOpportunity;
use App\Modules\Collaborator\Opportunity\Actions\SendDiagnosticSession;
use App\Modules\Collaborator\Opportunity\Builders\OpportunityProposalBuilder;
use App\Modules\Collaborator\Opportunity\Context\ContextEngine;
use App\Modules\Collaborator\Opportunity\Decision\DecisionEngine;
use App\Models\Proposal;
use App\Modules\Collaborator\Opportunity\Domain\Opportunity;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityDocument;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent;
use App\Modules\Collaborator\Opportunity\PreProposal\PreProposalBuilder;
use App\Modules\Collaborator\Opportunity\PreProposal\PreProposalViewModel;
use App\Modules\Collaborator\Opportunity\ViewModels\OpportunityViewModel;
use App\Modules\Collaborator\Opportunity\Workflow\WorkflowEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CollaboratorOpportunityController extends Controller
{
    public function __construct(
        private readonly WorkflowEngine  $workflow,
        private readonly ContextEngine   $context,
        private readonly DecisionEngine  $decision,
    ) {}

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $admin = $this->currentAdmin();

        $opportunities = ($admin->is_master ? Opportunity::query() : Opportunity::forAdmin($admin->id))
            ->with(['latestSession'])
            ->orderByRaw("CASE status
                WHEN 'diagnosis_received' THEN 0
                WHEN 'building'           THEN 1
                WHEN 'review'             THEN 2
                WHEN 'ready_for_approval' THEN 3
                WHEN 'awaiting_client'    THEN 4
                WHEN 'qualification'      THEN 5
                WHEN 'diagnosis'          THEN 6
                WHEN 'draft'              THEN 7
                WHEN 'approved'           THEN 8
                WHEN 'sent'               THEN 9
                WHEN 'negotiation'        THEN 10
                WHEN 'awarded'            THEN 11
                WHEN 'closed'             THEN 12
                ELSE 99 END")
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('announcements.opportunities.index', [
            'opportunities' => $opportunities,
            'services'      => config('service_guides.pt', []),
            'states'        => config('opportunity_workflow.states', []),
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(): View
    {
        return view('announcements.opportunities.create', [
            'services' => config('service_guides.pt', []),
        ]);
    }

    public function store(Request $request, CreateOpportunity $action): RedirectResponse
    {
        $data = $request->validate([
            'service_slug'    => ['required', 'string'],
            'client_name'     => ['required', 'string', 'max:190'],
            'client_contact'  => ['nullable', 'string', 'max:190'],
            'client_email'    => ['nullable', 'email', 'max:190'],
            'client_company'  => ['nullable', 'string', 'max:190'],
            'client_industry' => ['nullable', 'string', 'max:190'],
            'internal_notes'  => ['nullable', 'string', 'max:2000'],
            'expected_close_at'=> ['nullable', 'date'],
        ]);

        $adminId     = Session::get('announcement_admin_id');
        $opportunity = $action->execute($data, $adminId);

        return redirect()
            ->route('collaborator.opportunities.show', $opportunity)
            ->with('success', "Oportunidade {$opportunity->reference} criada com sucesso.");
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Opportunity $opportunity): View
    {
        $this->authorizeOpportunity($opportunity);

        $opportunity->load(['events', 'diagnosticSessions.responses', 'documents.ocrResult', 'latestSession']);

        return view('announcements.opportunities.show', [
            'vm'     => new OpportunityViewModel($opportunity),
            'guide'  => config("diagnostic_guides.{$opportunity->service_slug}",
                        config('diagnostic_guides._default')),
        ]);
    }

    // ── Workflow transition ───────────────────────────────────────────────────

    public function transition(Request $request, Opportunity $opportunity): RedirectResponse
    {
        $this->authorizeOpportunity($opportunity);

        $data = $request->validate([
            'to_status'   => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->workflow->transition(
                $opportunity,
                $data['to_status'],
                'collaborator',
                Session::get('announcement_admin_id'),
                $data['description'] ?? null
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['transition' => $e->getMessage()]);
        }

        return redirect()
            ->route('collaborator.opportunities.show', $opportunity)
            ->with('success', 'Estado actualizado com sucesso.');
    }

    // ── Send diagnostic link ──────────────────────────────────────────────────

    public function sendDiagnostic(Request $request, Opportunity $opportunity, SendDiagnosticSession $action): RedirectResponse
    {
        $this->authorizeOpportunity($opportunity);

        $data = $request->validate([
            'days_valid' => ['nullable', 'integer', 'min:1', 'max:60'],
        ]);

        $session = $action->execute(
            $opportunity,
            Session::get('announcement_admin_id'),
            $data['days_valid'] ?? 14
        );

        return redirect()
            ->route('collaborator.opportunities.show', $opportunity)
            ->with('portal_url', $session->portalUrl())
            ->with('success', $opportunity->client_email
                ? 'Link de diagnóstico gerado e enviado ao cliente.'
                : 'Link de diagnóstico gerado. Copie e envie ao cliente.');
    }

    // ── Refresh context ───────────────────────────────────────────────────────

    public function refreshContext(Opportunity $opportunity): RedirectResponse
    {
        $this->authorizeOpportunity($opportunity);

        $this->context->refresh($opportunity->fresh());
        $this->decision->evaluate($opportunity->fresh());

        return redirect()
            ->route('collaborator.opportunities.show', $opportunity)
            ->with('success', 'Contexto e score actualizados.');
    }

    // ── Add note ─────────────────────────────────────────────────────────────

    public function addNote(Request $request, Opportunity $opportunity): RedirectResponse
    {
        $this->authorizeOpportunity($opportunity);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $this->workflow->logEvent(
            $opportunity,
            OpportunityEvent::NOTE_ADDED,
            $data['note'],
            'collaborator',
            Session::get('announcement_admin_id')
        );

        return back()->with('success', 'Nota adicionada.');
    }

    // ── Pre-Proposal ─────────────────────────────────────────────────────────

    public function preProposal(Request $request, Opportunity $opportunity, PreProposalBuilder $builder): View
    {
        $this->authorizeOpportunity($opportunity);

        $opportunity->load(['latestSession']);

        $session   = $opportunity->latestSession;
        $portalUrl = ($session && $session->isOpen()) ? $session->portalUrl() : null;

        $lang        = in_array($request->query('lang'), ['pt', 'en']) ? $request->query('lang') : Session::get('collab_lang', 'pt');
        $preProposal = $builder->build($opportunity, $portalUrl, $lang);

        OpportunityEvent::create([
            'opportunity_id' => $opportunity->id,
            'event_type'     => OpportunityEvent::PROPOSAL_GENERATED,
            'actor_type'     => 'collaborator',
            'actor_id'       => Session::get('announcement_admin_id'),
            'description'    => 'Pré-proposta executiva gerada.',
            'occurred_at'    => now(),
        ]);

        return view('announcements.opportunities.pre-proposal', [
            'vm' => new PreProposalViewModel($preProposal, config('proposal_identity', [])),
        ]);
    }

    // ── Generate full proposal from context → save to Guardadas ─────────────

    public function generateProposal(
        Request $request,
        Opportunity $opportunity,
        OpportunityProposalBuilder $builder,
    ): RedirectResponse {
        $this->authorizeOpportunity($opportunity);

        $opportunity->load(['diagnosticSessions.responses', 'ocrResults']);

        // Numeric overrides from the fee form
        $overrides = array_filter([
            'fee'              => $request->input('fee') !== null ? (float) $request->input('fee') : null,
            'expenses'         => $request->input('expenses') !== null ? (float) $request->input('expenses') : null,
            'vat_rate'         => $request->input('vat_rate') !== null ? (float) $request->input('vat_rate') : null,
            'pricing_package'  => $request->input('pricing_package'),
            'payment_terms'    => $request->input('payment_terms'),
            'financial_notes'  => $request->input('financial_notes'),
            'lang'             => $request->input('lang'),
        ], fn ($v) => $v !== null && $v !== '');

        $formData = $builder->buildFormData($opportunity, $overrides);
        $adminId  = Session::get('announcement_admin_id');

        $saved = Proposal::create([
            'announcement_admin_id' => $adminId,
            'reference'             => $formData['proposal_reference'],
            'service_slug'          => $formData['service_slug'],
            'service_title'         => $opportunity->service_title,
            'client_name'           => $formData['client_name'],
            'client_contact'        => $formData['client_contact'] ?? null,
            'form_data'             => $formData,
            'status'                => 'rascunho',
            'expires_at'            => $formData['valid_until'] ?? null,
        ]);

        // Link proposal back to opportunity
        $opportunity->update(['proposal_id' => $saved->id]);

        OpportunityEvent::create([
            'opportunity_id' => $opportunity->id,
            'event_type'     => OpportunityEvent::PROPOSAL_GENERATED,
            'actor_type'     => 'collaborator',
            'actor_id'       => $adminId,
            'description'    => 'Proposta técnica e financeira gerada e guardada (ref: ' . $saved->reference . ').',
            'occurred_at'    => now(),
        ]);

        return redirect()
            ->route('collaborator.proposals.show', $saved)
            ->with('success', 'Proposta gerada e guardada com sucesso.');
    }

    // ── Download client document ──────────────────────────────────────────────

    public function downloadDocument(Opportunity $opportunity, OpportunityDocument $document): mixed
    {
        $this->authorizeOpportunity($opportunity);
        abort_unless((int) $document->opportunity_id === (int) $opportunity->id, 404);

        return Storage::disk($document->disk ?? 'local')
            ->download($document->stored_path, $document->original_name);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function destroy(Opportunity $opportunity): RedirectResponse
    {
        $this->authorizeOpportunity($opportunity);
        $opportunity->delete();

        return redirect()
            ->route('collaborator.opportunities.index')
            ->with('success', 'Oportunidade eliminada.');
    }

    // ── Auth helper ───────────────────────────────────────────────────────────

    private function authorizeOpportunity(Opportunity $opportunity): void
    {
        $admin = $this->currentAdmin();

        if ($admin->is_master) {
            return;
        }

        abort_if((int) $opportunity->announcement_admin_id !== (int) $admin->id, 403);
    }

    private function currentAdmin(): AnnouncementAdmin
    {
        return AnnouncementAdmin::query()->findOrFail(Session::get('announcement_admin_id'));
    }
}
