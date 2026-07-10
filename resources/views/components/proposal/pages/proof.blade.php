@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="14"
    :label="$en ? 'Social proof' : 'Prova social'"
    :title="$en ? 'Relevant results and experiences' : 'Resultados e experiências relevantes'"
    variant="proof">
    @if ($vm->impactMetrics()->isNotEmpty())
        <div class="proposal-credential-metrics proposal-impact-metrics">
            @foreach ($vm->impactMetrics() as $metric)
                <article><strong>{{ $metric['value'] }}</strong><span>{{ $metric['label'] }}</span></article>
            @endforeach
        </div>
    @endif
    @if ($vm->caseStudies()->isNotEmpty())
        <div class="proposal-case-grid">
            @foreach ($vm->caseStudies() as $case)
                <article>
                    <span>{{ $case['sector'] }}</span>
                    <h3>{{ $case['title'] }}</h3>
                    <p>{{ $case['result'] }}</p>
                    <div>
                        @foreach (($case['metrics'] ?? []) as $item)
                            <small>{{ $item }}</small>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    @endif
    @if ($vm->credibilityMetrics()->isNotEmpty())
        <div class="proposal-credential-metrics">
            @foreach ($vm->credibilityMetrics() as $metric)
                <article><strong>{{ $metric['value'] }}</strong><span>{{ $metric['label'] }}</span></article>
            @endforeach
        </div>
    @endif
</x-proposal.page>
