<?php

namespace App\Http\Controllers;

use App\Modules\Collaborator\Opportunity\Actions\SubmitDiagnostic;
use App\Modules\Collaborator\Opportunity\Domain\DiagnosticSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public-facing diagnostic portal — no authentication.
 * Access is controlled exclusively by the session token.
 */
class DiagnosticPortalController extends Controller
{
    // ── Show portal ───────────────────────────────────────────────────────────

    public function show(string $token): View|RedirectResponse
    {
        $session = DiagnosticSession::where('token', $token)
            ->with(['opportunity'])
            ->firstOrFail();

        $lang = in_array(request()->query('lang'), ['pt', 'en']) ? request()->query('lang') : 'pt';

        if ($session->isSubmitted()) {
            return view('portal.diagnostic-submitted', ['session' => $session, 'lang' => $lang]);
        }

        if ($session->isExpired()) {
            return view('portal.diagnostic-expired', ['session' => $session, 'lang' => $lang]);
        }

        // Mark first open
        if (! $session->opened_at) {
            $session->update(['opened_at' => now()]);

            // Log on opportunity timeline
            \App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent::create([
                'opportunity_id' => $session->opportunity_id,
                'event_type'     => \App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent::DIAGNOSTIC_OPENED,
                'actor_type'     => 'client',
                'description'    => 'Cliente abriu o link de diagnóstico pela primeira vez.',
                'occurred_at'    => now(),
            ]);
        }

        $guide = config(
            "diagnostic_guides.{$session->service_slug}",
            config('diagnostic_guides._default')
        );

        return view('portal.diagnostic', [
            'session'     => $session,
            'opportunity' => $session->opportunity,
            'guide'       => $guide,
            'draft'       => $session->draft_answers ?? [],
            'lang'        => $lang,
        ]);
    }

    // ── Autosave draft ────────────────────────────────────────────────────────

    public function save(Request $request, string $token): \Illuminate\Http\JsonResponse
    {
        $session = DiagnosticSession::where('token', $token)->firstOrFail();

        if (! $session->isOpen()) {
            return response()->json(['error' => 'Sessão inválida ou expirada.'], 422);
        }

        $answers = $request->input('answers', []);
        $session->update([
            'draft_answers'  => $answers,
            'last_saved_at'  => now(),
        ]);

        \App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent::updateOrCreate(
            [
                'opportunity_id' => $session->opportunity_id,
                'event_type'     => \App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent::DIAGNOSTIC_SAVED,
            ],
            [
                'actor_type'  => 'client',
                'description' => 'Cliente guardou progresso no diagnóstico.',
                'occurred_at' => now(),
            ]
        );

        return response()->json(['saved_at' => now()->format('H:i')]);
    }

    // ── Submit ────────────────────────────────────────────────────────────────

    public function submit(Request $request, string $token, SubmitDiagnostic $action): RedirectResponse
    {
        $session = DiagnosticSession::where('token', $token)
            ->with('opportunity')
            ->firstOrFail();

        if (! $session->isOpen()) {
            return redirect()->route('diagnostic.portal', $token)
                ->withErrors(['form' => 'Sessão expirada. Contacte a BD para um novo link.']);
        }

        $guide = config(
            "diagnostic_guides.{$session->service_slug}",
            config('diagnostic_guides._default')
        );

        // Collect validation rules from the guide
        $rules = $this->buildValidationRules($guide);
        $request->validate($rules);

        $answers = $request->except(['_token', '_method']);
        $files   = collect($request->allFiles())
            ->mapWithKeys(fn ($file, $key) => [$key => $file])
            ->toArray();

        $action->execute($session, $answers, $files);

        return redirect()->route('diagnostic.portal', $token)
            ->with('submitted', true);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function buildValidationRules(array $guide): array
    {
        $rules = [];
        foreach ($guide['groups'] as $group) {
            foreach ($group['questions'] as $question) {
                if (! ($question['required'] ?? false)) continue;

                $key = $question['key'];
                $type = $question['type'];

                $rule = match ($type) {
                    'file'        => ['nullable', 'file', 'max:20480'],
                    'number'      => ['required', 'numeric'],
                    'boolean'     => ['required', 'boolean'],
                    'date'        => ['required', 'date'],
                    'multiselect' => ['required', 'array'],
                    default       => ['required', 'string', 'max:2000'],
                };

                $rules[$key] = $rule;
            }
        }
        return $rules;
    }
}
