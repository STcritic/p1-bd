@props(['vm'])
<x-proposal.page number="08" label="Cronograma visual" title="Plano de execução indicativo">
    <div class="proposal-timeline">
        @foreach ($vm->timelinePlan as $item)
            <article>
                <span>{{ $item['period'] }}</span>
                <div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
</x-proposal.page>
