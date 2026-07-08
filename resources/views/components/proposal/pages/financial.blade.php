@props(['vm'])
<x-proposal.page number="11" label="Proposta financeira"
    title="Investimento e racional comercial"
    variant="financial">
    <div class="proposal-commercial-grid">
        <div class="proposal-block proposal-highlight">
            <h3>{{ $vm->pricingPackage['label'] ?? 'Pacote comercial' }}</h3>
            <p>{{ $vm->pricingPackage['description'] ?? '' }}</p>
        </div>
        <div class="proposal-block">
            <h3>Base de precificação</h3>
            <p>{{ $vm->pricingPolicy['base'] ?? ($vm->pricingPackage['pricing'] ?? 'Preço definido conforme escopo, complexidade e recursos necessários.') }}</p>
        </div>
    </div>
    @if (!empty($vm->pricingPolicy['drivers']))
        <div class="proposal-block">
            <h3>Factores considerados no preço</h3>
            <ul>
                @foreach ($vm->pricingPolicy['drivers'] as $driver)
                    <li>{{ $driver }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($vm->hasInvestment)
        <div class="proposal-investment-title">
            <span>Investimento específico</span>
            <strong>{{ $vm->serviceTitle }} para {{ $vm->clientName }}</strong>
        </div>
        <table class="proposal-financial-table">
            <tbody>
                <tr><th>Honorários profissionais</th><td>{{ $vm->currency }} {{ $vm->money($vm->fee) }}</td></tr>
                <tr><th>Despesas estimadas</th><td>{{ $vm->currency }} {{ $vm->money($vm->expenses) }}</td></tr>
                <tr><th>Subtotal</th><td>{{ $vm->currency }} {{ $vm->money($vm->subtotal) }}</td></tr>
                <tr><th>IVA ({{ $vm->money($vm->vatRate) }}%)</th><td>{{ $vm->currency }} {{ $vm->money($vm->vat) }}</td></tr>
                <tr class="proposal-total-row"><th>Total estimado</th><td>{{ $vm->currency }} {{ $vm->money($vm->total) }}</td></tr>
            </tbody>
        </table>
    @else
        <div class="proposal-no-investment">
            <span>Investimento a confirmar</span>
            <h3>O valor final deve ser inserido antes da submissão ao cliente.</h3>
            <p>Esta versão mantém metodologia, entregáveis e racional comercial. Para uma proposta final, o investimento deve ser apresentado como valor específico do projecto, evitando faixas genéricas que enfraquecem a decisão comercial.</p>
        </div>
    @endif
</x-proposal.page>
