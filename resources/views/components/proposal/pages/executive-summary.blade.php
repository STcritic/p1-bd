@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="03"
    :label="$en ? 'Executive summary' : 'Sumário executivo'"
    :title="$en ? 'An intervention designed to create measurable value' : 'Uma intervenção desenhada para criar valor mensurável'"
    variant="summary">
    <div class="proposal-editorial-visual proposal-summary-visual">
        <img src="{{ $vm->coverImageUrl }}" alt="">
        <div>
            <span>{{ $en ? 'Value proposition' : 'Proposta de valor' }}</span>
            <strong>{{ $vm->serviceValue }}</strong>
            <p>{{ $vm->contextualSummary }}</p>
        </div>
    </div>
    <div class="proposal-two-columns">
        <div class="proposal-block proposal-highlight">
            <h3>{{ $en ? 'Proposed response' : 'Resposta proposta' }}</h3>
            <p>{{ $en
                ? 'We propose a '.strtolower($vm->serviceTitle).' intervention aimed at reducing risks, accelerating decisions, producing usable deliverables and supporting the client team in practical implementation.'
                : 'Propomos uma intervenção de '.strtolower($vm->serviceTitle).' orientada para reduzir riscos, acelerar decisões, produzir entregáveis utilizáveis e apoiar a equipa do cliente na implementação prática.'
            }}</p>
        </div>
        <div class="proposal-block">
            <h3>{{ $en ? 'Expected outcome' : 'Resultado esperado' }}</h3>
            <p>{{ $vm->serviceValue }}</p>
        </div>
    </div>
    <div class="proposal-value-strip">
        @foreach ($vm->valueProposition() as $value)
            <article><span>✓</span><p>{{ $value }}</p></article>
        @endforeach
    </div>
    <div class="proposal-client-grid proposal-snapshot-grid">
        <div><small>{{ $en ? 'Client' : 'Cliente' }}</small><strong>{{ $vm->clientName }}</strong></div>
        @if ($vm->clientIndustry)<div><small>{{ $en ? 'Sector' : 'Sector' }}</small><strong>{{ $vm->clientIndustry }}</strong></div>@endif
        <div><small>{{ $en ? 'Package' : 'Pacote' }}</small><strong>{{ $vm->pricingPackage['label'] ?? ($en ? 'Structured implementation' : 'Implementação estruturada') }}</strong></div>
        <div><small>{{ $en ? 'Complexity' : 'Complexidade' }}</small><strong>{{ $vm->complexityLabel }}</strong></div>
        @if ($vm->clientContact)<div><small>{{ $en ? 'Contact' : 'Contacto' }}</small><strong>{{ $vm->clientContact }}</strong></div>@endif
        @if ($vm->clientLocation)<div><small>{{ $en ? 'Location' : 'Localização' }}</small><strong>{{ $vm->clientLocation }}</strong></div>@endif
    </div>
</x-proposal.page>
