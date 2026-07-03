@extends('announcements.layout')
@section('title', 'Proposta gerada')

@section('content')
@php
    $money = fn ($amount) => number_format((float) $amount, 2, ',', ' ');
    $proposalDate = \Illuminate\Support\Carbon::parse($proposal['proposal_date'])->format('d/m/Y');
    $validUntil = \Illuminate\Support\Carbon::parse($proposal['valid_until'])->format('d/m/Y');
    $package = $proposal['pricing_package'] ?? [];
    $pricingPolicy = $proposal['pricing_policy'] ?? [];
    $company = $identity['company'] ?? [];
    $bank = $identity['bank_details'] ?? [];
    $hasBank = collect(['bank_name', 'account_holder', 'account_number', 'nib', 'swift'])->contains(fn ($key) => filled($bank[$key] ?? null));
    $clients = collect($identity['clients'] ?? [])->take(6);
    $metrics = collect($identity['credibility_metrics'] ?? [])->take(4);
    $hasInvestment = (bool) ($proposal['has_investment'] ?? false);
@endphp
<main class="proposal-preview-shell">
    <div class="proposal-toolbar no-print">
        <a href="{{ route('collaborator.proposals.index') }}">← Nova proposta</a>
        <button type="button" onclick="window.print()">Guardar como PDF / Imprimir</button>
    </div>

    <article class="proposal-document proposal-premium-document">
        <section class="proposal-cover proposal-page proposal-cover-premium proposal-cover-editorial">
            <div class="proposal-cover-watermark" aria-hidden="true">BD</div>
            <div class="proposal-cover-top">
                <img class="proposal-cover-logo" src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
                <div class="proposal-cover-reference">
                    <span>{{ $proposal['proposal_reference'] }}</span>
                    <strong>{{ $proposalDate }}</strong>
                </div>
            </div>
            <div class="proposal-cover-title">
                <span>Proposta técnica e financeira</span>
                <h1>{{ $service['title'] }}</h1>
                <p>Preparada para {{ $proposal['client_name'] }}</p>
            </div>
            <div class="proposal-cover-photo">
                <img src="{{ $proposal['cover_image_url'] }}" alt="">
            </div>
            <div class="proposal-cover-band">
                <div>
                    <span>Cliente</span>
                    <strong>{{ $proposal['client_name'] }}</strong>
                </div>
                <div>
                    <span>Modelo de entrega</span>
                    <strong>{{ $package['label'] ?? 'Proposta personalizada' }}</strong>
                </div>
                <div>
                    <span>Validade</span>
                    <strong>Até {{ $validUntil }}</strong>
                </div>
            </div>
        </section>

        <section class="proposal-page proposal-index-page">
            <div class="proposal-page-head">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
                <span>{{ $proposal['proposal_reference'] }}</span>
            </div>
            <div class="proposal-section-title">
                <span>00</span>
                <div>
                    <small>Índice executivo</small>
                    <h2>Documento orientado para decisão</h2>
                </div>
            </div>
            <div class="proposal-index-grid proposal-index-grid-compact">
                @foreach ([
                    ['01', 'Carta ao cliente', 'Mensagem personalizada e entendimento da necessidade.'],
                    ['02', 'Sumário executivo', 'Resposta proposta, proposta de valor e dados-chave.'],
                    ['03', 'Porque é crítico', 'Riscos, impacto e razão estratégica para agir.'],
                    ['04', 'Sobre a BD', 'Experiência, princípios de qualidade e forma de trabalho.'],
                    ['05', 'Âmbito', 'Objectivos, módulos e entregáveis.'],
                    ['06', 'Processo e cronograma', 'Fluxo visual, roadmap e calendário de execução.'],
                    ['07', 'Utilidade prática', 'O que o cliente recebe e como usará os entregáveis.'],
                    ['08', 'Indicadores', 'KPIs de sucesso e diferenciais da Business Diversity.'],
                    ['09', 'Financeiro', 'Investimento, racional comercial, termos e garantias.'],
                    ['10', 'Credenciais', 'Clientes, equipa, métricas institucionais e certificados aplicáveis.'],
                    ['11', 'Aceitação', 'Fecho comercial, validade e assinatura.'],
                ] as [$number, $title, $text])
                    <article>
                        <span>{{ $number }}</span>
                        <div><h3>{{ $title }}</h3><p>{{ $text }}</p></div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="proposal-page proposal-letter-page">
            <div class="proposal-page-head">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
                <span>{{ $proposal['client_name'] }}</span>
            </div>
            <div class="proposal-section-title">
                <span>01</span>
                <div>
                    <small>Carta personalizada</small>
                    <h2>Preparada para a realidade da {{ $proposal['client_name'] }}</h2>
                </div>
            </div>
            <div class="proposal-letter-card">
                <p>{{ $proposal['personal_letter'] }}</p>
                <div class="proposal-letter-signature">
                    <span>Business Diversity Consultoria Empresarial</span>
                    <strong>{{ $proposal['prepared_by'] }}</strong>
                    <small>{{ $proposal['prepared_role'] }}</small>
                </div>
            </div>
            <div class="proposal-context-panel">
                <span>Entendimento preliminar</span>
                <p>{{ $proposal['contextual_summary'] }}</p>
            </div>
        </section>

        <section class="proposal-page proposal-summary-page">
            <div class="proposal-section-title">
                <span>02</span>
                <div>
                    <small>Sumário executivo</small>
                    <h2>Uma intervenção desenhada para criar valor mensurável</h2>
                </div>
            </div>

            <div class="proposal-editorial-visual proposal-summary-visual">
                <img src="{{ $proposal['cover_image_url'] }}" alt="">
                <div>
                    <span>Proposta de valor</span>
                    <strong>{{ $service['value'] }}</strong>
                    <p>{{ $proposal['contextual_summary'] }}</p>
                </div>
            </div>

            <div class="proposal-two-columns">
                <div class="proposal-block proposal-highlight">
                    <h3>Resposta proposta</h3>
                    <p>A Business Diversity propõe uma intervenção de {{ strtolower($service['title']) }} orientada para reduzir riscos, acelerar decisões, produzir entregáveis utilizáveis e apoiar a equipa do cliente na implementação prática.</p>
                </div>
                <div class="proposal-block">
                    <h3>Resultado esperado</h3>
                    <p>{{ $service['value'] }}</p>
                </div>
            </div>

            <div class="proposal-value-strip">
                @foreach (($identity['value_proposition'] ?? []) as $value)
                    <article><span>✓</span><p>{{ $value }}</p></article>
                @endforeach
            </div>

            <div class="proposal-client-grid proposal-snapshot-grid">
                <div><small>Cliente</small><strong>{{ $proposal['client_name'] }}</strong></div>
                @if ($proposal['client_industry'])<div><small>Sector</small><strong>{{ $proposal['client_industry'] }}</strong></div>@endif
                <div><small>Pacote</small><strong>{{ $package['label'] ?? 'Implementação estruturada' }}</strong></div>
                <div><small>Complexidade</small><strong>{{ $proposal['complexity_label'] }}</strong></div>
                @if ($proposal['client_contact'])<div><small>Contacto</small><strong>{{ $proposal['client_contact'] }}</strong></div>@endif
                @if ($proposal['client_location'])<div><small>Localização</small><strong>{{ $proposal['client_location'] }}</strong></div>@endif
            </div>
        </section>

        <section class="proposal-page proposal-critical-page">
            <div class="proposal-section-title">
                <span>03</span>
                <div>
                    <small>Argumento estratégico</small>
                    <h2>{{ $proposal['critical_case']['title'] }}</h2>
                </div>
            </div>

            <div class="proposal-critical-grid">
                <div class="proposal-critical-main">
                    <span>Impacto</span>
                    <p>{{ $proposal['critical_case']['intro'] }}</p>
                </div>
                @foreach ($proposal['critical_case']['items'] as $index => $item)
                    <article>
                        <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <p>{{ $item }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="proposal-page proposal-about-page">
            <div class="proposal-section-title">
                <span>04</span>
                <div>
                    <small>Sobre nós</small>
                    <h2>{{ $company['short_name'] ?? 'Business Diversity' }}</h2>
                </div>
            </div>

            <div class="proposal-about-grid">
                <div class="proposal-about-copy">
                    <p class="proposal-lead">{{ $company['summary'] ?? '' }}</p>
                    <div class="proposal-mini-metrics">
                        @foreach (($company['experience'] ?? []) as $item)
                            <article><strong>{{ \Illuminate\Support\Str::before($item, ' ') }}</strong><span>{{ \Illuminate\Support\Str::after($item, ' ') }}</span></article>
                        @endforeach
                    </div>
                </div>
                <div class="proposal-about-side">
                    <div class="proposal-about-photo-card">
                        <img src="{{ asset('assets/images/About.jpg') }}" alt="Business Diversity">
                    </div>
                    <div class="proposal-block proposal-highlight">
                        <h3>Princípios de qualidade</h3>
                        <ul>
                            @foreach (($identity['quality_principles'] ?? []) as $principle)
                                <li>{{ $principle }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="proposal-values-row">
                @foreach (($company['values'] ?? []) as $value)
                    <span>{{ $value }}</span>
                @endforeach
            </div>
        </section>

        <section class="proposal-page">
            <div class="proposal-section-title">
                <span>05</span>
                <div>
                    <small>Âmbito da proposta</small>
                    <h2>Objectivos, escopo e entregáveis</h2>
                </div>
            </div>

            <div class="proposal-two-columns">
                <div class="proposal-block">
                    <h3>Objectivos da intervenção</h3>
                    <ul>
                        @foreach ($lines($proposal['objectives']) as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="proposal-block proposal-highlight">
                    <h3>Âmbito técnico</h3>
                    <p>{!! nl2br(e($proposal['scope'])) !!}</p>
                </div>
            </div>

            <div class="proposal-two-columns">
                <div class="proposal-block">
                    <h3>Módulos incluídos</h3>
                    <ul>
                        @foreach ($proposal['selected_modules'] as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="proposal-block">
                    <h3>Entregáveis principais</h3>
                    <ul>
                        @foreach ($lines($proposal['deliverables']) as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        <section class="proposal-page">
            <div class="proposal-section-title">
                <span>06</span>
                <div>
                    <small>Processo e cronograma</small>
                    <h2>Como a intervenção será conduzida</h2>
                </div>
            </div>

            <div class="proposal-flow">
                @foreach ($proposal['process_flow'] as $index => $step)
                    <article>
                        <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <strong>{{ $step }}</strong>
                    </article>
                @endforeach
            </div>

            <div class="proposal-roadmap">
                @foreach (($proposal['roadmap'] ?? []) as $index => $phase)
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
        </section>

        <section class="proposal-page">
            <div class="proposal-section-title">
                <span>07</span>
                <div>
                    <small>Cronograma visual</small>
                    <h2>Plano de execução indicativo</h2>
                </div>
            </div>

            <div class="proposal-timeline">
                @foreach ($proposal['timeline_plan'] as $item)
                    <article>
                        <span>{{ $item['period'] }}</span>
                        <div>
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['text'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="proposal-page">
            <div class="proposal-section-title">
                <span>08</span>
                <div>
                    <small>Utilidade prática</small>
                    <h2>O que o cliente recebe no final</h2>
                </div>
            </div>

            <div class="proposal-output-grid">
                @foreach ($proposal['practical_outputs'] as $index => $output)
                    <article>
                        <span>✓</span>
                        <p>{{ $output }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="proposal-page">
            <div class="proposal-section-title">
                <span>09</span>
                <div>
                    <small>KPIs e diferenciais</small>
                    <h2>Como saberemos que a intervenção funcionou</h2>
                </div>
            </div>

            <div class="proposal-two-columns">
                <div class="proposal-block">
                    <h3>Indicadores de sucesso</h3>
                    <div class="proposal-kpi-list">
                        @foreach ($proposal['success_metrics'] as $metric)
                            <p><span></span>{{ $metric }}</p>
                        @endforeach
                    </div>
                </div>
                <div class="proposal-block proposal-highlight">
                    <h3>Porque escolher a Business Diversity</h3>
                    <ul>
                        @foreach ($proposal['differentiators'] as $differentiator)
                            <li>{{ $differentiator }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="proposal-block">
                <h3>Abordagens seleccionadas</h3>
                <div class="proposal-pill-grid">
                    @foreach ($proposal['selected_approaches'] as $approach)
                        <span>{{ $approach }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="proposal-page proposal-financial-page">
            <div class="proposal-section-title">
                <span>10</span>
                <div>
                    <small>Proposta financeira</small>
                    <h2>Investimento e racional comercial</h2>
                </div>
            </div>

            <div class="proposal-commercial-grid">
                <div class="proposal-block proposal-highlight">
                    <h3>{{ $package['label'] ?? 'Pacote comercial' }}</h3>
                    <p>{{ $package['description'] ?? '' }}</p>
                </div>
                <div class="proposal-block">
                    <h3>Base de precificação</h3>
                    <p>{{ $pricingPolicy['base'] ?? ($package['pricing'] ?? 'Preço definido conforme escopo, complexidade e recursos necessários.') }}</p>
                </div>
            </div>

            @if (!empty($pricingPolicy['drivers']))
                <div class="proposal-block">
                    <h3>Factores considerados no preço</h3>
                    <ul>
                        @foreach ($pricingPolicy['drivers'] as $driver)
                            <li>{{ $driver }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($pricingPolicy['ranges']))
                <div class="proposal-price-table">
                    @foreach ($pricingPolicy['ranges'] as $label => $range)
                        <div>
                            <span>{{ $label }}</span>
                            <strong>{{ $range }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($hasInvestment)
                <table class="proposal-financial-table">
                    <tbody>
                        <tr><th>Honorários profissionais</th><td>{{ $proposal['currency'] }} {{ $money($proposal['fee']) }}</td></tr>
                        <tr><th>Despesas estimadas</th><td>{{ $proposal['currency'] }} {{ $money($proposal['expenses']) }}</td></tr>
                        <tr><th>Subtotal</th><td>{{ $proposal['currency'] }} {{ $money($proposal['subtotal']) }}</td></tr>
                        <tr><th>IVA ({{ $money($proposal['vat_rate']) }}%)</th><td>{{ $proposal['currency'] }} {{ $money($proposal['vat']) }}</td></tr>
                        <tr class="proposal-total-row"><th>Total estimado</th><td>{{ $proposal['currency'] }} {{ $money($proposal['total']) }}</td></tr>
                    </tbody>
                </table>
            @else
                <div class="proposal-no-investment">
                    <span>Investimento a confirmar</span>
                    <h3>O valor final será fechado após validação do âmbito.</h3>
                    <p>Esta proposta apresenta a metodologia, entregáveis e política comercial. O investimento final deve ser preenchido pelo colaborador antes da submissão ao cliente ou confirmado em proposta/proforma complementar.</p>
                </div>
            @endif
        </section>

        <section class="proposal-page">
            <div class="proposal-section-title">
                <span>11</span>
                <div>
                    <small>Termos e garantias</small>
                    <h2>Condições, qualidade e dados de pagamento</h2>
                </div>
            </div>

            <div class="proposal-two-columns">
                <div class="proposal-block">
                    <h3>Condições de pagamento</h3>
                    <p>{!! nl2br(e($proposal['payment_terms'])) !!}</p>
                    <h3>Notas financeiras</h3>
                    <p>{!! nl2br(e($proposal['financial_notes'])) !!}</p>
                </div>
                <div class="proposal-block proposal-highlight">
                    <h3>Garantias de qualidade</h3>
                    <ul>
                        @foreach (($identity['guarantees'] ?? []) as $guarantee)
                            <li>{{ $guarantee }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="proposal-two-columns">
                <div class="proposal-block">
                    <h3>Termos e condições</h3>
                    <ul>
                        @foreach (($identity['commercial_terms'] ?? []) as $term)
                            <li>{{ $term }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="proposal-block">
                    <h3>Detalhes bancários</h3>
                    @if ($hasBank)
                        <div class="proposal-bank-list">
                            @if ($bank['bank_name'] ?? null)<p><span>Banco</span><strong>{{ $bank['bank_name'] }}</strong></p>@endif
                            @if ($bank['account_holder'] ?? null)<p><span>Titular</span><strong>{{ $bank['account_holder'] }}</strong></p>@endif
                            @if ($bank['account_number'] ?? null)<p><span>Conta</span><strong>{{ $bank['account_number'] }}</strong></p>@endif
                            @if ($bank['nib'] ?? null)<p><span>NIB</span><strong>{{ $bank['nib'] }}</strong></p>@endif
                            @if ($bank['swift'] ?? null)<p><span>SWIFT</span><strong>{{ $bank['swift'] }}</strong></p>@endif
                        </div>
                    @else
                        <p>{{ $bank['note'] ?? 'Os dados bancários serão confirmados na factura/proforma.' }}</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="proposal-page proposal-credentials-page">
            <div class="proposal-section-title">
                <span>12</span>
                <div>
                    <small>Credenciais</small>
                    <h2>Clientes, equipa e capacidade institucional</h2>
                </div>
            </div>

            @if ($metrics->isNotEmpty())
                <div class="proposal-credential-metrics">
                    @foreach ($metrics as $metric)
                        <article><strong>{{ $metric['value'] }}</strong><span>{{ $metric['label'] }}</span></article>
                    @endforeach
                </div>
            @endif

            @if ($clients->isNotEmpty())
                <div class="proposal-block">
                    <h3>Alguns dos nossos estimados clientes</h3>
                    <div class="proposal-client-logo-grid">
                        @foreach ($clients as $client)
                            <div><img src="{{ asset($client['logo']) }}" alt="{{ $client['name'] ?? 'Cliente Business Diversity' }}"></div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($proposal['team_members']))
                <div class="proposal-team-grid">
                    @foreach ($proposal['team_members'] as $member)
                        <article>
                            <img src="{{ asset($member['photo']) }}" alt="{{ $member['name'] }}">
                            <div>
                                <h3>{{ $member['name'] }}</h3>
                                <span>{{ $member['role'] }}</span>
                                <p>{{ $member['specialty'] }}</p>
                                <strong>{{ $member['experience'] }}</strong>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            <div class="proposal-two-columns">
                <div class="proposal-block proposal-highlight">
                    <h3>Credenciais institucionais</h3>
                    <ul>
                        @foreach (($identity['credentials'] ?? []) as $credential)
                            <li>{{ $credential }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="proposal-block">
                    <h3>Compromisso de valor</h3>
                    <p>A nossa proposta combina rigor técnico, comunicação clara e aplicação prática. O objectivo não é apenas entregar documentos, mas apoiar o cliente a tomar melhores decisões, implementar com segurança e construir capacidade interna.</p>
                </div>
            </div>
        </section>

        <section class="proposal-page proposal-acceptance-page">
            <div class="proposal-section-title">
                <span>13</span>
                <div>
                    <small>Fecho</small>
                    <h2>Próximo passo: adjudicação e arranque</h2>
                </div>
            </div>

            <div class="proposal-closing-note">
                <p>{{ $proposal['closing_note'] }}</p>
            </div>

            <p>Esta proposta é válida até {{ $validUntil }} e poderá ser ajustada caso haja alteração significativa do âmbito, prazos, premissas ou requisitos operacionais.</p>
            <div class="proposal-signature-grid">
                <div>
                    <span>Pela Business Diversity</span>
                    <strong>{{ $proposal['prepared_by'] }}</strong>
                    <small>{{ $proposal['prepared_role'] }}</small>
                </div>
                <div>
                    <span>Pelo cliente</span>
                    <strong>{{ $proposal['client_name'] }}</strong>
                    <small>Assinatura e carimbo</small>
                </div>
            </div>
        </section>
    </article>
</main>
@endsection
