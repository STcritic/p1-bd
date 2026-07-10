<?php

namespace App\Modules\Collaborator\Opportunity\Builders;

use App\Modules\Collaborator\Opportunity\Domain\Opportunity;
use App\Modules\Collaborator\Proposal\Builders\ProposalBuilder;
use App\Modules\Collaborator\Proposal\DTO\ProposalData;
use App\Modules\Collaborator\Proposal\Services\ContentGeneratorService;

/**
 * OpportunityProposalBuilder
 *
 * Bridge between the Opportunity context snapshot and the existing ProposalBuilder.
 * Maps accumulated diagnostic context → proposal $data array → ProposalBuilder::build().
 *
 * The more the client answered, the richer the result.
 * The collaborator never fills in what the context already knows.
 */
final class OpportunityProposalBuilder
{
    public function __construct(
        private readonly ProposalBuilder         $proposalBuilder,
        private readonly ContentGeneratorService $content,
    ) {}

    /**
     * Build a ProposalData from an opportunity.
     * $overrides lets the collaborator add financials and any missing fields.
     */
    public function build(Opportunity $opportunity, array $overrides = []): ProposalData
    {
        $en      = ($overrides['lang'] ?? 'pt') === 'en';
        $service = $this->resolveService($opportunity->service_slug, $en);
        return $this->proposalBuilder->build($this->buildFormData($opportunity, $overrides), $service);
    }

    /**
     * Return the raw form_data array — same format used by Proposal::form_data.
     * Call this when you need to persist the proposal to the proposals table.
     */
    public function buildFormData(Opportunity $opportunity, array $overrides = []): array
    {
        $ctx   = $opportunity->context_snapshot ?? [];
        $score = $opportunity->score_data       ?? [];
        $en    = ($overrides['lang'] ?? 'pt') === 'en';

        return array_merge(
            $this->buildDataFromContext($opportunity, $ctx, $score, $en),
            $overrides,
        );
    }

    // ── Data assembly ─────────────────────────────────────────────────────────

    private function buildDataFromContext(Opportunity $opportunity, array $ctx, array $score, bool $en = false): array
    {
        return [
            // Identity
            'service_slug'      => $opportunity->service_slug,
            'proposal_date'     => now()->format('Y-m-d'),
            'valid_until'       => now()->addDays(15)->format('Y-m-d'),
            'proposal_reference'=> $this->buildReference($opportunity),

            // Client — opportunity fields take priority, ctx fills gaps
            'client_name'     => $opportunity->client_name,
            'client_contact'  => $opportunity->client_contact,
            'client_email'    => $opportunity->client_email,
            'client_position' => $ctx['participantes_decisao'] ?? null,
            'client_location' => $ctx['local_trabalho']        ?? null,
            'client_industry' => $ctx['sector']                ?? $opportunity->client_industry,
            'client_insight'  => $this->buildClientInsight($ctx, $score),

            // Brief
            'challenge'    => $this->buildChallenge($opportunity, $ctx, $en),
            'objectives'   => $this->buildObjectives($ctx),
            'scope'        => '',
            'methodology'  => '',
            'deliverables' => '',
            'timeline'     => $this->buildTimeline($ctx, $opportunity->service_slug, $score, $en),
            'team'         => '',
            'assumptions'  => $this->buildAssumptions($opportunity->service_slug, $ctx, $score, $en),
            'out_of_scope' => '',

            // Financial — auto-computed from salary/policy when available
            'currency'        => 'MZN',
            'fee'             => $this->computeFee($opportunity->service_slug, $ctx),
            'expenses'        => 0,
            'vat_rate'        => 16,
            'expense_items'   => [],
            'payment_terms'   => $this->buildPaymentTerms($opportunity->service_slug, $en),
            'financial_notes' => $this->buildFinancialNotes($opportunity->service_slug, $ctx, $en),

            // Recruitment specifics
            'candidate_salary' => $this->resolveSalary($ctx),
            'recruit_type'     => $this->resolveRecruitType($ctx),

            // Complexity & package from score
            'pricing_package'  => 'implementacao',
            'complexity_level' => $this->resolveComplexity($score),

            // Selections — empty forces generator to use all presets
            'selected_approaches'   => [],
            'selected_modules'      => [],
            'selected_deliverables' => [],
            'selected_profiles'     => [],

            // Authorship — let defaults fill in
            'prepared_by'     => null,
            'prepared_role'   => null,
            'cover_image_url' => null,

            // Language
            'lang'            => $en ? 'en' : 'pt',
        ];
    }

    // ── Challenge builder ─────────────────────────────────────────────────────
    // Assembles a rich challenge statement from all available context.
    // Each new answer enriches the statement automatically.

    private function buildChallenge(Opportunity $opportunity, array $ctx, bool $en = false): string
    {
        $parts = [];

        if ($opportunity->service_slug === 'recrutamento-seleccao') {
            $cargo = $ctx['titulo_cargo'] ?? null;
            $dept  = $ctx['departamento'] ?? null;
            $nivel = $ctx['nivel_hierarquico'] ?? null;
            if ($cargo) {
                $nivelLabel = $en
                    ? match ($nivel) {
                        'executivo' => 'at executive level',
                        'gestao'    => 'in a management role',
                        'senior'    => 'at senior level',
                        default     => '',
                    }
                    : match ($nivel) {
                        'executivo' => 'ao nível executivo',
                        'gestao'    => 'de gestão',
                        'senior'    => 'sénior',
                        default     => '',
                    };
                $deptSuffix = $dept
                    ? ($en ? " for the {$dept} department" : " para o departamento de {$dept}")
                    : '';
                $parts[] = $en
                    ? "Recruitment need for the {$cargo} role" . ($nivelLabel ? " {$nivelLabel}" : '') . $deptSuffix . '.'
                    : "Necessidade de recrutamento do cargo de {$cargo}" . ($nivelLabel ? " {$nivelLabel}" : '') . $deptSuffix . '.';
            }
        }

        $desafio = $ctx['desafio_estrategico'] ?? null;
        if ($desafio) $parts[] = trim($desafio, '.');

        $situacao = $ctx['situacao_actual'] ?? $ctx['problemas_actuais'] ?? null;
        if ($situacao) $parts[] = trim($situacao, '.');

        $objectivo = $ctx['objectivo_principal'] ?? null;
        if ($objectivo && $objectivo !== ($situacao ?? '')) $parts[] = trim($objectivo, '.');

        $urgencia = $ctx['urgencia'] ?? null;
        if ($urgencia === 'critica') {
            $parts[] = $en
                ? 'The process is critical and requires an immediate response.'
                : 'O processo tem carácter crítico e requer resposta imediata.';
        } elseif ($urgencia === 'alta') {
            $parts[] = $en ? 'The process has high urgency.' : 'O processo tem urgência elevada.';
        }

        if (empty($parts)) {
            $serviceTitle = $this->resolveService($opportunity->service_slug, $en)['title'];
            return $en
                ? "Need for {$serviceTitle} aligned with the organisational objectives and context of {$opportunity->client_name}."
                : "Necessidade de {$serviceTitle} alinhada com os objectivos e contexto organizacional de {$opportunity->client_name}.";
        }

        return implode(' ', $parts);
    }

    private function buildObjectives(array $ctx): string
    {
        return $ctx['objectivo_principal'] ?? '';
    }

    private function buildClientInsight(array $ctx, array $score): string
    {
        $parts = [];

        $cultura = $ctx['cultura_organizacional'] ?? null;
        if ($cultura) $parts[] = $cultura;

        // Inject decision arguments as additional context
        $arguments = $score['arguments'] ?? [];
        foreach (array_slice($arguments, 0, 2) as $arg) {
            $parts[] = $arg;
        }

        return implode(' ', $parts);
    }

    // ── Timeline ──────────────────────────────────────────────────────────────

    private function buildTimeline(array $ctx, string $slug, array $score, bool $en = false): string
    {
        if ($complexityOverride = ($score['dimensions']['_complexity_override'] ?? null)) {
            if ($complexityOverride === 'alta' && $slug === 'recrutamento-seleccao') {
                return $en
                    ? '8 to 12 weeks after award and executive profile definition.'
                    : '8 a 12 semanas após adjudicação e definição do perfil executivo.';
            }
        }

        $urgencia = $ctx['urgencia'] ?? 'normal';

        if ($slug === 'recrutamento-seleccao') {
            return $en
                ? match ($urgencia) {
                    'critica' => '3 to 4 weeks in priority mode, after award and profile alignment.',
                    'alta'    => '4 to 6 weeks after award and profile alignment.',
                    default   => '4 to 8 weeks after award and profile alignment.',
                }
                : match ($urgencia) {
                    'critica' => '3 a 4 semanas em regime de prioridade, após adjudicação e alinhamento do perfil.',
                    'alta'    => '4 a 6 semanas após adjudicação e alinhamento do perfil.',
                    default   => '4 a 8 semanas após adjudicação e alinhamento do perfil.',
                };
        }

        return $en
            ? match ($urgencia) {
                'critica', 'alta' => '4 to 6 weeks after award.',
                default           => '6 to 10 weeks after award.',
            }
            : match ($urgencia) {
                'critica', 'alta' => '4 a 6 semanas após adjudicação.',
                default           => '6 a 10 semanas após adjudicação.',
            };
    }

    // ── Assumptions ──────────────────────────────────────────────────────────

    private function buildAssumptions(string $slug, array $ctx, array $score, bool $en = false): string
    {
        $parts = [];

        foreach ($score['timeline_notes'] ?? [] as $note) {
            $parts[] = $note;
        }

        if ($slug === 'recrutamento-seleccao') {
            $parts[] = $en
                ? 'Legal requirement (INEFP): current labour legislation requires the employer to notify the National Institute of Employment and Vocational Training (INEFP) of the intention to hire, with a minimum of 7 working days in advance. This obligation rests entirely with the client/employer. BD can assist with drafting the notification upon request.'
                : 'Obrigação legal (INEFP): a legislação laboral vigente determina que o empregador comunique ao Instituto Nacional do Emprego e Formação Profissional (INEFP) a intenção de contratar, com antecedência mínima de 7 dias úteis. Esta responsabilidade incumbe integralmente ao cliente/empregador. A BD pode apoiar na preparação da comunicação mediante solicitação.';
        }

        $docs = $ctx['documentos_disponiveis'] ?? null;
        if ($docs === 'nenhum') {
            $parts[] = $en
                ? 'The absence of initial documentation requires an in-field information gathering exercise, integrated within the scope of this proposal.'
                : 'A ausência de documentação inicial implica a realização de levantamento de informação em campo, integrado no âmbito da proposta.';
        }

        return implode("\n", $parts);
    }

    // ── Recruit type ──────────────────────────────────────────────────────────

    private function resolveRecruitType(array $ctx): string
    {
        return ($ctx['nivel_hierarquico'] ?? '') === 'executivo' ? 'headhunting' : 'standard';
    }

    private function resolveSalary(array $ctx): ?float
    {
        $max = $ctx['salario_max'] ?? null;
        $min = $ctx['salario_min'] ?? null;
        $v   = $max ?? $min ?? null;
        return $v !== null ? (float) $v : null;
    }

    // ── Complexity ────────────────────────────────────────────────────────────

    private function resolveComplexity(array $score): string
    {
        $override = $score['dimensions']['_complexity_override'] ?? null;
        if ($override) return $override;

        $total = $score['total'] ?? 40;
        return match (true) {
            $total >= 70 => 'alta',
            $total >= 45 => 'media',
            default      => 'baixa',
        };
    }

    // ── Reference ─────────────────────────────────────────────────────────────

    private function buildReference(Opportunity $opportunity): string
    {
        return 'BD-' . ($opportunity->reference ?? 'PROP-' . now()->format('Y'));
    }

    // ── Fee computation (recruitment commercial policy) ───────────────────────

    /**
     * Auto-compute the BD fee for recruitment from the salary in context.
     * Salary fields are monthly (MZN). Policy uses annual = monthly × 12.
     * Returns 0 for non-recruitment services (collaborator sets manually).
     */
    private function computeFee(string $slug, array $ctx): float
    {
        if ($slug !== 'recrutamento-seleccao') return 0.0;

        $monthly = (float) ($ctx['salario_max'] ?? $ctx['salario_min'] ?? 0);
        if ($monthly <= 0) return 0.0;

        $annual  = $monthly * 12;
        $isExec  = ($ctx['nivel_hierarquico'] ?? '') === 'executivo';
        $policy  = config('proposal_commercial_policy.recrutamento-seleccao', []);
        $bands   = $policy['bands'] ?? [];

        if ($isExec) {
            // Headhunting band: use rate_min as the base rate
            foreach ($bands as $band) {
                if (isset($band['rate_min'])) {
                    return round($annual * $band['rate_min'] / 100, 2);
                }
            }
        }

        // Find the matching standard band
        if ($annual <= 1_000_000) {
            $rate = $bands[0]['rate'] ?? 10;
        } elseif ($annual <= 2_000_000) {
            $rate = $bands[1]['rate'] ?? 12.5;
        } else {
            $rate = $bands[2]['rate'] ?? 15;
        }

        return round($annual * $rate / 100, 2);
    }

    private function buildPaymentTerms(string $slug, bool $en = false): string
    {
        if ($slug !== 'recrutamento-seleccao') return '';

        return $en
            ? '50% on award and process start; 50% on presentation of finalist candidates. ' .
              'For executive recruitment (headhunting): 33% on award, 33% on shortlist presentation, 34% on candidate admission.'
            : '50% na adjudicação e início do processo; 50% na apresentação dos candidatos finalistas. ' .
              'Em caso de recrutamento executivo (headhunting): 33% na adjudicação, 33% na apresentação da shortlist, 34% na admissão do candidato.';
    }

    private function buildFinancialNotes(string $slug, array $ctx, bool $en = false): string
    {
        if ($slug !== 'recrutamento-seleccao') return '';

        $monthly = (float) ($ctx['salario_max'] ?? $ctx['salario_min'] ?? 0);
        $notes   = [];

        if ($monthly > 0) {
            $annual = $monthly * 12;
            $notes[] = $en
                ? 'Fee calculated on the gross annual salary of the selected candidate (' .
                  'base salary: MZN ' . number_format($monthly, 0, ',', ' ') . '/month × 12 = MZN ' .
                  number_format($annual, 0, ',', ' ') . '/year).'
                : 'Fee calculado sobre o salário anual bruto do candidato seleccionado (' .
                  'salário base: MZN ' . number_format($monthly, 0, ',', ' ') . '/mês × 12 = MZN ' .
                  number_format($annual, 0, ',', ' ') . '/ano).';
        } else {
            $notes[] = $en
                ? 'Fee calculated on the gross annual salary of the selected candidate (salary to be confirmed with the client).'
                : 'Fee calculado sobre o salário anual bruto do candidato seleccionado (salário a confirmar com o cliente).';
        }

        $policy = config('proposal_commercial_policy.recrutamento-seleccao', []);
        if ($guarantee = $policy['guarantee']['note'] ?? null) {
            $notes[] = ($en ? 'Guarantee: ' : 'Garantia: ') . $guarantee;
        }

        $notes[] = $en
            ? 'Operational expenses (assessments, logistics, tools) are an additional charge to the fee, to be presented in a separate budget or upon approval.'
            : 'Despesas operacionais (assessments, logística, ferramentas) constituem encargo adicional ao fee, a apresentar em orçamento separado ou mediante aprovação.';

        return implode("\n", $notes);
    }

    // ── Service config ────────────────────────────────────────────────────────

    private function resolveService(string $slug, bool $en = false): array
    {
        $lang   = $en ? 'en' : 'pt';
        $guides = config("service_guides.{$lang}", config('service_guides.pt', []));
        foreach ($guides as $guide) {
            if (($guide['slug'] ?? '') === $slug) return $guide;
        }
        return ['slug' => $slug, 'title' => $slug, 'value' => ''];
    }
}
