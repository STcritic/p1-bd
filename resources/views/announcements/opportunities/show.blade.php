@extends('announcements.layout')
@php $en = ($collabLang ?? 'pt') === 'en'; @endphp
@section('title', $vm->ref() . ': ' . $vm->clientName())

@section('content')
<main class="announcement-dashboard">
@include('announcements.partials.nav', ['active' => 'oportunidades', 'pageTitle' => $vm->clientName() . ', ' . $vm->serviceTitle()])

<div class="announcement-admin-shell">
<div class="opp-show">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <header class="opp-show-header">
        <a href="{{ route('collaborator.opportunities.index') }}" class="opp-back">
            ← {{ $en ? 'Opportunities' : 'Oportunidades' }}
        </a>
        <div class="opp-show-identity">
            <span class="opp-ref">{{ $vm->ref() }}</span>
            <h1>{{ $vm->clientName() }}</h1>
            <p>{{ $vm->serviceTitle() }}</p>
        </div>
        <div class="opp-show-status">
            <span class="opp-status-badge opp-status-badge--lg opp-color-{{ $vm->statusColor() }}">
                {{ $vm->statusLabel($en ? 'en' : 'pt') }}
            </span>
            @if($vm->tags())
                <div class="opp-tags">
                    @foreach($vm->tags() as $tag)
                        <span class="opp-tag">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </header>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('portal_url'))
        <div class="opp-portal-banner">
            <strong>{{ $en ? 'Diagnostic link generated:' : 'Link de diagnóstico gerado:' }}</strong>
            <code>{{ session('portal_url') }}</code>
            <button onclick="navigator.clipboard.writeText('{{ session('portal_url') }}')">
                {{ $en ? 'Copy' : 'Copiar' }}
            </button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    {{-- ── Dashboard progress ───────────────────────────────────────────────── --}}
    <div class="opp-dashboard">

        <div class="opp-dashboard-progress">
            <div class="opp-progress-ring" data-pct="{{ $vm->progressPct() }}">
                <svg viewBox="0 0 56 56">
                    <circle cx="28" cy="28" r="24" fill="none" stroke="#e5eaf0" stroke-width="4"/>
                    <circle cx="28" cy="28" r="24" fill="none" stroke="var(--blue)" stroke-width="4"
                        stroke-dasharray="{{ round(2 * M_PI * 24, 2) }}"
                        stroke-dashoffset="{{ round(2 * M_PI * 24 * (1 - $vm->progressPct() / 100), 2) }}"
                        transform="rotate(-90 28 28)"/>
                </svg>
                <span>{{ $vm->progressPct() }}%</span>
            </div>
            <div class="opp-dashboard-progress-labels">
                <strong>{{ $en ? 'Completed' : 'Concluído' }}</strong>
                <span>{{ $en ? 'Status:' : 'Estado:' }} <em>{{ $vm->statusLabel($en ? 'en' : 'pt') }}</em></span>
            </div>
        </div>

        @php $step = $vm->currentStep($en ? 'en' : 'pt'); @endphp
        @if($step['action'] ?? null)
            <div class="opp-dashboard-step">
                <div class="opp-step-card">
                    <span class="opp-step-label">{{ $en ? 'Current stage' : 'Etapa actual' }}</span>
                    <strong>{{ $vm->statusLabel($en ? 'en' : 'pt') }}</strong>
                </div>
                <div class="opp-step-arrow">→</div>
                <div class="opp-step-card opp-step-card--next">
                    <span class="opp-step-label">{{ $en ? 'Next action' : 'Próxima acção' }}</span>
                    <strong>{{ $step['action'] }}</strong>
                    @if($step['minutes'] ?? 0)
                        <em>≈ {{ $step['minutes'] }} min</em>
                    @endif
                </div>
            </div>
            <p class="opp-step-guide">{{ $step['guide'] }}</p>
        @endif

        @if($vm->hasScore())
            <div class="opp-score-panel">
                <div class="opp-score-item">
                    <strong>{{ $vm->totalScore() }}%</strong>
                    <span>{{ $en ? 'Complexity score' : 'Score de complexidade' }}</span>
                </div>
                <div class="opp-score-item opp-score-risk--{{ $vm->riskLevel() }}">
                    <strong>{{ ucfirst($vm->riskLevel()) }}</strong>
                    <span>{{ $en ? 'Risk' : 'Risco' }}</span>
                </div>
                @foreach($vm->scoreDimensions() as $dim => $val)
                    @if(!str_starts_with($dim, '_'))
                        <div class="opp-score-item">
                            <strong>{{ $val }}%</strong>
                            <span>{{ ucfirst(str_replace('_', ' ', $dim)) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Two-column layout ───────────────────────────────────────────────── --}}
    <div class="opp-show-grid">

        {{-- LEFT: actions + context --}}
        <div class="opp-show-left">

            {{-- Workflow transition --}}
            @if($vm->transitionOptions($en ? 'en' : 'pt'))
                <section class="opp-panel">
                    <h2>{{ $en ? 'Advance workflow' : 'Avançar workflow' }}</h2>
                    @foreach($vm->transitionOptions($en ? 'en' : 'pt') as $opt)
                        <form action="{{ route('collaborator.opportunities.transition', $vm->id()) }}" method="POST">
                            @csrf
                            <input type="hidden" name="to_status" value="{{ $opt['state'] }}">
                            <div class="opp-field">
                                <label>{{ $en ? 'Note (optional)' : 'Nota (opcional)' }}</label>
                                <input type="text" name="description"
                                       placeholder="{{ $en ? 'Reason or context for this transition' : 'Motivo ou contexto desta transição' }}"
                                       maxlength="1000">
                            </div>
                            <button type="submit" class="btn-primary btn-block">
                                → {{ $opt['label'] }}
                            </button>
                        </form>
                    @endforeach
                </section>
            @endif

            {{-- Send diagnostic --}}
            @if(in_array($vm->status, ['qualification', 'diagnosis', 'awaiting_client']))
                <section class="opp-panel">
                    <h2>{{ $en ? 'Diagnostic Portal' : 'Portal de Diagnóstico' }}</h2>
                    @if($vm->latestPortalUrl())
                        <p class="opp-portal-active">
                            {{ $en ? 'Portal active, expires' : 'Portal activo, expira' }} {{ $vm->latestSessionExpiry() }}
                        </p>
                        <input type="text" class="opp-url-copy" value="{{ $vm->latestPortalUrl() }}" readonly
                               onclick="this.select(); document.execCommand('copy')">
                        <small>{{ $en ? 'Click to copy. Send to the client by e-mail or WhatsApp.' : 'Clique para copiar. Envie ao cliente por e-mail ou WhatsApp.' }}</small>
                    @endif

                    <form action="{{ route('collaborator.opportunities.send-diagnostic', $vm->id()) }}" method="POST">
                        @csrf
                        <div class="opp-field">
                            <label>{{ $en ? 'Link validity (days)' : 'Validade do link (dias)' }}</label>
                            <input type="number" name="days_valid" value="14" min="1" max="60">
                        </div>
                        <button type="submit" class="btn-secondary btn-block">
                            {{ $vm->latestPortalUrl()
                                ? ($en ? 'Generate new link' : 'Gerar novo link')
                                : ($en ? 'Generate diagnostic link' : 'Gerar link de diagnóstico') }}
                        </button>
                    </form>
                </section>
            @endif

            {{-- Context summary --}}
            @if($vm->hasContext())
                <section class="opp-panel">
                    <h2>{{ $en ? 'Consolidated context' : 'Contexto consolidado' }}</h2>
                    @php $ctx = $vm->id() > 0 ? ($vm->context_snapshot ?? []) : []; @endphp
                    @foreach($ctx as $key => $value)
                        @if(!str_starts_with($key, '_') && $value)
                            <div class="opp-ctx-row">
                                <span>{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                <strong>{{ is_array($value) ? implode(', ', $value) : $value }}</strong>
                            </div>
                        @endif
                    @endforeach

                    <form action="{{ route('collaborator.opportunities.refresh-context', $vm->id()) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn-ghost btn-sm">
                            ↺ {{ $en ? 'Refresh context' : 'Actualizar contexto' }}
                        </button>
                    </form>
                </section>
            @endif

            {{-- Risk flags --}}
            @if($vm->riskFlags())
                <section class="opp-panel opp-panel--warning">
                    <h2>{{ $en ? 'Risk factors' : 'Factores de risco' }}</h2>
                    <ul>
                        @foreach($vm->riskFlags() as $risk)
                            <li>{{ $risk }}</li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- Decision arguments --}}
            @if($vm->decisionArguments())
                <section class="opp-panel">
                    <h2>{{ $en ? 'Generated arguments' : 'Argumentos gerados' }}</h2>
                    <p class="opp-panel-hint">{{ $en ? 'The Proposal Builder will use these arguments in the proposal.' : 'O Proposal Builder utilizará estes argumentos na proposta.' }}</p>
                    @foreach($vm->decisionArguments() as $arg)
                        <blockquote class="opp-argument">{{ $arg }}</blockquote>
                    @endforeach
                </section>
            @endif

            {{-- Pre-proposal (available from qualification onwards) --}}
            @if(!in_array($vm->status, ['draft', 'closed']))
                <section class="opp-panel">
                    <h2>{{ $en ? 'Executive Pre-Proposal' : 'Pré-Proposta Executiva' }}</h2>
                    <p class="opp-panel-hint">{{ $en ? '4-page document, no price. To generate interest and request the diagnostic.' : 'Documento de 4 páginas, sem preço. Para despertar interesse e solicitar o diagnóstico.' }}</p>
                    <div class="opp-pre-proposal-langs">
                        <a href="{{ route('collaborator.opportunities.pre-proposal', $vm->id()) }}?lang=pt"
                           target="_blank" class="btn-secondary">
                            PT →
                        </a>
                        <a href="{{ route('collaborator.opportunities.pre-proposal', $vm->id()) }}?lang=en"
                           target="_blank" class="btn-secondary">
                            EN →
                        </a>
                    </div>
                </section>
            @endif

            {{-- Full proposal from context (available once diagnosis received) --}}
            @if(in_array($vm->status, ['building', 'review', 'ready_for_approval', 'approved', 'sent', 'negotiation']))
                <section class="opp-panel opp-panel--action">
                    <h2>{{ $en ? 'Technical and Financial Proposal' : 'Proposta Técnica e Financeira' }}</h2>
                    <p class="opp-panel-hint">{{ $en ? 'Context is consolidated. The proposal is built from the client\'s responses and saved automatically.' : 'O contexto está consolidado. A proposta é construída a partir das respostas do cliente e guardada automaticamente.' }}</p>

                    @php $suggestedFee = $vm->suggestedFee(); @endphp

                    <form action="{{ route('collaborator.opportunities.generate-proposal', $vm->id()) }}" method="GET" target="_blank">
                        <input type="hidden" name="lang" value="{{ $collabLang ?? 'pt' }}">
                        <div class="opp-field-row">
                            <div class="opp-field">
                                <label>{{ $en ? 'BD fee (MZN)' : 'Honorário BD (MZN)' }}
                                    @if($suggestedFee)
                                        <span class="opp-field-hint">Auto: {{ number_format($suggestedFee, 0, ',', ' ') }}</span>
                                    @endif
                                </label>
                                <input type="number" name="fee"
                                       value="{{ $suggestedFee ?? '' }}"
                                       placeholder="{{ $en ? 'E.g. 350 000' : 'Ex: 350 000' }}"
                                       min="0" step="1000" required>
                                @if($vm->suggestedFeeExplanation())
                                    <small class="opp-fee-formula">{{ $vm->suggestedFeeExplanation() }}</small>
                                @endif
                            </div>
                            <div class="opp-field">
                                <label>{{ $en ? 'Additional expenses (MZN)' : 'Despesas adicionais (MZN)' }}</label>
                                <input type="number" name="expenses" value="0" min="0" step="100">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary btn-block">
                            {{ $en ? 'Generate and save proposal →' : 'Gerar e guardar proposta →' }}
                        </button>
                    </form>

                    @if($vm->opportunity->proposal_id ?? false)
                        <a href="{{ route('collaborator.proposals.show', $vm->opportunity->proposal_id) }}"
                           class="btn-ghost btn-block" style="margin-top:6px">
                            {{ $en ? 'View saved proposal ↗' : 'Ver proposta guardada ↗' }}
                        </a>
                    @endif
                </section>
            @endif

            {{-- Add note --}}
            <section class="opp-panel">
                <h2>{{ $en ? 'Add note' : 'Adicionar nota' }}</h2>
                <form action="{{ route('collaborator.opportunities.add-note', $vm->id()) }}" method="POST">
                    @csrf
                    <div class="opp-field">
                        <textarea name="note" rows="3"
                                  placeholder="{{ $en ? 'Record client feedback, decisions taken, relevant context...' : 'Registe feedback do cliente, decisões tomadas, contexto relevante...' }}"
                                  maxlength="2000"></textarea>
                    </div>
                    <button type="submit" class="btn-ghost">
                        {{ $en ? 'Add note' : 'Adicionar nota' }}
                    </button>
                </form>
            </section>
        </div>

        {{-- RIGHT: timeline --}}
        <div class="opp-show-right">
            <section class="opp-panel">
                <h2>Timeline</h2>
                <div class="opp-timeline">
                    @forelse($vm->timeline() as $event)
                        <div class="opp-timeline-item opp-tl-{{ $event->event_type }}">
                            <div class="opp-tl-dot">{{ $event->iconForType() }}</div>
                            <div class="opp-tl-content">
                                <time>{{ $event->occurred_at->format('d/m/Y H:i') }}</time>
                                <p>{{ $event->description }}</p>
                                @if($event->event_type === 'state_changed' && $event->to_status)
                                    <span class="opp-tl-badge">
                                        {{ config("opportunity_workflow.states.{$event->to_status}.label", $event->to_status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="opp-timeline-empty">{{ $en ? 'No events recorded.' : 'Sem eventos registados.' }}</p>
                    @endforelse
                </div>
            </section>

            {{-- Documents --}}
            @if($vm->documents()->isNotEmpty())
                <section class="opp-panel">
                    <h2>{{ $en ? 'Client documents' : 'Documentos do cliente' }}</h2>
                    @foreach($vm->documents() as $doc)
                        <div class="opp-doc-row">
                            <div class="opp-doc-info">
                                <span class="opp-doc-name">{{ $doc->original_name }}</span>
                                <span class="opp-doc-size">{{ $doc->fileSizeFormatted() }}</span>
                                @if($doc->question_key)
                                    <span class="opp-doc-question">{{ $doc->question_key }}</span>
                                @endif
                            </div>
                            <div class="opp-doc-actions">
                                @if($doc->ocr_processed)
                                    <span class="opp-doc-ocr">OCR ✓</span>
                                @elseif($doc->ocr_eligible)
                                    <span class="opp-doc-ocr opp-doc-ocr--pending">{{ $en ? 'OCR pending' : 'OCR pendente' }}</span>
                                @endif
                                <a href="{{ route('collaborator.opportunities.document-download', [$vm->id(), $doc->id]) }}"
                                   class="opp-doc-download" title="{{ $en ? 'Download' : 'Descarregar' }}">
                                    ↓ {{ $en ? 'Download' : 'Descarregar' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </section>
            @endif
        </div>
    </div>

</div>{{-- .opp-show --}}
</div>{{-- .announcement-admin-shell --}}
</main>
@endsection
