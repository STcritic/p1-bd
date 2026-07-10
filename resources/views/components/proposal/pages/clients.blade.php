@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="15"
    :label="$en ? 'Clients and partners' : 'Clientes e parceiros'"
    :title="$en ? 'Some of our esteemed clients' : 'Alguns dos nossos estimados clientes'"
    variant="clients">
    @if ($vm->clients()->isNotEmpty())
        <div class="proposal-block">
            <h3>{{ $en ? 'Organisations that reinforce our market experience' : 'Organizações que reforçam a nossa experiência no mercado' }}</h3>
            <div class="proposal-client-logo-grid">
                @foreach ($vm->clients() as $client)
                    <div>
                        <img src="{{ asset($client['logo']) }}" alt="{{ $client['name'] ?? 'BD Client' }}">
                        <span>{{ $client['name'] ?? 'BD Client' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-proposal.page>
