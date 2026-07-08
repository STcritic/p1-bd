@props(['vm'])
<x-proposal.page number="03" label="Sumário executivo"
    title="Uma intervenção desenhada para criar valor mensurável"
    variant="summary">
    <div class="proposal-editorial-visual proposal-summary-visual">
        <img src="{{ $vm->coverImageUrl }}" alt="">
        <div>
            <span>Proposta de valor</span>
            <strong>{{ $vm->serviceValue }}</strong>
            <p>{{ $vm->contextualSummary }}</p>
        </div>
    </div>
    <div class="proposal-two-columns">
        <div class="proposal-block proposal-highlight">
            <h3>Resposta proposta</h3>
            <p>Propomos uma intervenção de {{ strtolower($vm->serviceTitle) }} orientada para reduzir riscos, acelerar decisões, produzir entregáveis utilizáveis e apoiar a equipa do cliente na implementação prática.</p>
        </div>
        <div class="proposal-block">
            <h3>Resultado esperado</h3>
            <p>{{ $vm->serviceValue }}</p>
        </div>
    </div>
    <div class="proposal-value-strip">
        @foreach ($vm->valueProposition() as $value)
            <article><span>✓</span><p>{{ $value }}</p></article>
        @endforeach
    </div>
    <div class="proposal-client-grid proposal-snapshot-grid">
        <div><small>Cliente</small><strong>{{ $vm->clientName }}</strong></div>
        @if ($vm->clientIndustry)<div><small>Sector</small><strong>{{ $vm->clientIndustry }}</strong></div>@endif
        <div><small>Pacote</small><strong>{{ $vm->pricingPackage['label'] ?? 'Implementação estruturada' }}</strong></div>
        <div><small>Complexidade</small><strong>{{ $vm->complexityLabel }}</strong></div>
        @if ($vm->clientContact)<div><small>Contacto</small><strong>{{ $vm->clientContact }}</strong></div>@endif
        @if ($vm->clientLocation)<div><small>Localização</small><strong>{{ $vm->clientLocation }}</strong></div>@endif
    </div>
</x-proposal.page>
