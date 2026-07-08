@extends('announcements.layout')
@section('title', 'Gerador de propostas')

@section('content')
@php
    $packages    = $presets['packages']    ?? [];
    $complexity  = $presets['complexity']  ?? [];
    $profiles    = $presets['profiles']    ?? [];
    $prefill     = $prefill ?? [];
    $isEditing   = isset($editProposal);

    // Helper: old() → prefill → hardcoded default
    $v = fn(string $k, mixed $d = null): mixed => old($k, $prefill[$k] ?? $d);

    $serviceQuestions = collect($services)->mapWithKeys(function ($service) use ($presets, $profiles) {
        $preset = $presets['services'][$service['slug']] ?? [];

        return [
            $service['slug'] => [
                'title'       => $service['title'],
                'questions'   => $preset['questions']    ?? ($service['checklist']   ?? []),
                'approaches'  => $preset['approaches']   ?? [],
                'modules'     => $preset['modules']      ?? [],
                'deliverables'=> $preset['deliverables'] ?? ($service['deliverables'] ?? []),
                'profiles'    => collect($preset['profiles'] ?? [])
                    ->map(fn ($key) => ['key' => $key, 'text' => $profiles[$key] ?? $key])
                    ->values()
                    ->all(),
                'pricing' => $preset['pricing'] ?? [],
                'scope'   => $service['value'] ?? $service['short'],
            ],
        ];
    });

    $oldSelection = [
        'service'              => $v('service_slug'),
        'selected_approaches'  => $v('selected_approaches',  []),
        'selected_modules'     => $v('selected_modules',     []),
        'selected_deliverables'=> $v('selected_deliverables',[]),
        'selected_profiles'    => $v('selected_profiles',    []),
    ];

    $proposalBuilderData = [
        'serviceQuestions' => $serviceQuestions,
        'packages'         => $packages,
        'complexity'       => $complexity,
        'oldSelection'     => $oldSelection,
    ];

    $hasOldOptional = $v('objectives') || $v('scope') || $v('methodology') || $v('deliverables')
        || $v('timeline') || $v('team') || $v('assumptions') || $v('out_of_scope')
        || $v('payment_terms') || $v('financial_notes');
@endphp
<main class="announcement-dashboard">
    <header class="announcement-admin-header">
        <div>
            <a class="announcement-admin-logo" href="{{ route('home') }}"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"></a>
            <div><span class="eyebrow">PORTAL BD</span><h1>{{ $isEditing ? 'Editar proposta' : 'Gerador de propostas' }}</h1></div>
        </div>
        <div class="announcement-admin-user">
            <a class="announcement-admin-link" href="{{ route('announcements.dashboard') }}">Anúncios</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.events.index') }}">Eventos</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.schedule.index') }}">Agenda</a>
            <a class="announcement-admin-link is-active" href="{{ route('collaborator.proposals.index') }}">Nova proposta</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.proposals.saved') }}">Guardadas@if($recentProposals->isNotEmpty()) <span class="proposal-count-badge">{{ $recentProposals->count() }}</span>@endif</a>
            <span>{{ $admin->name }}</span>
            <form method="POST" action="{{ route('announcements.logout') }}">@csrf<button type="submit">Sair</button></form>
        </div>
    </header>

    <div class="announcement-admin-shell">
        @if ($errors->any())
            <div class="alert-error" role="alert">Há campos por corrigir. Veja as mensagens no formulário.</div>
        @endif

        @if ($isEditing)
            <div class="proposal-edit-notice">
                <div>
                    <strong>A editar:</strong> {{ $editProposal->reference }} — {{ $editProposal->client_name }}
                    <span class="proposal-status-badge proposal-status-{{ $editProposal->statusColor() }}">{{ $editProposal->statusLabel() }}</span>
                </div>
                <a href="{{ route('collaborator.proposals.show', $editProposal) }}" class="proposal-edit-back">← Ver proposta actual</a>
            </div>
        @endif

        <div data-draft-notice class="proposal-draft-notice" hidden>
            <p>Rascunho recuperado: <strong data-draft-client></strong> — guardado em <strong data-draft-time></strong>.</p>
            <div class="proposal-draft-actions">
                <button type="button" data-draft-restore>Restaurar</button>
                <button type="button" data-draft-clear>Descartar</button>
            </div>
        </div>

        <section class="proposal-builder-layout">
            <form method="POST" action="{{ route('collaborator.proposals.generate') }}" class="announcement-panel announcement-form proposal-builder-form">
                @csrf
                @if ($isEditing)
                    <input type="hidden" name="_edit_proposal_id" value="{{ $editProposal->id }}">
                @endif
                <div class="announcement-panel-heading">
                    <span class="eyebrow">PROPOSTA ASSISTIDA</span>
                    <h2>{{ $isEditing ? 'Actualizar e regenerar proposta' : 'Gerar proposta robusta sem escrever demais' }}</h2>
                    <p>Escolha o serviço, confirme as opções técnicas recomendadas e preencha apenas o contexto específico do cliente.</p>
                </div>

                <div class="field-row">
                    <label><span>Serviço *</span><select name="service_slug" required data-proposal-service>
                        @foreach ($services as $service)
                            <option value="{{ $service['slug'] }}" @selected($v('service_slug') === $service['slug'])>{{ $service['title'] }}</option>
                        @endforeach
                    </select>@error('service_slug')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Referência</span><input name="proposal_reference" value="{{ $v('proposal_reference', $defaults['proposal_reference']) }}">@error('proposal_reference')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Pacote comercial *</span><select name="pricing_package" required data-proposal-package>
                        @foreach ($packages as $key => $package)
                            <option value="{{ $key }}" @selected($v('pricing_package', 'implementacao') === $key)>{{ $package['label'] }}</option>
                        @endforeach
                    </select>@error('pricing_package')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Complexidade *</span><select name="complexity_level" required data-proposal-complexity>
                        @foreach ($complexity as $key => $label)
                            <option value="{{ $key }}" @selected($v('complexity_level', 'media') === $key)>{{ \Illuminate\Support\Str::before($label, ' —') }}</option>
                        @endforeach
                    </select>@error('complexity_level')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Data *</span><input type="date" name="proposal_date" value="{{ $v('proposal_date', $defaults['proposal_date']) }}" required>@error('proposal_date')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Validade</span><input type="date" name="valid_until" value="{{ $v('valid_until', $defaults['valid_until']) }}">@error('valid_until')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Cliente / organização *</span><input name="client_name" value="{{ $v('client_name') }}" required>@error('client_name')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Sector</span><input name="client_industry" value="{{ $v('client_industry') }}" placeholder="Ex.: energia, banca, ONG, indústria...">@error('client_industry')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Pessoa de contacto</span><input name="client_contact" value="{{ $v('client_contact') }}">@error('client_contact')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Cargo</span><input name="client_position" value="{{ $v('client_position') }}">@error('client_position')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Email</span><input type="email" name="client_email" value="{{ $v('client_email') }}">@error('client_email')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Localização</span><input name="client_location" value="{{ $v('client_location', 'Moçambique') }}">@error('client_location')<small>{{ $message }}</small>@enderror</label>
                </div>

                <label><span>Imagem de capa externa</span><input type="url" name="cover_image_url" value="{{ $v('cover_image_url') }}" placeholder="Opcional: link público de imagem institucional">@error('cover_image_url')<small>{{ $message }}</small>@enderror
                    <div data-cover-preview class="proposal-cover-preview" hidden></div>
                </label>

                <label><span>Desafio / contexto do cliente *</span><textarea name="challenge" rows="5" required data-char-max="2500" placeholder="Descreva a dor real, oportunidade, decisão ou risco que motivou a proposta.">{{ $v('challenge') }}</textarea>@error('challenge')<small>{{ $message }}</small>@enderror
                    <span class="field-counter" data-char-counter></span>
                </label>
                <label><span>Leitura do cliente</span><textarea name="client_insight" rows="3" data-char-max="1800" placeholder="Opcional: 2 ou 3 linhas sobre o momento do cliente, operação, crescimento, urgência ou risco. Se ficar em branco, o sistema cria uma leitura automática.">{{ $v('client_insight') }}</textarea>@error('client_insight')<small>{{ $message }}</small>@enderror
                    <span class="field-counter" data-char-counter></span>
                </label>

                <section class="proposal-preset-section">
                    <div class="proposal-preset-head">
                        <div><span class="eyebrow">ABORDAGEM TÉCNICA</span><h3>Opções recomendadas</h3>
                        <p>Já vêm marcadas com base no serviço. Desmarque apenas o que não se aplica.</p></div>
                        <button type="button" class="proposal-select-all-btn" data-select-all>Todos / Nenhum</button>
                    </div>
                    <div class="proposal-option-grid" data-proposal-approaches></div>
                </section>

                <section class="proposal-preset-section">
                    <div class="proposal-preset-head">
                        <div><span class="eyebrow">ESCOPO</span><h3>Módulos incluídos</h3></div>
                        <button type="button" class="proposal-select-all-btn" data-select-all>Todos / Nenhum</button>
                    </div>
                    <div class="proposal-option-grid" data-proposal-modules></div>
                </section>

                <section class="proposal-preset-section">
                    <div class="proposal-preset-head">
                        <div><span class="eyebrow">ENTREGÁVEIS</span><h3>Produtos da proposta</h3></div>
                        <button type="button" class="proposal-select-all-btn" data-select-all>Todos / Nenhum</button>
                    </div>
                    <div class="proposal-option-grid" data-proposal-deliverable-options></div>
                </section>

                <section class="proposal-preset-section">
                    <div class="proposal-preset-head">
                        <div><span class="eyebrow">EQUIPA</span><h3>Perfis técnicos recomendados</h3></div>
                        <button type="button" class="proposal-select-all-btn" data-select-all>Todos / Nenhum</button>
                    </div>
                    <div class="proposal-option-grid proposal-option-grid-compact" data-proposal-profile-options></div>
                </section>

                <details class="proposal-optional-section" {{ $hasOldOptional ? 'open' : '' }}>
                    <summary>
                        <strong>Personalizar conteúdo <span>(opcional)</span></strong>
                        <span>O sistema preenche automaticamente objectivos, metodologia, entregáveis, equipa e notas financeiras com base nas opções acima seleccionadas</span>
                    </summary>
                    <div class="proposal-optional-inner">
                        <label><span>Objectivos da intervenção</span><textarea name="objectives" rows="4" placeholder="Opcional: escreva apenas se quiser substituir os objectivos automáticos.">{{ $v('objectives') }}</textarea>@error('objectives')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Âmbito técnico</span><textarea name="scope" rows="5" data-proposal-scope placeholder="Opcional: deixe vazio para o sistema construir o âmbito com base no serviço e nos módulos seleccionados.">{{ $v('scope') }}</textarea>@error('scope')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Metodologia / abordagem</span><textarea name="methodology" rows="5" placeholder="Opcional: o sistema usa a abordagem técnica seleccionada.">{{ $v('methodology') }}</textarea>@error('methodology')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Entregáveis</span><textarea name="deliverables" rows="5" data-proposal-deliverables placeholder="Opcional: deixe vazio para o sistema usar os entregáveis seleccionados.">{{ $v('deliverables') }}</textarea>@error('deliverables')<small>{{ $message }}</small>@enderror</label>

                        <div class="field-row">
                            <label><span>Cronograma</span><textarea name="timeline" rows="4" placeholder="Ex.: 4 a 6 semanas após adjudicação.">{{ $v('timeline') }}</textarea>@error('timeline')<small>{{ $message }}</small>@enderror</label>
                            <label><span>Equipa proposta</span><textarea name="team" rows="4" placeholder="Opcional: use quando quiser indicar nomes específicos.">{{ $v('team') }}</textarea>@error('team')<small>{{ $message }}</small>@enderror</label>
                        </div>

                        <label><span>Premissas e responsabilidades do cliente</span><textarea name="assumptions" rows="4" placeholder="Opcional: acessos, documentos, interlocutores, aprovações...">{{ $v('assumptions') }}</textarea>@error('assumptions')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Fora do âmbito</span><textarea name="out_of_scope" rows="3" placeholder="Opcional: actividades que devem ficar fora da proposta.">{{ $v('out_of_scope') }}</textarea>@error('out_of_scope')<small>{{ $message }}</small>@enderror</label>

                        <label><span>Condições de pagamento</span><textarea name="payment_terms" rows="3">{{ $v('payment_terms') }}</textarea>@error('payment_terms')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Notas financeiras</span><textarea name="financial_notes" rows="3" placeholder="Opcional: o sistema inclui pacote, complexidade e factores de preço.">{{ $v('financial_notes') }}</textarea>@error('financial_notes')<small>{{ $message }}</small>@enderror</label>
                    </div>
                </details>

                <div class="field-row">
                    <label><span>Moeda</span><input name="currency" value="{{ $v('currency', $defaults['currency']) }}" required>@error('currency')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Honorários</span><input type="number" min="0" step="0.01" name="fee" value="{{ $v('fee') }}" placeholder="0.00">@error('fee')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Despesas estimadas</span><input type="number" min="0" step="0.01" name="expenses" value="{{ $v('expenses', 0) }}">@error('expenses')<small>{{ $message }}</small>@enderror</label>
                    <label><span>IVA (%)</span><input type="number" min="0" max="100" step="0.01" name="vat_rate" value="{{ $v('vat_rate', $defaults['vat_rate']) }}">@error('vat_rate')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Preparado por</span><input name="prepared_by" value="{{ $v('prepared_by', $admin->name) }}">@error('prepared_by')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Função</span><input name="prepared_role" value="{{ $v('prepared_role', 'Business Diversity') }}">@error('prepared_role')<small>{{ $message }}</small>@enderror</label>
                </div>

                <button class="button button-primary" type="submit">{{ $isEditing ? 'Regenerar proposta →' : 'Gerar proposta →' }}</button>
            </form>

            <aside class="announcement-panel proposal-question-panel">
                <span class="eyebrow">PAINEL DE DECISÃO</span>
                <h2 data-proposal-question-title>Serviço</h2>
                <p>Confirme rapidamente o que precisa ser perguntado, como o preço deve ser defendido e que equipa técnica faz sentido.</p>

                <div class="proposal-side-block proposal-side-financials">
                    <h3>Resumo financeiro</h3>
                    <div class="proposal-policy-card" data-proposal-financials>
                        <p class="proposal-financial-empty">Preencha os honorários para ver o resumo.</p>
                    </div>
                </div>

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

<script id="proposal-builder-data" type="application/json">
{!! json_encode($proposalBuilderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
</script>
@vite('resources/js/proposal-builder.js')
@endsection
