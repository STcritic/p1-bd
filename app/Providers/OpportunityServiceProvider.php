<?php

namespace App\Providers;

use App\Modules\Collaborator\Opportunity\Actions\CreateOpportunity;
use App\Modules\Collaborator\Opportunity\Actions\SendDiagnosticSession;
use App\Modules\Collaborator\Opportunity\Actions\SubmitDiagnostic;
use App\Modules\Collaborator\Opportunity\Builders\OpportunityProposalBuilder;
use App\Modules\Collaborator\Opportunity\Context\ContextEngine;
use App\Modules\Collaborator\Opportunity\Decision\DecisionEngine;
use App\Modules\Collaborator\Opportunity\Ocr\OcrService;
use App\Modules\Collaborator\Opportunity\PreProposal\PreProposalBuilder;
use App\Modules\Collaborator\Opportunity\Workflow\WorkflowEngine;
use App\Modules\Collaborator\Proposal\Builders\ProposalBuilder;
use App\Modules\Collaborator\Proposal\Factories\ContentStrategyFactory;
use App\Modules\Collaborator\Proposal\Services\ContentGeneratorService;
use Illuminate\Support\ServiceProvider;

class OpportunityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WorkflowEngine::class, function (): WorkflowEngine {
            return new WorkflowEngine(config('opportunity_workflow'));
        });

        $this->app->singleton(ContextEngine::class,  fn () => new ContextEngine());
        $this->app->singleton(DecisionEngine::class, fn () => new DecisionEngine());
        $this->app->singleton(OcrService::class,     fn () => new OcrService());

        $this->app->bind(CreateOpportunity::class,    fn () => new CreateOpportunity());
        $this->app->bind(SendDiagnosticSession::class, fn ($app) => new SendDiagnosticSession(
            $app->make(WorkflowEngine::class)
        ));
        $this->app->bind(SubmitDiagnostic::class, fn ($app) => new SubmitDiagnostic(
            $app->make(WorkflowEngine::class),
            $app->make(ContextEngine::class),
            $app->make(DecisionEngine::class),
            $app->make(OcrService::class),
        ));

        $this->app->bind(PreProposalBuilder::class, fn ($app) => new PreProposalBuilder(
            $app->make(ContentGeneratorService::class),
            $app->make(ContentStrategyFactory::class),
        ));

        $this->app->bind(OpportunityProposalBuilder::class, fn ($app) => new OpportunityProposalBuilder(
            $app->make(ProposalBuilder::class),
            $app->make(ContentGeneratorService::class),
        ));
    }
}
