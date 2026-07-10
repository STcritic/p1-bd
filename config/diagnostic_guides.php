<?php

/**
 * Diagnostic questionnaire definitions per service.
 *
 * Structure per service:
 *   groups[]
 *     key       – machine key
 *     label     – section heading shown to client
 *     guide     – short instruction line shown above the group
 *     conditions – (optional) array of conditions — group only shown if ALL match
 *     questions[]
 *       key       – field key in diagnostic_responses
 *       label     – question text
 *       type      – text|textarea|number|select|multiselect|date|file|boolean|radio
 *       options   – (select/multiselect/radio) [ value => label ]
 *       required  – bool
 *       hint      – (optional) helper text below the field
 *       conditions – (optional) conditional display rules within the group
 *       weight    – (optional) contribution to info_availability score (default 1)
 *
 * Condition format: [ field, operator, value ]
 * Operators: eq, neq, in, not_in, gt, gte, lt, lte, filled, empty
 */
return [

    /* ════════════════════════════════════════════════════════════════════════
     * RECRUTAMENTO E SELECÇÃO
     * ════════════════════════════════════════════════════════════════════════ */
    'recrutamento-seleccao' => [
        'title'  => 'Diagnóstico de Recrutamento',
        'intro'  => 'As suas respostas permitem-nos compreender exactamente o perfil que procura e o contexto organizacional, para que a proposta seja construída especificamente para a sua realidade.',
        'groups' => [

            [
                'key'   => 'cargo',
                'label' => 'Contexto do Cargo',
                'guide' => 'Comece por descrever a função e o seu enquadramento na organização.',
                'questions' => [
                    ['key' => 'titulo_cargo',         'label' => 'Título/Designação do cargo',               'type' => 'text',     'required' => true,  'hint' => 'Ex: Gestor de Recursos Humanos, Director Financeiro'],
                    ['key' => 'departamento',         'label' => 'Departamento ou área funcional',            'type' => 'text',     'required' => true],
                    ['key' => 'nivel_hierarquico',    'label' => 'Nível hierárquico',                        'type' => 'select',   'required' => true,  'options' => [
                        'executivo'    => 'Executivo / C-Level',
                        'gestao'       => 'Gestão (Director, Chefe de Departamento)',
                        'senior'       => 'Sénior (5+ anos)',
                        'mid'          => 'Pleno (2–5 anos)',
                        'junior'       => 'Júnior (0–2 anos)',
                        'tecnico'      => 'Técnico Especializado',
                        'operacional'  => 'Operacional',
                    ]],
                    ['key' => 'tipo_contrato',        'label' => 'Tipo de contrato',                         'type' => 'select',   'required' => true,  'options' => [
                        'efectivo'    => 'Contrato por tempo indeterminado',
                        'prazo'       => 'Contrato a prazo',
                        'prestacao'   => 'Prestação de serviços',
                        'part_time'   => 'Part-time',
                    ]],
                    ['key' => 'numero_vagas',         'label' => 'Número de vagas',                          'type' => 'number',   'required' => true,  'hint' => 'Quantas posições pretende preencher'],
                    ['key' => 'motivo',               'label' => 'Motivo da contratação',                    'type' => 'select',   'required' => true,  'options' => [
                        'novo_posto'   => 'Novo posto de trabalho',
                        'expansao'     => 'Expansão da equipa',
                        'substituicao' => 'Substituição',
                        'saida'        => 'Saída não prevista',
                    ]],
                ],
            ],

            [
                'key'   => 'perfil',
                'label' => 'Perfil Pretendido',
                'guide' => 'Descreva as competências, formação e experiência esperadas.',
                'questions' => [
                    ['key' => 'formacao_minima',       'label' => 'Formação académica mínima',               'type' => 'select',   'required' => false, 'options' => [
                        'basica'       => 'Ensino básico',
                        'medio'        => 'Ensino médio',
                        'licenciatura' => 'Licenciatura',
                        'mestrado'     => 'Mestrado',
                        'doutoramento' => 'Doutoramento',
                        'indiferente'  => 'Indiferente',
                    ]],
                    ['key' => 'area_formacao',         'label' => 'Área de formação preferencial',           'type' => 'text',     'required' => false, 'hint' => 'Ex: Gestão, Engenharia Civil, Contabilidade'],
                    ['key' => 'experiencia_anos',      'label' => 'Anos de experiência mínimos',             'type' => 'number',   'required' => true],
                    ['key' => 'competencias_tecnicas', 'label' => 'Competências técnicas obrigatórias',      'type' => 'textarea', 'required' => true,  'hint' => 'Liste as competências técnicas essenciais para o cargo', 'weight' => 2],
                    ['key' => 'competencias_soft',     'label' => 'Competências comportamentais valorizadas','type' => 'textarea', 'required' => false, 'hint' => 'Ex: Liderança, comunicação, resolução de problemas'],
                    ['key' => 'idiomas',               'label' => 'Idiomas requeridos',                      'type' => 'text',     'required' => false, 'hint' => 'Ex: Inglês fluente, Português nativo'],
                    ['key' => 'ferramentas',           'label' => 'Ferramentas/software específicos',        'type' => 'text',     'required' => false],
                ],
            ],

            [
                'key'   => 'condicoes',
                'label' => 'Condições e Pacote',
                'guide' => 'As condições da oferta ajudam-nos a posicionar correctamente a pesquisa no mercado.',
                'questions' => [
                    ['key' => 'salario_min',           'label' => 'Salário base mínimo (MZN)',                'type' => 'number',   'required' => false, 'hint' => 'Deixe em branco se ainda não definido'],
                    ['key' => 'salario_max',           'label' => 'Salário base máximo (MZN)',                'type' => 'number',   'required' => false],
                    ['key' => 'beneficios',            'label' => 'Benefícios incluídos',                    'type' => 'textarea', 'required' => false, 'hint' => 'Ex: Viatura, subsídio de alimentação, seguro de saúde'],
                    ['key' => 'local_trabalho',        'label' => 'Local de trabalho',                       'type' => 'text',     'required' => true,  'hint' => 'Cidade e província'],
                    ['key' => 'modelo_trabalho',       'label' => 'Modelo de trabalho',                      'type' => 'select',   'required' => true,  'options' => [
                        'presencial' => 'Presencial',
                        'hibrido'    => 'Híbrido',
                        'remoto'     => 'Remoto',
                    ]],
                    ['key' => 'disponibilidade_viagem','label' => 'Disponibilidade para viagens?',           'type' => 'boolean',  'required' => false],
                ],
            ],

            [
                'key'   => 'processo',
                'label' => 'Urgência e Processo',
                'guide' => 'Estes dados permitem-nos calibrar o plano de trabalho.',
                'questions' => [
                    ['key' => 'urgencia',              'label' => 'Nível de urgência',                       'type' => 'select',   'required' => true,  'options' => [
                        'critica' => 'Crítica: precisamos de alguém já',
                        'alta'    => 'Alta: no prazo de 4 semanas',
                        'normal'  => 'Normal: dentro do planeamento habitual',
                        'baixa'   => 'Baixa: sem pressão de prazo',
                    ]],
                    ['key' => 'prazo_inicio',          'label' => 'Data ideal para início do colaborador',   'type' => 'date',     'required' => false],
                    ['key' => 'participantes_decisao', 'label' => 'Quem participa na decisão final?',        'type' => 'textarea', 'required' => false, 'hint' => 'Ex: CEO, Director de RH, Director de Área'],
                    ['key' => 'etapas_seleccao',       'label' => 'Etapas de selecção internas planeadas',  'type' => 'textarea', 'required' => false, 'hint' => 'Ex: Entrevista inicial, teste técnico, entrevista com CEO'],
                    ['key' => 'experiencias_anteriores','label' => 'Já recorreu a empresas de recrutamento?','type' => 'boolean',  'required' => false],
                    ['key' => 'experiencias_notas',    'label' => 'Se sim, partilhe a experiência',         'type' => 'textarea', 'required' => false, 'conditions' => [['field' => 'experiencias_anteriores', 'operator' => 'eq', 'value' => true]]],
                ],
            ],

            [
                'key'        => 'contexto_org',
                'label'      => 'Contexto Organizacional',
                'guide'      => 'Compreender a organização permite-nos identificar o perfil cultural certo.',
                'conditions' => [],
                'questions'  => [
                    ['key' => 'dimensao_empresa',      'label' => 'Número total de colaboradores',           'type' => 'number',   'required' => false],
                    ['key' => 'sector',                'label' => 'Sector de actividade',                    'type' => 'text',     'required' => true,  'hint' => 'Ex: Industrial, Financeiro, Retalho, Telecomunicações'],
                    ['key' => 'cultura_organizacional','label' => 'Descreva a cultura da organização',       'type' => 'textarea', 'required' => false, 'hint' => 'Como descreveria o ambiente de trabalho e os valores da empresa?', 'weight' => 2],
                    ['key' => 'subordinados_directos', 'label' => 'Número de subordinados directos do cargo','type' => 'number',   'required' => false],
                    ['key' => 'maturidade_rh',         'label' => 'Maturidade do departamento de RH',       'type' => 'select',   'required' => false, 'options' => [
                        'inicial'     => 'Inicial: sem estrutura formal',
                        'basica'      => 'Básica: processos informais',
                        'estruturada' => 'Estruturada: processos definidos',
                        'avancada'    => 'Avançada: RH estratégico',
                    ]],
                    ['key' => 'documentos_disponiveis','label' => 'Documentação disponível',                 'type' => 'select',   'required' => false, 'options' => [
                        'completa'    => 'Completa (descrição de funções, organograma, política salarial)',
                        'parcial'     => 'Parcial',
                        'nenhum'      => 'Nenhuma documentação disponível',
                    ]],
                ],
            ],

            [
                'key'        => 'executivo',
                'label'      => 'Perfil Executivo',
                'guide'      => 'Para posições de liderança sénior, estas informações são determinantes.',
                'conditions' => [['field' => 'nivel_hierarquico', 'operator' => 'in', 'value' => ['executivo', 'gestao']]],
                'questions'  => [
                    ['key' => 'desafio_estrategico',   'label' => 'Principal desafio estratégico do cargo',  'type' => 'textarea', 'required' => false, 'hint' => 'O que deve o novo colaborador resolver ou transformar?', 'weight' => 3],
                    ['key' => 'governanca',            'label' => 'Estrutura de governança e reporte',       'type' => 'textarea', 'required' => false, 'hint' => 'A quem reporta? Que comités ou boards existem?'],
                    ['key' => 'estrategia_empresa',    'label' => 'Estratégia da empresa nos próximos 3 anos','type' => 'textarea', 'required' => false, 'hint' => 'Partilhe os objectivos estratégicos relevantes para esta posição', 'weight' => 2],
                    ['key' => 'plano_sucessao',        'label' => 'Existe plano de sucessão para este cargo?','type' => 'boolean', 'required' => false],
                    ['key' => 'kpis_cargo',            'label' => 'KPIs e métricas de sucesso do cargo',    'type' => 'textarea', 'required' => false, 'hint' => 'Como será medido o desempenho nos primeiros 12 meses?', 'weight' => 2],
                ],
            ],

            [
                'key'   => 'documentos',
                'label' => 'Documentos de Suporte',
                'guide' => 'Partilhe qualquer documento relevante: descrições de funções, organogramas, perfis anteriores.',
                'questions' => [
                    ['key' => 'ficheiro_descricao',    'label' => 'Descrição de funções (se existir)',       'type' => 'file',     'required' => false, 'hint' => 'PDF, Word ou imagem'],
                    ['key' => 'ficheiro_organograma',  'label' => 'Organograma (se disponível)',             'type' => 'file',     'required' => false],
                    ['key' => 'notas_adicionais',      'label' => 'Notas ou contexto adicional',            'type' => 'textarea', 'required' => false, 'hint' => 'Qualquer informação que considere relevante partilhar', 'weight' => 2],
                ],
            ],
        ],
    ],

    /* ════════════════════════════════════════════════════════════════════════
     * PLANOS DE CARREIRA E SUCESSÃO
     * ════════════════════════════════════════════════════════════════════════ */
    'carreira-sucessao' => [
        'title'  => 'Diagnóstico de Carreira e Sucessão',
        'intro'  => 'O diagnóstico permite-nos mapear a estrutura de carreira existente e desenhar um plano adaptado à realidade da sua organização.',
        'groups' => [
            [
                'key'   => 'estrutura',
                'label' => 'Estrutura Organizacional',
                'guide' => 'Partilhe informação sobre a dimensão e estrutura da organização.',
                'questions' => [
                    ['key' => 'dimensao_empresa',      'label' => 'Total de colaboradores',                  'type' => 'number',   'required' => true],
                    ['key' => 'numero_funcoes',        'label' => 'Número de funções/cargos distintos',      'type' => 'number',   'required' => false],
                    ['key' => 'sectores',              'label' => 'Departamentos ou áreas funcionais',       'type' => 'textarea', 'required' => true],
                    ['key' => 'sector',                'label' => 'Sector de actividade',                    'type' => 'text',     'required' => true],
                    ['key' => 'ficheiro_organograma',  'label' => 'Organograma actual',                     'type' => 'file',     'required' => false, 'hint' => 'Se disponível', 'weight' => 2],
                ],
            ],
            [
                'key'   => 'situacao_actual',
                'label' => 'Situação Actual',
                'guide' => 'Ajude-nos a entender o estado actual das carreiras e da gestão de talento.',
                'questions' => [
                    ['key' => 'politica_carreira_existe','label' => 'Existe política de progressão de carreira?','type' => 'boolean','required' => true],
                    ['key' => 'criterios_actuais',     'label' => 'Critérios de progressão actuais',        'type' => 'textarea', 'required' => false, 'conditions' => [['field' => 'politica_carreira_existe', 'operator' => 'eq', 'value' => true]]],
                    ['key' => 'problemas_actuais',     'label' => 'Principais problemas ou lacunas identificados','type' => 'textarea','required' => true,'hint' => 'O que motivou este projecto?', 'weight' => 3],
                    ['key' => 'rotatividade',          'label' => 'Taxa de rotatividade anual estimada (%)', 'type' => 'number',   'required' => false],
                    ['key' => 'cargos_criticos',       'label' => 'Cargos críticos sem sucessor identificado','type' => 'textarea','required' => false,'hint' => 'Quais as posições cuja saída causaria maior impacto?', 'weight' => 2],
                    ['key' => 'urgencia',              'label' => 'Urgência do projecto',                   'type' => 'select',   'required' => true,  'options' => [
                        'critica' => 'Crítica',
                        'alta'    => 'Alta',
                        'normal'  => 'Normal',
                        'baixa'   => 'Baixa',
                    ]],
                ],
            ],
            [
                'key'   => 'objectivos',
                'label' => 'Objectivos e Âmbito',
                'guide' => 'Defina o que espera alcançar com este projecto.',
                'questions' => [
                    ['key' => 'objectivo_principal',   'label' => 'Objectivo principal do projecto',        'type' => 'textarea', 'required' => true, 'weight' => 3],
                    ['key' => 'ambito',                'label' => 'Âmbito do projecto: toda a organização ou só alguns departamentos?','type' => 'textarea','required' => true],
                    ['key' => 'prazo_implementacao',   'label' => 'Prazo ideal de implementação',           'type' => 'date',     'required' => false],
                    ['key' => 'envolvimento_lideranca','label' => 'Nível de envolvimento da liderança',     'type' => 'select',   'required' => false,'options' => [
                        'total'   => 'Total: liderança comprometida',
                        'parcial' => 'Parcial: algum apoio',
                        'baixo'   => 'Baixo: necessita sensibilização',
                    ]],
                    ['key' => 'notas_adicionais',      'label' => 'Informação adicional relevante',         'type' => 'textarea', 'required' => false, 'weight' => 2],
                    ['key' => 'documentos',            'label' => 'Documentos de suporte',                  'type' => 'file',     'required' => false],
                ],
            ],
        ],
    ],

    /* ════════════════════════════════════════════════════════════════════════
     * GESTÃO DE DESEMPENHO
     * ════════════════════════════════════════════════════════════════════════ */
    'gestao-desempenho' => [
        'title'  => 'Diagnóstico de Gestão de Desempenho',
        'intro'  => 'Compreender o contexto actual permite-nos desenhar um sistema de desempenho que se integra na cultura da organização.',
        'groups' => [
            [
                'key'   => 'situacao',
                'label' => 'Situação Actual',
                'guide' => 'Descreva o estado actual da gestão de desempenho na organização.',
                'questions' => [
                    ['key' => 'dimensao_empresa',      'label' => 'Total de colaboradores',                  'type' => 'number',   'required' => true],
                    ['key' => 'sector',                'label' => 'Sector de actividade',                    'type' => 'text',     'required' => true],
                    ['key' => 'sistema_actual',        'label' => 'Sistema de avaliação actual',             'type' => 'select',   'required' => true, 'options' => [
                        'nenhum'     => 'Sem sistema formal',
                        'informal'   => 'Avaliação informal/subjectiva',
                        'basico'     => 'Sistema básico (formulários simples)',
                        'estruturado'=> 'Sistema estruturado com KPIs',
                        'avancado'   => 'Sistema avançado (OKRs, 360°)',
                    ]],
                    ['key' => 'problemas_actuais',     'label' => 'Principais problemas com o sistema actual','type' => 'textarea','required' => true, 'weight' => 3],
                    ['key' => 'urgencia',              'label' => 'Urgência',                                'type' => 'select',   'required' => true, 'options' => ['critica' => 'Crítica', 'alta' => 'Alta', 'normal' => 'Normal', 'baixa' => 'Baixa']],
                ],
            ],
            [
                'key'   => 'objectivos',
                'label' => 'Objectivos e Modelo Pretendido',
                'guide' => 'Defina o que espera alcançar e o modelo que prefere.',
                'questions' => [
                    ['key' => 'modelo_preferido',      'label' => 'Modelo de avaliação preferido',          'type' => 'multiselect','required' => false,'options' => [
                        'kpi'       => 'KPIs por função',
                        'okr'       => 'OKRs',
                        '360'       => 'Avaliação 360°',
                        'competencias'=> 'Por competências',
                        'mbo'       => 'Gestão por objectivos (MBO)',
                    ]],
                    ['key' => 'objectivo_principal',   'label' => 'O que pretende alcançar?',               'type' => 'textarea', 'required' => true, 'weight' => 3],
                    ['key' => 'ligacao_remuneracao',   'label' => 'Pretende ligar desempenho à remuneração?','type' => 'boolean',  'required' => false],
                    ['key' => 'periodicidade',         'label' => 'Periodicidade das avaliações',            'type' => 'select',   'required' => false,'options' => [
                        'mensal'     => 'Mensal',
                        'trimestral' => 'Trimestral',
                        'semestral'  => 'Semestral',
                        'anual'      => 'Anual',
                    ]],
                    ['key' => 'prazo',                 'label' => 'Prazo de implementação',                 'type' => 'date',     'required' => false],
                    ['key' => 'notas_adicionais',      'label' => 'Contexto adicional',                     'type' => 'textarea', 'required' => false, 'weight' => 2],
                    ['key' => 'documentos',            'label' => 'Documentos de suporte',                  'type' => 'file',     'required' => false],
                ],
            ],
        ],
    ],

    /* ════════════════════════════════════════════════════════════════════════
     * DEFAULT — used for any service without a specific guide
     * ════════════════════════════════════════════════════════════════════════ */
    '_default' => [
        'title'  => 'Diagnóstico Inicial',
        'intro'  => 'As suas respostas permitem-nos personalizar a proposta à realidade da sua organização.',
        'groups' => [
            [
                'key'   => 'contexto',
                'label' => 'Contexto do Projecto',
                'guide' => 'Descreva a situação actual e o que motivou este projecto.',
                'questions' => [
                    ['key' => 'dimensao_empresa',      'label' => 'Número de colaboradores',                 'type' => 'number',   'required' => false],
                    ['key' => 'sector',                'label' => 'Sector de actividade',                    'type' => 'text',     'required' => true],
                    ['key' => 'situacao_actual',       'label' => 'Situação actual',                        'type' => 'textarea', 'required' => true, 'hint' => 'Descreva o contexto que motivou este projecto', 'weight' => 3],
                    ['key' => 'objectivo_principal',   'label' => 'Objectivo principal',                    'type' => 'textarea', 'required' => true, 'weight' => 3],
                    ['key' => 'urgencia',              'label' => 'Urgência',                               'type' => 'select',   'required' => true, 'options' => ['critica' => 'Crítica', 'alta' => 'Alta', 'normal' => 'Normal', 'baixa' => 'Baixa']],
                    ['key' => 'prazo',                 'label' => 'Prazo pretendido',                       'type' => 'date',     'required' => false],
                    ['key' => 'notas_adicionais',      'label' => 'Informação adicional',                   'type' => 'textarea', 'required' => false, 'weight' => 2],
                    ['key' => 'documentos',            'label' => 'Documentos de suporte',                  'type' => 'file',     'required' => false],
                ],
            ],
        ],
    ],
];
