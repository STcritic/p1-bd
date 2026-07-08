<?php

namespace App\Modules\Collaborator\Proposal\Strategies;

use App\Modules\Collaborator\Proposal\Contracts\ServiceContentStrategy;

class GestaoDesempenhoStrategy implements ServiceContentStrategy
{
    public function serviceNeed(string $serviceTitle): string
    {
        return config('proposal_services.gestao-desempenho.service_need')
            ?? 'A prioridade não é apenas avaliar pessoas; é criar um ciclo que alinhe objectivos, feedback, desenvolvimento e decisões de gestão.';
    }

    public function positioningStatement(string $serviceTitle): string
    {
        return config('proposal_services.gestao-desempenho.positioning_statement')
            ?? 'A BD não desenha apenas formulários de avaliação. Construímos sistemas de desempenho que ajudam líderes e colaboradores a conversar melhor, medir melhor e decidir com mais justiça.';
    }

    public function bdSignatureExtras(): array
    {
        return config('proposal_services.gestao-desempenho.bd_signature_extras', [
            ['label' => 'Conversas que movem desempenho', 'text' => 'O sistema é desenhado para melhorar objectivos, feedback, calibração e planos de desenvolvimento.'],
        ]);
    }

    public function contextualSummary(
        string $clientName,
        string $sectorPhrase,
        string $challenge,
        string $serviceTitle,
    ): string {
        return "Com base na informação partilhada, a Business Diversity entende que {$clientName}{$sectorPhrase} procura um sistema de {$serviceTitle} capaz de responder ao seguinte contexto: {$challenge}. A proposta foi estruturada para transformar esta necessidade num ciclo de desempenho claro, justo e aplicável pela liderança.";
    }
}
