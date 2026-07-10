@props(['vm'])
@php
    $en = $vm->lang() === 'en';
    $nonZeroItems = collect($vm->expenseItems ?? [])
        ->filter(fn ($item) => (float)($item['amount'] ?? 0) > 0)
        ->values();
    $hasExpenseItems = $nonZeroItems->isNotEmpty();

    // Find applicable recruitment band for the salary note
    $salaryNote = null;
    if ($vm->serviceSlug === 'recrutamento-seleccao' && ($vm->candidateSalary ?? 0) > 0) {
        $policy      = $vm->recruitmentPolicy();
        $sal         = $vm->candidateSalary;
        $recruitType = $vm->recruitType ?? 'standard';

        if ($recruitType === 'headhunting') {
            // Executive / headhunting band
            foreach ($policy['bands'] ?? [] as $band) {
                if (isset($band['rate_min'])) {
                    $salaryNote = ['salary' => $sal, 'rate_min' => $band['rate_min'], 'rate_max' => $band['rate_max'], 'days' => $band['guarantee_days'], 'label' => $band['label']];
                    break;
                }
            }
        } else {
            // Match salary to the correct progressive band
            $simpleBands = array_values(array_filter($policy['bands'] ?? [], fn($b) => isset($b['rate'])));
            if (count($simpleBands) >= 3) {
                if ($sal <= 1000000)      $applicableBand = $simpleBands[0];
                elseif ($sal <= 2000000)  $applicableBand = $simpleBands[1];
                else                      $applicableBand = $simpleBands[2];
                $salaryNote = ['salary' => $sal, 'rate' => $applicableBand['rate'], 'days' => $applicableBand['guarantee_days'], 'label' => $applicableBand['label']];
            }
        }
    }
@endphp
<x-proposal.page number="11"
    :label="$en ? 'Financial proposal' : 'Proposta financeira'"
    :title="$en ? 'Investment and commercial rationale' : 'Investimento e racional comercial'"
    variant="financial">
    <div class="proposal-commercial-grid">
        <div class="proposal-block proposal-highlight">
            <h3>{{ $vm->pricingPackage['label'] ?? ($en ? 'Commercial package' : 'Pacote comercial') }}</h3>
            <p>{{ $vm->pricingPackage['description'] ?? '' }}</p>
        </div>
        <div class="proposal-block">
            <h3>{{ $en ? 'Pricing basis' : 'Base de precificação' }}</h3>
            <p>{{ $vm->pricingPolicy['base'] ?? ($vm->pricingPackage['pricing'] ?? ($en ? 'Price defined according to scope, complexity and required resources.' : 'Preço definido conforme escopo, complexidade e recursos necessários.')) }}</p>
        </div>
    </div>
    @if (!empty($vm->pricingPolicy['drivers']))
        <div class="proposal-block">
            <h3>{{ $en ? 'Factors considered in the price' : 'Factores considerados no preço' }}</h3>
            <ul>
                @foreach ($vm->pricingPolicy['drivers'] as $driver)
                    <li>{{ $driver }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($vm->hasInvestment)
        <div class="proposal-investment-title">
            <span>{{ $en ? 'Specific investment' : 'Investimento específico' }}</span>
            <strong>{{ $vm->serviceTitle }} {{ $en ? 'for' : 'para' }} {{ $vm->clientName }}</strong>
        </div>
        <table class="proposal-financial-table">
            <tbody>
                <tr>
                    <th>
                        {{ $en ? 'Professional fees' : 'Honorários profissionais' }}
                        @if ($salaryNote)
                            <small class="proposal-salary-basis">
                                @if (isset($salaryNote['rate']))
                                    Base: {{ $vm->currency }} {{ $vm->money($salaryNote['salary']) }} × {{ $salaryNote['rate'] }}%, {{ $salaryNote['label'] }}, {{ $salaryNote['days'] }}d {{ $en ? 'guarantee' : 'garantia' }}
                                @else
                                    Base: {{ $vm->currency }} {{ $vm->money($salaryNote['salary']) }} × {{ $salaryNote['rate_min'] }}–{{ $salaryNote['rate_max'] }}%, {{ $salaryNote['label'] }}, {{ $salaryNote['days'] }}d {{ $en ? 'guarantee' : 'garantia' }}
                                @endif
                            </small>
                        @endif
                    </th>
                    <td>{{ $vm->currency }} {{ $vm->money($vm->fee) }}</td>
                </tr>
                @if ($hasExpenseItems)
                    @foreach ($nonZeroItems as $item)
                        <tr class="proposal-expense-sub-row">
                            <th>{{ $item['label'] ?? ($en ? 'Expense' : 'Despesa') }}</th>
                            <td>{{ $vm->currency }} {{ $vm->money((float)($item['amount'] ?? 0)) }}</td>
                        </tr>
                    @endforeach
                    <tr><th>{{ $en ? 'Total expenses' : 'Total despesas' }}</th><td>{{ $vm->currency }} {{ $vm->money($vm->expenses) }}</td></tr>
                @elseif ($vm->expenses > 0)
                    <tr><th>{{ $en ? 'Estimated expenses' : 'Despesas estimadas' }}</th><td>{{ $vm->currency }} {{ $vm->money($vm->expenses) }}</td></tr>
                @endif
                <tr><th>Subtotal</th><td>{{ $vm->currency }} {{ $vm->money($vm->subtotal) }}</td></tr>
                <tr><th>{{ $en ? 'VAT' : 'IVA' }} ({{ $vm->money($vm->vatRate) }}%)</th><td>{{ $vm->currency }} {{ $vm->money($vm->vat) }}</td></tr>
                <tr class="proposal-total-row"><th>{{ $en ? 'Estimated total' : 'Total estimado' }}</th><td>{{ $vm->currency }} {{ $vm->money($vm->total) }}</td></tr>
            </tbody>
        </table>
    @else
        <div class="proposal-no-investment">
            <span>{{ $en ? 'Investment to be confirmed' : 'Investimento a confirmar' }}</span>
            <h3>{{ $en ? 'The final value must be inserted before submission to the client.' : 'O valor final deve ser inserido antes da submissão ao cliente.' }}</h3>
            <p>{{ $en
                ? 'This version maintains methodology, deliverables and commercial rationale. For a final proposal, the investment should be presented as a specific project value, avoiding generic ranges that weaken the commercial decision.'
                : 'Esta versão mantém metodologia, entregáveis e racional comercial. Para uma proposta final, o investimento deve ser apresentado como valor específico do projecto, evitando faixas genéricas que enfraquecem a decisão comercial.'
            }}</p>
        </div>
    @endif
</x-proposal.page>
