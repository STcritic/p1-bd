@props(['vm'])
@php $bank = $vm->bank(); $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="12"
    :label="$en ? 'Terms and guarantees' : 'Termos e garantias'"
    :title="$en ? 'Conditions, quality and payment details' : 'Condições, qualidade e dados de pagamento'">
    @if ($vm->serviceSlug === 'recrutamento-seleccao')
        @php $policy = $vm->recruitmentPolicy(); @endphp
        @if (!empty($policy['bands']))
            <div class="proposal-recruitment-policy">
                <h3>{{ $en ? 'Commercial fee policy' : 'Política comercial de honorários' }}</h3>
                @if (!empty($policy['model_label']))
                    <p class="proposal-policy-model">{{ $policy['model_label'] }}</p>
                @endif
                @php
                    $recruitType = $vm->recruitType ?? 'standard';
                @endphp
                <table class="proposal-financial-table proposal-policy-bands">
                    <thead>
                        <tr><th>{{ $en ? 'Salary band' : 'Faixa salarial' }}</th><th>Fee</th><th>{{ $en ? 'Guarantee' : 'Garantia' }}</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($policy['bands'] as $band)
                            <tr>
                                <th>{{ $band['label'] }}</th>
                                <td>{{ isset($band['rate']) ? $band['rate'].'%' : ($band['rate_min'].'–'.$band['rate_max'].'%') }}</td>
                                <td>{{ $band['guarantee_days'] }} {{ $en ? 'days' : 'dias' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if (!empty($policy['mass_note']))
                    <p class="proposal-policy-note">{{ $policy['mass_note'] }}</p>
                @endif
                <div class="proposal-inep-legal">
                    <strong>{{ $en ? 'INEP compliance' : 'Conformidade: INEP' }}</strong>
                    <span>{{ $en
                        ? 'Labour Law (Law No. 13/2023 of 28 December): mandatory notification to the National Employment and Professional Institute at least 7 working days before the start of the process. Employer\'s responsibility.'
                        : 'Lei do Trabalho (Lei n.º 13/2023 de 28 de Dezembro): comunicação obrigatória ao Instituto Nacional do Emprego e Profissional com mínimo de 7 dias úteis antes do início do processo. Responsabilidade do empregador.'
                    }}</span>
                </div>
                @if (!empty($policy['guarantee']))
                    <div class="proposal-guarantee-terms">
                        <strong>{{ $policy['guarantee']['note'] ?? ($en ? 'Replacement guarantee' : 'Garantia de substituição') }}</strong>
                        <ul>
                            @foreach ($policy['guarantee']['credit_options'] ?? [] as $opt)
                                <li>{{ $en
                                    ? $opt['credit_pct'].'% credit if departure within '.$opt['within_days'].' days of start'
                                    : 'Crédito de '.$opt['credit_pct'].'% se saída até '.$opt['within_days'].' dias após entrada'
                                }}</li>
                            @endforeach
                            @if (!empty($policy['guarantee']['max_uses_per_role']))
                                <li>{{ $en
                                    ? 'Maximum of '.$policy['guarantee']['max_uses_per_role'].' guarantees per position'
                                    : 'Máximo de '.$policy['guarantee']['max_uses_per_role'].' garantias por posição'
                                }}</li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    @endif

    <div class="proposal-two-columns">
        <div class="proposal-block">
            <h3>{{ $en ? 'Payment conditions' : 'Condições de pagamento' }}</h3>
            <p>{!! nl2br(e($vm->paymentTerms)) !!}</p>
            <h3>{{ $en ? 'Financial notes' : 'Notas financeiras' }}</h3>
            <p>{!! nl2br(e($vm->financialNotes)) !!}</p>
        </div>
        <div class="proposal-block proposal-highlight">
            <h3>{{ $en ? 'Quality guarantees' : 'Garantias de qualidade' }}</h3>
            <ul>
                @foreach ($vm->guarantees() as $guarantee)
                    <li>{{ $guarantee }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="proposal-two-columns">
        <div class="proposal-block">
            <h3>{{ $en ? 'Terms and conditions' : 'Termos e condições' }}</h3>
            <ul>
                @foreach ($vm->commercialTerms() as $term)
                    <li>{{ $term }}</li>
                @endforeach
            </ul>
        </div>
        <div class="proposal-block">
            <h3>{{ $en ? 'Banking details' : 'Detalhes bancários' }}</h3>
            @if ($vm->hasBank())
                <div class="proposal-bank-list">
                    @if ($bank['bank_name']      ?? null)<p><span>{{ $en ? 'Bank' : 'Banco' }}</span><strong>{{ $bank['bank_name'] }}</strong></p>@endif
                    @if ($bank['account_holder'] ?? null)<p><span>{{ $en ? 'Account holder' : 'Titular' }}</span><strong>{{ $bank['account_holder'] }}</strong></p>@endif
                    @if ($bank['account_number'] ?? null)<p><span>{{ $en ? 'Account' : 'Conta' }}</span><strong>{{ $bank['account_number'] }}</strong></p>@endif
                    @if ($bank['nib']            ?? null)<p><span>NIB</span><strong>{{ $bank['nib'] }}</strong></p>@endif
                    @if ($bank['swift']          ?? null)<p><span>SWIFT</span><strong>{{ $bank['swift'] }}</strong></p>@endif
                </div>
            @else
                <p>{{ $bank['note'] ?? ($en ? 'Banking details will be confirmed on the invoice/proforma.' : 'Os dados bancários serão confirmados na factura/proforma.') }}</p>
            @endif
        </div>
    </div>
</x-proposal.page>
