@extends('announcements.layout')
@php $en = ($collabLang ?? 'pt') === 'en'; @endphp
@section('title', $en ? 'New opportunity' : 'Nova oportunidade')

@section('content')
<main class="announcement-dashboard">
@include('announcements.partials.nav', ['active' => 'oportunidades', 'pageTitle' => $en ? 'New opportunity' : 'Nova oportunidade'])

<div class="announcement-admin-shell opp-page opp-page--narrow">

    <div class="opp-page-header">
        <div>
            <a href="{{ route('collaborator.opportunities.index') }}" class="opp-back">
                ← {{ $en ? 'Opportunities' : 'Oportunidades' }}
            </a>
            <p>{{ $en ? 'The system will guide the process from here to award.' : 'O sistema guiará o processo desde aqui até à adjudicação.' }}</p>
        </div>
    </div>

    <form action="{{ route('collaborator.opportunities.store') }}" method="POST" class="opp-form">
        @csrf

        {{-- Step indicator --}}
        <div class="opp-form-step-label">
            <span>{{ $en ? 'Step 1 of 1' : 'Passo 1 de 1' }}</span>
            <strong>{{ $en ? 'Opportunity identification' : 'Identificação da oportunidade' }}</strong>
            <em>≈ 3 {{ $en ? 'min' : 'minutos' }}</em>
        </div>

        <fieldset class="opp-fieldset">
            <legend>{{ $en ? 'Service and client' : 'Serviço e cliente' }}</legend>

            <div class="opp-field">
                <label for="service_slug">{{ $en ? 'Service *' : 'Serviço *' }}</label>
                <select name="service_slug" id="service_slug" required>
                    <option value="">{{ $en ? 'Select service' : 'Seleccione o serviço' }}</option>
                    @foreach($services as $service)
                        <option value="{{ $service['slug'] }}" @selected(old('service_slug') === $service['slug'])>
                            {{ $service['title'] }}
                        </option>
                    @endforeach
                </select>
                @error('service_slug')<span class="opp-error">{{ $message }}</span>@enderror
            </div>

            <div class="opp-field-row">
                <div class="opp-field">
                    <label for="client_name">{{ $en ? 'Client / company name *' : 'Nome do cliente / empresa *' }}</label>
                    <input type="text" name="client_name" id="client_name"
                           value="{{ old('client_name') }}" required maxlength="190"
                           placeholder="{{ $en ? 'E.g. Acme Industries Ltd' : 'Ex: Acme Industries, Lda' }}">
                    @error('client_name')<span class="opp-error">{{ $message }}</span>@enderror
                </div>

                <div class="opp-field">
                    <label for="client_industry">{{ $en ? 'Sector' : 'Sector' }}</label>
                    <input type="text" name="client_industry" id="client_industry"
                           value="{{ old('client_industry') }}" maxlength="190"
                           placeholder="{{ $en ? 'E.g. Industrial, Finance, Retail' : 'Ex: Industrial, Financeiro, Retalho' }}">
                </div>
            </div>

            <div class="opp-field-row">
                <div class="opp-field">
                    <label for="client_contact">{{ $en ? 'Contact (name)' : 'Contacto (nome)' }}</label>
                    <input type="text" name="client_contact" id="client_contact"
                           value="{{ old('client_contact') }}" maxlength="190"
                           placeholder="{{ $en ? 'Contact person name' : 'Nome do interlocutor' }}">
                </div>

                <div class="opp-field">
                    <label for="client_email">{{ $en ? 'Client e-mail' : 'E-mail do cliente' }}</label>
                    <input type="email" name="client_email" id="client_email"
                           value="{{ old('client_email') }}" maxlength="190"
                           placeholder="{{ $en ? 'For sending the diagnostic link' : 'Para envio do link de diagnóstico' }}">
                </div>
            </div>
        </fieldset>

        <fieldset class="opp-fieldset">
            <legend>{{ $en ? 'Internal notes (visible to team only)' : 'Notas internas (visíveis apenas para a equipa)' }}</legend>

            <div class="opp-field">
                <label for="internal_notes">{{ $en ? 'Initial notes' : 'Notas iniciais' }}</label>
                <textarea name="internal_notes" id="internal_notes" rows="4"
                          placeholder="{{ $en ? 'Lead origin context, initial observations, sensitivities...' : 'Contexto da origem do lead, observações iniciais, sensibilidades...' }}">{{ old('internal_notes') }}</textarea>
            </div>

            <div class="opp-field">
                <label for="expected_close_at">{{ $en ? 'Expected close date' : 'Data esperada de fecho' }}</label>
                <input type="date" name="expected_close_at" id="expected_close_at"
                       value="{{ old('expected_close_at') }}">
            </div>
        </fieldset>

        <div class="opp-form-actions">
            <a href="{{ route('collaborator.opportunities.index') }}" class="btn-ghost">
                {{ $en ? 'Cancel' : 'Cancelar' }}
            </a>
            <button type="submit" class="btn-primary">
                {{ $en ? 'Create opportunity →' : 'Criar oportunidade →' }}
            </button>
        </div>
    </form>
</div>{{-- .opp-page --}}
</main>
@endsection
