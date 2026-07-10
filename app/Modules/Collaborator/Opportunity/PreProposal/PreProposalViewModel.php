<?php

namespace App\Modules\Collaborator\Opportunity\PreProposal;

/**
 * Thin wrapper for Blade — provides formatting helpers.
 */
final class PreProposalViewModel
{
    public function __construct(
        private readonly PreProposalData $data,
        private readonly array           $identity,
    ) {}

    public function __get(string $name): mixed { return $this->data->$name; }

    public function company(): array   { return $this->identity['company']      ?? []; }
    public function contact(): array   { return $this->identity['contact']      ?? []; }
    public function teamMembers(): array { return $this->identity['team_members'] ?? []; }

    public function recipientLine(): string
    {
        if ($this->data->clientContact) {
            $pos = $this->data->clientPosition ? ", {$this->data->clientPosition}" : '';
            return "Para: {$this->data->clientContact}{$pos}";
        }
        return "Para: Direcção de {$this->data->clientName}";
    }

    public function pdfReference(): string
    {
        return $this->data->reference;
    }
}
