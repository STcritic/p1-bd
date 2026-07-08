@props(['vm'])
<x-proposal.page number="04" label="Argumento estratégico"
    :title="$vm->criticalCase['title']"
    variant="critical">
    <div class="proposal-critical-grid">
        <div class="proposal-critical-main">
            <span>Impacto</span>
            <p>{{ $vm->criticalCase['intro'] }}</p>
        </div>
        @foreach ($vm->criticalCase['items'] as $index => $item)
            <article>
                <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <p>{{ $item }}</p>
            </article>
        @endforeach
    </div>
</x-proposal.page>
