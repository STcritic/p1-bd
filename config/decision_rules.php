<?php

/**
 * Decision Engine rules.
 *
 * Each rule has:
 *   id          – unique identifier
 *   services    – which service slugs this rule applies to (empty = all)
 *   conditions  – array of [ field, operator, value ] — ALL must match (AND logic)
 *   actions     – what happens when the rule fires
 *
 * Operators: eq, neq, in, not_in, gt, gte, lt, lte, contains, starts_with
 *
 * Actions:
 *   set_score_dimension  – sets/adds to a score dimension (urgency, complexity, etc.)
 *   add_argument         – injects a text argument into the proposal narrative
 *   add_timeline_note    – appends a note to timeline/assumptions
 *   set_complexity       – overrides complexity level
 *   add_tag              – tags the opportunity (used downstream for filtering)
 *   flag_risk            – flags a risk factor (shown in decision summary)
 */
return [

    'rules' => [

        // ── Urgency ──────────────────────────────────────────────────────────

        [
            'id'         => 'urgency_critical',
            'services'   => [],
            'conditions' => [['field' => 'urgencia', 'operator' => 'eq', 'value' => 'critica']],
            'actions'    => [
                'set_score_dimension' => ['urgency' => 100],
                'add_argument'        => 'Este processo tem carácter crítico e requer mobilização imediata. A BD adopta uma abordagem de resposta rápida, priorizando este mandato no calendário de execução.',
                'add_tag'             => 'urgente',
                'set_complexity'      => 'alta',
                'add_timeline_note'   => 'Prazo crítico declarado pelo cliente. Processo em regime de prioridade.',
            ],
        ],

        [
            'id'         => 'urgency_high',
            'services'   => [],
            'conditions' => [['field' => 'urgencia', 'operator' => 'eq', 'value' => 'alta']],
            'actions'    => [
                'set_score_dimension' => ['urgency' => 75],
                'add_argument'        => 'A urgência declarada pelo cliente justifica uma abordagem acelerada, com etapas de triagem comprimidas e comunicação proactiva.',
                'add_tag'             => 'urgente',
            ],
        ],

        [
            'id'         => 'urgency_normal',
            'services'   => [],
            'conditions' => [['field' => 'urgencia', 'operator' => 'eq', 'value' => 'normal']],
            'actions'    => [
                'set_score_dimension' => ['urgency' => 40],
            ],
        ],

        // ── Industry context ─────────────────────────────────────────────────

        [
            'id'         => 'sector_industrial',
            'services'   => [],
            'conditions' => [['field' => 'sector', 'operator' => 'contains', 'value' => 'industrial']],
            'actions'    => [
                'set_score_dimension' => ['complexity' => 20],
                'add_argument'        => 'O ambiente industrial exige perfis com elevada robustez técnica, sensibilidade para normas de segurança operacional e capacidade de trabalho em contextos de pressão contínua.',
                'add_tag'             => 'industrial',
            ],
        ],

        [
            'id'         => 'sector_financeiro',
            'services'   => [],
            'conditions' => [['field' => 'sector', 'operator' => 'contains', 'value' => 'financ']],
            'actions'    => [
                'add_argument'        => 'O sector financeiro impõe requisitos específicos de conformidade regulatória, integridade e discrição, factores que a BD integra nos critérios de avaliação.',
                'add_tag'             => 'financas',
            ],
        ],

        // ── Role level ───────────────────────────────────────────────────────

        [
            'id'         => 'role_executive',
            'services'   => ['recrutamento-seleccao'],
            'conditions' => [['field' => 'nivel_hierarquico', 'operator' => 'eq', 'value' => 'executivo']],
            'actions'    => [
                'set_score_dimension' => ['complexity' => 40, 'urgency' => 10],
                'add_argument'        => 'O perfil executivo requer uma abordagem de headhunting: pesquisa proactiva, avaliação de competências de liderança e alinhamento com a visão estratégica da organização.',
                'add_tag'             => 'headhunting',
                'set_complexity'      => 'alta',
            ],
        ],

        [
            'id'         => 'role_management',
            'services'   => ['recrutamento-seleccao'],
            'conditions' => [['field' => 'nivel_hierarquico', 'operator' => 'eq', 'value' => 'gestao']],
            'actions'    => [
                'set_score_dimension' => ['complexity' => 20],
                'add_argument'        => 'O nível de gestão exige avaliação de competências de liderança, capacidade de decisão e alinhamento cultural.',
            ],
        ],

        // ── Company size ─────────────────────────────────────────────────────

        [
            'id'         => 'company_large',
            'services'   => [],
            'conditions' => [['field' => 'dimensao_empresa', 'operator' => 'gte', 'value' => 500]],
            'actions'    => [
                'set_score_dimension' => ['complexity' => 15],
                'add_argument'        => 'Numa organização desta dimensão, o processo implica uma cuidadosa gestão de expectativas internas, comunicação transversal e alinhamento com múltiplos stakeholders.',
                'add_tag'             => 'grande-empresa',
                'add_timeline_note'   => 'Organização com 500+ colaboradores: prevê-se gestão de stakeholders adicional.',
            ],
        ],

        [
            'id'         => 'company_very_large',
            'services'   => [],
            'conditions' => [['field' => 'dimensao_empresa', 'operator' => 'gte', 'value' => 1000]],
            'actions'    => [
                'set_score_dimension' => ['complexity' => 25],
                'add_argument'        => 'A dimensão organizacional impõe uma metodologia de mudança estruturada, com atenção particular à resistência interna e à comunicação descendente.',
                'add_tag'             => 'mudanca-organizacional',
            ],
        ],

        // ── HR Maturity ──────────────────────────────────────────────────────

        [
            'id'         => 'hr_maturity_low',
            'services'   => [],
            'conditions' => [['field' => 'maturidade_rh', 'operator' => 'in', 'value' => ['inicial', 'basica']]],
            'actions'    => [
                'set_score_dimension' => ['complexity' => 20, 'hr_maturity' => 20],
                'add_argument'        => 'A organização encontra-se numa fase inicial de estruturação de RH, o que amplia o valor estratégico deste projecto e requer uma abordagem pedagógica e orientadora.',
                'flag_risk'           => 'Maturidade de RH baixa: é expectável que seja necessário maior suporte durante a implementação.',
            ],
        ],

        // ── Information availability ─────────────────────────────────────────

        [
            'id'         => 'info_scarce',
            'services'   => [],
            'conditions' => [['field' => 'documentos_disponiveis', 'operator' => 'eq', 'value' => 'nenhum']],
            'actions'    => [
                'set_score_dimension' => ['info_availability' => 10],
                'flag_risk'           => 'Cliente sem documentação disponível; o levantamento de informação em campo fará parte do âmbito.',
                'add_timeline_note'   => 'Fase de diagnóstico alargada: recolha de informação em campo incluída no âmbito.',
            ],
        ],

        // ── Multiple vacancies ───────────────────────────────────────────────

        [
            'id'         => 'multiple_vacancies',
            'services'   => ['recrutamento-seleccao'],
            'conditions' => [['field' => 'numero_vagas', 'operator' => 'gt', 'value' => 2]],
            'actions'    => [
                'set_score_dimension' => ['complexity' => 10],
                'add_argument'        => 'O processo envolve múltiplas vagas, o que permite uma abordagem de pesquisa consolidada com economia de escala e campanhas de marca empregadora focadas.',
                'add_tag'             => 'multi-vaga',
            ],
        ],

        // ── Replacement ──────────────────────────────────────────────────────

        [
            'id'         => 'replacement_process',
            'services'   => ['recrutamento-seleccao'],
            'conditions' => [['field' => 'motivo', 'operator' => 'eq', 'value' => 'substituicao']],
            'actions'    => [
                'add_argument'        => 'Tratando-se de substituição, a metodologia inclui análise do perfil cessante, ajuste de expectativas e gestão de transição cuidada.',
                'flag_risk'           => 'Processo de substituição: confirmar disponibilidade de informação sobre o titular anterior.',
            ],
        ],

    ],

    /*
     * Score dimension weights for final scoring.
     * Total = weighted average.
     */
    'score_weights' => [
        'urgency'          => 0.25,
        'complexity'       => 0.35,
        'hr_maturity'      => 0.20,
        'info_availability'=> 0.20,
    ],

    /*
     * Score dimension defaults (before rules fire).
     */
    'score_defaults' => [
        'urgency'          => 30,
        'complexity'       => 30,
        'hr_maturity'      => 60,
        'info_availability'=> 60,
    ],

    /*
     * Risk thresholds.
     */
    'risk_thresholds' => [
        'low'    => 40,
        'medium' => 65,
        'high'   => 80,
    ],
];
