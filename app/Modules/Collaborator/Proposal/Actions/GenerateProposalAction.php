<?php

namespace App\Modules\Collaborator\Proposal\Actions;

use App\Modules\Collaborator\Proposal\Builders\ProposalBuilder;
use App\Modules\Collaborator\Proposal\DTO\ProposalData;

class GenerateProposalAction
{
    public function __construct(
        private readonly ProposalBuilder $builder,
    ) {}

    public function execute(array $validated, array $service): ProposalData
    {
        return $this->builder->build($validated, $service);
    }
}
