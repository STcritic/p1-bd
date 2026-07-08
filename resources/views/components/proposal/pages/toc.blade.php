@props(['vm'])
<x-proposal.page number="00" label="Índice executivo" title="Documento orientado para decisão" variant="index"
    :pageHead="$vm->reference">
    <div class="proposal-index-grid proposal-index-grid-compact">
        @foreach ([
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
