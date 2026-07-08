<?php

/*
 * Configuração central do módulo de propostas.
 * Contém defaults, mapeamentos e valores que antes estavam hardcoded no controller.
 */

return [

    'defaults' => [
        'reference_prefix' => 'BD-PROP-',
        'validity_days'    => 15,
        'currency'         => 'MZN',
        'vat_rate'         => 16,
        'prepared_by'      => 'Business Diversity',
        'prepared_role'    => 'Consultoria Empresarial',

        'timeline'     => 'Cronograma a confirmar após reunião de alinhamento e validação do âmbito final.',
        'assumptions'  => "Acesso atempado à informação necessária\nDisponibilidade dos interlocutores-chave\nValidação de decisões críticas pela liderança do cliente",
        'out_of_scope' => 'Actividades não descritas no âmbito técnico serão tratadas como pedidos adicionais.',
        'payment_terms'=> '50% na adjudicação e 50% mediante entrega dos principais produtos, salvo acordo específico.',
    ],

    'cover_images' => [
        'recrutamento-seleccao'           => 'assets/images/service_03.jpg',
        'gestao-desempenho'               => 'assets/images/pexels-pixabay-265087.jpg',
        'carreira-sucessao'               => 'assets/images/hero-consulting-team.png',
        'avaliacao-classificacao-cargos'  => 'assets/images/service_02.jpg',
        'perfil-comportamental'           => 'assets/images/service_00.jpg',
        'politicas-procedimentos'         => 'assets/images/service_02.jpg',
        'remuneracao-beneficios'          => 'assets/images/pexels-pixabay-265087.jpg',
        'formacao-desenvolvimento'        => 'assets/images/service_01.jpg',
        'assessoria-outsourcing-rh'       => 'assets/images/hero-consulting-team.png',
        'digitalizacao-rh-endomarketing'  => 'assets/images/pexels-pixabay-265087.jpg',
        '_default'                        => 'assets/images/hero-consulting-team.png',
    ],

    /*
     * Mapeia chaves de perfil para chaves de membro da equipa em proposal_identity.team_members.
     * Perfis não listados aqui não produzem um team member card.
     */
    'profile_team_map' => [
        'senior_hr'       => 'sandra',
        'talent'          => 'sandra',
        'learning'        => 'sandra',
        'reward'          => 'sandra',
        'legal_hr'        => 'sandra',
        'project_pm'      => 'sandra',
        'hr_digital'      => 'shelzer',
        'people_analytics'=> 'shelzer',
    ],

];
