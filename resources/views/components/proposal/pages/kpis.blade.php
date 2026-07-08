@props(['vm'])
<x-proposal.page number="10" label="KPIs e diferenciais" title="Como saberemos que a intervenção funcionou">
    <div class="proposal-two-columns">
        <div class="proposal-block">
            <h3>Indicadores de sucesso</h3>
            <div class="proposal-kpi-list">
                @foreach ($vm->successMetrics as $metric)
                    <article>
                        <div>
                            <span></span>
                            <strong>{{ $metric['label'] }}</strong>
                            <p>{{ $metric['note'] }}</p>
                        </div>
                        <em>{{ $metric['target'] }}</em>
                    </article>
                @endforeach
            </div>
        </div>
        <div class="proposal-block proposal-highlight">
            <h3>Porque escolher a BD</h3>
            <ul>
                @foreach ($vm->differentiators as $differentiator)
                    <li>{{ $differentiator }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="proposal-block">
        <h3>Abordagens seleccionadas</h3>
        <div class="proposal-pill-grid">
            @foreach ($vm->selectedApproaches as $approach)
                <span>{{ $approach }}</span>
            @endforeach
        </div>
    </div>
</x-proposal.page>
