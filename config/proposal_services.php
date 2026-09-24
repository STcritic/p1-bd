<?php

/*
 * Dados comerciais por serviço — extraídos de CollaboratorProposalController::serviceCommercialData().
 * A chave 'generic' define os defaults; cada serviço sobrepõe apenas o que difere.
 * Leitura via ContentGeneratorService::serviceData($slug).
 */

return [

    'generic' => [

        'service_need'          => null, // calculado dinamicamente com $serviceTitle
        'positioning_statement' => null, // calculado dinamicamente com $serviceTitle

        'bd_signature_base' => [
            ['label' => 'Leitura antes da solução', 'text' => 'Começamos por entender o contexto, o risco e a decisão que o cliente precisa tomar antes de propor instrumentos.'],
            ['label' => 'Rigor com utilidade',      'text' => 'Traduzimos boas práticas de RH em ferramentas simples de usar, com critérios claros e aplicáveis no dia-a-dia.'],
            ['label' => 'Entrega com transferência','text' => 'A intervenção não termina no documento; procuramos deixar capacidade interna, método e próximos passos.'],
        ],

        'bd_signature_extras' => [],

        'critical_case' => [
            'title' => 'Porque esta intervenção é crítica',
            'intro' => 'Quando decisões de pessoas são adiadas ou tratadas sem método, a organização perde clareza, tempo, confiança e capacidade de execução.',
            'items' => ['Decisões menos subjectivas', 'Mais clareza para a liderança', 'Processos mais consistentes', 'Capacidade interna reforçada'],
        ],

        'featured_case' => [
            'title'        => 'Caso aplicado: estruturação de práticas de RH',
            'sector'       => 'Organização em crescimento',
            'challenge'    => 'A liderança precisava clarificar práticas, reduzir dependência de decisões informais e criar instrumentos de RH utilizáveis por gestores.',
            'intervention' => 'A BD combinou diagnóstico documental, entrevistas com intervenientes-chave, desenho técnico e validação executiva para transformar necessidades dispersas em instrumentos práticos.',
            'results'      => [
                'Critérios mais claros para tomada de decisão.',
                'Instrumentos de RH aplicáveis pela equipa interna.',
                'Plano de continuidade com responsabilidades e próximos passos.',
            ],
            'note' => 'Casos nominativos podem ser apresentados quando houver autorização expressa dos clientes envolvidos.',
        ],

        'process_flow' => ['Alinhar', 'Diagnosticar', 'Desenhar', 'Validar', 'Implementar', 'Medir'],

        'methodology' => [
            'Kickoff e alinhamento executivo.',
            'Diagnóstico com recolha documental, entrevistas e análise de dados.',
            'Desenho técnico da solução e validação intermédia.',
            'Implementação acompanhada, comunicação e capacitação.',
            'Entrega final, transferência de conhecimento e próximos passos.',
        ],

        'roadmap' => [
            ['label' => 'Arranque',    'title' => 'Alinhamento e mobilização',    'text' => 'Confirmação do âmbito, interlocutores, documentos necessários, calendário e critérios de sucesso.'],
            ['label' => 'Diagnóstico', 'title' => 'Leitura do contexto',           'text' => 'Recolha estruturada de informação, entrevistas, análise documental e identificação dos pontos críticos.'],
            ['label' => 'Desenho',     'title' => 'Construção da solução',         'text' => 'Desenvolvimento dos instrumentos, modelos, políticas, matrizes ou materiais previstos na proposta.'],
            ['label' => 'Validação',   'title' => 'Ajustes com stakeholders',      'text' => 'Sessões de validação, calibração técnica, refinamento dos entregáveis e preparação da implementação.'],
            ['label' => 'Entrega',     'title' => 'Implementação e transferência', 'text' => 'Entrega final, capacitação, plano de continuidade e recomendações para acompanhamento interno.'],
        ],

        'timeline' => [
            ['period' => 'Semana 1',   'title' => 'Arranque',    'text' => 'Kickoff, alinhamento, recolha documental e confirmação do plano de trabalho.'],
            ['period' => 'Semana 2',   'title' => 'Diagnóstico', 'text' => 'Entrevistas, análise de dados e identificação das principais lacunas.'],
            ['period' => 'Semana 3-4', 'title' => 'Desenho',     'text' => 'Construção dos instrumentos e validação intermédia com a equipa do cliente.'],
            ['period' => 'Semana 5',   'title' => 'Entrega',     'text' => 'Ajustes finais, apresentação executiva e recomendações de implementação.'],
        ],

        'success_metrics' => [
            ['label' => 'Cronograma cumprido',     'target' => '>=95%',  'note' => 'Marcos executados conforme plano aprovado ou ajustados com validação formal.'],
            ['label' => 'Entregáveis aprovados',   'target' => '100%',   'note' => 'Documentos finais revistos tecnicamente e validados pela liderança.'],
            ['label' => 'Adopção interna inicial', 'target' => '>=80%',  'note' => 'Utilização prática dos instrumentos pelas áreas envolvidas após transferência.'],
            ['label' => 'Tempo de resposta BD',    'target' => '24-48h', 'note' => 'Resposta a dúvidas críticas durante a execução do projecto.'],
        ],

        'technical_tools' => [
            ['name' => 'Matriz de diagnóstico e decisão', 'use' => 'Organiza riscos, prioridades, responsáveis e decisões críticas do projecto.'],
        ],

        'practical_outputs' => [],

        'differentiators' => [
            'Conhecimento do mercado moçambicano e das dinâmicas locais de gestão de pessoas',
            'Experiência sénior em Recursos Humanos combinada com competências digitais e analíticas',
            'Metodologia baseada em evidências, validação e aplicação prática',
            'Relatórios executivos claros para apoiar decisões de liderança',
            'Acompanhamento próximo para transferir capacidade à equipa interna',
        ],

        'faqs' => [
            ['question' => 'Porque contratar uma consultora?',      'answer' => 'Porque decisões de pessoas exigem método, confidencialidade, isenção e capacidade técnica para transformar necessidades internas em soluções aplicáveis.'],
            ['question' => 'Quanto tempo demora?',                  'answer' => 'O prazo final é confirmado após alinhamento do âmbito, disponibilidade dos interlocutores e volume de informação a analisar.'],
            ['question' => 'Como é garantida a confidencialidade?', 'answer' => 'A informação partilhada é tratada apenas pela equipa envolvida no projecto e utilizada exclusivamente para os fins acordados.'],
            ['question' => 'Quem conduz o trabalho?',               'answer' => 'A equipa é definida conforme o serviço, combinando liderança sénior de RH e perfis técnicos especializados quando necessário.'],
            ['question' => 'Qual será o envolvimento do cliente?',  'answer' => 'O cliente participa nos momentos críticos: kickoff, disponibilização de informação, validações intermédias e aprovação final.'],
            ['question' => 'Existe acompanhamento após a entrega?', 'answer' => 'Sim. O modelo pode incluir uma janela de ajustes, transferência de conhecimento e apoio à implementação.'],
        ],

        'next_steps' => [
            'Aprovação da proposta',
            'Reunião de kickoff',
            'Levantamento e diagnóstico',
            'Execução técnica',
            'Validação intermédia',
            'Entrega e transferência',
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // Serviços — cada um sobrepõe apenas o que difere do generic
    // ─────────────────────────────────────────────────────────────────────────

    'recrutamento-seleccao' => [

        'service_need'          => 'A decisão não é apenas preencher uma vaga; é escolher a pessoa certa para proteger produtividade, cultura, continuidade e confiança interna.',
        'positioning_statement' => 'A BD não trata recrutamento como simples triagem de CVs. Estruturamos decisões de talento: clarificamos o perfil, avaliamos evidências e apoiamos a escolha de pessoas capazes de gerar desempenho no contexto real do cliente.',

        'bd_signature_extras' => [
            ['label' => 'Decisão protegida', 'text' => 'Cada candidato é analisado contra critérios definidos, evidências observáveis e aderência ao contexto da função.'],
        ],

        'critical_case' => [
            'title' => 'Porque esta contratação é crítica',
            'intro' => 'Uma contratação errada pode afectar produtividade, liderança, cultura, custos e confiança interna. Para posições críticas, seleccionar bem não é apenas preencher uma vaga; é proteger a continuidade e o desempenho da organização.',
            'items' => ['Redução do risco de má contratação', 'Protecção da cultura e da produtividade', 'Decisão baseada em evidências', 'Melhor adaptação do candidato escolhido'],
        ],

        'featured_case' => [
            'title'        => 'Caso aplicado: recrutamento crítico para função de gestão',
            'sector'       => 'Serviços, operações e administração',
            'challenge'    => 'O cliente precisava preencher uma função sensível sem transformar o processo numa simples recepção de CVs.',
            'intervention' => 'A BD definiu o scorecard da função, estruturou a triagem, conduziu entrevistas por competências, validou evidências e apresentou uma shortlist comparável com parecer técnico para decisão executiva.',
            'results'      => [
                'Shortlist apresentada com candidatos comparáveis e critérios explícitos.',
                'Mapa de decisão com forças, riscos e pontos de atenção por candidato.',
                'Recomendação de integração para proteger os primeiros meses de desempenho.',
            ],
            'note' => 'Os nomes de clientes e candidatos são preservados por confidencialidade.',
        ],

        'process_flow' => ['Perfil', 'Divulgação', 'Triagem', 'Entrevistas', 'Assessment', 'Shortlist', 'Escolha', 'Integração'],

        'methodology' => [
            'Levantamento da vaga e alinhamento com o gestor.',
            'Definição do perfil, critérios obrigatórios e scorecard.',
            'Divulgação e sourcing nos canais adequados.',
            'Triagem estruturada, entrevistas e validação de evidências.',
            'Shortlist, parecer técnico, apoio à decisão e integração.',
        ],

        'roadmap' => [
            ['label' => 'Perfil',    'title' => 'Levantamento da vaga',    'text' => 'Clarificação da necessidade, responsabilidades, contexto da equipa, critérios técnicos e comportamentais.'],
            ['label' => 'Mercado',   'title' => 'Divulgação e sourcing',   'text' => 'Escolha de canais, comunicação da oportunidade e identificação activa de candidatos com potencial.'],
            ['label' => 'Avaliação', 'title' => 'Triagem e entrevistas',   'text' => 'Análise curricular, entrevistas estruturadas, validação de evidências e avaliação comportamental.'],
            ['label' => 'Decisão',   'title' => 'Shortlist e parecer',     'text' => 'Mapa comparativo, relatórios individuais e recomendação técnica para entrevista final.'],
            ['label' => 'Entrada',   'title' => 'Contratação e integração','text' => 'Apoio à negociação, comunicação final e recomendações para integração do candidato seleccionado.'],
        ],

        'timeline' => [
            ['period' => 'Semana 1', 'title' => 'Kickoff e perfil', 'text' => 'Alinhamento com o gestor, definição do perfil e scorecard da vaga.'],
            ['period' => 'Semana 2', 'title' => 'Divulgação',       'text' => 'Sourcing, publicação e activação de canais de pesquisa.'],
            ['period' => 'Semana 3', 'title' => 'Triagem',          'text' => 'Análise curricular, entrevistas iniciais e filtro técnico/comportamental.'],
            ['period' => 'Semana 4', 'title' => 'Avaliação',        'text' => 'Entrevistas aprofundadas, assessment ou case quando aplicável.'],
            ['period' => 'Semana 5', 'title' => 'Shortlist',        'text' => 'Relatórios, mapa comparativo e apresentação de finalistas.'],
            ['period' => 'Semana 6', 'title' => 'Decisão',          'text' => 'Apoio à entrevista final, negociação e recomendações de integração.'],
        ],

        'success_metrics' => [
            ['label' => 'Tempo até shortlist',              'target' => '10-15 dias úteis', 'note' => 'Após validação do perfil e abertura formal do processo.'],
            ['label' => 'Candidatos qualificados avaliados','target' => 'mín. 5',           'note' => 'Número sujeito à disponibilidade real do mercado e requisitos obrigatórios.'],
            ['label' => 'Aderência ao scorecard',           'target' => '>=80%',            'note' => 'Finalistas alinhados aos critérios técnicos, comportamentais e culturais definidos.'],
            ['label' => 'Tempo de resposta ao cliente',     'target' => '24h',              'note' => 'Actualizações críticas e respostas operacionais durante o processo.'],
            ['label' => 'Garantia de substituição',         'target' => '30-60 dias',       'note' => 'Aplicável conforme termos aprovados, função e condições de contratação.'],
            ['label' => 'Integração acompanhada',           'target' => '30 dias',          'note' => 'Recomendações iniciais para reduzir risco pós-contratação.'],
        ],

        'technical_tools' => [
            ['name' => 'Scorecard da vaga',              'use' => 'Critérios técnicos, comportamentais e culturais para comparar candidatos.'],
            ['name' => 'Entrevista por competências',    'use' => 'Guião estruturado para recolher evidências e reduzir subjectividade.'],
            ['name' => 'Mapa comparativo de finalistas', 'use' => 'Síntese executiva para apoiar a decisão do cliente.'],
        ],

        'practical_outputs' => [
            'Perfil da vaga', 'Estratégia de divulgação', 'Scorecard de avaliação', 'Shortlist qualificada',
            'Relatórios individuais', 'Mapa comparativo de candidatos', 'Parecer do consultor',
            'Apoio à negociação', 'Recomendações de integração',
        ],

        'differentiators' => [
            'Conhecimento do mercado moçambicano de talentos',
            'Avaliação técnica e comportamental estruturada',
            'Triagem baseada em critérios objectivos',
            'Relatórios executivos para decisão rápida',
            'Apoio à integração e redução de risco pós-contratação',
        ],

        'faqs' => [
            ['question' => 'Porque não fazer o processo internamente?',       'answer' => 'O cliente pode fazer internamente. A BD acrescenta método, confidencialidade, leitura de mercado, triagem independente e parecer técnico para reduzir risco de decisão.'],
            ['question' => 'Quantos candidatos serão apresentados?',          'answer' => 'A shortlist dependerá da disponibilidade do mercado e dos critérios definidos, mas será composta apenas por candidatos tecnicamente defensáveis para decisão.'],
            ['question' => 'Existe garantia?',                                'answer' => 'Sim. Para processos elegíveis, a proposta pode incluir garantia de substituição dentro do período acordado, desde que as condições de contratação e integração sejam mantidas.'],
            ['question' => 'Quem conduz as entrevistas?',                     'answer' => 'As entrevistas são conduzidas por consultores com experiência em RH, assessment e leitura comportamental, com validação do gestor do cliente nos momentos-chave.'],
            ['question' => 'Como saberemos que o candidato está alinhado?',   'answer' => 'A decisão será apoiada por scorecard, entrevistas estruturadas, evidências técnicas/comportamentais e parecer comparativo.'],
            ['question' => 'O cliente participa em que momentos?',            'answer' => 'Participa no levantamento do perfil, validação da shortlist, entrevistas finais e decisão de contratação.'],
        ],

        'next_steps' => [
            'Aprovação da proposta', 'Kickoff com gestor da vaga', 'Levantamento do perfil',
            'Divulgação e sourcing', 'Triagem e entrevistas', 'Shortlist e decisão', 'Integração do candidato',
        ],
    ],

    'gestao-desempenho' => [
        'service_need'          => 'A prioridade não é apenas avaliar pessoas; é criar um ciclo que alinhe objectivos, feedback, desenvolvimento e decisões de gestão.',
        'positioning_statement' => 'A BD não desenha apenas formulários de avaliação. Construímos sistemas de desempenho que ajudam líderes e colaboradores a conversar melhor, medir melhor e decidir com mais justiça.',
        'bd_signature_extras'   => [
            ['label' => 'Conversas que movem desempenho', 'text' => 'O sistema é desenhado para melhorar objectivos, feedback, calibração e planos de desenvolvimento.'],
        ],
        'process_flow' => ['Objectivos', 'Indicadores', 'Feedback', 'Avaliação', 'Calibração', 'Desenvolvimento'],
        'technical_tools' => [
            ['name' => 'Matriz de objectivos e indicadores', 'use' => 'Liga objectivos individuais, de equipa e prioridades estratégicas.'],
            ['name' => 'Guião de feedback e calibração',     'use' => 'Apoia conversas de desempenho mais consistentes entre gestores e colaboradores.'],
            ['name' => 'Scorecard de desempenho',            'use' => 'Critérios comparáveis para avaliação, desenvolvimento e decisão.'],
        ],
        'success_metrics' => [
            ['label' => 'Objectivos definidos por área/função', 'target' => '100%',  'note' => 'Cada área abrangida termina com objectivos e evidências de desempenho definidos.'],
            ['label' => 'Gestores preparados para feedback',    'target' => '>=90%', 'note' => 'Participação dos gestores nas sessões de preparação e calibração.'],
            ['label' => 'Critérios compreendidos',              'target' => '>=85%', 'note' => 'Clareza validada por comunicação interna e guias simples de aplicação.'],
            ['label' => 'Planos de desenvolvimento gerados',    'target' => '>=80%', 'note' => 'Avaliações convertidas em acções práticas de desenvolvimento.'],
            ['label' => 'Ciclo concluído no prazo',             'target' => '>=95%', 'note' => 'Conclusão do ciclo conforme calendário acordado.'],
        ],
    ],

    'diagnostico-cultura-organizacional' => [
        'service_need'          => 'A prioridade não é apenas medir clima; é compreender como valores, liderança, comunicação, rituais e práticas reais influenciam confiança, colaboração e execução.',
        'positioning_statement' => 'A BD não trata cultura como discurso institucional. Diagnosticamos evidências, padrões de comportamento e incoerências entre valores declarados e práticas reais para apoiar decisões de liderança com clareza.',
        'bd_signature_extras'   => [
            ['label' => 'Cultura traduzida em evidências', 'text' => 'Combinamos questionários, entrevistas, documentos e sinais do dia-a-dia para separar percepção, prática e prioridade de acção.'],
        ],
        'critical_case' => [
            'title' => 'Porque diagnosticar cultura é crítico',
            'intro' => 'Quando a cultura não é compreendida, mudanças falham, valores ficam apenas no papel e a liderança decide com base em impressões dispersas. Um diagnóstico estruturado revela o que fortalece ou bloqueia confiança, colaboração, adaptação e desempenho.',
            'items' => ['Alinhamento entre valores e práticas reais', 'Confiança e comunicação entre liderança e equipas', 'Capacidade de adaptação a mudanças', 'Prioridades claras para intervenção cultural'],
        ],
        'featured_case' => [
            'title'        => 'Caso aplicado: diagnóstico cultural para alinhamento de liderança',
            'sector'       => 'Organização em crescimento e mudança',
            'challenge'    => 'A liderança percebia desalinhamentos entre áreas, comunicação inconsistente e dificuldade em transformar valores institucionais em comportamentos observáveis.',
            'intervention' => 'A BD estruturou um diagnóstico com questionário, entrevistas, análise de práticas e sessão executiva de devolução, ligando resultados a prioridades de acção e rituais de liderança.',
            'results'      => [
                'Mapa claro de forças culturais, tensões e riscos de mudança.',
                'Identificação de incoerências entre valores declarados e práticas vividas.',
                'Plano de acção cultural com prioridades, responsáveis e indicadores simples.',
            ],
            'note' => 'O diagnóstico preserva confidencialidade e apresenta resultados agregados, evitando exposição individual dos participantes.',
        ],
        'process_flow' => ['Alinhar', 'Medir', 'Escutar', 'Triangular', 'Priorizar', 'Activar'],
        'methodology' => [
            'Kickoff executivo para clarificar objectivo, escopo, stakeholders e critérios de confidencialidade.',
            'Recolha multimétodo: questionário, entrevistas/focus groups, análise documental e leitura de rituais/práticas de gestão.',
            'Análise por dimensões culturais: missão, envolvimento, consistência, adaptabilidade, liderança, comunicação e confiança.',
            'Triangulação de evidências para distinguir percepção, prática recorrente, tensão cultural e risco de mudança.',
            'Devolução executiva com mapa de cultura actual, cultura desejada, prioridades e plano de acção.',
        ],
        'roadmap' => [
            ['label' => 'Arranque',    'title' => 'Alinhamento e desenho do diagnóstico', 'text' => 'Confirmação do objectivo, segmentos a auscultar, canais, calendário, comunicação e garantias de confidencialidade.'],
            ['label' => 'Escuta',      'title' => 'Questionário e conversas estruturadas', 'text' => 'Aplicação do diagnóstico, entrevistas/focus groups e recolha de percepções por grupos relevantes.'],
            ['label' => 'Evidências',  'title' => 'Triangulação cultural',                'text' => 'Análise de respostas, documentos, rituais, práticas de liderança, comunicação interna e padrões de decisão.'],
            ['label' => 'Síntese',     'title' => 'Mapa cultural e riscos',                'text' => 'Identificação de forças, incoerências, tensões, alavancas e riscos prioritários para a liderança.'],
            ['label' => 'Activação',   'title' => 'Plano de acção cultural',               'text' => 'Workshop executivo para priorizar acções, responsabilidades, rituais, comunicação e indicadores de acompanhamento.'],
        ],
        'timeline' => [
            ['period' => 'Semana 1',   'title' => 'Preparação',        'text' => 'Kickoff, desenho do diagnóstico, amostra, comunicação e instrumentos.'],
            ['period' => 'Semana 2-3', 'title' => 'Recolha',           'text' => 'Questionário, entrevistas/focus groups e recolha documental.'],
            ['period' => 'Semana 4',   'title' => 'Análise',           'text' => 'Tratamento de dados, segmentação e triangulação de evidências.'],
            ['period' => 'Semana 5',   'title' => 'Devolução',         'text' => 'Relatório executivo, mapa cultural e workshop com liderança.'],
            ['period' => 'Semana 6',   'title' => 'Plano de acção',    'text' => 'Priorização de iniciativas, indicadores, rituais e próximos passos.'],
        ],
        'success_metrics' => [
            ['label' => 'Participação no diagnóstico',       'target' => '>=70%',      'note' => 'Meta ajustável conforme dimensão, canais e população abrangida.'],
            ['label' => 'Segmentos críticos auscultados',    'target' => '100%',       'note' => 'Liderança, RH e grupos-chave incluídos conforme escopo aprovado.'],
            ['label' => 'Evidências trianguladas',           'target' => '3 fontes',   'note' => 'Questionário, entrevistas/focus groups, documentos ou práticas observáveis.'],
            ['label' => 'Prioridades culturais definidas',   'target' => '3-5',        'note' => 'Foco em poucas alavancas com impacto e governança clara.'],
            ['label' => 'Plano de acção aprovado',           'target' => '100%',       'note' => 'Acções, responsáveis, calendário e indicadores validados pela liderança.'],
        ],
        'technical_tools' => [
            ['name' => 'Matriz de diagnóstico cultural',       'use' => 'Organiza dimensões, evidências, forças, tensões e prioridades por grupo analisado.'],
            ['name' => 'Guião de entrevista cultural',         'use' => 'Explora valores vividos, práticas de liderança, comunicação, confiança e pressupostos do dia-a-dia.'],
            ['name' => 'Mapa de alinhamento valores-práticas', 'use' => 'Compara valores declarados com comportamentos, rituais, decisões e sistemas existentes.'],
        ],
        'practical_outputs' => [
            'Questionário de cultura', 'Guiões de entrevista/focus group', 'Mapa da cultura actual',
            'Mapa da cultura desejada', 'Relatório de forças e riscos culturais',
            'Matriz valores-práticas', 'Plano de acção cultural de 90 dias',
            'Recomendações de comunicação e liderança',
        ],
        'differentiators' => [
            'Leitura cultural baseada em evidências e não apenas em opiniões isoladas',
            'Combinação de questionário, escuta qualitativa e análise de práticas reais',
            'Confidencialidade e comunicação cuidadosa para aumentar confiança dos participantes',
            'Recomendações accionáveis para liderança, RH, comunicação e rituais de gestão',
            'Foco em prioridades realistas, mensuráveis e ligadas ao desempenho organizacional',
        ],
        'faqs' => [
            ['question' => 'Este diagnóstico é uma pesquisa de clima?', 'answer' => 'Pode incluir indicadores de clima, mas vai além disso: cruza valores, comportamentos, liderança, comunicação, rituais, práticas e pressupostos que influenciam a forma como a organização funciona.'],
            ['question' => 'As respostas dos colaboradores são confidenciais?', 'answer' => 'Sim. A BD trabalha com tratamento agregado dos resultados e boas práticas de confidencialidade, evitando exposição individual dos participantes.'],
            ['question' => 'Quem deve participar?', 'answer' => 'Depende do objectivo. O diagnóstico pode envolver toda a organização ou uma amostra por áreas, níveis e unidades críticas, incluindo liderança e grupos de colaboradores.'],
            ['question' => 'O resultado será apenas um relatório?', 'answer' => 'Não. O relatório é acompanhado por prioridades, recomendações e plano de acção para transformar achados culturais em decisões práticas.'],
            ['question' => 'Quanto tempo demora?', 'answer' => 'O prazo típico varia entre 4 e 8 semanas, conforme dimensão da população, número de entrevistas/focus groups e nível de segmentação pretendido.'],
            ['question' => 'A BD ajuda depois da devolução?', 'answer' => 'Sim. O serviço pode evoluir para facilitação de workshops, comunicação interna, rituais de liderança, acompanhamento de indicadores e apoio à implementação do plano cultural.'],
        ],
        'next_steps' => [
            'Aprovação da proposta', 'Kickoff executivo', 'Plano de comunicação do diagnóstico',
            'Aplicação do questionário e entrevistas', 'Análise e triangulação',
            'Workshop de devolução', 'Plano de acção cultural',
        ],
    ],

    'carreira-sucessao' => [
        'service_need' => 'A prioridade não é apenas desenhar carreiras; é reduzir risco de continuidade e preparar talento para funções críticas.',
        'process_flow' => ['Cargos críticos', 'Talentos', 'Potencial', 'Sucessores', 'Desenvolvimento', 'Comité'],
        'success_metrics' => [
            ['label' => 'Cargos críticos mapeados',          'target' => '100%',          'note' => 'Funções-chave identificadas com risco, impacto e prioridade.'],
            ['label' => 'Sucessores identificados',          'target' => '1-3 por cargo', 'note' => 'Sempre que existir pipeline interno suficiente.'],
            ['label' => 'Critérios de progressão aprovados', 'target' => '100%',          'note' => 'Critérios validados pela liderança e comunicáveis à organização.'],
            ['label' => 'Planos de desenvolvimento activos', 'target' => '>=80%',         'note' => 'Talentos críticos com acções de desenvolvimento associadas.'],
            ['label' => 'Risco de continuidade reduzido',    'target' => 'Visível',       'note' => 'Risco acompanhado por matriz de prontidão e plano de sucessão.'],
        ],
    ],

    'avaliacao-classificacao-cargos' => [
        'service_need' => 'A prioridade não é apenas descrever cargos; é criar uma base justa para estrutura, remuneração, carreira e decisões organizacionais.',
        'process_flow' => ['Inventário', 'Análise', 'Descrição', 'Factores', 'Classificação', 'Validação'],
        'technical_tools' => [
            'Paterson Job Evaluation',
            ['name' => 'Matriz de descrição de funções', 'use' => 'Organiza responsabilidades, requisitos, autonomia, impacto e indicadores por cargo.'],
            ['name' => 'Mapa de famílias e níveis',      'use' => 'Apoia decisões de carreira, remuneração e estrutura organizacional.'],
        ],
        'success_metrics' => [
            ['label' => 'Cargos inventariados',          'target' => '100%',  'note' => 'Lista consolidada de cargos, famílias e titulares-chave.'],
            ['label' => 'Descrições validadas',          'target' => '>=95%', 'note' => 'Descrições revistas com gestores ou responsáveis funcionais.'],
            ['label' => 'Famílias e níveis definidos',   'target' => '100%',  'note' => 'Arquitectura de cargos pronta para decisões de estrutura, carreira e remuneração.'],
            ['label' => 'Critérios documentados',        'target' => '100%',  'note' => 'Factores de avaliação e enquadramento registados de forma auditável.'],
            ['label' => 'Base técnica para remuneração', 'target' => 'Pronta','note' => 'Informação organizada para suportar política salarial e progressão.'],
        ],
    ],

    'perfil-comportamental' => [
        'process_flow' => ['Objectivo', 'Instrumento', 'Aplicação', 'Análise', 'Devolução', 'Plano'],
        'technical_tools' => [
            'PDA Behavioral Assessment',
            ['name' => 'Mapa comportamental de equipa', 'use' => 'Identifica padrões de colaboração, riscos de comunicação e complementaridades.'],
            ['name' => 'Guião de devolução',            'use' => 'Transforma resultados em conversas práticas de desenvolvimento.'],
        ],
        'featured_case' => [
            'title'        => 'Caso aplicado: leitura comportamental para decisão de equipa',
            'sector'       => 'Equipas técnicas e liderança intermédia',
            'challenge'    => 'A organização precisava compreender estilos de trabalho, riscos de comunicação e pontos de complementaridade antes de tomar decisões de equipa.',
            'intervention' => 'A BD estruturou a aplicação, análise e devolução dos perfis, ligando resultados a recomendações práticas de liderança e colaboração.',
            'results'      => ['Leitura mais clara dos perfis individuais.', 'Recomendações de comunicação e liderança.', 'Plano de desenvolvimento para melhorar colaboração.'],
            'note'         => 'Os instrumentos utilizados são seleccionados conforme objectivo, população e sensibilidade da decisão.',
        ],
        'success_metrics' => [
            ['label' => 'Participantes avaliados',  'target' => '100%',  'note' => 'Avaliações concluídas conforme população acordada.'],
            ['label' => 'Devoluções realizadas',    'target' => '>=90%', 'note' => 'Sessões individuais ou colectivas realizadas conforme modelo aprovado.'],
            ['label' => 'Relatórios entregues',     'target' => '100%',  'note' => 'Relatórios com leitura comportamental, riscos e recomendações.'],
            ['label' => 'Planos de desenvolvimento','target' => '>=80%', 'note' => 'Acções práticas definidas para participantes ou equipas críticas.'],
        ],
    ],

    'politicas-procedimentos' => [
        'process_flow' => ['Auditar', 'Priorizar', 'Redigir', 'Validar', 'Comunicar', 'Aplicar'],
        'success_metrics' => [
            ['label' => 'Políticas críticas concluídas',      'target' => '100%',  'note' => 'Documentos priorizados entregues dentro do âmbito aprovado.'],
            ['label' => 'Fluxos e responsabilidades definidos','target' => '100%', 'note' => 'Procedimentos com responsáveis, prazos e evidências.'],
            ['label' => 'Conformidade revista',               'target' => '100%',  'note' => 'Revisão técnica para reduzir risco operacional e laboral.'],
            ['label' => 'Gestores orientados',                'target' => '>=90%', 'note' => 'Sessões de esclarecimento para aplicação consistente.'],
        ],
    ],

    'remuneracao-beneficios' => [
        'process_flow' => ['Dados', 'Equidade', 'Mercado', 'Bandas', 'Cenários', 'Política'],
        'success_metrics' => [
            ['label' => 'Dados salariais tratados',        'target' => '100%',  'note' => 'Base limpa e organizada para análise.'],
            ['label' => 'Cargos enquadrados',              'target' => '>=95%', 'note' => 'Posicionamento por níveis, famílias ou bandas conforme modelo.'],
            ['label' => 'Cenários financeiros produzidos', 'target' => '2-3',   'note' => 'Alternativas para decisão executiva e sustentabilidade.'],
            ['label' => 'Política remuneratória validada', 'target' => '100%',  'note' => 'Critérios claros para entrada, progressão e revisão.'],
        ],
    ],

    'formacao-desenvolvimento' => [
        'process_flow' => ['Necessidade', 'Desenho', 'Materiais', 'Facilitação', 'Prática', 'Impacto'],
        'success_metrics' => [
            ['label' => 'Objectivos de aprendizagem definidos', 'target' => '100%',  'note' => 'Resultados esperados claros antes da facilitação.'],
            ['label' => 'Participação nas sessões',             'target' => '>=90%', 'note' => 'Presença e envolvimento conforme turmas acordadas.'],
            ['label' => 'Satisfação dos participantes',         'target' => '>=90%', 'note' => 'Avaliação pós-sessão por utilidade, clareza e aplicabilidade.'],
            ['label' => 'Plano de aplicação no trabalho',       'target' => '>=80%', 'note' => 'Participantes com acções práticas para transferência.'],
        ],
    ],

    'assessoria-outsourcing-rh' => [
        'process_flow' => ['Priorizar', 'SLA', 'Executar', 'Reportar', 'Melhorar', 'Transferir'],
        'success_metrics' => [
            ['label' => 'Plano mensal executado',          'target' => '>=95%',    'note' => 'Actividades concluídas ou reprogramadas com validação.'],
            ['label' => 'Tempo de resposta operacional',   'target' => '24-48h',   'note' => 'Conforme urgência, escopo e disponibilidade de informação.'],
            ['label' => 'Relatório executivo entregue',    'target' => 'Mensal',   'note' => 'Resumo de actividades, riscos, pendentes e próximos passos.'],
            ['label' => 'Capacidade interna transferida',  'target' => 'Progressiva','note' => 'Documentação e orientação para reduzir dependência externa.'],
        ],
    ],

    'digitalizacao-rh-endomarketing' => [
        'service_need'          => 'A prioridade não é apenas digitalizar processos; é melhorar a experiência do colaborador e aumentar a adopção real das soluções.',
        'positioning_statement' => 'A BD não digitaliza processos por moda. Simplificamos jornadas, comunicamos a mudança e ajudamos equipas a adoptar ferramentas que tornam RH mais ágil e próximo das pessoas.',
        'bd_signature_extras'   => [
            ['label' => 'Tecnologia que as pessoas adoptam', 'text' => 'Digitalizamos processos com foco em experiência do colaborador, comunicação interna e adopção real.'],
        ],
        'process_flow' => ['Jornada', 'Processos', 'Ferramentas', 'Automação', 'Comunicação', 'Adopção'],
        'success_metrics' => [
            ['label' => 'Processos críticos mapeados',           'target' => '100%',    'note' => 'Fluxos priorizados antes de qualquer digitalização ou automação.'],
            ['label' => 'Redução de retrabalho',                 'target' => '20-30%',  'note' => 'Meta indicativa a confirmar conforme maturidade e volume de processos.'],
            ['label' => 'Adopção pelos utilizadores',            'target' => '>=80%',   'note' => 'Utilização inicial acompanhada por comunicação e suporte.'],
            ['label' => 'Materiais de comunicação lançados',     'target' => '100%',    'note' => 'Peças de endomarketing e instruções para adopção.'],
            ['label' => 'Indicadores de experiência acompanhados','target' => 'Mensal', 'note' => 'Dashboard simples para monitorar uso, dúvidas e melhoria contínua.'],
        ],
    ],

];
