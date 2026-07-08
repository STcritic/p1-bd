@props(['vm'])
<x-proposal.page number="06" label="Âmbito da proposta" title="Objectivos, escopo e entregáveis">
    <div class="proposal-two-columns">
        <div class="proposal-block">
            <h3>Objectivos da intervenção</h3>
            <ul>
                @foreach ($vm->lines($vm->objectives) as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
        <div class="proposal-block proposal-highlight">
            <h3>Âmbito técnico</h3>
            <p>{!! nl2br(e($vm->scope)) !!}</p>
            @if (!empty($vm->selectedModules))
                <div class="proposal-module-tags">
                    @foreach ($vm->selectedModules as $module)
                        <span>{{ $module }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="proposal-block proposal-scope-deliverables">
        <h3>Entregáveis principais</h3>
        <ul class="proposal-deliverables-list">
            @foreach ($vm->lines($vm->deliverables) as $line)
                <li>{{ $line }}</li>
            @endforeach
        </ul>
    </div>
</x-proposal.page>
