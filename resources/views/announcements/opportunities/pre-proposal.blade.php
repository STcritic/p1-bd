@php
    /** @var \App\Modules\Collaborator\Opportunity\PreProposal\PreProposalViewModel $vm */
    $company = $vm->company();
    $lang    = $vm->lang ?? 'pt';
    $en      = $lang === 'en';
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vm->reference }}: {{ $vm->clientName }}</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Isolate pre-proposal shell from the announcements layout */
        body { background: #f0f4f8; }
    </style>
</head>
<body>

{{-- Screen-only: download button and back link --}}
<div class="pp-screen-toolbar no-print">
    <a href="{{ route('collaborator.opportunities.show', $vm->opportunityId) }}" class="pp-toolbar-back">
        ← {{ $en ? 'Back to opportunity' : 'Voltar à oportunidade' }}
    </a>
    <div class="pp-toolbar-actions">
        <button class="pp-toolbar-btn"
                data-pdf-reference="{{ $vm->pdfReference() }}"
                onclick="
                    var ref = this.dataset.pdfReference;
                    var prev = document.title;
                    document.title = ref;
                    window.addEventListener('afterprint', function r(){ document.title=prev; window.removeEventListener('afterprint',r); });
                    setTimeout(function(){ window.print(); }, 60);
                ">
            {{ $en ? 'Download PDF' : 'Baixar PDF' }}
        </button>
        @if($vm->portalUrl)
            <a href="{{ $vm->portalUrl }}" target="_blank" class="pp-toolbar-btn pp-toolbar-btn--outline">
                {{ $en ? 'Open diagnostic portal →' : 'Abrir portal de diagnóstico →' }}
            </a>
        @endif
    </div>
</div>

<div class="pp-document">

    {{-- ════════════════════════════════════════════════════════════════════
         PÁGINA 1 — CAPA
    ═══════════════════════════════════════════════════════════════════════ --}}
    <x-pre-proposal.page variant="cover">

        {{-- BD branding strip --}}
        <div class="pp-cover-brand">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="{{ $company['name'] ?? 'BD' }}"
                 class="pp-cover-logo" onerror="this.style.display='none'">
            <div class="pp-cover-brand-text">
                <strong>{{ $company['name'] ?? 'Business Diversity' }}</strong>
                <span>{{ ($en ? ($company['tagline_en'] ?? null) : null) ?? $company['tagline'] ?? 'Consultoria de Gestão de Pessoas' }}</span>
            </div>
        </div>

        {{-- Document type badge --}}
        <div class="pp-cover-badge">{{ $en ? 'Partnership Proposal' : 'Proposta de Parceria' }}</div>

        {{-- Main identity --}}
        <div class="pp-cover-main">
            <div class="pp-cover-service">{{ $vm->serviceTitle }}</div>
            <h1 class="pp-cover-client">{{ $vm->clientName }}</h1>
        </div>

        {{-- Cover footer --}}
        <div class="pp-cover-footer">
            <div class="pp-cover-meta">
                <div class="pp-cover-meta-row">
                    <span>{{ $en ? 'Reference' : 'Referência' }}</span>
                    <strong>{{ $vm->reference }}</strong>
                </div>
                <div class="pp-cover-meta-row">
                    <span>{{ $en ? 'Date' : 'Data' }}</span>
                    <strong>{{ $vm->date }}</strong>
                </div>
                <div class="pp-cover-meta-row">
                    <span>{{ $en ? 'Prepared by' : 'Preparado por' }}</span>
                    <strong>{{ $vm->preparedBy }}</strong>
                </div>
                @if($vm->clientContact)
                    <div class="pp-cover-meta-row">
                        <span>{{ $en ? 'Recipient' : 'Destinatário' }}</span>
                        <strong>{{ $vm->clientContact }}{{ $vm->clientPosition ? ', ' . $vm->clientPosition : '' }}</strong>
                    </div>
                @endif
            </div>
            <div class="pp-cover-confidential">{{ $en ? 'Confidential document. For client use only.' : 'Documento confidencial. Uso exclusivo do cliente.' }}</div>
        </div>

    </x-pre-proposal.page>

    {{-- ════════════════════════════════════════════════════════════════════
         PÁGINA 2 — CONTEXTO E DESAFIO
    ═══════════════════════════════════════════════════════════════════════ --}}
    <x-pre-proposal.page variant="context">

        <div class="pp-page-head">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="BD" class="pp-head-logo"
                 onerror="this.style.display='none'">
            <span>{{ $vm->reference }}</span>
        </div>

        <div class="pp-section-label">{{ $en ? '01 / Context' : '01 / Contexto' }}</div>
        <h2 class="pp-section-title">{{ $en ? 'We understand the challenge' : 'Compreendemos o desafio' }}</h2>

        <p class="pp-context-intro">{{ $vm->clientContextIntro }}</p>

        <div class="pp-context-signals">
            @foreach($vm->contextSignals as $signal)
                <div class="pp-signal-card">
                    <span class="pp-signal-label">{{ $signal['label'] }}</span>
                    <p class="pp-signal-text">{{ $signal['text'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="pp-positioning-block">
            <div class="pp-positioning-label">{{ $en ? 'Our response' : 'A nossa resposta' }}</div>
            <p class="pp-positioning-text">{{ $vm->positioningStatement }}</p>
        </div>

    </x-pre-proposal.page>

    {{-- ════════════════════════════════════════════════════════════════════
         PÁGINA 3 — ABORDAGEM E METODOLOGIA
    ═══════════════════════════════════════════════════════════════════════ --}}
    <x-pre-proposal.page variant="approach">

        <div class="pp-page-head">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="BD" class="pp-head-logo"
                 onerror="this.style.display='none'">
            <span>{{ $vm->reference }}</span>
        </div>

        <div class="pp-section-label">{{ $en ? '02 / Approach' : '02 / Abordagem' }}</div>
        <h2 class="pp-section-title">{{ $en ? 'How we work' : 'Como trabalhamos' }}</h2>

        <p class="pp-approach-intro">{{ $vm->approachIntro }}</p>

        {{-- Methodology steps --}}
        <div class="pp-methodology-steps">
            @foreach($vm->methodologySteps as $step)
                <div class="pp-method-step">
                    <div class="pp-method-num">{{ $step['num'] }}</div>
                    <div class="pp-method-content">
                        <strong>{{ $step['label'] }}</strong>
                        <p>{{ $step['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Timeline estimate --}}
        <div class="pp-timeline-estimate">
            <span class="pp-tl-label">{{ $en ? 'Timeline estimate' : 'Estimativa de prazo' }}</span>
            <strong class="pp-tl-value">{{ $vm->timelineEstimate }}</strong>
        </div>

        {{-- Differentiators --}}
        <div class="pp-differentiators">
            @foreach($vm->differentiators as $diff)
                <div class="pp-diff-card">
                    <span class="pp-diff-icon">{{ $diff['icon'] }}</span>
                    <div>
                        <strong>{{ $diff['label'] }}</strong>
                        <p>{{ $diff['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="pp-team-brief">{{ $vm->teamBrief }}</p>

    </x-pre-proposal.page>

    {{-- ════════════════════════════════════════════════════════════════════
         PÁGINA 4 — PRÓXIMOS PASSOS E DIAGNÓSTICO
    ═══════════════════════════════════════════════════════════════════════ --}}
    <x-pre-proposal.page variant="next-steps">

        <div class="pp-page-head">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="BD" class="pp-head-logo"
                 onerror="this.style.display='none'">
            <span>{{ $vm->reference }}</span>
        </div>

        <div class="pp-section-label">{{ $en ? '03 / Next steps' : '03 / Próximos passos' }}</div>
        <h2 class="pp-section-title">{{ $en ? 'To build the ideal proposal' : 'Para construir a proposta ideal' }}</h2>

        <p class="pp-diagnostic-intro">{{ $vm->diagnosticIntro }}</p>

        {{-- Benefits of the diagnostic --}}
        <div class="pp-diagnostic-benefits">
            <div class="pp-benefits-label">{{ $en ? 'The diagnostic allows us to deliver:' : 'O diagnóstico permite-nos entregar:' }}</div>
            <ul class="pp-benefits-list">
                @foreach($vm->diagnosticBenefits as $benefit)
                    <li>{{ $benefit }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Portal URL --}}
        @if($vm->portalUrl)
            <div class="pp-portal-block">
                <div class="pp-portal-label">{{ $en ? 'Diagnostic link' : 'Link de diagnóstico' }}</div>
                <div class="pp-portal-url">{{ $vm->portalUrl }}</div>
                <div class="pp-portal-hint">{{ $en ? 'The link can be saved and resumed. No account required.' : 'O link pode ser guardado e retomado. Não requer criação de conta.' }}</div>
            </div>
        @endif

        <p class="pp-cta-text">{{ $vm->diagnosticCallToAction }}</p>

        {{-- Closing --}}
        <div class="pp-closing-block">
            <p class="pp-closing-statement">{{ $vm->closingStatement }}</p>
            <div class="pp-closing-signature">
                <strong>{{ $vm->preparedBy }}</strong>
                <span>{{ $vm->preparedRole }}</span>
                @php $contact = $vm->contact(); @endphp
                @if($contact['email'] ?? null)
                    <span>{{ $contact['email'] }}</span>
                @endif
                @if($contact['phone'] ?? null)
                    <span>{{ $contact['phone'] }}</span>
                @endif
            </div>
        </div>

        {{-- Page footer --}}
        <div class="pp-page-footer">
            <span>{{ $company['name'] ?? 'Business Diversity' }}</span>
            <span>{{ $vm->reference }}</span>
            <span>{{ $en ? 'Confidential document' : 'Documento confidencial' }}</span>
        </div>

    </x-pre-proposal.page>

</div><!-- .pp-document -->
</body>
</html>
