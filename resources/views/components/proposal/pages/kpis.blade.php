@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="10"
    :label="$en ? 'KPIs and differentials' : 'KPIs e diferenciais'"
    :title="$en ? 'How we will know the intervention worked' : 'Como saberemos que a intervenção funcionou'">
    <div class="proposal-two-columns">
        <div class="proposal-block">
            <h3>{{ $en ? 'Success indicators' : 'Indicadores de sucesso' }}</h3>
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
            <h3>{{ $en ? 'Why choose BD' : 'Porque escolher a BD' }}</h3>
            <ul>
                @foreach ($vm->differentiators as $differentiator)
                    <li>{{ $differentiator }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="proposal-block">
        <h3>{{ $en ? 'Selected approaches' : 'Abordagens seleccionadas' }}</h3>
        <div class="proposal-pill-grid">
            @foreach ($vm->selectedApproaches as $approach)
                <span>{{ $approach }}</span>
            @endforeach
        </div>
    </div>
</x-proposal.page>
