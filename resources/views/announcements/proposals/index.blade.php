@extends('announcements.layout')
@section('title', 'Gerador de propostas')

@section('content')
@php
    $packages = $presets['packages'] ?? [];
    $complexity = $presets['complexity'] ?? [];
    $profiles = $presets['profiles'] ?? [];

    $serviceQuestions = collect($services)->mapWithKeys(function ($service) use ($presets, $profiles) {
        $preset = $presets['services'][$service['slug']] ?? [];

        return [
            $service['slug'] => [
                'title' => $service['title'],
                'questions' => $preset['questions'] ?? ($service['checklist'] ?? []),
                'approaches' => $preset['approaches'] ?? [],
                'modules' => $preset['modules'] ?? [],
                'deliverables' => $preset['deliverables'] ?? ($service['deliverables'] ?? []),
                'profiles' => collect($preset['profiles'] ?? [])
                    ->map(fn ($key) => ['key' => $key, 'text' => $profiles[$key] ?? $key])
                    ->values()
                    ->all(),
                'pricing' => $preset['pricing'] ?? [],
                'scope' => $service['value'] ?? $service['short'],
            ],
        ];
    });

    $oldSelection = [
        'service' => old('service_slug'),
        'selected_approaches' => old('selected_approaches', []),
        'selected_modules' => old('selected_modules', []),
        'selected_deliverables' => old('selected_deliverables', []),
        'selected_profiles' => old('selected_profiles', []),
    ];
@endphp
<main class="announcement-dashboard">
    <header class="announcement-admin-header">
        <div>
            <a class="announcement-admin-logo" href="{{ route('home') }}"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"></a>
            <div><span class="eyebrow">PORTAL BD</span><h1>Gerador de propostas</h1></div>
        </div>
        <div class="announcement-admin-user">
            <a class="announcement-admin-link" href="{{ route('announcements.dashboard') }}">Anúncios</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.events.index') }}">Eventos</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.schedule.index') }}">Agenda</a>
            <a class="announcement-admin-link is-active" href="{{ route('collaborator.proposals.index') }}">Propostas</a>
            <span>{{ $admin->name }}</span>
            <form method="POST" action="{{ route('announcements.logout') }}">@csrf<button type="submit">Sair</button></form>
        </div>
    </header>

    <div class="announcement-admin-shell">
        @if ($errors->any())
            <div class="alert-error" role="alert">Há campos por corrigir. Veja as mensagens no formulário.</div>
        @endif

        <section class="proposal-builder-layout">
            <form method="POST" action="{{ route('collaborator.proposals.generate') }}" class="announcement-panel announcement-form proposal-builder-form">
                @csrf
                <div class="announcement-panel-heading">
                    <span class="eyebrow">PROPOSTA ASSISTIDA</span>
                    <h2>Gerar proposta robusta sem escrever demais</h2>
                    <p>Escolha o serviço, confirme as opções técnicas recomendadas e preencha apenas o contexto específico do cliente.</p>
                </div>

                <div class="field-row">
                    <label><span>Serviço *</span><select name="service_slug" required data-proposal-service>
                        @foreach ($services as $service)
                            <option value="{{ $service['slug'] }}" @selected(old('service_slug') === $service['slug'])>{{ $service['title'] }}</option>
                        @endforeach
                    </select>@error('service_slug')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Referência</span><input name="proposal_reference" value="{{ old('proposal_reference', $defaults['proposal_reference']) }}">@error('proposal_reference')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Pacote comercial *</span><select name="pricing_package" required data-proposal-package>
                        @foreach ($packages as $key => $package)
                            <option value="{{ $key }}" @selected(old('pricing_package', 'implementacao') === $key)>{{ $package['label'] }}</option>
                        @endforeach
                    </select>@error('pricing_package')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Complexidade *</span><select name="complexity_level" required data-proposal-complexity>
                        @foreach ($complexity as $key => $label)
                            <option value="{{ $key }}" @selected(old('complexity_level', 'media') === $key)>{{ \Illuminate\Support\Str::before($label, ' —') }}</option>
                        @endforeach
                    </select>@error('complexity_level')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Data *</span><input type="date" name="proposal_date" value="{{ old('proposal_date', $defaults['proposal_date']) }}" required>@error('proposal_date')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Validade</span><input type="date" name="valid_until" value="{{ old('valid_until', $defaults['valid_until']) }}">@error('valid_until')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Cliente / organização *</span><input name="client_name" value="{{ old('client_name') }}" required>@error('client_name')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Sector</span><input name="client_industry" value="{{ old('client_industry') }}" placeholder="Ex.: energia, banca, ONG, indústria...">@error('client_industry')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Pessoa de contacto</span><input name="client_contact" value="{{ old('client_contact') }}">@error('client_contact')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Cargo</span><input name="client_position" value="{{ old('client_position') }}">@error('client_position')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Email</span><input type="email" name="client_email" value="{{ old('client_email') }}">@error('client_email')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Localização</span><input name="client_location" value="{{ old('client_location', 'Moçambique') }}">@error('client_location')<small>{{ $message }}</small>@enderror</label>
                </div>

                <label><span>Imagem de capa externa</span><input type="url" name="cover_image_url" value="{{ old('cover_image_url') }}" placeholder="Opcional: link público de imagem institucional">@error('cover_image_url')<small>{{ $message }}</small>@enderror</label>

                <label><span>Desafio / contexto do cliente *</span><textarea name="challenge" rows="5" required placeholder="Descreva a dor real, oportunidade, decisão ou risco que motivou a proposta.">{{ old('challenge') }}</textarea>@error('challenge')<small>{{ $message }}</small>@enderror</label>

                <section class="proposal-preset-section">
                    <div class="proposal-preset-head">
                        <span class="eyebrow">ABORDAGEM TÉCNICA</span>
                        <h3>Opções recomendadas</h3>
                        <p>Já vêm marcadas com base no serviço. Desmarque apenas o que não se aplica.</p>
                    </div>
                    <div class="proposal-option-grid" data-proposal-approaches></div>
                </section>

                <section class="proposal-preset-section">
                    <div class="proposal-preset-head">
                        <span class="eyebrow">ESCOPO</span>
                        <h3>Módulos incluídos</h3>
                    </div>
                    <div class="proposal-option-grid" data-proposal-modules></div>
                </section>

                <section class="proposal-preset-section">
                    <div class="proposal-preset-head">
                        <span class="eyebrow">ENTREGÁVEIS</span>
                        <h3>Produtos da proposta</h3>
                    </div>
                    <div class="proposal-option-grid" data-proposal-deliverable-options></div>
                </section>

                <section class="proposal-preset-section">
                    <div class="proposal-preset-head">
                        <span class="eyebrow">EQUIPA</span>
                        <h3>Perfis técnicos recomendados</h3>
                    </div>
                    <div class="proposal-option-grid proposal-option-grid-compact" data-proposal-profile-options></div>
                </section>

                <div class="proposal-context-note">
                    <strong>Campos abaixo são opcionais.</strong>
                    Se ficar em branco, o sistema monta automaticamente objectivos, metodologia, entregáveis, equipa e notas financeiras com base nas opções seleccionadas.
                </div>

                <label><span>Objectivos da intervenção</span><textarea name="objectives" rows="4" placeholder="Opcional: escreva apenas se quiser substituir os objectivos automáticos.">{{ old('objectives') }}</textarea>@error('objectives')<small>{{ $message }}</small>@enderror</label>
                <label><span>Âmbito técnico</span><textarea name="scope" rows="5" data-proposal-scope placeholder="Opcional: o sistema preenche com os módulos seleccionados.">{{ old('scope') }}</textarea>@error('scope')<small>{{ $message }}</small>@enderror</label>
                <label><span>Metodologia / abordagem</span><textarea name="methodology" rows="5" placeholder="Opcional: o sistema usa a abordagem técnica seleccionada.">{{ old('methodology') }}</textarea>@error('methodology')<small>{{ $message }}</small>@enderror</label>
                <label><span>Entregáveis</span><textarea name="deliverables" rows="5" data-proposal-deliverables placeholder="Opcional: o sistema usa os entregáveis seleccionados.">{{ old('deliverables') }}</textarea>@error('deliverables')<small>{{ $message }}</small>@enderror</label>

                <div class="field-row">
                    <label><span>Cronograma</span><textarea name="timeline" rows="4" placeholder="Ex.: 4 a 6 semanas após adjudicação.">{{ old('timeline') }}</textarea>@error('timeline')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Equipa proposta</span><textarea name="team" rows="4" placeholder="Opcional: use quando quiser indicar nomes específicos.">{{ old('team') }}</textarea>@error('team')<small>{{ $message }}</small>@enderror</label>
                </div>

                <label><span>Premissas e responsabilidades do cliente</span><textarea name="assumptions" rows="4" placeholder="Opcional: acessos, documentos, interlocutores, aprovações...">{{ old('assumptions') }}</textarea>@error('assumptions')<small>{{ $message }}</small>@enderror</label>
                <label><span>Fora do âmbito</span><textarea name="out_of_scope" rows="3" placeholder="Opcional: actividades que devem ficar fora da proposta.">{{ old('out_of_scope') }}</textarea>@error('out_of_scope')<small>{{ $message }}</small>@enderror</label>

                <div class="field-row">
                    <label><span>Moeda</span><input name="currency" value="{{ old('currency', $defaults['currency']) }}" required>@error('currency')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Honorários</span><input type="number" min="0" step="0.01" name="fee" value="{{ old('fee') }}" placeholder="0.00">@error('fee')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Despesas estimadas</span><input type="number" min="0" step="0.01" name="expenses" value="{{ old('expenses', 0) }}">@error('expenses')<small>{{ $message }}</small>@enderror</label>
                    <label><span>IVA (%)</span><input type="number" min="0" max="100" step="0.01" name="vat_rate" value="{{ old('vat_rate', $defaults['vat_rate']) }}">@error('vat_rate')<small>{{ $message }}</small>@enderror</label>
                </div>

                <label><span>Condições de pagamento</span><textarea name="payment_terms" rows="3">{{ old('payment_terms') }}</textarea>@error('payment_terms')<small>{{ $message }}</small>@enderror</label>
                <label><span>Notas financeiras</span><textarea name="financial_notes" rows="3" placeholder="Opcional: o sistema inclui pacote, complexidade e factores de preço.">{{ old('financial_notes') }}</textarea>@error('financial_notes')<small>{{ $message }}</small>@enderror</label>

                <div class="field-row">
                    <label><span>Preparado por</span><input name="prepared_by" value="{{ old('prepared_by', $admin->name) }}">@error('prepared_by')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Função</span><input name="prepared_role" value="{{ old('prepared_role', 'Business Diversity') }}">@error('prepared_role')<small>{{ $message }}</small>@enderror</label>
                </div>

                <button class="button button-primary" type="submit">Gerar proposta <span>→</span></button>
            </form>

            <aside class="announcement-panel proposal-question-panel">
                <span class="eyebrow">PAINEL DE DECISÃO</span>
                <h2 data-proposal-question-title>Serviço</h2>
                <p>Confirme rapidamente o que precisa ser perguntado, como o preço deve ser defendido e que equipa técnica faz sentido.</p>

                <div class="proposal-side-block">
                    <h3>Questões essenciais</h3>
                    <div class="proposal-question-list" data-proposal-questions></div>
                </div>

                <div class="proposal-side-block">
                    <h3>Política de preços</h3>
                    <div class="proposal-policy-card" data-proposal-pricing></div>
                </div>

                <div class="proposal-side-block">
                    <h3>Perfis indicados</h3>
                    <div class="proposal-profile-list" data-proposal-profiles></div>
                </div>
            </aside>
        </section>
    </div>
</main>

<script>
(() => {
    const data = @json($serviceQuestions);
    const packages = @json($packages);
    const complexity = @json($complexity);
    const previous = @json($oldSelection);
    const select = document.querySelector('[data-proposal-service]');
    const packageSelect = document.querySelector('[data-proposal-package]');
    const complexitySelect = document.querySelector('[data-proposal-complexity]');
    const title = document.querySelector('[data-proposal-question-title]');
    const questions = document.querySelector('[data-proposal-questions]');
    const pricing = document.querySelector('[data-proposal-pricing]');
    const profileSummary = document.querySelector('[data-proposal-profiles]');
    const scope = document.querySelector('[data-proposal-scope]');
    const deliverables = document.querySelector('[data-proposal-deliverables]');
    const targets = {
        approaches: document.querySelector('[data-proposal-approaches]'),
        modules: document.querySelector('[data-proposal-modules]'),
        deliverables: document.querySelector('[data-proposal-deliverable-options]'),
        profiles: document.querySelector('[data-proposal-profile-options]'),
    };

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const previousFor = (key) => previous.service === select.value && Array.isArray(previous[key]) ? previous[key] : [];

    const renderChecks = (target, name, items, previousValues = [], profileMode = false) => {
        if (!target) return;

        target.innerHTML = items.map((item, index) => {
            const value = profileMode ? item.key : item;
            const text = profileMode ? item.text : item;
            const checked = previousValues.length ? previousValues.includes(value) : true;

            return `
                <label class="proposal-check-card">
                    <input type="checkbox" name="${name}[]" value="${escapeHtml(value)}" ${checked ? 'checked' : ''}>
                    <span>${String(index + 1).padStart(2, '0')}</span>
                    <p>${escapeHtml(text)}</p>
                </label>
            `;
        }).join('');
    };

    const selectedLabels = (target) => Array.from(target?.querySelectorAll('input:checked') ?? [])
        .map(input => input.closest('label')?.querySelector('p')?.textContent.trim())
        .filter(Boolean);

    const syncOptionalText = () => {
        const item = data[select.value];
        if (!item) return;

        if (scope && !scope.value.trim()) {
            const modules = selectedLabels(targets.modules);
            scope.value = [item.scope, modules.length ? `\nMódulos previstos:\n${modules.join('\n')}` : ''].join('\n').trim();
        }

        if (deliverables && !deliverables.value.trim()) {
            const selected = selectedLabels(targets.deliverables);
            deliverables.value = selected.join('\n');
        }
    };

    const renderSidePanel = (item) => {
        title.textContent = item.title;

        questions.innerHTML = (item.questions ?? []).map((question, index) => `
            <article><span>${String(index + 1).padStart(2, '0')}</span><p>${escapeHtml(question)}</p></article>
        `).join('');

        const packageData = packages[packageSelect.value] ?? {};
        const complexityLabel = complexity[complexitySelect.value] ?? '';
        const drivers = item.pricing?.drivers ?? [];
        const ranges = item.pricing?.ranges ?? {};

        pricing.innerHTML = `
            <strong>${escapeHtml(packageData.label ?? 'Pacote')}</strong>
            <p>${escapeHtml(packageData.pricing ?? item.pricing?.base ?? '')}</p>
            <small>${escapeHtml(complexityLabel)}</small>
            ${drivers.length ? `<ul>${drivers.map(driver => `<li>${escapeHtml(driver)}</li>`).join('')}</ul>` : ''}
            ${Object.keys(ranges).length ? `<div class="proposal-price-ranges">${Object.entries(ranges).map(([label, value]) => `<span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong>`).join('')}</div>` : ''}
        `;

        profileSummary.innerHTML = (item.profiles ?? []).map((profile) => `
            <article><span>${escapeHtml(profile.key)}</span><p>${escapeHtml(profile.text)}</p></article>
        `).join('');
    };

    const render = () => {
        const item = data[select.value];
        if (!item) return;

        renderChecks(targets.approaches, 'selected_approaches', item.approaches ?? [], previousFor('selected_approaches'));
        renderChecks(targets.modules, 'selected_modules', item.modules ?? [], previousFor('selected_modules'));
        renderChecks(targets.deliverables, 'selected_deliverables', item.deliverables ?? [], previousFor('selected_deliverables'));
        renderChecks(targets.profiles, 'selected_profiles', item.profiles ?? [], previousFor('selected_profiles'), true);

        renderSidePanel(item);
        syncOptionalText();
    };

    select?.addEventListener('change', () => {
        if (scope) scope.value = '';
        if (deliverables) deliverables.value = '';
        render();
    });
    packageSelect?.addEventListener('change', () => renderSidePanel(data[select.value]));
    complexitySelect?.addEventListener('change', () => renderSidePanel(data[select.value]));
    Object.values(targets).forEach(target => target?.addEventListener('change', syncOptionalText));
    render();
})();
</script>
@endsection
