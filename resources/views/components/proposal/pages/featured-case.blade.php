@props(['vm'])
@php $case = $vm->featuredCase; $en = $vm->lang() === 'en'; @endphp
@if (!empty($case))
<x-proposal.page number="13"
    :label="$en ? 'Success case' : 'Caso de sucesso'"
    :title="$case['title']"
    variant="featured-case">
    <div class="proposal-featured-case">
        <div class="proposal-featured-case-main">
            <span>{{ $case['sector'] }}</span>
            <h3>{{ $en ? 'Challenge' : 'Desafio' }}</h3>
            <p>{{ $case['challenge'] }}</p>
            <h3>{{ $en ? 'BD Intervention' : 'Intervenção BD' }}</h3>
            <p>{{ $case['intervention'] }}</p>
        </div>
        <div class="proposal-featured-case-results">
            <span>{{ $en ? 'Observed results' : 'Resultados observados' }}</span>
            @foreach (($case['results'] ?? []) as $result)
                <article>
                    <strong>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</strong>
                    <p>{{ $result }}</p>
                </article>
            @endforeach
        </div>
    </div>
    @if (!empty($case['note']))
        <div class="proposal-case-note">{{ $case['note'] }}</div>
    @endif
</x-proposal.page>
@endif
