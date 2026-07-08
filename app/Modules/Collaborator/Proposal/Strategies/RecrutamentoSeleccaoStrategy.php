<?php

namespace App\Modules\Collaborator\Proposal\Strategies;

use App\Modules\Collaborator\Proposal\Contracts\ServiceContentStrategy;

class RecrutamentoSeleccaoStrategy implements ServiceContentStrategy
{
    public function serviceNeed(string $serviceTitle): string
    {
        return config('proposal_services.recrutamento-seleccao.service_need')
            ?? 'A decisão não é apenas preencher uma vaga; é escolher a pessoa certa para proteger produtividade, cultura, continuidade e confiança interna.';
    }

    public function positioningStatement(string $serviceTitle): string
    {
        return config('proposal_services.recrutamento-seleccao.positioning_statement')
            ?? 'A BD não trata recrutamento como simples triagem de CVs. Estruturamos decisões de talento: clarificamos o perfil, avaliamos evidências e apoiamos a escolha de pessoas capazes de gerar desempenho no contexto real do cliente.';
    }

    public function bdSignatureExtras(): array
    {
        return config('proposal_services.recrutamento-seleccao.bd_signature_extras', [
            ['label' => 'Decisão protegida', 'text' => 'Cada candidato é analisado contra critérios definidos, evidências observáveis e aderência ao contexto da função.'],
        ]);
    }

    public function contextualSummary(
        string $clientName,
        string $sectorPhrase,
        string $challenge,
        string $serviceTitle,
    ): string {
        return "Após a informação preliminar partilhada, identificámos a necessidade de apoiar {$clientName}{$sectorPhrase} na identificação e selecção de um profissional alinhado ao desafio apresentado: {$challenge}. A intervenção procura reduzir o risco de uma contratação inadequada, acelerar a tomada de decisão e garantir que o perfil escolhido contribui para a liderança, produtividade e cultura da organização.";
    }
}
