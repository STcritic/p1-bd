<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth handled by announcement.admin middleware
    }

    public function rules(): array
    {
        $services   = collect(config('service_guides.pt', []));
        $packages   = config('proposal_presets.packages', []);
        $complexity = config('proposal_presets.complexity', []);

        return [
            '_edit_proposal_id'     => ['nullable', 'integer', 'exists:proposals,id'],
            'service_slug'          => ['required', 'string', Rule::in($services->pluck('slug')->all())],
            'proposal_reference'    => ['nullable', 'string', 'max:80'],
            'proposal_date'         => ['required', 'date'],
            'valid_until'           => ['nullable', 'date', 'after_or_equal:proposal_date'],
            'client_name'           => ['required', 'string', 'max:190'],
            'client_contact'        => ['nullable', 'string', 'max:190'],
            'client_position'       => ['nullable', 'string', 'max:190'],
            'client_email'          => ['nullable', 'email', 'max:190'],
            'client_location'       => ['nullable', 'string', 'max:190'],
            'client_industry'       => ['nullable', 'string', 'max:190'],
            'client_insight'        => ['nullable', 'string', 'max:1800'],
            'prepared_by'           => ['nullable', 'string', 'max:190'],
            'prepared_role'         => ['nullable', 'string', 'max:190'],
            'cover_image_url'       => ['nullable', 'url', 'max:1000'],
            'challenge'             => ['required', 'string', 'max:2500'],
            'selected_approaches'   => ['nullable', 'array'],
            'selected_approaches.*' => ['string', 'max:500'],
            'selected_modules'      => ['nullable', 'array'],
            'selected_modules.*'    => ['string', 'max:500'],
            'selected_deliverables' => ['nullable', 'array'],
            'selected_deliverables.*'=> ['string', 'max:500'],
            'selected_profiles'     => ['nullable', 'array'],
            'selected_profiles.*'   => ['string', 'max:80'],
            'pricing_package'       => ['required', 'string', Rule::in(array_keys($packages))],
            'complexity_level'      => ['required', 'string', Rule::in(array_keys($complexity))],
            'objectives'            => ['nullable', 'string', 'max:2500'],
            'scope'                 => ['nullable', 'string', 'max:3000'],
            'methodology'           => ['nullable', 'string', 'max:3000'],
            'deliverables'          => ['nullable', 'string', 'max:3000'],
            'timeline'              => ['nullable', 'string', 'max:1500'],
            'team'                  => ['nullable', 'string', 'max:1500'],
            'assumptions'           => ['nullable', 'string', 'max:2500'],
            'out_of_scope'          => ['nullable', 'string', 'max:1500'],
            'currency'              => ['required', 'string', 'max:8'],
            'fee'                    => ['nullable', 'numeric', 'min:0'],
            'expenses'               => ['nullable', 'numeric', 'min:0'],
            'vat_rate'               => ['nullable', 'numeric', 'min:0', 'max:100'],
            'expense_items'              => ['nullable', 'array'],
            'expense_items.*.label'      => ['nullable', 'string', 'max:120'],
            'expense_items.*.amount'     => ['nullable', 'numeric', 'min:0'],
            'recruit_type'               => ['nullable', 'in:standard,headhunting'],
            'candidate_salary'           => ['nullable', 'numeric', 'min:0'],
            'policy_band_0_rate'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'policy_band_0_days'         => ['nullable', 'integer', 'min:0'],
            'policy_band_1_rate'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'policy_band_1_days'         => ['nullable', 'integer', 'min:0'],
            'policy_band_2_rate'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'policy_band_2_days'         => ['nullable', 'integer', 'min:0'],
            'policy_band_3_rate_min'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'policy_band_3_rate_max'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'policy_band_3_days'         => ['nullable', 'integer', 'min:0'],
            'policy_mass_note'           => ['nullable', 'string', 'max:500'],
            'policy_guarantee_note'      => ['nullable', 'string', 'max:1000'],
            'payment_terms'         => ['nullable', 'string', 'max:1500'],
            'financial_notes'       => ['nullable', 'string', 'max:1500'],
        ];
    }
}
