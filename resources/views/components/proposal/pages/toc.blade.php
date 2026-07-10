@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="00"
    :label="$en ? 'Executive index' : 'Índice executivo'"
    :title="$en ? 'Decision-oriented document' : 'Documento orientado para decisão'"
    variant="index"
    :pageHead="$vm->reference">
    <div class="proposal-index-grid proposal-index-grid-compact">
        @foreach ($en ? [
            ['01', 'Letter to the client',     'Personalised message and understanding of the need.'],
            ['02', 'Why now',                  'Client reading, decision moment and expected impact.'],
            ['03', 'Executive summary',        'Proposed response, value proposition and key data.'],
            ['04', 'Why it is critical',       'Risks, impact and strategic reason to act.'],
            ['05', 'The BD signature',         'Philosophy, differentiation and our own way of working.'],
            ['06', 'Scope',                    'Objectives, modules and deliverables.'],
            ['07', 'Methodology',              'Work stages, roadmap and validation points.'],
            ['08', 'Timeline',                 'Indicative execution plan.'],
            ['09', 'Practical utility',        'What the client receives and how they will use the deliverables.'],
            ['10', 'Indicators',               'Success KPIs, targets and BD differentials.'],
            ['11', 'Financial',                'Specific investment and commercial rationale.'],
            ['12', 'Terms and guarantees',     'Conditions, payment details and quality guarantees.'],
            ['13', 'Success case',             'Applied example of intervention and results.'],
            ['14', 'Social proof',             'Relevant results and experiences.'],
            ['15', 'Clients and partners',     'Logos and institutional references.'],
            ['16', 'Project team',             'Technical profiles recommended for this intervention.'],
            ['17', 'Credentials and partnerships', 'Applicable certifications, partnerships and recognitions.'],
            ['18', 'FAQ',                      'Frequently asked questions and next steps.'],
            ['19', 'Acceptance',               'Commercial close, validity and signature.'],
        ] : [
            ['01', 'Carta ao cliente',         'Mensagem personalizada e entendimento da necessidade.'],
            ['02', 'Porque agora',              'Leitura do cliente, momento de decisão e impacto esperado.'],
            ['03', 'Sumário executivo',         'Resposta proposta, proposta de valor e dados-chave.'],
            ['04', 'Porque é crítico',          'Riscos, impacto e razão estratégica para agir.'],
            ['05', 'A assinatura BD',           'Filosofia, diferenciação e forma própria de trabalhar.'],
            ['06', 'Âmbito',                    'Objectivos, módulos e entregáveis.'],
            ['07', 'Metodologia',               'Etapas de trabalho, roadmap e pontos de validação.'],
            ['08', 'Cronograma',                'Plano indicativo de execução.'],
            ['09', 'Utilidade prática',         'O que o cliente recebe e como usará os entregáveis.'],
            ['10', 'Indicadores',               'KPIs de sucesso, metas e diferenciais da BD.'],
            ['11', 'Financeiro',                'Investimento específico e racional comercial.'],
            ['12', 'Termos e garantias',        'Condições, dados de pagamento e garantias de qualidade.'],
            ['13', 'Caso de sucesso',           'Exemplo aplicado de intervenção e resultados.'],
            ['14', 'Prova social',              'Resultados e experiências relevantes.'],
            ['15', 'Clientes e parceiros',      'Logótipos e referências institucionais.'],
            ['16', 'Equipa do projecto',        'Perfis técnicos recomendados para esta intervenção.'],
            ['17', 'Credenciais e parcerias',   'Certificações, parcerias e reconhecimentos aplicáveis.'],
            ['18', 'FAQ',                       'Perguntas frequentes e próximos passos.'],
            ['19', 'Aceitação',                 'Fecho comercial, validade e assinatura.'],
        ] as [$number, $title, $text])
            <article>
                <span>{{ $number }}</span>
                <div><h3>{{ $title }}</h3><p>{{ $text }}</p></div>
            </article>
        @endforeach
    </div>
</x-proposal.page>
