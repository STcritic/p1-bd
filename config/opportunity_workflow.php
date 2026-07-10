<?php

/**
 * Consulting Proposal Workflow Engine
 * State machine configuration — all transitions and step guidance live here.
 * No business logic in config; only topology and labels.
 */
return [

    'states' => [
        'draft'               => ['label' => 'Rascunho',              'label_en' => 'Draft',               'color' => 'gray',    'icon' => '○'],
        'qualification'       => ['label' => 'Qualificação',          'label_en' => 'Qualification',       'color' => 'blue',    'icon' => '◈'],
        'diagnosis'           => ['label' => 'Diagnóstico',           'label_en' => 'Diagnosis',           'color' => 'indigo',  'icon' => '◉'],
        'awaiting_client'     => ['label' => 'Aguardando Cliente',    'label_en' => 'Awaiting Client',     'color' => 'amber',   'icon' => '◌'],
        'diagnosis_received'  => ['label' => 'Diagnóstico Recebido',  'label_en' => 'Diagnosis Received',  'color' => 'teal',    'icon' => '◎'],
        'building'            => ['label' => 'Em Construção',         'label_en' => 'Building',            'color' => 'violet',  'icon' => '◈'],
        'review'              => ['label' => 'Revisão',               'label_en' => 'Review',              'color' => 'orange',  'icon' => '◐'],
        'ready_for_approval'  => ['label' => 'Pronto p/ Aprovação',   'label_en' => 'Ready for Approval',  'color' => 'blue',    'icon' => '◑'],
        'approved'            => ['label' => 'Aprovado',              'label_en' => 'Approved',            'color' => 'green',   'icon' => '●'],
        'sent'                => ['label' => 'Enviado',               'label_en' => 'Sent',                'color' => 'cyan',    'icon' => '→'],
        'negotiation'         => ['label' => 'Negociação',            'label_en' => 'Negotiation',         'color' => 'amber',   'icon' => '⇄'],
        'awarded'             => ['label' => 'Adjudicado',            'label_en' => 'Awarded',             'color' => 'emerald', 'icon' => '✓'],
        'closed'              => ['label' => 'Encerrado',             'label_en' => 'Closed',              'color' => 'slate',   'icon' => '✕'],
    ],

    /*
     * allowed_from => [allowed_to, ...]
     * The WorkflowEngine enforces these; any other transition throws.
     */
    'transitions' => [
        'draft'              => ['qualification', 'closed'],
        'qualification'      => ['diagnosis', 'closed'],
        'diagnosis'          => ['awaiting_client', 'building', 'closed'],
        'awaiting_client'    => ['diagnosis_received', 'closed'],
        'diagnosis_received' => ['building', 'closed'],
        'building'           => ['review', 'closed'],
        'review'             => ['ready_for_approval', 'building'],
        'ready_for_approval' => ['approved', 'review'],
        'approved'           => ['sent'],
        'sent'               => ['negotiation', 'awarded', 'closed'],
        'negotiation'        => ['awarded', 'closed'],
        'awarded'            => ['closed'],
        'closed'             => [],
    ],

    /*
     * Per-state guidance shown to the collaborator.
     * action  → CTA button label
     * guide   → explanatory sentence
     * minutes → estimated time to complete this step
     */
    'steps' => [
        'draft' => [
            'action'     => 'Qualificar oportunidade',
            'action_en'  => 'Qualify opportunity',
            'guide'      => 'Avalie se a oportunidade é viável e alinhada com a capacidade da BD. Verifique cliente, serviço e timing.',
            'guide_en'   => 'Assess whether the opportunity is viable and aligned with BD\'s capacity. Check client, service and timing.',
            'minutes'    => 5,
            'next'       => 'qualification',
        ],
        'qualification' => [
            'action'     => 'Iniciar diagnóstico',
            'action_en'  => 'Start diagnosis',
            'guide'      => 'Prepare a pré-proposta executiva e envie o questionário de diagnóstico ao cliente para recolher informação.',
            'guide_en'   => 'Prepare the executive pre-proposal and send the diagnostic questionnaire to the client to gather information.',
            'minutes'    => 15,
            'next'       => 'diagnosis',
        ],
        'diagnosis' => [
            'action'     => 'Enviar link ao cliente',
            'action_en'  => 'Send link to client',
            'guide'      => 'Envie o link seguro de diagnóstico ao cliente. O cliente responde no seu próprio ritmo, sem necessidade de login.',
            'guide_en'   => 'Send the secure diagnostic link to the client. The client responds at their own pace, no login required.',
            'minutes'    => 3,
            'next'       => 'awaiting_client',
        ],
        'awaiting_client' => [
            'action'     => 'Aguardar resposta',
            'action_en'  => 'Awaiting response',
            'guide'      => 'O cliente recebeu o link. Acompanhe via timeline. Receberá uma notificação quando o diagnóstico for submetido.',
            'guide_en'   => 'The client has received the link. Monitor via the timeline. You will be notified when the diagnostic is submitted.',
            'minutes'    => 0,
            'next'       => 'diagnosis_received',
        ],
        'diagnosis_received' => [
            'action'     => 'Analisar e construir proposta',
            'action_en'  => 'Analyse and build proposal',
            'guide'      => 'O cliente respondeu ao diagnóstico. Analise o contexto consolidado e inicie a construção da proposta técnica.',
            'guide_en'   => 'The client has answered the diagnostic. Analyse the consolidated context and start building the technical proposal.',
            'minutes'    => 20,
            'next'       => 'building',
        ],
        'building' => [
            'action'     => 'Submeter para revisão',
            'action_en'  => 'Submit for review',
            'guide'      => 'A proposta está a ser construída com base no contexto do cliente. Reveja e ajuste antes de submeter.',
            'guide_en'   => 'The proposal is being built based on the client\'s context. Review and adjust before submitting.',
            'minutes'    => 30,
            'next'       => 'review',
        ],
        'review' => [
            'action'     => 'Aprovar proposta',
            'action_en'  => 'Approve proposal',
            'guide'      => 'Reveja a proposta com atenção: conteúdo, valores, timelines e termos. Submeta para aprovação final.',
            'guide_en'   => 'Review the proposal carefully: content, values, timelines and terms. Submit for final approval.',
            'minutes'    => 15,
            'next'       => 'ready_for_approval',
        ],
        'ready_for_approval' => [
            'action'     => 'Enviar ao cliente',
            'action_en'  => 'Send to client',
            'guide'      => 'Proposta aprovada. Gere o PDF final e envie ao cliente. Registe o envio na oportunidade.',
            'guide_en'   => 'Proposal approved. Generate the final PDF and send it to the client. Record the delivery in the opportunity.',
            'minutes'    => 5,
            'next'       => 'approved',
        ],
        'approved' => [
            'action'     => 'Registar envio',
            'action_en'  => 'Record delivery',
            'guide'      => 'Registe a data e método de envio ao cliente. Acompanhe o prazo de validade da proposta.',
            'guide_en'   => 'Record the date and method of delivery to the client. Monitor the proposal validity deadline.',
            'minutes'    => 2,
            'next'       => 'sent',
        ],
        'sent' => [
            'action'     => 'Actualizar negociação',
            'action_en'  => 'Update negotiation',
            'guide'      => 'Acompanhe a resposta do cliente. Registe feedback, ajustes e qualquer comunicação relevante.',
            'guide_en'   => 'Monitor the client\'s response. Record feedback, adjustments and any relevant communication.',
            'minutes'    => 0,
            'next'       => 'negotiation',
        ],
        'negotiation' => [
            'action'     => 'Confirmar adjudicação',
            'action_en'  => 'Confirm award',
            'guide'      => 'Confirme os termos finais negociados e registe a adjudicação. Prepare a transição para execução.',
            'guide_en'   => 'Confirm the final negotiated terms and record the award. Prepare the transition to execution.',
            'minutes'    => 10,
            'next'       => 'awarded',
        ],
        'awarded' => [
            'action'     => 'Encerrar oportunidade',
            'action_en'  => 'Close opportunity',
            'guide'      => 'Oportunidade adjudicada com sucesso. Encerre e transfira para a equipa de execução.',
            'guide_en'   => 'Opportunity successfully awarded. Close and hand over to the execution team.',
            'minutes'    => 5,
            'next'       => 'closed',
        ],
        'closed' => [
            'action'     => '',
            'action_en'  => '',
            'guide'      => 'Esta oportunidade está encerrada.',
            'guide_en'   => 'This opportunity is closed.',
            'minutes'    => 0,
            'next'       => null,
        ],
    ],

    /*
     * Progress percentage per state — drives the dashboard progress bar.
     */
    'progress' => [
        'draft'              => 5,
        'qualification'      => 12,
        'diagnosis'          => 22,
        'awaiting_client'    => 35,
        'diagnosis_received' => 48,
        'building'           => 60,
        'review'             => 72,
        'ready_for_approval' => 82,
        'approved'           => 87,
        'sent'               => 90,
        'negotiation'        => 94,
        'awarded'            => 98,
        'closed'             => 100,
    ],

    /*
     * Terminal states — no further transitions allowed.
     */
    'terminal' => ['closed', 'awarded'],

    /*
     * States that require a diagnostic session to be completed before advancing.
     */
    'requires_diagnosis' => ['building'],
];
