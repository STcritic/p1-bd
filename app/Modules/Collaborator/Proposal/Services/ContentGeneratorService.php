<?php

namespace App\Modules\Collaborator\Proposal\Services;

use App\Modules\Collaborator\Proposal\Factories\ContentStrategyFactory;
use App\Modules\Collaborator\Proposal\Support\TextCleaner;

class ContentGeneratorService
{
    public function __construct(
        private readonly ContentStrategyFactory $strategyFactory,
    ) {}

    // ─── Config helpers ──────────────────────────────────────────────────────

    public function serviceData(string $slug): array
    {
        return array_replace_recursive(
            config('proposal_services.generic', []),
            config("proposal_services.{$slug}", [])
        );
    }

    // ─── Text content ────────────────────────────────────────────────────────

    public function personalLetter(array $data, array $service, string $challenge): string
    {
        $recipient = filled($data['client_contact'] ?? null)
            ? trim(($data['client_position'] ?? 'Exmo(a).')." {$data['client_contact']}")
            : 'Exma. Direcção';

        return "{$recipient}, agradecemos a oportunidade de apresentar esta proposta. Preparámos uma abordagem específica para {$data['client_name']}, considerando o desafio identificado e a relevância estratégica de {$service['title']} para a continuidade, desempenho e crescimento da organização. O nosso compromisso é entregar uma solução tecnicamente sólida, prática e suficientemente clara para apoiar decisões de gestão com segurança.";
    }

    public function contextualSummary(array $data, array $service, string $challenge): string
    {
        $strategy    = $this->strategyFactory->make($data['service_slug']);
        $clientName  = $data['client_name'];
        $industry    = trim((string) ($data['client_industry'] ?? ''));
        $sectorPhrase = $industry !== '' ? " no sector de {$industry}" : '';

        return $strategy->contextualSummary($clientName, $sectorPhrase, $challenge, $service['title']);
    }

    public function clientContext(array $data, array $service, string $challenge): array
    {
        $strategy    = $this->strategyFactory->make($data['service_slug']);
        $client      = $data['client_name'];
        $sector      = trim((string) ($data['client_industry'] ?? ''));
        $location    = trim((string) ($data['client_location'] ?? ''));

        $sectorPhrase = $sector !== ''
            ? "no sector de {$sector}"
            : 'num contexto competitivo onde pessoas, processos e decisões de gestão precisam estar bem alinhados';

        $locationPhrase = $location !== '' ? " em {$location}" : '';

        $intro = filled($data['client_insight'] ?? null)
            ? TextCleaner::clean((string) $data['client_insight'])
            : "Compreendemos que {$client} opera {$sectorPhrase}{$locationPhrase}, onde a rapidez na tomada de decisão, a estabilidade das equipas e a qualidade da execução são factores críticos. O desafio apresentado - {$challenge} - exige uma resposta prática, tecnicamente sólida e suficientemente clara para apoiar a liderança.";

        $serviceNeed = $strategy->serviceNeed($service['title']);

        return [
            'title'       => "Porque {$client} precisa deste projecto agora",
            'intro'       => $intro,
            'service_need'=> $serviceNeed,
            'signals'     => [
                ['label' => 'Momento do cliente',    'text' => $challenge],
                ['label' => 'Decisão em jogo',        'text' => $serviceNeed],
                ['label' => 'Resultado a proteger',   'text' => $service['value'] ?? 'Melhores decisões, instrumentos claros e capacidade interna reforçada.'],
            ],
        ];
    }

    public function positioningStatement(string $slug, string $serviceTitle): string
    {
        return $this->strategyFactory->make($slug)->positioningStatement($serviceTitle);
    }

    public function bdSignature(string $slug): array
    {
        $base     = config('proposal_services.generic.bd_signature_base', []);
        $extras   = $this->strategyFactory->make($slug)->bdSignatureExtras();

        return [...$base, ...$extras];
    }

    public function closingNote(array $data, array $service): string
    {
        return "As melhores organizações não crescem apenas por contratar pessoas, aprovar políticas ou implementar ferramentas. Crescem porque tomam melhores decisões sobre pessoas. É esse o compromisso da BD nesta proposta: apoiar a {$data['client_name']} a decidir melhor, executar com segurança e transformar {$service['title']} em valor real para o negócio.";
    }

    // ─── Defaults (used when user leaves optional fields blank) ──────────────

    public function defaultObjectives(string $client, string $serviceTitle, array $approaches): string
    {
        $objectives = [
            "Apoiar {$client} a tomar decisões consistentes no domínio de {$serviceTitle}.",
            'Transformar o diagnóstico inicial em instrumentos práticos, aplicáveis e mensuráveis.',
        ];

        foreach (array_slice($approaches, 0, 3) as $approach) {
            $objectives[] = $approach;
        }

        return implode("\n", $objectives);
    }

    public function defaultScope(array $service, array $modules): string
    {
        return trim(($service['value'] ?? '')."\n\nMódulos previstos:\n".implode("\n", $modules));
    }

    public function defaultMethodology(string $slug): string
    {
        $steps = $this->serviceData($slug)['methodology'] ?? [];

        return implode("\n", $steps);
    }

    public function defaultFinancialNotes(array $package, string $complexity, array $pricing): string
    {
        $notes = [
            "Pacote seleccionado: {$package['label']} — {$package['description']}",
            "Complexidade considerada: {$complexity}",
            'Política de preço: '.($pricing['base'] ?? $package['pricing']),
        ];

        foreach (($pricing['drivers'] ?? []) as $driver) {
            $notes[] = "Factor de preço: {$driver}";
        }

        return implode("\n", $notes);
    }

    public function defaultRoadmap(string $slug, array $modules): array
    {
        $phases = $this->serviceData($slug)['roadmap'] ?? [];

        foreach (array_slice($modules, 0, count($phases)) as $index => $module) {
            $phases[$index]['module'] = $module;
        }

        return $phases;
    }

    // ─── Service-data readers (read from proposal_services config) ───────────

    public function criticalCase(string $slug, string $serviceTitle): array
    {
        return $this->serviceData($slug)['critical_case'] ?? [
            'title' => "Porque {$serviceTitle} é uma decisão crítica",
            'intro' => 'Intervenções de capital humano têm impacto directo na produtividade, cultura, retenção, custos e qualidade da execução.',
            'items' => ['Redução de subjectividade nas decisões', 'Maior clareza para gestores e colaboradores', 'Melhor utilização de dados e evidências', 'Menor risco operacional e reputacional'],
        ];
    }

    public function featuredCase(string $slug): array
    {
        return $this->serviceData($slug)['featured_case'] ?? [];
    }

    public function processFlow(string $slug): array
    {
        return $this->serviceData($slug)['process_flow'] ?? ['Alinhar', 'Diagnosticar', 'Desenhar', 'Validar', 'Implementar', 'Medir'];
    }

    public function timelinePlan(string $slug): array
    {
        return $this->serviceData($slug)['timeline'] ?? [];
    }

    public function successMetrics(string $slug): array
    {
        $metrics = $this->serviceData($slug)['success_metrics'] ?? [];

        return collect($metrics)
            ->map(fn ($metric): array => is_array($metric)
                ? [
                    'label'  => $metric['label']  ?? $metric['name'] ?? 'Indicador',
                    'target' => $metric['target']  ?? 'A confirmar',
                    'note'   => $metric['note']    ?? 'Meta a ajustar após alinhamento do âmbito.',
                ]
                : [
                    'label'  => (string) $metric,
                    'target' => 'A confirmar',
                    'note'   => 'Meta a ajustar após alinhamento do âmbito.',
                ])
            ->values()
            ->all();
    }

    public function practicalOutputs(string $slug, array $selectedDeliverables): array
    {
        $outputs = $this->serviceData($slug)['practical_outputs'] ?? [];

        return array_values(array_unique([...$selectedDeliverables, ...$outputs]));
    }

    public function technicalTools(string $slug): array
    {
        $profileTools = config('proposal_identity.hr_tools', []);
        $tools        = $this->serviceData($slug)['technical_tools'] ?? [];

        if ($tools === []) {
            return [
                collect($profileTools)->firstWhere('name', 'Scorecards e matrizes de decisão') ?? [
                    'name' => 'Scorecards e matrizes de decisão',
                    'use'  => 'Critérios comparáveis para reduzir subjectividade e apoiar decisões executivas.',
                ],
            ];
        }

        return collect($tools)
            ->map(function ($tool) use ($profileTools): array {
                if (is_string($tool)) {
                    return collect($profileTools)->firstWhere('name', $tool)
                        ?? ['name' => $tool, 'use' => 'Ferramenta técnica aplicável ao serviço proposto.'];
                }

                return [
                    'name' => $tool['name'] ?? 'Ferramenta técnica',
                    'use'  => $tool['use']  ?? 'Ferramenta técnica aplicável ao serviço proposto.',
                ];
            })
            ->take(3)
            ->values()
            ->all();
    }

    public function differentiators(string $slug): array
    {
        return $this->serviceData($slug)['differentiators'] ?? [];
    }

    public function faqs(string $slug): array
    {
        return $this->serviceData($slug)['faqs'] ?? [];
    }

    public function nextSteps(string $slug): array
    {
        return $this->serviceData($slug)['next_steps'] ?? [];
    }

    public function teamMembers(array $profileKeys): array
    {
        $members  = config('proposal_identity.team_members', []);
        $keyMap   = config('proposals.profile_team_map', []);

        return collect($profileKeys)
            ->map(fn (string $key): ?string => $keyMap[$key] ?? null)
            ->filter()
            ->unique()
            ->map(fn (string $memberKey): array => $members[$memberKey] ?? [])
            ->filter()
            ->values()
            ->all();
    }
}
