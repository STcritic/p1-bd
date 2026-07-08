<?php

namespace App\Modules\Collaborator\Proposal\Services;

class FinancialCalculator
{
    public function calculate(float $fee, float $expenses, float $vatRate): array
    {
        $subtotal = $fee + $expenses;
        $vat      = round($subtotal * ($vatRate / 100), 2);
        $total    = $subtotal + $vat;

        return [
            'fee'           => $fee,
            'expenses'      => $expenses,
            'vat_rate'      => $vatRate,
            'subtotal'      => $subtotal,
            'vat'           => $vat,
            'total'         => $total,
            'has_investment'=> $total > 0,
        ];
    }

    public function format(float $amount): string
    {
        return number_format($amount, 2, ',', ' ');
    }
}
