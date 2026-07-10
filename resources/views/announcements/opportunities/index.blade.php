@extends('announcements.layout')
@php $en = ($collabLang ?? 'pt') === 'en'; @endphp
@section('title', $en ? 'Opportunities' : 'Oportunidades')

@section('content')
<main class="announcement-dashboard">
@include('announcements.partials.nav', ['active' => 'oportunidades', 'pageTitle' => $en ? 'Opportunities' : 'Oportunidades'])

<div class="announcement-admin-shell opp-page">

    <div class="opp-page-header">
        <div>
            <p class="opp-page-subtitle">{{ $en ? 'Consulting pipeline: from first contact to award' : 'Pipeline consultivo: do primeiro contacto à adjudicação' }}</p>
        </div>
        <a href="{{ route('collaborator.opportunities.create') }}" class="btn-primary">
            {{ $en ? '+ New opportunity' : '+ Nova oportunidade' }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Status filters --}}
    <nav class="opp-status-nav">
        @foreach($states as $key => $state)
            <a href="#" class="opp-status-pill opp-color-{{ $state['color'] }}"
               data-filter="{{ $key }}">
                {{ $en ? ($state['label_en'] ?? $state['label']) : $state['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- Opportunities list --}}
    @forelse($opportunities as $opp)
        @php $progress = config("opportunity_workflow.progress.{$opp->status}", 0); @endphp
        <article class="opp-card" data-status="{{ $opp->status }}">
            <div class="opp-card-main">
                <div class="opp-card-identity">
                    <span class="opp-ref">{{ $opp->reference }}</span>
                    <h3>{{ $opp->client_name }}</h3>
                    <p class="opp-service">{{ $opp->service_title }}</p>
                </div>

                <div class="opp-card-progress">
                    <div class="opp-progress-bar">
                        <div class="opp-progress-fill" style="width:{{ $progress }}%"></div>
                    </div>
                    <span class="opp-progress-pct">{{ $progress }}%</span>
                </div>

                <div class="opp-card-status">
                    <span class="opp-status-badge opp-color-{{ config("opportunity_workflow.states.{$opp->status}.color", 'gray') }}">
                        {{ $opp->statusLabel($en ? 'en' : 'pt') }}
                    </span>
                    @if($opp->latestSession?->isOpen())
                        <span class="opp-badge-secondary">{{ $en ? 'Portal active' : 'Portal activo' }}</span>
                    @endif
                </div>

                <div class="opp-card-meta">
                    <time>{{ $opp->updated_at->format('d/m/Y') }}</time>
                    @if($opp->tags)
                        @foreach(array_slice($opp->tags, 0, 3) as $tag)
                            <span class="opp-tag">{{ $tag }}</span>
                        @endforeach
                    @endif
                </div>

                <a href="{{ route('collaborator.opportunities.show', $opp) }}" class="opp-card-link">
                    {{ $en ? 'View →' : 'Ver →' }}
                </a>
            </div>

            {{-- Step hint --}}
            @php $step = config("opportunity_workflow.steps.{$opp->status}", []); @endphp
            @if($step['action'] ?? null)
                <div class="opp-card-next-step">
                    <span>{{ $en ? 'Next step:' : 'Próximo passo:' }}</span>
                    <strong>{{ $en ? ($step['action_en'] ?? $step['action']) : $step['action'] }}</strong>
                    @if($step['minutes'] ?? 0)
                        <em>≈ {{ $step['minutes'] }} min</em>
                    @endif
                </div>
            @endif
        </article>
    @empty
        <div class="opp-empty">
            <p>{{ $en ? 'No opportunities yet.' : 'Ainda não existem oportunidades.' }}</p>
            <a href="{{ route('collaborator.opportunities.create') }}" class="btn-primary">
                {{ $en ? 'Create first opportunity' : 'Criar primeira oportunidade' }}
            </a>
        </div>
    @endforelse

    {{ $opportunities->links() }}
</div>{{-- .opp-page --}}
</main>
@endsection
