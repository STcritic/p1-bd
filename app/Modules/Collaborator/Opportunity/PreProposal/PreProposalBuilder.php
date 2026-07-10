<?php

namespace App\Modules\Collaborator\Opportunity\PreProposal;

use App\Modules\Collaborator\Opportunity\Domain\Opportunity;
use App\Modules\Collaborator\Proposal\Factories\ContentStrategyFactory;
use App\Modules\Collaborator\Proposal\Services\ContentGeneratorService;

/**
 * PreProposalBuilder
 *
 * Builds a pre-proposal from an opportunity WITHOUT requiring the diagnostic.
 * Uses service guides, BD identity config, and whatever context is already available.
 */
final class PreProposalBuilder
{
    public function __construct(
        private readonly ContentGeneratorService $content,
        private readonly ContentStrategyFactory  $strategyFactory,
    ) {}

    public function build(Opportunity $opportunity, ?string $portalUrl = null, string $lang = 'pt'): PreProposalData
    {
        $slug    = $opportunity->service_slug;
        $ctx     = $opportunity->context_snapshot ?? [];
        $score   = $opportunity->score_data ?? [];
        $en      = $lang === 'en';

        $strategy   = $this->strategyFactory->make($slug);
        $defaults   = config('proposals.defaults', []);
        $identity   = config('proposal_identity', []);

        $preparedBy   = $defaults['prepared_by']   ?? config('proposal_identity.team_members.sandra.name', 'BD Team');
        $preparedRole = $defaults['prepared_role']  ?? config('proposal_identity.team_members.sandra.role', $en ? 'Business Consulting' : 'Consultoria Empresarial');
        $coverImages  = config('proposals.cover_images', []);
        $coverUrl     = asset($coverImages[$slug] ?? $coverImages['_default'] ?? 'assets/images/hero-consulting-team.png');

        $service = $this->resolveService($slug, $en);

        return new PreProposalData(
            serviceSlug:            $slug,
            serviceTitle:           $service['title'],
            clientName:             $opportunity->client_name,
            clientContact:          $opportunity->client_contact,
            clientPosition:         null,
            reference:              'BD-PRE-' . ($opportunity->reference ?? strtoupper(substr($slug, 0, 4)) . '-' . now()->format('Y')),
            date:                   now()->format($en ? 'F j, Y' : 'd \d\e F \d\e Y'),
            preparedBy:             $preparedBy,
            preparedRole:           $preparedRole,
            coverImageUrl:          $coverUrl,

            challengeStatement:     $this->buildChallengeStatement($opportunity, $ctx, $service, $en),
            positioningStatement:   $strategy->positioningStatement($service['title']),
            contextSignals:         $this->buildContextSignals($opportunity, $ctx, $service, $score, $en),
            clientContextIntro:     $this->buildContextIntro($opportunity, $ctx, $service, $en),

            approachIntro:          $this->buildApproachIntro($slug, $service, $en),
            methodologySteps:       $this->buildMethodologySteps($slug, $en),
            differentiators:        $this->buildDifferentiators($slug, $en),
            teamBrief:              $this->buildTeamBrief($en),
            timelineEstimate:       $this->buildTimelineEstimate($slug, $ctx, $en),

            diagnosticIntro:        $this->buildDiagnosticIntro($slug, $opportunity->client_name, $en),
            diagnosticBenefits:     $this->buildDiagnosticBenefits($slug, $en),
            diagnosticCallToAction: $this->buildDiagnosticCTA($opportunity, $portalUrl, $en),
            closingStatement:       $this->buildClosingStatement($opportunity->client_name, $service['title'], $en),
            portalUrl:              $portalUrl,
            opportunityId:          $opportunity->id,
            lang:                   $lang,
        );
    }

    // ── Page 2 builders ───────────────────────────────────────────────────────

    private function buildChallengeStatement(Opportunity $opportunity, array $ctx, array $service, bool $en = false): string
    {
        $client = $opportunity->client_name;
        $slug   = $opportunity->service_slug;

        $situacao  = $ctx['situacao_actual'] ?? $ctx['problemas_actuais'] ?? null;
        $objectivo = $ctx['objectivo_principal'] ?? null;
        $sector    = $ctx['sector'] ?? $opportunity->client_industry;

        if ($situacao && $objectivo) {
            return $en
                ? "{$situacao} The stated objective is: {$objectivo}"
                : "{$situacao} O objectivo declarado é: {$objectivo}";
        }
        if ($situacao) return $situacao;
        if ($objectivo) return $objectivo;

        $sectorPhrase = $sector ? ($en ? " in the {$sector} sector" : " no sector de {$sector}") : '';
        return match ($slug) {
            'recrutamento-seleccao'          => $en
                ? "{$client} has identified a recruitment need{$sectorPhrase} that requires a structured approach to ensure the right hire."
                : "A {$client} identificou uma necessidade de recrutamento{$sectorPhrase} que exige uma abordagem estruturada para garantir a contratação do perfil certo.",
            'gestao-desempenho'              => $en
                ? "{$client} seeks to build or improve a performance management system to align teams and organisational results."
                : "A {$client} pretende estruturar ou melhorar o sistema de gestão de desempenho para alinhar equipas e resultados organizacionais.",
            'carreira-sucessao'              => $en
                ? "{$client} needs a career and succession plan that retains talent and ensures organisational continuity."
                : "A {$client} necessita de um plano de carreira e sucessão que retenha talento e assegure a continuidade organizacional.",
            'avaliacao-classificacao-cargos' => $en
                ? "{$client} aims to evaluate and classify existing roles{$sectorPhrase}, creating internal equity and structural clarity."
                : "A {$client} pretende avaliar e classificar os cargos existentes{$sectorPhrase}, criando equidade interna e clareza estrutural.",
            'remuneracao-beneficios'         => $en
                ? "{$client} wants to structure a competitive, fair and sustainable remuneration and benefits policy."
                : "A {$client} quer estruturar uma política de remuneração e benefícios competitiva, justa e sustentável.",
            'formacao-desenvolvimento'       => $en
                ? "{$client} has identified training and development needs that require a structured response with measurable impact."
                : "A {$client} identificou necessidades de formação e desenvolvimento que exigem uma resposta estruturada e com impacto medido.",
            default                          => $en
                ? "{$client} faces a {$service['title']} challenge that requires a specialised, methodological approach adapted to its organisational context."
                : "A {$client} enfrenta um desafio de {$service['title']} que requer uma abordagem especializada, metodológica e adaptada ao contexto organizacional.",
        };
    }

    private function buildContextSignals(Opportunity $opportunity, array $ctx, array $service, array $score, bool $en = false): array
    {
        $signals = [];

        $signals[] = [
            'label' => $en ? 'Identified challenge' : 'Desafio identificado',
            'text'  => $this->buildChallengeStatement($opportunity, $ctx, $service, $en),
        ];

        $signals[] = [
            'label' => $en ? 'Strategic relevance' : 'Relevância estratégica',
            'text'  => $service['value'] ?? ($en
                ? "{$service['title']} directly impacts organisational performance, talent retention and the competitiveness of {$opportunity->client_name}."
                : "A {$service['title']} impacta directamente a performance organizacional, a retenção de talento e a competitividade de {$opportunity->client_name}."),
        ];

        $args = $score['arguments'] ?? [];
        if (! empty($args)) {
            $signals[] = ['label' => $en ? 'Additional context' : 'Contexto adicional', 'text' => $args[0]];
        } else {
            $signals[] = [
                'label' => $en ? 'Right moment' : 'Momento certo',
                'text'  => $en
                    ? "Organisations that invest in {$service['title']} at the right moment reduce operational risks, increase internal clarity and accelerate decision-making."
                    : "As organizações que investem em {$service['title']} no momento certo reduzem riscos operacionais, aumentam a clareza interna e aceleram a tomada de decisão.",
            ];
        }

        return $signals;
    }

    private function buildContextIntro(Opportunity $opportunity, array $ctx, array $service, bool $en = false): string
    {
        $client  = $opportunity->client_name;
        $sector  = $ctx['sector'] ?? $opportunity->client_industry;

        if ($en) {
            $sectorPhrase = $sector ? "in the {$sector} sector" : 'in a demanding organisational context';
            return "We understand that {$client} operates {$sectorPhrase}, where the quality of people decisions, process clarity and team stability are critical competitive factors. The identified challenge calls for a response that goes beyond a simple deliverable — it requires methodology, experience and partnership.";
        }

        $sectorPhrase = $sector ? "no sector de {$sector}" : 'num contexto organizacional exigente';
        return "Compreendemos que {$client} opera {$sectorPhrase}, onde a qualidade das decisões de gestão de pessoas, a clareza dos processos e a estabilidade das equipas são factores críticos de competitividade. O desafio identificado exige uma resposta que vai além de um simples entregável — exige metodologia, experiência e parceria.";
    }

    // ── Page 3 builders ───────────────────────────────────────────────────────

    private function buildApproachIntro(string $slug, array $service, bool $en = false): string
    {
        return $en
            ? "BD adopts an integrated consulting approach for {$service['title']}. We don't just deliver documents — we deliver solutions that work in the client's real context, with knowledge transfer and implementation support."
            : "A BD adopta uma abordagem de consultoria integrada para {$service['title']}. Não entregamos apenas documentos — entregamos soluções que funcionam na realidade da organização cliente, com transferência de conhecimento e suporte à implementação.";
    }

    private function buildMethodologySteps(string $slug, bool $en = false): array
    {
        if ($en) return match ($slug) {
            'recrutamento-seleccao' => [
                ['num' => '01', 'label' => 'Profile Definition',  'text' => 'Technical and behavioural profile alignment with the internal team.'],
                ['num' => '02', 'label' => 'Search & Screening',  'text' => 'Active market search, CV analysis and technical screening.'],
                ['num' => '03', 'label' => 'Technical Interviews','text' => 'In-depth assessment of technical and behavioural competencies.'],
                ['num' => '04', 'label' => 'Shortlist & Report',  'text' => 'Presentation of finalist candidates with assessment report.'],
                ['num' => '05', 'label' => 'Decision Support',    'text' => 'Support through the decision process and offer negotiation.'],
            ],
            default => [
                ['num' => '01', 'label' => 'Diagnose',    'text' => 'Gather information and analyse the organisational context.'],
                ['num' => '02', 'label' => 'Design',      'text' => 'Structure the solution adapted to the client\'s reality.'],
                ['num' => '03', 'label' => 'Validate',    'text' => 'Present and validate with relevant stakeholders.'],
                ['num' => '04', 'label' => 'Implement',   'text' => 'Guided execution with knowledge transfer.'],
                ['num' => '05', 'label' => 'Evaluate',    'text' => 'Measure results and consolidate deliverables.'],
            ],
        };

        return match ($slug) {
            'recrutamento-seleccao' => [
                ['num' => '01', 'label' => 'Definição do Perfil',    'text' => 'Alinhamento técnico e comportamental do perfil com a equipa interna.'],
                ['num' => '02', 'label' => 'Pesquisa e Triagem',     'text' => 'Pesquisa activa de mercado, análise curricular e triagem técnica.'],
                ['num' => '03', 'label' => 'Entrevistas Técnicas',   'text' => 'Avaliação aprofundada das competências técnicas e comportamentais.'],
                ['num' => '04', 'label' => 'Shortlist & Relatório',  'text' => 'Apresentação dos candidatos finalistas com relatório de avaliação.'],
                ['num' => '05', 'label' => 'Apoio à Decisão',        'text' => 'Suporte ao processo de decisão e negociação da proposta de contratação.'],
            ],
            'gestao-desempenho' => [
                ['num' => '01', 'label' => 'Diagnóstico',            'text' => 'Análise do sistema actual de avaliação e identificação de lacunas.'],
                ['num' => '02', 'label' => 'Desenho do Modelo',      'text' => 'Definição do modelo de avaliação, KPIs e critérios por função.'],
                ['num' => '03', 'label' => 'Validação',              'text' => 'Workshops com liderança e validação do modelo proposto.'],
                ['num' => '04', 'label' => 'Implementação',          'text' => 'Instalação do sistema, formação de avaliadores e comunicação interna.'],
                ['num' => '05', 'label' => 'Monitorização',          'text' => 'Acompanhamento do primeiro ciclo de avaliação e ajustes necessários.'],
            ],
            'carreira-sucessao' => [
                ['num' => '01', 'label' => 'Mapeamento',             'text' => 'Levantamento da estrutura organizacional e funções existentes.'],
                ['num' => '02', 'label' => 'Análise de Cargos',      'text' => 'Avaliação de funções críticas e identificação de sucessores potenciais.'],
                ['num' => '03', 'label' => 'Desenho de Trilhos',     'text' => 'Definição de trilhos de carreira e critérios de progressão claros.'],
                ['num' => '04', 'label' => 'Plano de Sucessão',      'text' => 'Construção dos planos de desenvolvimento e sucessão prioritários.'],
                ['num' => '05', 'label' => 'Implementação',          'text' => 'Apresentação à liderança, comunicação e instrumentalização interna.'],
            ],
            default => [
                ['num' => '01', 'label' => 'Diagnóstico',            'text' => 'Levantamento de informação e análise do contexto organizacional.'],
                ['num' => '02', 'label' => 'Desenho',                'text' => 'Estruturação da solução adaptada à realidade do cliente.'],
                ['num' => '03', 'label' => 'Validação',              'text' => 'Apresentação e validação com os stakeholders relevantes.'],
                ['num' => '04', 'label' => 'Implementação',          'text' => 'Execução acompanhada com transferência de conhecimento.'],
                ['num' => '05', 'label' => 'Avaliação',              'text' => 'Medição de resultados e consolidação dos entregáveis.'],
            ],
        };
    }

    private function buildDifferentiators(string $slug, bool $en = false): array
    {
        if ($en) {
            $base = [
                ['icon' => '◈', 'label' => 'Sector specialisation', 'text' => 'Deep knowledge of the Mozambican market — behaviour, legislation and salary benchmarks.'],
                ['icon' => '◉', 'label' => 'Methodological approach','text' => 'Structured and documented processes, with clear deliverables at every stage.'],
                ['icon' => '●', 'label' => 'Long-term partnership',  'text' => 'We are not occasional suppliers — we are your organisation\'s strategic HR partner.'],
            ];
            if ($slug === 'recrutamento-seleccao') {
                $base[0] = ['icon' => '◈', 'label' => 'Active talent network', 'text' => 'Qualified candidate base and active professional network in the local market.'];
            }
            return $base;
        }

        $base = [
            ['icon' => '◈', 'label' => 'Especialização sectorial',  'text' => 'Conhecimento profundo do mercado moçambicano — comportamento, legislação e referências salariais.'],
            ['icon' => '◉', 'label' => 'Abordagem metodológica',    'text' => 'Processos estruturados e documentados, com entregáveis claros em cada etapa.'],
            ['icon' => '●', 'label' => 'Parceria de longo prazo',   'text' => 'Não somos prestadores ocasionais — somos o parceiro estratégico de RH da sua organização.'],
        ];
        if ($slug === 'recrutamento-seleccao') {
            $base[0] = ['icon' => '◈', 'label' => 'Rede de talentos activa', 'text' => 'Base de candidatos qualificados e rede de contactos profissionais activa no mercado local.'];
        }
        return $base;
    }

    private function buildTeamBrief(bool $en = false): string
    {
        $members = config('proposal_identity.team_members', []);
        $names   = collect($members)->pluck('name')->filter()->take(3)->implode(', ');
        if ($en) {
            return $names
                ? "The proposal will be led by the BD team: {$names} — consultants with proven experience in people management and organisational consulting in Mozambique."
                : 'The proposal will be led by BD\'s senior consulting team, with proven experience in people management and organisational consulting in Mozambique.';
        }
        return $names
            ? "A proposta será conduzida pela equipa BD: {$names} — consultores com experiência comprovada em gestão de pessoas e consultoria organizacional em Moçambique."
            : 'A proposta será conduzida pela equipa de consultores sénior da BD, com experiência comprovada em gestão de pessoas e consultoria organizacional em Moçambique.';
    }

    private function buildTimelineEstimate(string $slug, array $ctx, bool $en = false): string
    {
        $urgencia = $ctx['urgencia'] ?? 'normal';

        if ($en) return match ($slug) {
            'recrutamento-seleccao' => match ($urgencia) {
                'critica' => '3 to 4 weeks after award (priority mode)',
                'alta'    => '4 to 6 weeks after award',
                default   => '4 to 8 weeks after award',
            },
            'gestao-desempenho', 'carreira-sucessao' => '6 to 10 weeks after award',
            default => '4 to 8 weeks after award',
        };

        return match ($slug) {
            'recrutamento-seleccao' => match ($urgencia) {
                'critica' => '3 a 4 semanas após adjudicação (regime de prioridade)',
                'alta'    => '4 a 6 semanas após adjudicação',
                default   => '4 a 8 semanas após adjudicação',
            },
            'gestao-desempenho', 'carreira-sucessao' => '6 a 10 semanas após adjudicação',
            default => '4 a 8 semanas após adjudicação',
        };
    }

    // ── Page 4 builders ───────────────────────────────────────────────────────

    private function buildDiagnosticIntro(string $slug, string $clientName, bool $en = false): string
    {
        $serviceTitle = $this->resolveService($slug, $en)['title'];
        return $en
            ? "To build a truly personalised proposal for {$clientName} — with realistic timelines, calibrated resources and adapted methodology — we need to understand the organisation's context better. For this, we have developed a digital diagnostic specific to {$serviceTitle}."
            : "Para construir uma proposta verdadeiramente personalizada para {$clientName} — com timelines realistas, recursos calibrados e metodologia adaptada — precisamos de conhecer melhor o contexto da organização. Para isso, desenvolvemos um diagnóstico digital específico para {$serviceTitle}.";
    }

    private function buildDiagnosticBenefits(string $slug, bool $en = false): array
    {
        return $en ? [
            'Proposal with values specific to the organisational context',
            'Methodology calibrated to the identified maturity level and urgency',
            'Realistic timelines based on actual process conditions',
            'Arguments and deliverables adapted to the sector and scale',
            'Prior identification of process risks and dependencies',
        ] : [
            'Proposta com valores específicos para o contexto da organização',
            'Metodologia calibrada ao nível de maturidade e urgência identificados',
            'Timelines realistas baseadas nas condições reais do processo',
            'Argumentos e entregáveis adaptados ao sector e dimensão',
            'Identificação prévia de riscos e dependências do processo',
        ];
    }

    private function buildDiagnosticCTA(Opportunity $opportunity, ?string $portalUrl, bool $en = false): string
    {
        $client = $opportunity->client_name;
        if ($portalUrl) {
            return $en
                ? "Share the diagnostic link with the decision-maker at {$client}. The process takes approximately 15 minutes, can be saved at any time and resumed when convenient. No account creation required."
                : "Partilhe o link de diagnóstico com o responsável pela decisão em {$client}. O processo demora aproximadamente 15 minutos, pode ser guardado a qualquer momento e retomado quando conveniente. Não requer criação de conta.";
        }
        return $en
            ? "The diagnostic link will be shared directly by the BD team. The process takes approximately 15 minutes, can be saved and resumed when convenient. No account creation required."
            : "O link de diagnóstico será partilhado directamente pela equipa BD. O processo demora aproximadamente 15 minutos, pode ser guardado e retomado quando conveniente. Não requer criação de conta.";
    }

    private function buildClosingStatement(string $clientName, string $serviceTitle, bool $en = false): string
    {
        return $en
            ? "BD commits to presenting a complete technical and financial proposal, specific to {$clientName}, within 3 business days of receiving the diagnostic. We look forward to the opportunity to work with you."
            : "A BD compromete-se a apresentar uma proposta técnica e financeira completa, específica para {$clientName}, no prazo de 3 dias úteis após a recepção do diagnóstico. Aguardamos a oportunidade de trabalhar consigo.";
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

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
