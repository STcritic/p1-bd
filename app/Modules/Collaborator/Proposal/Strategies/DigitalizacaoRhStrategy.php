<?php

namespace App\Modules\Collaborator\Proposal\Strategies;

use App\Modules\Collaborator\Proposal\Contracts\ServiceContentStrategy;

class DigitalizacaoRhStrategy implements ServiceContentStrategy
{
    public function serviceNeed(string $serviceTitle): string
    {
        return config('proposal_services.digitalizacao-rh-endomarketing.service_need')
            ?? 'A prioridade não é apenas digitalizar processos; é melhorar a experiência do colaborador e aumentar a adopção real das soluções.';
    }

    public function positioningStatement(string $serviceTitle): string
    {
        return config('proposal_services.digitalizacao-rh-endomarketing.positioning_statement')
            ?? 'A BD não digitaliza processos por moda. Simplificamos jornadas, comunicamos a mudança e ajudamos equipas a adoptar ferramentas que tornam RH mais ágil e próximo das pessoas.';
    }

    public function bdSignatureExtras(): array
    {
        return config('proposal_services.digitalizacao-rh-endomarketing.bd_signature_extras', [
            ['label' => 'Tecnologia que as pessoas adoptam', 'text' => 'Digitalizamos processos com foco em experiência do colaborador, comunicação interna e adopção real.'],
        ]);
    }

    public function contextualSummary(
        string $clientName,
        string $sectorPhrase,
        string $challenge,
        string $serviceTitle,
    ): string {
        return "Com base na informação partilhada, a Business Diversity entende que {$clientName}{$sectorPhrase} procura simplificar e digitalizar processos de RH capazes de responder ao seguinte contexto: {$challenge}. A proposta foi estruturada para transformar esta necessidade em jornadas mais simples, adopção real pelas equipas e melhor experiência do colaborador.";
    }
}
