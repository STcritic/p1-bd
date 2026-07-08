<?php

namespace App\Modules\Collaborator\Proposal\Strategies;

use App\Modules\Collaborator\Proposal\Contracts\ServiceContentStrategy;

class DefaultContentStrategy implements ServiceContentStrategy
{
    public function serviceNeed(string $serviceTitle): string
    {
        return config('proposal_services.generic.service_need')
            ?? "A prioridade é transformar {$serviceTitle} numa decisão prática, mensurável e aplicável ao contexto real da organização.";
    }

    public function positioningStatement(string $serviceTitle): string
    {
        return config('proposal_services.generic.positioning_statement')
            ?? "A BD não entrega apenas documentos de {$serviceTitle}. Estruturamos decisões de pessoas, traduzimos complexidade em instrumentos práticos e acompanhamos a aplicação até que a solução faça sentido no dia-a-dia do cliente.";
    }

    public function bdSignatureExtras(): array
    {
        return [];
    }

    public function contextualSummary(
        string $clientName,
        string $sectorPhrase,
        string $challenge,
        string $serviceTitle,
    ): string {
        return "Com base na informação partilhada, a Business Diversity entende que {$clientName}{$sectorPhrase} procura uma solução de {$serviceTitle} capaz de responder ao seguinte contexto: {$challenge}. A proposta foi estruturada para transformar esta necessidade em decisões práticas, instrumentos utilizáveis e resultados acompanháveis pela liderança.";
    }
}
