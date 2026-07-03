<?php

return [
    'profiles' => [
        'senior_hr' => 'Consultor sénior de Recursos Humanos — governação técnica, desenho metodológico, validação executiva e gestão de stakeholders.',
        'people_analytics' => 'Especialista em people analytics — análise de dados, indicadores, dashboards, segmentação e evidências para decisão.',
        'reward' => 'Especialista em remuneração e benefícios — bandas salariais, equidade interna, benefícios e critérios remuneratórios.',
        'talent' => 'Especialista em talento e assessment — recrutamento, sucessão, entrevistas estruturadas, avaliação comportamental e potencial.',
        'learning' => 'Especialista em aprendizagem — desenho instrucional, facilitação, avaliação de impacto e transferência para o posto de trabalho.',
        'hr_digital' => 'Especialista em integração digital de RH — processos digitais, automação, experiência do colaborador e adopção tecnológica.',
        'legal_hr' => 'Especialista em políticas e conformidade laboral — revisão de políticas, procedimentos, riscos e alinhamento legal.',
        'project_pm' => 'Gestor de projecto — planeamento, controlo de entregáveis, cronograma, qualidade e comunicação com o cliente.',
    ],

    'packages' => [
        'diagnostico' => [
            'label' => 'Diagnóstico executivo',
            'description' => 'Levantamento rápido, recomendações prioritárias e roteiro de decisão.',
            'pricing' => 'Preço fixo por diagnóstico, ajustado por número de áreas, entrevistas e volume documental.',
        ],
        'implementacao' => [
            'label' => 'Implementação estruturada',
            'description' => 'Desenho, validação e implementação acompanhada dos instrumentos.',
            'pricing' => 'Preço por projecto, calculado por módulos, complexidade, número de colaboradores abrangidos e workshops.',
        ],
        'retainer' => [
            'label' => 'Acompanhamento mensal',
            'description' => 'Suporte contínuo, melhoria incremental e apoio à equipa interna.',
            'pricing' => 'Preço mensal com horas/entregáveis acordados, SLA simples e revisão periódica do âmbito.',
        ],
    ],

    'complexity' => [
        'baixa' => 'Baixa — uma área, poucos stakeholders, informação organizada e decisões rápidas.',
        'media' => 'Média — várias áreas, validações intermédias, necessidade de workshops e consolidação documental.',
        'alta' => 'Alta — múltiplas unidades, dados dispersos, impacto sensível em pessoas, remuneração, cultura ou tecnologia.',
    ],

    'services' => [
        'gestao-desempenho' => [
            'approaches' => [
                'Alinhamento de objectivos individuais, de equipa e prioridades estratégicas.',
                'Definição de indicadores, evidências de desempenho e critérios de avaliação.',
                'Ciclos de feedback contínuo, check-ins e conversa de desenvolvimento.',
                'Calibração com liderança para reduzir subjectividade e enviesamentos.',
                'Integração entre desempenho, desenvolvimento, reconhecimento e recompensa.',
            ],
            'modules' => [
                'Diagnóstico do modelo actual de desempenho e maturidade de gestão.',
                'Desenho de matriz de objectivos, indicadores e pesos.',
                'Modelo de avaliação por competências, resultados e comportamentos.',
                'Guiões para feedback, one-on-one e plano de desenvolvimento individual.',
                'Workshop de preparação de gestores e sessão de calibração.',
                'Piloto controlado e ajustes antes da implementação geral.',
            ],
            'deliverables' => [
                'Modelo de gestão de desempenho.',
                'Matriz de objectivos e indicadores.',
                'Instrumentos de avaliação e feedback.',
                'Guia de calibração e tomada de decisão.',
                'Plano de comunicação e implementação.',
            ],
            'questions' => [
                'Que decisões serão tomadas com base na avaliação: desenvolvimento, bónus, promoção ou sucessão?',
                'A organização já possui objectivos estratégicos traduzidos por área?',
                'Quantos colaboradores e gestores serão abrangidos no primeiro ciclo?',
                'Há sistema digital existente ou o processo será feito com instrumentos simples?',
                'Existe histórico de resistência, baixa confiança ou avaliações muito subjectivas?',
            ],
            'profiles' => ['senior_hr', 'people_analytics', 'project_pm'],
            'pricing' => [
                'base' => 'Por projecto, com faixa por número de colaboradores, número de famílias de função e nível de digitalização.',
                'drivers' => ['Número de colaboradores abrangidos', 'Quantidade de áreas/departamentos', 'Necessidade de calibração executiva', 'Integração com recompensa ou carreira', 'Workshops de gestores'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 85.000 – 180.000',
                    'Implementação estruturada' => 'MZN 220.000 – 650.000',
                    'Acompanhamento mensal' => 'MZN 55.000 – 180.000/mês',
                ],
            ],
        ],

        'carreira-sucessao' => [
            'approaches' => [
                'Identificação de cargos críticos e riscos de continuidade.',
                'Mapeamento de talento, prontidão, potencial e aspiração de carreira.',
                'Arquitectura de carreira com critérios claros de progressão.',
                'Planos de sucessão por função crítica e pools de talento.',
                'Planos de desenvolvimento orientados por experiências críticas.',
            ],
            'modules' => [
                'Diagnóstico de estrutura, níveis e funções críticas.',
                'Definição de critérios de carreira, mobilidade e progressão.',
                'Mapa de sucessão e matriz de prontidão.',
                'Assessment de potencial e lacunas de desenvolvimento.',
                'Plano de desenvolvimento para sucessores e talentos-chave.',
                'Comité de talento e governação do ciclo anual.',
            ],
            'deliverables' => [
                'Arquitectura de carreira.',
                'Mapa de funções críticas.',
                'Matriz de sucessão e prontidão.',
                'Critérios de progressão.',
                'Planos de desenvolvimento de talentos.',
            ],
            'questions' => [
                'Quais funções colocam o negócio em risco caso fiquem vagas?',
                'A empresa quer privilegiar mobilidade interna, contratação externa ou modelo híbrido?',
                'Há critérios actuais de promoção e progressão?',
                'Quantos níveis/famílias profissionais serão tratados?',
                'A liderança está disponível para comité de talento e validação de sucessores?',
            ],
            'profiles' => ['senior_hr', 'talent', 'people_analytics', 'project_pm'],
            'pricing' => [
                'base' => 'Por projecto, ajustado por número de famílias de carreira, cargos críticos e profundidade de assessment.',
                'drivers' => ['Número de cargos críticos', 'Número de talentos avaliados', 'Necessidade de assessment individual', 'Comités de talento', 'Plano de desenvolvimento por pessoa'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 95.000 – 220.000',
                    'Implementação estruturada' => 'MZN 280.000 – 850.000',
                    'Acompanhamento mensal' => 'MZN 65.000 – 200.000/mês',
                ],
            ],
        ],

        'avaliacao-classificacao-cargos' => [
            'approaches' => [
                'Análise de funções com validação por titulares e supervisores.',
                'Descrição de cargos por responsabilidades, requisitos e indicadores.',
                'Avaliação por factores: impacto, complexidade, autonomia, conhecimento e liderança.',
                'Estruturação de níveis, famílias e enquadramento organizacional.',
                'Base técnica para remuneração, recrutamento, carreira e desempenho.',
            ],
            'modules' => [
                'Inventário de cargos e análise documental.',
                'Entrevistas/focus group com titulares e gestores.',
                'Descrição e especificação de cargos.',
                'Avaliação e classificação por factores.',
                'Desenho de famílias, níveis e matriz de enquadramento.',
                'Workshop de validação com liderança.',
            ],
            'deliverables' => [
                'Inventário de cargos.',
                'Descrições de funções.',
                'Matriz de avaliação de cargos.',
                'Estrutura de níveis e famílias.',
                'Relatório de enquadramento e recomendações.',
            ],
            'questions' => [
                'Quantos cargos distintos existem e quantos precisam ser revistos?',
                'Há organograma actualizado e descrições anteriores?',
                'O objectivo principal é estrutura, remuneração, compliance ou carreira?',
                'Há cargos semelhantes com títulos diferentes?',
                'Que nível de envolvimento terão gestores e titulares?',
            ],
            'profiles' => ['senior_hr', 'reward', 'people_analytics', 'project_pm'],
            'pricing' => [
                'base' => 'Por número de cargos/famílias, com acréscimo quando houver entrevistas extensas ou ligação a remuneração.',
                'drivers' => ['Número de cargos', 'Número de entrevistas', 'Complexidade da estrutura', 'Necessidade de política remuneratória', 'Validações executivas'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 90.000 – 200.000',
                    'Implementação estruturada' => 'MZN 300.000 – 950.000',
                    'Acompanhamento mensal' => 'MZN 60.000 – 180.000/mês',
                ],
            ],
        ],

        'perfil-comportamental' => [
            'approaches' => [
                'Definição ética e objectiva do uso de instrumentos comportamentais.',
                'Mapeamento de perfis individuais e padrões de equipa.',
                'Ligação entre perfil, competências da função e contexto cultural.',
                'Feedback individual ou de equipa com foco em desenvolvimento.',
                'Recomendações práticas para liderança, selecção e colaboração.',
            ],
            'modules' => [
                'Definição do objectivo e população-alvo.',
                'Aplicação de instrumento ou matriz comportamental.',
                'Análise individual e consolidação por equipa.',
                'Sessões de devolução e leitura de resultados.',
                'Plano de desenvolvimento e recomendações de composição de equipa.',
            ],
            'deliverables' => [
                'Relatórios individuais.',
                'Mapa comportamental da equipa.',
                'Recomendações de desenvolvimento.',
                'Guião de entrevista/feedback.',
                'Plano de acção da liderança.',
            ],
            'questions' => [
                'O objectivo é selecção, desenvolvimento, liderança, equipa ou sucessão?',
                'Quantas pessoas serão avaliadas?',
                'Haverá devolução individual, colectiva ou ambas?',
                'Que decisões serão tomadas com base nos resultados?',
                'Há sensibilidade interna que exija comunicação prévia mais cuidadosa?',
            ],
            'profiles' => ['talent', 'senior_hr', 'learning'],
            'pricing' => [
                'base' => 'Por participante, com componente fixa de desenho, análise e devolução.',
                'drivers' => ['Número de participantes', 'Tipo de devolução', 'Relatórios individuais', 'Workshops de equipa', 'Uso em selecção ou desenvolvimento'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 45.000 – 120.000',
                    'Implementação estruturada' => 'MZN 6.500 – 18.000 por participante + facilitação',
                    'Acompanhamento mensal' => 'MZN 45.000 – 120.000/mês',
                ],
            ],
        ],

        'recrutamento-seleccao' => [
            'approaches' => [
                'Levantamento rigoroso do perfil da vaga antes da divulgação.',
                'Critérios técnicos, comportamentais e culturais definidos com o gestor.',
                'Triagem estruturada com evidências e shortlisting transparente.',
                'Entrevistas por competências e validação de referências.',
                'Recomendação final com riscos, pontos fortes e plano de integração.',
            ],
            'modules' => [
                'Definição do perfil e scorecard da vaga.',
                'Estratégia de sourcing e divulgação.',
                'Triagem curricular e entrevista inicial.',
                'Entrevista técnica/comportamental estruturada.',
                'Testes, assessment ou case prático quando aplicável.',
                'Relatório de candidatos finalistas e apoio à decisão.',
            ],
            'deliverables' => [
                'Perfil e scorecard da vaga.',
                'Shortlist qualificada.',
                'Relatórios de avaliação.',
                'Mapa comparativo de candidatos.',
                'Recomendações de integração.',
            ],
            'questions' => [
                'A vaga é nova, substituição ou reforço temporário?',
                'Qual é o salário/faixa e pacote disponível?',
                'O perfil é local, regional, executivo, técnico ou massivo?',
                'Quais requisitos são obrigatórios e quais são negociáveis?',
                'Qual é a urgência e quem decide a contratação?',
            ],
            'profiles' => ['talent', 'senior_hr', 'project_pm'],
            'pricing' => [
                'base' => 'Por vaga, por pacote de vagas ou success fee, conforme senioridade e escassez do perfil.',
                'drivers' => ['Senioridade da vaga', 'Escassez do perfil', 'Número de candidatos avaliados', 'Testes/assessment', 'Garantia de substituição'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 35.000 – 95.000',
                    'Implementação estruturada' => '12% – 20% do salário anual ou fee fixo por vaga',
                    'Acompanhamento mensal' => 'MZN 75.000 – 250.000/mês para pacote de vagas',
                ],
            ],
        ],

        'politicas-procedimentos' => [
            'approaches' => [
                'Diagnóstico de práticas actuais, lacunas e riscos de aplicação.',
                'Desenho de políticas simples, aplicáveis e alinhadas à cultura.',
                'Procedimentos com responsáveis, prazos, evidências e pontos de controlo.',
                'Validação de conformidade laboral e coerência interna.',
                'Plano de comunicação e implementação para gestores e colaboradores.',
            ],
            'modules' => [
                'Auditoria documental e levantamento de práticas.',
                'Priorização de políticas críticas.',
                'Redacção/revisão de políticas e procedimentos.',
                'Criação de formulários, fluxos e modelos.',
                'Sessões de validação e capacitação de gestores.',
                'Plano de comunicação interna.',
            ],
            'deliverables' => [
                'Manual de políticas de RH.',
                'Fluxos de procedimento.',
                'Modelos e formulários.',
                'Matriz de responsabilidades.',
                'Plano de implementação.',
            ],
            'questions' => [
                'Quais políticas são críticas: disciplina, férias, benefícios, viagens, assiduidade, recrutamento?',
                'Há documentos existentes ou será criação de raiz?',
                'A prioridade é compliance, padronização, cultura ou auditoria?',
                'Quem aprova políticas internamente?',
                'Será necessário capacitar gestores para aplicação?',
            ],
            'profiles' => ['legal_hr', 'senior_hr', 'project_pm'],
            'pricing' => [
                'base' => 'Por número de políticas/procedimentos e nível de revisão legal/operacional.',
                'drivers' => ['Número de documentos', 'Criação de raiz vs revisão', 'Complexidade legal', 'Workshops de implementação', 'Formulários e fluxos'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 75.000 – 160.000',
                    'Implementação estruturada' => 'MZN 180.000 – 700.000',
                    'Acompanhamento mensal' => 'MZN 50.000 – 160.000/mês',
                ],
            ],
        ],

        'remuneracao-beneficios' => [
            'approaches' => [
                'Análise de equidade interna, competitividade e sustentabilidade financeira.',
                'Estrutura de bandas salariais ligada a cargos, níveis e mercado.',
                'Política remuneratória com critérios de entrada, progressão e revisão.',
                'Revisão de benefícios com foco em valor percebido e custo.',
                'Cenários financeiros para decisão executiva.',
            ],
            'modules' => [
                'Diagnóstico remuneratório e análise de folha.',
                'Ligação com avaliação/classificação de cargos.',
                'Desenho de bandas salariais e critérios de posicionamento.',
                'Revisão de benefícios e proposta de pacote total.',
                'Simulação de impacto financeiro.',
                'Política remuneratória e comunicação executiva.',
            ],
            'deliverables' => [
                'Diagnóstico de remuneração.',
                'Estrutura de bandas salariais.',
                'Política de remuneração e benefícios.',
                'Simulações de impacto.',
                'Recomendações de implementação.',
            ],
            'questions' => [
                'Há dados salariais e organograma actualizados?',
                'A empresa pretende corrigir inequidade, reter talento ou rever benefícios?',
                'Existem cargos avaliados/classificados?',
                'Há referência de mercado disponível ou será necessário benchmark?',
                'Qual é o nível de sensibilidade/confidencialidade do projecto?',
            ],
            'profiles' => ['reward', 'people_analytics', 'senior_hr'],
            'pricing' => [
                'base' => 'Por projecto, sensível ao volume de dados, número de cargos e necessidade de simulação financeira.',
                'drivers' => ['Número de cargos/colaboradores', 'Qualidade dos dados', 'Benchmark externo', 'Simulações financeiras', 'Confidencialidade e validação executiva'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 120.000 – 300.000',
                    'Implementação estruturada' => 'MZN 380.000 – 1.200.000',
                    'Acompanhamento mensal' => 'MZN 80.000 – 250.000/mês',
                ],
            ],
        ],

        'formacao-desenvolvimento' => [
            'approaches' => [
                'Diagnóstico de necessidades alinhado ao negócio e desempenho esperado.',
                'Desenho instrucional com objectivos mensuráveis e aplicação prática.',
                'Metodologia blended: workshop, prática, acompanhamento e materiais.',
                'Avaliação de aprendizagem, aplicação no trabalho e impacto.',
                'Plano de transferência para gestores e participantes.',
            ],
            'modules' => [
                'Levantamento de necessidades e público-alvo.',
                'Desenho de programa e objectivos de aprendizagem.',
                'Desenvolvimento de materiais e exercícios práticos.',
                'Facilitação presencial, online ou híbrida.',
                'Avaliação de aprendizagem e aplicação.',
                'Relatório de impacto e recomendações.',
            ],
            'deliverables' => [
                'Diagnóstico de necessidades.',
                'Plano de formação.',
                'Materiais do participante/facilitador.',
                'Sessões de formação.',
                'Relatório de avaliação e impacto.',
            ],
            'questions' => [
                'Qual comportamento ou competência deve mudar após a formação?',
                'Quantos participantes e quais perfis?',
                'Será presencial, online ou híbrido?',
                'Há gestores disponíveis para acompanhar aplicação pós-formação?',
                'Que indicadores mostrarão que a formação funcionou?',
            ],
            'profiles' => ['learning', 'senior_hr', 'people_analytics'],
            'pricing' => [
                'base' => 'Por desenho + facilitação por turma/dia, com acréscimo para materiais, avaliação e acompanhamento.',
                'drivers' => ['Número de participantes', 'Número de turmas', 'Duração', 'Customização de conteúdo', 'Avaliação de impacto'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 45.000 – 130.000',
                    'Implementação estruturada' => 'MZN 85.000 – 240.000 por turma/dia + desenho',
                    'Acompanhamento mensal' => 'MZN 50.000 – 180.000/mês',
                ],
            ],
        ],

        'assessoria-outsourcing-rh' => [
            'approaches' => [
                'Modelo de apoio flexível com prioridades mensais e indicadores simples.',
                'Separação clara entre operação recorrente, projectos e decisões executivas.',
                'Ritual de acompanhamento com relatório executivo e plano de acção.',
                'Transferência gradual de capacidade para a equipa interna.',
                'Gestão de riscos, confidencialidade e continuidade operacional.',
            ],
            'modules' => [
                'Diagnóstico de maturidade e carga operacional de RH.',
                'Definição de escopo mensal, SLA e canais de trabalho.',
                'Execução de processos prioritários de RH/back office.',
                'Apoio em projectos críticos e decisões de liderança.',
                'Relatórios executivos e indicadores de serviço.',
                'Capacitação da equipa interna.',
            ],
            'deliverables' => [
                'Plano mensal de apoio.',
                'SLA e matriz de responsabilidades.',
                'Relatórios executivos.',
                'Processos e documentos actualizados.',
                'Plano de transferência de capacidade.',
            ],
            'questions' => [
                'A necessidade é temporária, recorrente ou transição para equipa interna?',
                'Que processos entram no escopo mensal?',
                'Quantas horas ou dias de apoio são esperados?',
                'Quem será o ponto focal do cliente?',
                'Que indicadores demonstram bom serviço?',
            ],
            'profiles' => ['senior_hr', 'project_pm', 'legal_hr', 'people_analytics'],
            'pricing' => [
                'base' => 'Retainer mensal por horas/dias, escopo e senioridade; projectos críticos podem ser cotados à parte.',
                'drivers' => ['Horas/dias mensais', 'Número de processos', 'Nível de senioridade', 'Urgência', 'Confidencialidade e risco'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 65.000 – 150.000',
                    'Implementação estruturada' => 'MZN 180.000 – 550.000 por projecto',
                    'Acompanhamento mensal' => 'MZN 95.000 – 450.000/mês',
                ],
            ],
        ],

        'digitalizacao-rh-endomarketing' => [
            'approaches' => [
                'Mapeamento da jornada do colaborador e pontos de fricção.',
                'Simplificação de processos antes da digitalização.',
                'Escolha ou integração de ferramentas com foco em adopção real.',
                'Comunicação interna e endomarketing para engajar utilizadores.',
                'Indicadores de experiência, adopção e eficiência operacional.',
            ],
            'modules' => [
                'Diagnóstico de processos e experiência do colaborador.',
                'Mapa de processos digitais e requisitos funcionais.',
                'Desenho de automações, formulários ou fluxos simples.',
                'Plano de adopção tecnológica e comunicação interna.',
                'Campanhas de endomarketing e materiais de lançamento.',
                'Dashboard de adopção e melhoria contínua.',
            ],
            'deliverables' => [
                'Mapa de processos digitais.',
                'Plano de adopção tecnológica.',
                'Fluxos/formulários digitais.',
                'Campanha de comunicação interna.',
                'Indicadores de experiência e adopção.',
            ],
            'questions' => [
                'Quais processos causam mais retrabalho ou atraso?',
                'Já existe sistema de RH, intranet, Microsoft/Google Workspace ou ferramenta similar?',
                'Quem são os utilizadores principais e qual é o nível de literacia digital?',
                'O foco é eficiência operacional, comunicação interna ou experiência do colaborador?',
                'Que restrições existem sobre dados, acessos e aprovações?',
            ],
            'profiles' => ['hr_digital', 'senior_hr', 'people_analytics', 'project_pm'],
            'pricing' => [
                'base' => 'Por projecto ou sprint, conforme número de processos, integrações, campanhas e nível de automação.',
                'drivers' => ['Número de processos', 'Ferramentas envolvidas', 'Complexidade de integração', 'Campanhas internas', 'Acompanhamento pós-lançamento'],
                'ranges' => [
                    'Diagnóstico executivo' => 'MZN 85.000 – 220.000',
                    'Implementação estruturada' => 'MZN 250.000 – 900.000',
                    'Acompanhamento mensal' => 'MZN 70.000 – 220.000/mês',
                ],
            ],
        ],
    ],
];
