@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="18"
    :label="$en ? 'Frequently asked questions' : 'Perguntas frequentes'"
    :title="$en ? 'Questions we anticipate from you' : 'Perguntas que antecipamos da sua parte'"
    variant="faq">
    @if (!empty($vm->faqs))
        <div class="proposal-faq-list">
            @foreach ($vm->faqs as $index => $item)
                <article class="proposal-faq-item">
                    <header>
                        <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $item['question'] }}</h3>
                    </header>
                    <div>{{ $item['answer'] }}</div>
                </article>
            @endforeach
        </div>
    @endif
    @if (!empty($vm->nextSteps))
        <div class="proposal-next-steps">
            <h3>{{ $en ? 'Next steps' : 'Próximos passos' }}</h3>
            <ol>
                @foreach ($vm->nextSteps as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>
        </div>
    @endif
</x-proposal.page>
