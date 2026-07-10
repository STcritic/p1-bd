<?php

/*
 * Política comercial por serviço.
 * Altere aqui para manter preços, condições de garantia e categorias de despesa centralizadas.
 */

return [

    /*
     | Categorias de despesa por serviço.
     | Apresentadas no formulário como linhas individuais (descrição + montante).
     | '_default' aplica-se a todos os serviços sem entrada específica.
     */
    'expense_types' => [
        'recrutamento-seleccao' => [
            ['key' => 'assessments',  'label' => 'Testes e assessments'],
            ['key' => 'logistica',    'label' => 'Logística e deslocações'],
            ['key' => 'ferramentas',  'label' => 'Ferramentas e plataformas de recrutamento'],
            ['key' => 'outros',       'label' => 'Outros'],
        ],
        'formacao-desenvolvimento' => [
            ['key' => 'materiais',   'label' => 'Materiais e impressão'],
            ['key' => 'logistica',   'label' => 'Logística e sala'],
            ['key' => 'tecnologia',  'label' => 'Tecnologia e plataforma LMS'],
            ['key' => 'outros',      'label' => 'Outros'],
        ],
        'perfil-comportamental' => [
            ['key' => 'instrumentos', 'label' => 'Instrumentos / licenças de avaliação'],
            ['key' => 'logistica',    'label' => 'Logística e deslocações'],
            ['key' => 'outros',       'label' => 'Outros'],
        ],
        '_default' => [
            ['key' => 'logistica',   'label' => 'Logística'],
            ['key' => 'outros',      'label' => 'Outros'],
        ],
    ],

    /*
     | Política de honorários de recrutamento.
     | Modelo: percentagem sobre o salário anual bruto do candidato seleccionado.
     */
    'recrutamento-seleccao' => [
        'model'       => 'percentage_of_annual_salary',
        'model_label' => 'Fee calculado sobre o salário anual bruto do candidato seleccionado',
        'bands' => [
            [
                'label'          => 'Até MZN 1.000.000 / ano',
                'rate'           => 10,
                'guarantee_days' => 90,
            ],
            [
                'label'          => 'MZN 1.000.001 – 2.000.000 / ano',
                'rate'           => 12.5,
                'guarantee_days' => 90,
            ],
            [
                'label'          => 'Acima de MZN 2.000.000 / ano',
                'rate'           => 15,
                'guarantee_days' => 90,
            ],
            [
                'label'          => 'Executivo / Headhunting',
                'rate_min'       => 17.5,
                'rate_max'       => 25,
                'guarantee_days' => 180,
            ],
        ],
        'mass_note' => 'Recrutamento massivo ou temporário requer programa específico. Contacte-nos para proposta separada.',
        'guarantee' => [
            'free_replacement_days' => 90,
            'note'                  => 'Substituição gratuita se o candidato abandonar o posto nos primeiros 3 meses.',
            'credit_options'        => [
                ['within_days' => 22, 'credit_pct' => 80],
                ['within_days' => 44, 'credit_pct' => 65],
                ['within_days' => 66, 'credit_pct' => 50],
            ],
            'max_uses_per_role' => 3,
        ],
    ],

];
