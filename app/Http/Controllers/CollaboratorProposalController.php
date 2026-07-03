<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CollaboratorProposalController extends Controller
{
    public function index(Request $request): View
    {
        return view('announcements.proposals.index', [
            'admin' => $this->currentAdmin($request),
            'services' => config('service_guides.pt', []),
            'presets' => config('proposal_presets', []),
            'defaults' => $this->defaults(),
        ]);
    }

    public function generate(Request $request): View
    {
        $services = collect(config('service_guides.pt', []));
        $packages = config('proposal_presets.packages', []);
        $complexity = config('proposal_presets.complexity', []);

        $data = $request->validate([
            'service_slug' => ['required', 'string', Rule::in($services->pluck('slug')->all())],
            'proposal_reference' => ['nullable', 'string', 'max:80'],
            'proposal_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:proposal_date'],
            'client_name' => ['required', 'string', 'max:190'],
            'client_contact' => ['nullable', 'string', 'max:190'],
            'client_position' => ['nullable', 'string', 'max:190'],
            'client_email' => ['nullable', 'email', 'max:190'],
            'client_location' => ['nullable', 'string', 'max:190'],
            'client_industry' => ['nullable', 'string', 'max:190'],
            'prepared_by' => ['nullable', 'string', 'max:190'],
            'prepared_role' => ['nullable', 'string', 'max:190'],
            'cover_image_url' => ['nullable', 'url', 'max:1000'],
            'challenge' => ['required', 'string', 'max:2500'],
            'selected_approaches' => ['nullable', 'array'],
            'selected_approaches.*' => ['string', 'max:500'],
            'selected_modules' => ['nullable', 'array'],
            'selected_modules.*' => ['string', 'max:500'],
            'selected_deliverables' => ['nullable', 'array'],
            'selected_deliverables.*' => ['string', 'max:500'],
            'selected_profiles' => ['nullable', 'array'],
            'selected_profiles.*' => ['string', 'max:80'],
            'pricing_package' => ['required', 'string', Rule::in(array_keys($packages))],
            'complexity_level' => ['required', 'string', Rule::in(array_keys($complexity))],
            'objectives' => ['nullable', 'string', 'max:2500'],
            'scope' => ['nullable', 'string', 'max:3000'],
            'methodology' => ['nullable', 'string', 'max:3000'],
            'deliverables' => ['nullable', 'string', 'max:3000'],
            'timeline' => ['nullable', 'string', 'max:1500'],
            'team' => ['nullable', 'string', 'max:1500'],
            'assumptions' => ['nullable', 'string', 'max:2500'],
            'out_of_scope' => ['nullable', 'string', 'max:1500'],
            'currency' => ['required', 'string', 'max:8'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'expenses' => ['nullable', 'numeric', 'min:0'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_terms' => ['nullable', 'string', 'max:1500'],
            'financial_notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $service = $services->firstWhere('slug', $data['service_slug']);
        abort_unless($service, 404);

        $preset = config("proposal_presets.services.{$data['service_slug']}", []);
        $profiles = config('proposal_presets.profiles', []);
        $identity = config('proposal_identity', []);

        $selectedApproaches = $this->selectedFrom($preset['approaches'] ?? [], $data['selected_approaches'] ?? null);
        $selectedModules = $this->selectedFrom($preset['modules'] ?? [], $data['selected_modules'] ?? null);
        $selectedDeliverables = $this->selectedFrom($preset['deliverables'] ?? ($service['deliverables'] ?? []), $data['selected_deliverables'] ?? null);
        $selectedProfileKeys = $data['selected_profiles'] ?? ($preset['profiles'] ?? []);
        $selectedProfiles = collect($selectedProfileKeys)
            ->filter(fn (string $key): bool => isset($profiles[$key]))
            ->map(fn (string $key): string => $profiles[$key])
            ->values()
            ->all();

        if ($selectedProfiles === []) {
            $selectedProfiles = $this->selectedFrom($profiles, $preset['profiles'] ?? []);
        }

        $package = $packages[$data['pricing_package']] ?? reset($packages);
        $complexityLabel = $complexity[$data['complexity_level']] ?? 'Média';
        $pricing = $preset['pricing'] ?? [];
        $challenge = $this->cleanClientText($data['challenge']);

        $fee = (float) ($data['fee'] ?? 0);
        $expenses = (float) ($data['expenses'] ?? 0);
        $vatRate = (float) ($data['vat_rate'] ?? 16);
        $subtotal = $fee + $expenses;
        $vat = round($subtotal * ($vatRate / 100), 2);
        $slug = $data['service_slug'];

        $proposal = [
            ...$data,
            'challenge' => $challenge,
            'proposal_reference' => $data['proposal_reference'] ?: 'BD-PROP-'.now()->format('Ymd'),
            'valid_until' => $data['valid_until'] ?: Carbon::parse($data['proposal_date'])->addDays(15)->format('Y-m-d'),
            'prepared_by' => $data['prepared_by'] ?: 'Business Diversity',
            'prepared_role' => $data['prepared_role'] ?: 'Consultoria Empresarial',
            'cover_image_url' => $data['cover_image_url'] ?: $this->proposalCoverImage($slug),
            'objectives' => $data['objectives'] ?: $this->defaultObjectives($data['client_name'], $service['title'], $selectedApproaches),
            'scope' => $data['scope'] ?: $this->defaultScope($service, $selectedModules),
            'methodology' => $data['methodology'] ?: $this->defaultMethodology($slug),
            'deliverables' => $data['deliverables'] ?: implode("\n", $selectedDeliverables),
            'timeline' => $data['timeline'] ?: 'Cronograma a confirmar após reunião de alinhamento e validação do âmbito final.',
            'team' => $data['team'] ?: implode("\n", $selectedProfiles),
            'assumptions' => $data['assumptions'] ?: "Acesso atempado à informação necessária\nDisponibilidade dos interlocutores-chave\nValidação de decisões críticas pela liderança do cliente",
            'out_of_scope' => $data['out_of_scope'] ?: 'Actividades não descritas no âmbito técnico serão tratadas como pedidos adicionais.',
            'payment_terms' => $data['payment_terms'] ?: '50% na adjudicação e 50% mediante entrega dos principais produtos, salvo acordo específico.',
            'financial_notes' => $data['financial_notes'] ?: $this->defaultFinancialNotes($package, $complexityLabel, $pricing),
            'contextual_summary' => $this->contextualSummary($data, $service, $challenge),
            'personal_letter' => $this->personalLetter($data, $service, $challenge),
            'critical_case' => $this->criticalCase($slug, $service['title']),
            'process_flow' => $this->processFlow($slug),
            'timeline_plan' => $this->timelinePlan($slug),
            'success_metrics' => $this->successMetrics($slug),
            'practical_outputs' => $this->practicalOutputs($slug, $selectedDeliverables),
            'differentiators' => $this->differentiators($slug),
            'team_members' => $this->teamMembers($selectedProfileKeys),
            'closing_note' => $this->closingNote($data, $service),
            'selected_approaches' => $selectedApproaches,
            'selected_modules' => $selectedModules,
            'selected_deliverables' => $selectedDeliverables,
            'selected_profiles' => $selectedProfiles,
            'roadmap' => $this->defaultRoadmap($slug, $selectedModules),
            'pricing_package' => $package,
            'complexity_label' => $complexityLabel,
            'pricing_policy' => $pricing,
            'fee' => $fee,
            'expenses' => $expenses,
            'vat_rate' => $vatRate,
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $subtotal + $vat,
            'has_investment' => ($subtotal + $vat) > 0,
        ];

        return view('announcements.proposals.show', [
            'admin' => $this->currentAdmin($request),
            'service' => $service,
            'proposal' => $proposal,
            'identity' => $identity,
            'lines' => fn (?string $text): array => $this->lines($text),
        ]);
    }

    private function defaults(): array
    {
        return [
            'proposal_reference' => 'BD-PROP-'.now()->format('Ymd'),
            'proposal_date' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(15)->format('Y-m-d'),
            'currency' => 'MZN',
            'vat_rate' => 16,
        ];
    }

    private function proposalCoverImage(string $slug): string
    {
        $images = [
            'recrutamento-seleccao' => 'assets/images/service_03.jpg',
            'gestao-desempenho' => 'assets/images/pexels-pixabay-265087.jpg',
            'carreira-sucessao' => 'assets/images/hero-consulting-team.png',
            'avaliacao-classificacao-cargos' => 'assets/images/service_02.jpg',
            'perfil-comportamental' => 'assets/images/service_00.jpg',
            'politicas-procedimentos' => 'assets/images/service_02.jpg',
            'remuneracao-beneficios' => 'assets/images/pexels-pixabay-265087.jpg',
            'formacao-desenvolvimento' => 'assets/images/service_01.jpg',
            'assessoria-outsourcing-rh' => 'assets/images/hero-consulting-team.png',
            'digitalizacao-rh-endomarketing' => 'assets/images/pexels-pixabay-265087.jpg',
        ];

        return asset($images[$slug] ?? 'assets/images/hero-consulting-team.png');
    }

    private function lines(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n|;/', (string) $text))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function selectedFrom(array $options, ?array $selected): array
    {
        if ($selected === null || $selected === []) {
            return array_values($options);
        }

        return collect($selected)
            ->filter(fn (string $value): bool => in_array($value, $options, true) || array_key_exists($value, $options))
            ->map(fn (string $value): string => $options[$value] ?? $value)
            ->values()
            ->all();
    }

    private function defaultObjectives(string $client, string $service, array $approaches): string
    {
        $objectives = [
            "Apoiar {$client} a tomar decisões consistentes no domínio de {$service}.",
            'Transformar o diagnóstico inicial em instrumentos práticos, aplicáveis e mensuráveis.',
        ];

        foreach (array_slice($approaches, 0, 3) as $approach) {
            $objectives[] = $approach;
        }

        return implode("\n", $objectives);
    }

    private function defaultScope(array $service, array $modules): string
    {
        return trim(($service['value'] ?? '')."\n\nMódulos previstos:\n".implode("\n", $modules));
    }

    private function defaultMethodology(string $slug): string
    {
        $steps = $this->serviceCommercialData($slug)['methodology'] ?? [
            'Reunião de arranque e alinhamento com a liderança.',
            'Diagnóstico documental, entrevistas ou recolha de dados conforme o serviço.',
            'Desenho técnico da solução e validação intermédia.',
            'Implementação acompanhada, comunicação e capacitação dos intervenientes.',
            'Entrega final com recomendações, próximos passos e critérios de acompanhamento.',
        ];

        return implode("\n", $steps);
    }

    private function defaultFinancialNotes(array $package, string $complexity, array $pricing): string
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

    private function defaultRoadmap(string $slug, array $modules): array
    {
        $data = $this->serviceCommercialData($slug);
        $phases = $data['roadmap'] ?? [
            ['label' => 'Arranque', 'title' => 'Alinhamento e mobilização', 'text' => 'Confirmação do âmbito, interlocutores, documentos necessários, calendário e critérios de sucesso.'],
            ['label' => 'Diagnóstico', 'title' => 'Leitura do contexto', 'text' => 'Recolha estruturada de informação, entrevistas, análise documental e identificação dos pontos críticos.'],
            ['label' => 'Desenho', 'title' => 'Construção da solução', 'text' => 'Desenvolvimento dos instrumentos, modelos, políticas, matrizes ou materiais previstos na proposta.'],
            ['label' => 'Validação', 'title' => 'Ajustes com stakeholders', 'text' => 'Sessões de validação, calibração técnica, refinamento dos entregáveis e preparação da implementação.'],
            ['label' => 'Entrega', 'title' => 'Implementação e transferência', 'text' => 'Entrega final, capacitação, plano de continuidade e recomendações para acompanhamento interno.'],
        ];

        foreach (array_slice($modules, 0, count($phases)) as $index => $module) {
            $phases[$index]['module'] = $module;
        }

        return $phases;
    }

    private function contextualSummary(array $data, array $service, string $challenge): string
    {
        $client = $data['client_name'];
        $industry = trim((string) ($data['client_industry'] ?? ''));
        $sector = $industry !== '' ? " no sector de {$industry}" : '';

        if ($data['service_slug'] === 'recrutamento-seleccao') {
            return "Após a informação preliminar partilhada, identificámos a necessidade de apoiar {$client}{$sector} na identificação e selecção de um profissional alinhado ao desafio apresentado: {$challenge}. A intervenção procura reduzir o risco de uma contratação inadequada, acelerar a tomada de decisão e garantir que o perfil escolhido contribui para a liderança, produtividade e cultura da organização.";
        }

        return "Com base na informação partilhada, a Business Diversity entende que {$client}{$sector} procura uma solução de {$service['title']} capaz de responder ao seguinte contexto: {$challenge}. A proposta foi estruturada para transformar esta necessidade em decisões práticas, instrumentos utilizáveis e resultados acompanháveis pela liderança.";
    }

    private function personalLetter(array $data, array $service, string $challenge): string
    {
        $recipient = filled($data['client_contact'] ?? null)
            ? trim(($data['client_position'] ?? 'Exmo(a).')." {$data['client_contact']}")
            : 'Exma. Direcção';

        return "{$recipient}, agradecemos a oportunidade de apresentar esta proposta. Preparámos uma abordagem específica para {$data['client_name']}, considerando o desafio identificado e a relevância estratégica de {$service['title']} para a continuidade, desempenho e crescimento da organização. O nosso compromisso é entregar uma solução tecnicamente sólida, prática e suficientemente clara para apoiar decisões de gestão com segurança.";
    }

    private function criticalCase(string $slug, string $serviceTitle): array
    {
        $data = $this->serviceCommercialData($slug);

        return $data['critical_case'] ?? [
            'title' => "Porque {$serviceTitle} é uma decisão crítica",
            'intro' => 'Intervenções de capital humano têm impacto directo na produtividade, cultura, retenção, custos e qualidade da execução. Quando são tratadas apenas como tarefas administrativas, tornam-se fontes de risco e retrabalho.',
            'items' => ['Redução de subjectividade nas decisões', 'Maior clareza para gestores e colaboradores', 'Melhor utilização de dados e evidências', 'Menor risco operacional e reputacional'],
        ];
    }

    private function processFlow(string $slug): array
    {
        return $this->serviceCommercialData($slug)['process_flow'] ?? ['Alinhar', 'Diagnosticar', 'Desenhar', 'Validar', 'Implementar', 'Medir'];
    }

    private function timelinePlan(string $slug): array
    {
        return $this->serviceCommercialData($slug)['timeline'] ?? [
            ['period' => 'Semana 1', 'title' => 'Arranque', 'text' => 'Kickoff, alinhamento, recolha documental e confirmação do plano de trabalho.'],
            ['period' => 'Semana 2', 'title' => 'Diagnóstico', 'text' => 'Entrevistas, análise de dados e identificação das principais lacunas.'],
            ['period' => 'Semana 3-4', 'title' => 'Desenho', 'text' => 'Construção dos instrumentos e validação intermédia com a equipa do cliente.'],
            ['period' => 'Semana 5', 'title' => 'Entrega', 'text' => 'Ajustes finais, apresentação executiva e recomendações de implementação.'],
        ];
    }

    private function successMetrics(string $slug): array
    {
        return $this->serviceCommercialData($slug)['success_metrics'] ?? [
            'Cumprimento do cronograma acordado',
            'Entregáveis aprovados pela liderança',
            'Nível de adopção dos instrumentos pela equipa interna',
            'Clareza dos critérios de decisão e acompanhamento',
        ];
    }

    private function practicalOutputs(string $slug, array $selectedDeliverables): array
    {
        $outputs = $this->serviceCommercialData($slug)['practical_outputs'] ?? [];

        return array_values(array_unique([...$selectedDeliverables, ...$outputs]));
    }

    private function differentiators(string $slug): array
    {
        return $this->serviceCommercialData($slug)['differentiators'] ?? [
            'Conhecimento do mercado moçambicano e das dinâmicas locais de gestão de pessoas',
            'Experiência sénior em Recursos Humanos combinada com competências digitais e analíticas',
            'Metodologia baseada em evidências, validação e aplicação prática',
            'Relatórios executivos claros para apoiar decisões de liderança',
            'Acompanhamento próximo para transferir capacidade à equipa interna',
        ];
    }

    private function teamMembers(array $profileKeys): array
    {
        $members = config('proposal_identity.team_members', []);
        $map = [
            'senior_hr' => 'sandra',
            'talent' => 'sandra',
            'learning' => 'sandra',
            'reward' => 'sandra',
            'legal_hr' => 'sandra',
            'project_pm' => 'sandra',
            'hr_digital' => 'shelzer',
            'people_analytics' => 'shelzer',
        ];

        return collect($profileKeys)
            ->map(fn (string $key): ?string => $map[$key] ?? null)
            ->filter()
            ->unique()
            ->map(fn (string $memberKey): array => $members[$memberKey] ?? [])
            ->filter()
            ->values()
            ->all();
    }

    private function closingNote(array $data, array $service): string
    {
        return "Agradecemos a oportunidade de apresentar esta proposta. Estamos confiantes de que a experiência, metodologia e compromisso da Business Diversity permitirão à {$data['client_name']} implementar {$service['title']} com rigor, segurança e foco em resultados sustentáveis. Estamos disponíveis para discutir ajustes ao âmbito e avançar para a fase de adjudicação.";
    }

    private function cleanClientText(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        $replacements = [
            ' de nico ' => ' de um técnico ',
            ' nico ' => ' técnico ',
            ' tecnico ' => ' técnico ',
            'Tecnico' => 'Técnico',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    private function serviceCommercialData(string $slug): array
    {
        $generic = [
            'critical_case' => [
                'title' => 'Porque esta intervenção é crítica',
                'intro' => 'Quando decisões de pessoas são adiadas ou tratadas sem método, a organização perde clareza, tempo, confiança e capacidade de execução.',
                'items' => ['Decisões menos subjectivas', 'Mais clareza para a liderança', 'Processos mais consistentes', 'Capacidade interna reforçada'],
            ],
            'process_flow' => ['Alinhar', 'Diagnosticar', 'Desenhar', 'Validar', 'Implementar', 'Medir'],
            'methodology' => ['Kickoff e alinhamento executivo.', 'Diagnóstico com recolha documental, entrevistas e análise de dados.', 'Desenho técnico da solução e validação intermédia.', 'Implementação acompanhada, comunicação e capacitação.', 'Entrega final, transferência de conhecimento e próximos passos.'],
        ];

        $services = [
            'recrutamento-seleccao' => [
                'critical_case' => [
                    'title' => 'Porque esta contratação é crítica',
                    'intro' => 'Uma contratação errada pode afectar produtividade, liderança, cultura, custos e confiança interna. Para posições críticas, seleccionar bem não é apenas preencher uma vaga; é proteger a continuidade e o desempenho da organização.',
                    'items' => ['Redução do risco de má contratação', 'Protecção da cultura e da produtividade', 'Decisão baseada em evidências', 'Melhor adaptação do candidato escolhido'],
                ],
                'process_flow' => ['Perfil', 'Divulgação', 'Triagem', 'Entrevistas', 'Assessment', 'Shortlist', 'Escolha', 'Integração'],
                'methodology' => ['Levantamento da vaga e alinhamento com o gestor.', 'Definição do perfil, critérios obrigatórios e scorecard.', 'Divulgação e sourcing nos canais adequados.', 'Triagem estruturada, entrevistas e validação de evidências.', 'Shortlist, parecer técnico, apoio à decisão e integração.'],
                'roadmap' => [
                    ['label' => 'Perfil', 'title' => 'Levantamento da vaga', 'text' => 'Clarificação da necessidade, responsabilidades, contexto da equipa, critérios técnicos e comportamentais.'],
                    ['label' => 'Mercado', 'title' => 'Divulgação e sourcing', 'text' => 'Escolha de canais, comunicação da oportunidade e identificação activa de candidatos com potencial.'],
                    ['label' => 'Avaliação', 'title' => 'Triagem e entrevistas', 'text' => 'Análise curricular, entrevistas estruturadas, validação de evidências e avaliação comportamental.'],
                    ['label' => 'Decisão', 'title' => 'Shortlist e parecer', 'text' => 'Mapa comparativo, relatórios individuais e recomendação técnica para entrevista final.'],
                    ['label' => 'Entrada', 'title' => 'Contratação e integração', 'text' => 'Apoio à negociação, comunicação final e recomendações para integração do candidato seleccionado.'],
                ],
                'timeline' => [
                    ['period' => 'Semana 1', 'title' => 'Kickoff e perfil', 'text' => 'Alinhamento com o gestor, definição do perfil e scorecard da vaga.'],
                    ['period' => 'Semana 2', 'title' => 'Divulgação', 'text' => 'Sourcing, publicação e activação de canais de pesquisa.'],
                    ['period' => 'Semana 3', 'title' => 'Triagem', 'text' => 'Análise curricular, entrevistas iniciais e filtro técnico/comportamental.'],
                    ['period' => 'Semana 4', 'title' => 'Avaliação', 'text' => 'Entrevistas aprofundadas, assessment ou case quando aplicável.'],
                    ['period' => 'Semana 5', 'title' => 'Shortlist', 'text' => 'Relatórios, mapa comparativo e apresentação de finalistas.'],
                    ['period' => 'Semana 6', 'title' => 'Decisão', 'text' => 'Apoio à entrevista final, negociação e recomendações de integração.'],
                ],
                'success_metrics' => ['Tempo previsto até shortlist', 'Número mínimo de candidatos qualificados avaliados', 'Percentagem de candidatos alinhados ao scorecard', 'Tempo de resposta ao cliente', 'Garantia de substituição quando aplicável', 'Qualidade da integração nos primeiros meses'],
                'practical_outputs' => ['Perfil da vaga', 'Estratégia de divulgação', 'Scorecard de avaliação', 'Shortlist qualificada', 'Relatórios individuais', 'Mapa comparativo de candidatos', 'Parecer do consultor', 'Apoio à negociação', 'Recomendações de integração'],
                'differentiators' => ['Conhecimento do mercado moçambicano de talentos', 'Avaliação técnica e comportamental estruturada', 'Triagem baseada em critérios objectivos', 'Relatórios executivos para decisão rápida', 'Apoio à integração e redução de risco pós-contratação'],
            ],
            'gestao-desempenho' => [
                'process_flow' => ['Objectivos', 'Indicadores', 'Feedback', 'Avaliação', 'Calibração', 'Desenvolvimento'],
                'success_metrics' => ['Objectivos definidos por área/função', 'Gestores preparados para feedback', 'Critérios compreendidos pelos colaboradores', 'Planos de desenvolvimento gerados', 'Ciclo de avaliação concluído no prazo'],
            ],
            'carreira-sucessao' => [
                'process_flow' => ['Cargos críticos', 'Talentos', 'Potencial', 'Sucessores', 'Desenvolvimento', 'Comité'],
                'success_metrics' => ['Cargos críticos mapeados', 'Sucessores identificados', 'Critérios de progressão aprovados', 'Planos de desenvolvimento activos', 'Risco de continuidade reduzido'],
            ],
            'avaliacao-classificacao-cargos' => [
                'process_flow' => ['Inventário', 'Análise', 'Descrição', 'Factores', 'Classificação', 'Validação'],
                'success_metrics' => ['Cargos inventariados', 'Descrições validadas', 'Famílias e níveis definidos', 'Critérios de classificação documentados', 'Base técnica para remuneração e carreira'],
            ],
            'digitalizacao-rh-endomarketing' => [
                'process_flow' => ['Jornada', 'Processos', 'Ferramentas', 'Automação', 'Comunicação', 'Adopção'],
                'success_metrics' => ['Processos críticos mapeados', 'Redução de retrabalho', 'Aumento da adopção pelos utilizadores', 'Materiais de comunicação lançados', 'Indicadores de experiência acompanhados'],
            ],
        ];

        return array_replace_recursive($generic, $services[$slug] ?? []);
    }

    private function currentAdmin(Request $request): AnnouncementAdmin
    {
        return AnnouncementAdmin::query()->findOrFail($request->session()->get('announcement_admin_id'));
    }
}
