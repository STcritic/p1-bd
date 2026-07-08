@props(['vm'])
<x-proposal.page number="19" label="Aceitação da proposta"
    title="Autorização e confirmação de início"
    variant="acceptance">
    <div class="proposal-acceptance-intro">
        <p>{{ $vm->closingNote }}</p>
    </div>
    <div class="proposal-acceptance-grid">
        <div class="proposal-block proposal-acceptance-block">
            <h3>Em nome do Cliente</h3>
            <div class="proposal-signature-area">
                <div class="proposal-signature-line"></div>
                <p>Assinatura e carimbo</p>
            </div>
            <div class="proposal-acceptance-fields">
                <div><span>Nome:</span><div class="proposal-field-line"></div></div>
                <div><span>Cargo:</span><div class="proposal-field-line"></div></div>
                <div><span>Data:</span><div class="proposal-field-line"></div></div>
            </div>
            <div class="proposal-acceptance-client-prefill">
                <p><strong>{{ $vm->clientName }}</strong></p>
                <p>{{ $vm->clientPosition }}</p>
                <p>{{ $vm->clientContact }}</p>
            </div>
        </div>
        <div class="proposal-block proposal-acceptance-block proposal-highlight">
            <h3>Em nome de {{ $vm->company()['short_name'] ?? $vm->company()['legal_name'] ?? 'Business Diversity' }}</h3>
            <div class="proposal-signature-area">
                <div class="proposal-signature-line"></div>
                <p>Assinatura e carimbo</p>
            </div>
            <div class="proposal-acceptance-fields">
                <div><span>Nome:</span><div class="proposal-field-line"></div></div>
                <div><span>Cargo:</span><div class="proposal-field-line"></div></div>
                <div><span>Data:</span><div class="proposal-field-line"></div></div>
            </div>
            <div class="proposal-acceptance-company-prefill">
                <p><strong>{{ $vm->preparedBy }}</strong></p>
                <p>{{ $vm->preparedRole }}</p>
                <p>{{ $vm->company()['email'] ?? '' }}</p>
            </div>
        </div>
    </div>
    <div class="proposal-reference-footer">
        <span>Ref. {{ $vm->reference }}</span>
        <span>Válida até {{ $vm->formattedValidUntil() }}</span>
        <span>{{ $vm->company()['name'] ?? '' }} &mdash; {{ $vm->company()['nif'] ?? '' }}</span>
    </div>
</x-proposal.page>
