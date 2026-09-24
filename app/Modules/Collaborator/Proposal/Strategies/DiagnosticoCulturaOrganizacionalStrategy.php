<?php

namespace App\Modules\Collaborator\Proposal\Strategies;

use App\Modules\Collaborator\Proposal\Contracts\ServiceContentStrategy;

class DiagnosticoCulturaOrganizacionalStrategy implements ServiceContentStrategy
{
    public function serviceNeed(string $serviceTitle): string
    {
        return config('proposal_services.diagnostico-cultura-organizacional.service_need')
            ?? 'A prioridade não é apenas medir clima; é compreender como valores, liderança, comunicação, rituais e práticas reais influenciam confiança, colaboração e execução.';
    }

    public function positioningStatement(string $serviceTitle): string
    {
        return config('proposal_services.diagnostico-cultura-organizacional.positioning_statement')
            ?? 'A BD diagnostica cultura a partir de evidências, padrões de comportamento e incoerências entre valores declarados e práticas reais, para apoiar decisões de liderança com clareza.';
    }

    public function bdSignatureExtras(): array
    {
        return config('proposal_services.diagnostico-cultura-organizacional.bd_signature_extras', [
            ['label' => 'Cultura traduzida em evidências', 'text' => 'Combinamos questionários, entrevistas, documentos e sinais do dia-a-dia para separar percepção, prática e prioridade de acção.'],
        ]);
    }

    public function contextualSummary(
        string $clientName,
        string $sectorPhrase,
        string $challenge,
        string $serviceTitle,
    ): string {
        return "Com base no diagnóstico e na informação partilhada, a Business Diversity entende que {$clientName}{$sectorPhrase} precisa compreender os padrões culturais que estão a influenciar confiança, colaboração, liderança e execução: {$challenge}. A proposta foi estruturada para transformar percepções dispersas em evidências, prioridades e acções de cultura organizacional acompanháveis pela liderança.";
    }
}
