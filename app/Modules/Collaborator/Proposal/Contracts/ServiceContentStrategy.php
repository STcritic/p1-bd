<?php

namespace App\Modules\Collaborator\Proposal\Contracts;

interface ServiceContentStrategy
{
    public function serviceNeed(string $serviceTitle): string;

    public function positioningStatement(string $serviceTitle): string;

    public function bdSignatureExtras(): array;

    public function contextualSummary(
        string $clientName,
        string $sectorPhrase,
        string $challenge,
        string $serviceTitle,
    ): string;
}
