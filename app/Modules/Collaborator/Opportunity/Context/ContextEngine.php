<?php

namespace App\Modules\Collaborator\Opportunity\Context;

use App\Modules\Collaborator\Opportunity\Domain\Opportunity;
use App\Modules\Collaborator\Opportunity\Domain\OpportunityEvent;
use Illuminate\Support\Collection;

/**
 * ContextEngine — aggregates all knowledge about an opportunity.
 *
 * Sources (in priority order):
 *   1. Diagnostic responses (client answers)
 *   2. OCR-extracted text from uploaded documents
 *   3. Opportunity base fields (client, service, etc.)
 *   4. Event history (timeline facts)
 *
 * Output: a flat keyed array (context_snapshot) stored on the opportunity.
 * The ProposalBuilder reads ONLY from this snapshot — never directly from
 * individual sources.
 */
final class ContextEngine
{
    /**
     * Build and persist the consolidated context snapshot.
     */
    public function refresh(Opportunity $opportunity): array
    {
        $opportunity->load(['diagnosticSessions.responses', 'ocrResults']);

        $context = array_merge(
            $this->baseContext($opportunity),
            $this->diagnosticContext($opportunity),
            $this->ocrContext($opportunity),
        );

        $context['_meta'] = [
            'refreshed_at'      => now()->toIso8601String(),
            'response_count'    => count($context) - 1,
            'has_ocr'           => ! empty($context['_ocr_text']),
            'diagnostic_complete' => $opportunity->hasCompletedDiagnosis(),
        ];

        $opportunity->update(['context_snapshot' => $context]);

        OpportunityEvent::create([
            'opportunity_id' => $opportunity->id,
            'event_type'     => OpportunityEvent::CONTEXT_REFRESHED,
            'actor_type'     => 'system',
            'description'    => 'Contexto consolidado actualizado.',
            'payload'        => ['keys' => array_keys($context)],
            'occurred_at'    => now(),
        ]);

        return $context;
    }

    /**
     * Return current snapshot without rebuilding.
     */
    public function snapshot(Opportunity $opportunity): array
    {
        return $opportunity->context_snapshot ?? [];
    }

    /**
     * Get a specific value from context, with optional fallback.
     */
    public function get(Opportunity $opportunity, string $key, mixed $default = null): mixed
    {
        return $this->snapshot($opportunity)[$key] ?? $default;
    }

    /**
     * Return the best available description of the role/project for narrative use.
     * Progressively richer the more the client has answered.
     */
    public function narrativeDescription(Opportunity $opportunity): string
    {
        $ctx = $this->snapshot($opportunity);

        // Recruitment: build rich role description
        if ($opportunity->service_slug === 'recrutamento-seleccao') {
            return $this->buildRecruitmentNarrative($ctx);
        }

        // Generic
        $parts = array_filter([
            $ctx['objectivo_principal'] ?? null,
            $ctx['situacao_actual']     ?? null,
        ]);

        return implode(' ', $parts) ?: $opportunity->service_title;
    }

    // ── Private builders ──────────────────────────────────────────────────────

    private function baseContext(Opportunity $opportunity): array
    {
        return [
            'client_name'     => $opportunity->client_name,
            'client_contact'  => $opportunity->client_contact,
            'client_email'    => $opportunity->client_email,
            'client_company'  => $opportunity->client_company,
            'client_industry' => $opportunity->client_industry,
            'service_slug'    => $opportunity->service_slug,
            'service_title'   => $opportunity->service_title,
            'opportunity_ref' => $opportunity->reference,
        ];
    }

    private function diagnosticContext(Opportunity $opportunity): array
    {
        $context = [];

        // Use the latest submitted session
        $session = $opportunity->diagnosticSessions
            ->filter(fn ($s) => $s->submitted_at !== null)
            ->sortByDesc('submitted_at')
            ->first();

        if (! $session) return $context;

        foreach ($session->responses as $response) {
            $value = $response->answer_value;
            // Flatten single-element arrays to scalar
            if (is_array($value) && count($value) === 1) $value = $value[0];
            $context[$response->question_key] = $value;
        }

        return $context;
    }

    private function ocrContext(Opportunity $opportunity): array
    {
        $texts = $opportunity->ocrResults
            ->filter(fn ($r) => ! $r->has_errors && strlen($r->raw_text) > 30)
            ->pluck('raw_text')
            ->all();

        if (empty($texts)) return [];

        return [
            '_ocr_text'        => implode("\n\n---\n\n", $texts),
            '_ocr_doc_count'   => count($texts),
        ];
    }

    private function buildRecruitmentNarrative(array $ctx): string
    {
        $parts = [];

        $cargo = $ctx['titulo_cargo'] ?? null;
        if ($cargo) $parts[] = $cargo;

        $exp = $ctx['experiencia_anos'] ?? null;
        if ($exp) $parts[] = "com experiência mínima de {$exp} anos";

        $sector = $ctx['sector'] ?? null;
        if ($sector) $parts[] = "em ambiente {$sector}";

        $subs = $ctx['subordinados_directos'] ?? null;
        if ($subs && $subs > 0) $parts[] = "responsável por liderar uma equipa de {$subs} colaboradores";

        $competencias = $ctx['competencias_tecnicas'] ?? null;
        if ($competencias) {
            $short = mb_strlen($competencias) > 120 ? mb_substr($competencias, 0, 117) . '...' : $competencias;
            $parts[] = "com competências em {$short}";
        }

        $desafio = $ctx['desafio_estrategico'] ?? null;
        if ($desafio) $parts[] = trim($desafio, '.');

        return count($parts) ? implode(', ', $parts) . '.' : ($ctx['titulo_cargo'] ?? '');
    }
}
