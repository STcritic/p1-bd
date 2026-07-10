@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="19"
    :label="$en ? 'Proposal acceptance' : 'Aceitação da proposta'"
    :title="$en ? 'Authorisation and start confirmation' : 'Autorização e confirmação de início'"
    variant="acceptance">
    <div class="proposal-acceptance-intro">
        <p>{{ $vm->closingNote }}</p>
    </div>
    <div class="proposal-acceptance-grid">
        <div class="proposal-block proposal-acceptance-block">
            <h3>{{ $en ? 'On behalf of the Client' : 'Em nome do Cliente' }}</h3>
            <div class="proposal-signature-area">
                <div class="proposal-signature-line"></div>
                <p>{{ $en ? 'Signature and stamp' : 'Assinatura e carimbo' }}</p>
            </div>
            <div class="proposal-acceptance-fields">
                <div><span>{{ $en ? 'Name:' : 'Nome:' }}</span><div class="proposal-field-line"></div></div>
                <div><span>{{ $en ? 'Title:' : 'Cargo:' }}</span><div class="proposal-field-line"></div></div>
                <div><span>{{ $en ? 'Date:' : 'Data:' }}</span><div class="proposal-field-line"></div></div>
            </div>
            <div class="proposal-acceptance-client-prefill">
                <p><strong>{{ $vm->clientName }}</strong></p>
                <p>{{ $vm->clientPosition }}</p>
                <p>{{ $vm->clientContact }}</p>
            </div>
        </div>
        <div class="proposal-block proposal-acceptance-block proposal-highlight">
            <h3>{{ $en ? 'On behalf of' : 'Em nome de' }} {{ $vm->company()['short_name'] ?? $vm->company()['legal_name'] ?? 'Business Diversity' }}</h3>
            <div class="proposal-signature-area">
                <div class="proposal-signature-line"></div>
                <p>{{ $en ? 'Signature and stamp' : 'Assinatura e carimbo' }}</p>
            </div>
            <div class="proposal-acceptance-fields">
                <div><span>{{ $en ? 'Name:' : 'Nome:' }}</span><div class="proposal-field-line"></div></div>
                <div><span>{{ $en ? 'Title:' : 'Cargo:' }}</span><div class="proposal-field-line"></div></div>
                <div><span>{{ $en ? 'Date:' : 'Data:' }}</span><div class="proposal-field-line"></div></div>
            </div>
            <div class="proposal-acceptance-company-prefill">
                <p><strong>{{ $vm->preparedBy }}</strong></p>
                <p>{{ $vm->preparedRole }}</p>
                @if (!empty($vm->company()['email']))<p>{{ $vm->company()['email'] }}</p>@endif
                @if (!empty($vm->company()['phone']))<p>{{ $vm->company()['phone'] }}</p>@endif
                @if (!empty($vm->company()['website']))<p>{{ $vm->company()['website'] }}</p>@endif
                @if (!empty($vm->company()['address']))<p class="proposal-acceptance-address">{{ $vm->company()['address'] }}</p>@endif
            </div>
        </div>
    </div>
    <div class="proposal-reference-footer">
        <span>Ref. {{ $vm->reference }}</span>
        <span>{{ $en ? 'Valid until' : 'Válida até' }} {{ $vm->formattedValidUntil() }}</span>
        <span>{{ $vm->company()['short_name'] ?? $vm->company()['legal_name'] ?? '' }}</span>
    </div>
</x-proposal.page>
