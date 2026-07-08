@props(['vm'])
<x-proposal.page number="01" label="Carta personalizada"
    :title="'Preparada para a realidade da '.$vm->clientName"
    variant="letter"
    :pageHead="$vm->clientName">
    <div class="proposal-letter-card">
        <p>{{ $vm->personalLetter }}</p>
        <div class="proposal-letter-signature">
            <span>{{ $vm->company()['short_name'] ?? 'Business Diversity' }}</span>
            @php $personName = $vm->preparedBy; $companyShort = $vm->company()['short_name'] ?? ''; @endphp
            @if ($personName && $personName !== $companyShort)
                <strong>{{ $personName }}</strong>
            @endif
            <small>{{ $vm->preparedRole }}</small>
        </div>
    </div>
    <div class="proposal-context-panel">
        <span>Entendimento preliminar</span>
        <p>{{ $vm->contextualSummary }}</p>
    </div>
</x-proposal.page>
