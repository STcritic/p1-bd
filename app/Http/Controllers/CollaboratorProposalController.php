<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateProposalRequest;
use App\Models\AnnouncementAdmin;
use App\Models\Proposal;
use App\Modules\Collaborator\Proposal\Actions\GenerateProposalAction;
use App\Modules\Collaborator\Proposal\Services\FinancialCalculator;
use App\Modules\Collaborator\Proposal\ViewModels\ProposalViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollaboratorProposalController extends Controller
{
    public function __construct(private readonly GenerateProposalAction $action) {}

    public function index(Request $request): View
    {
        $defaults = config('proposals.defaults', []);
        $admin    = $this->currentAdmin($request);

        return view('announcements.proposals.index', [
            'admin'           => $admin,
            'services'        => config('service_guides.pt', []),
            'presets'         => config('proposal_presets', []),
            'recentProposals' => Proposal::where('announcement_admin_id', $admin->id)->latest()->limit(5)->get(),
            'defaults'        => [
                'proposal_reference' => ($defaults['reference_prefix'] ?? 'BD-PROP-') . now()->format('Ymd'),
                'proposal_date'      => now()->format('Y-m-d'),
                'valid_until'        => now()->addDays((int) ($defaults['validity_days'] ?? 15))->format('Y-m-d'),
                'currency'           => $defaults['currency'] ?? 'MZN',
                'vat_rate'           => $defaults['vat_rate'] ?? 16,
            ],
        ]);
    }

    public function generate(GenerateProposalRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $editProposalId = $validated['_edit_proposal_id'] ?? null;
        unset($validated['_edit_proposal_id']);

        $services  = collect(config('service_guides.pt', []));
        $service   = $services->firstWhere('slug', $validated['service_slug']);

        abort_unless($service, 404);

        $admin    = $this->currentAdmin($request);
        $proposal = $this->action->execute($validated, $service);

        $payload = [
            'announcement_admin_id' => $admin->id,
            'reference'             => $proposal->reference,
            'service_slug'          => $proposal->serviceSlug,
            'service_title'         => $proposal->serviceTitle,
            'client_name'           => $proposal->clientName,
            'client_contact'        => $proposal->clientContact,
            'form_data'             => $validated,
            'expires_at'            => $proposal->validUntil ?: null,
        ];

        if ($editProposalId) {
            $saved = Proposal::query()
                ->where('announcement_admin_id', $admin->id)
                ->findOrFail($editProposalId);

            $saved->update($payload);

            return redirect()->route('collaborator.proposals.show', $saved);
        }

        $duplicate = Proposal::query()
            ->where('announcement_admin_id', $admin->id)
            ->where('reference', $proposal->reference)
            ->where('service_slug', $proposal->serviceSlug)
            ->where('client_name', $proposal->clientName)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->latest()
            ->get()
            ->first(fn (Proposal $existing): bool => $existing->form_data == $validated);

        if ($duplicate) {
            return redirect()->route('collaborator.proposals.show', $duplicate);
        }

        $saved = Proposal::create($payload + [
            'status' => 'rascunho',
        ]);

        return redirect()->route('collaborator.proposals.show', $saved);
    }

    public function savedIndex(Request $request): View
    {
        $admin = $this->currentAdmin($request);

        $proposals = Proposal::where('announcement_admin_id', $admin->id)
            ->latest()
            ->paginate(20);

        return view('announcements.proposals.saved', compact('admin', 'proposals'));
    }

    public function edit(Proposal $proposal, Request $request): View
    {
        $admin = $this->currentAdmin($request);
        abort_unless($proposal->announcement_admin_id === $admin->id, 403);

        $fd = $proposal->form_data;

        return view('announcements.proposals.index', [
            'admin'           => $admin,
            'services'        => config('service_guides.pt', []),
            'presets'         => config('proposal_presets', []),
            'recentProposals' => Proposal::where('announcement_admin_id', $admin->id)->latest()->limit(5)->get(),
            'editProposal'    => $proposal,
            'prefill'         => $fd,
            'defaults'        => [
                'proposal_reference' => $fd['proposal_reference'] ?? '',
                'proposal_date'      => $fd['proposal_date']      ?? now()->format('Y-m-d'),
                'valid_until'        => $fd['valid_until']        ?? now()->addDays(15)->format('Y-m-d'),
                'currency'           => $fd['currency']           ?? 'MZN',
                'vat_rate'           => $fd['vat_rate']           ?? 16,
            ],
        ]);
    }

    public function show(Proposal $proposal, Request $request): View
    {
        $admin = $this->currentAdmin($request);
        abort_unless($proposal->announcement_admin_id === $admin->id, 403);

        $services = collect(config('service_guides.pt', []));
        $service  = $services->firstWhere('slug', $proposal->service_slug);
        abort_unless($service, 404);

        $generated = $this->action->execute($proposal->form_data, $service);

        return view('announcements.proposals.show', [
            'admin'         => $admin,
            'vm'            => new ProposalViewModel($generated, config('proposal_identity', []), new FinancialCalculator()),
            'savedProposal' => $proposal,
        ]);
    }

    public function updateStatus(Proposal $proposal, Request $request): RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        abort_unless($proposal->announcement_admin_id === $admin->id, 403);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Proposal::statuses()))],
            'notes'  => ['nullable', 'string', 'max:3000'],
        ]);

        $proposal->update($data);

        return redirect()->route('collaborator.proposals.show', $proposal)
            ->with('success', 'Estado actualizado.');
    }

    public function destroy(Proposal $proposal, Request $request): RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        abort_unless($proposal->announcement_admin_id === $admin->id, 403);

        $proposal->delete();

        return redirect()->route('collaborator.proposals.saved')
            ->with('success', 'Proposta eliminada.');
    }

    private function currentAdmin(Request $request): AnnouncementAdmin
    {
        return AnnouncementAdmin::query()->findOrFail($request->session()->get('announcement_admin_id'));
    }
}
