@props(['vm'])
@php $bank = $vm->bank(); @endphp
<x-proposal.page number="12" label="Termos e garantias" title="Condições, qualidade e dados de pagamento">
    <div class="proposal-two-columns">
        <div class="proposal-block">
            <h3>Condições de pagamento</h3>
            <p>{!! nl2br(e($vm->paymentTerms)) !!}</p>
            <h3>Notas financeiras</h3>
            <p>{!! nl2br(e($vm->financialNotes)) !!}</p>
        </div>
        <div class="proposal-block proposal-highlight">
            <h3>Garantias de qualidade</h3>
            <ul>
                @foreach ($vm->guarantees() as $guarantee)
                    <li>{{ $guarantee }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="proposal-two-columns">
        <div class="proposal-block">
            <h3>Termos e condições</h3>
            <ul>
                @foreach ($vm->commercialTerms() as $term)
                    <li>{{ $term }}</li>
                @endforeach
            </ul>
        </div>
        <div class="proposal-block">
            <h3>Detalhes bancários</h3>
            @if ($vm->hasBank())
                <div class="proposal-bank-list">
                    @if ($bank['bank_name']      ?? null)<p><span>Banco</span><strong>{{ $bank['bank_name'] }}</strong></p>@endif
                    @if ($bank['account_holder'] ?? null)<p><span>Titular</span><strong>{{ $bank['account_holder'] }}</strong></p>@endif
                    @if ($bank['account_number'] ?? null)<p><span>Conta</span><strong>{{ $bank['account_number'] }}</strong></p>@endif
                    @if ($bank['nib']            ?? null)<p><span>NIB</span><strong>{{ $bank['nib'] }}</strong></p>@endif
                    @if ($bank['swift']          ?? null)<p><span>SWIFT</span><strong>{{ $bank['swift'] }}</strong></p>@endif
                </div>
            @else
                <p>{{ $bank['note'] ?? 'Os dados bancários serão confirmados na factura/proforma.' }}</p>
            @endif
        </div>
    </div>
</x-proposal.page>
