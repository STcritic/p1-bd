@props(['vm'])
<x-proposal.page number="15" label="Clientes e parceiros"
    title="Alguns dos nossos estimados clientes"
    variant="clients">
    @if ($vm->clients()->isNotEmpty())
        <div class="proposal-block">
            <h3>Organizações que reforçam a nossa experiência no mercado</h3>
            <div class="proposal-client-logo-grid">
                @foreach ($vm->clients() as $client)
                    <div>
                        <img src="{{ asset($client['logo']) }}" alt="{{ $client['name'] ?? 'Cliente BD' }}">
                        <span>{{ $client['name'] ?? 'Cliente BD' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-proposal.page>
