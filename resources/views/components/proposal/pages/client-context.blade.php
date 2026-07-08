@props(['vm'])
<x-proposal.page number="02" label="Leitura do cliente"
    :title="$vm->clientContext['title']"
    variant="client-context">
    <div class="proposal-client-context-hero">
        <div>
            <span>O que entendemos</span>
            <p>{{ $vm->clientContext['intro'] }}</p>
        </div>
        <strong>{{ $vm->clientContext['service_need'] }}</strong>
    </div>
    <div class="proposal-client-signal-grid">
        @foreach ($vm->clientContext['signals'] as $signal)
            <article>
                <span>{{ $signal['label'] }}</span>
                <p>{{ $signal['text'] }}</p>
            </article>
        @endforeach
    </div>
</x-proposal.page>
