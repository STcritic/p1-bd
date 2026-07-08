<?php

namespace App\Modules\Collaborator\Proposal\Factories;

use App\Modules\Collaborator\Proposal\Contracts\ServiceContentStrategy;
use App\Modules\Collaborator\Proposal\Strategies\DefaultContentStrategy;
use App\Modules\Collaborator\Proposal\Strategies\DigitalizacaoRhStrategy;
use App\Modules\Collaborator\Proposal\Strategies\GestaoDesempenhoStrategy;
use App\Modules\Collaborator\Proposal\Strategies\RecrutamentoSeleccaoStrategy;

class ContentStrategyFactory
{
    public function make(string $slug): ServiceContentStrategy
    {
        return match ($slug) {
            'recrutamento-seleccao'          => new RecrutamentoSeleccaoStrategy(),
            'gestao-desempenho'              => new GestaoDesempenhoStrategy(),
            'digitalizacao-rh-endomarketing' => new DigitalizacaoRhStrategy(),
            default                          => new DefaultContentStrategy(),
        };
    }
}
