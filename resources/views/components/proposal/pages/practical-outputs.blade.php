@props(['vm'])
<x-proposal.page number="09" label="Utilidade prática" title="O que o cliente recebe no final">
    <div class="proposal-output-grid">
        @foreach ($vm->practicalOutputs as $output)
            <article>
                <span>✓</span>
                <p>{{ $output }}</p>
            </article>
        @endforeach
    </div>
    @if (!empty($vm->technicalTools))
        <div class="proposal-tool-strip">
            <h3>Ferramentas e referências técnicas aplicáveis</h3>
            <div>
                @foreach ($vm->technicalTools as $tool)
                    <article>
                        <strong>{{ $tool['name'] }}</strong>
                        <p>{{ $tool['use'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</x-proposal.page>
