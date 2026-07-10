@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="07"
    :label="$en ? 'BD Methodology' : 'Metodologia BD'"
    :title="$en ? 'From context reading to implemented decision' : 'Da leitura do contexto à decisão implementada'"
    variant="process">
    <div class="proposal-methodology-map">
        <div><span>01</span><strong>{{ $en ? 'Diagnose' : 'Diagnosticar' }}</strong><p>{{ $en ? 'Understand the context, risks, available data and decision to protect.' : 'Entender o contexto, riscos, dados disponíveis e decisão a proteger.' }}</p></div>
        <div><span>02</span><strong>{{ $en ? 'Design' : 'Desenhar' }}</strong><p>{{ $en ? 'Build instruments, criteria and technical approach tailored to the client.' : 'Construir instrumentos, critérios e abordagem técnica ajustados ao cliente.' }}</p></div>
        <div><span>03</span><strong>{{ $en ? 'Validate' : 'Validar' }}</strong><p>{{ $en ? 'Test the solution with stakeholders, correct gaps and align expectations.' : 'Testar a solução com stakeholders, corrigir lacunas e alinhar expectativas.' }}</p></div>
        <div><span>04</span><strong>{{ $en ? 'Transfer' : 'Transferir' }}</strong><p>{{ $en ? 'Deliver, guide application and leave next steps clear.' : 'Entregar, orientar a aplicação e deixar próximos passos claros.' }}</p></div>
    </div>
    <div class="proposal-flow">
        @foreach ($vm->processFlow as $index => $step)
            <article>
                <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <strong>{{ $step }}</strong>
            </article>
        @endforeach
    </div>
    <div class="proposal-roadmap">
        @foreach ($vm->roadmap as $index => $phase)
            <article>
                <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <small>{{ $phase['label'] }}</small>
                <h3>{{ $phase['title'] }}</h3>
                <p>{{ $phase['text'] }}</p>
                @if (!empty($phase['module']))
                    <strong>{{ $phase['module'] }}</strong>
                @endif
            </article>
        @endforeach
    </div>
</x-proposal.page>
