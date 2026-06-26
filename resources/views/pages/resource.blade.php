@extends('layouts.app')
@section('title', $guide['title'].' - '.($locale === 'en' ? 'Advisory guide' : 'Guia prático'))
@section('description', $guide['short'])

@section('content')
@php
    $en = $locale === 'en';
    $primaryAlerts = array_slice($guide['alerts'], 0, 2);
    $methodSteps = $en
        ? [
            ['title' => 'Clarify the decision', 'text' => 'Define what the organisation needs to decide, who owns the decision and what evidence is required.'],
            ['title' => 'Map current practice', 'text' => 'Review processes, documents, behaviours and data to separate symptoms from root causes.'],
            ['title' => 'Design practical tools', 'text' => 'Translate the solution into simple instruments, responsibilities, routines and implementation criteria.'],
            ['title' => 'Implement and transfer', 'text' => 'Pilot, adjust, train users and transfer capability so the organisation can sustain the practice.'],
        ]
        : [
            ['title' => 'Clarificar a decisão', 'text' => 'Definir o que a organização precisa decidir, quem decide e que evidências são necessárias.'],
            ['title' => 'Mapear a prática actual', 'text' => 'Rever processos, documentos, comportamentos e dados para separar sintomas de causas reais.'],
            ['title' => 'Desenhar ferramentas práticas', 'text' => 'Traduzir a solução em instrumentos simples, responsabilidades, rotinas e critérios de implementação.'],
            ['title' => 'Implementar e transferir', 'text' => 'Pilotar, ajustar, capacitar utilizadores e transferir capacidade para sustentar a prática.'],
        ];
    $decisionQuestions = $en
        ? [
            'What business decision is currently being delayed or made with weak evidence?',
            'Which leaders, teams or employee groups will be most affected by this intervention?',
            'What must change in behaviour, process or data for the solution to be considered successful?',
        ]
        : [
            'Que decisão de negócio está a ser adiada ou tomada com pouca evidência?',
            'Que líderes, equipas ou grupos de colaboradores serão mais afectados por esta intervenção?',
            'O que deve mudar em comportamento, processo ou dados para a solução ser considerada bem-sucedida?',
        ];
@endphp
<section class="guide-hero">
    <div class="container guide-hero-grid">
        <div>
            <a class="guide-back-link" href="{{ route($en ? 'en.services' : 'services') }}">← {{ $en ? 'Back to services' : 'Voltar aos serviços' }}</a>
            <span class="eyebrow light">{{ $en ? 'BD ADVISORY GUIDE' : 'GUIA PRÁTICO BD' }}</span>
            <h1>{{ $guide['title'] }}</h1>
            <p>{{ $guide['short'] }}</p>
            <div class="guide-actions">
                <button class="button button-light" type="button" data-print-guide>{{ $en ? 'Export PDF' : 'Exportar PDF' }} <span>↗</span></button>
                <a class="button button-primary" href="{{ route($en ? 'en.contact' : 'contact', ['service' => $guide['title']]) }}">{{ $en ? 'Discuss this service' : 'Conversar sobre este serviço' }} <span>→</span></a>
            </div>
        </div>
        <aside class="guide-cover-card" aria-label="{{ $en ? 'Guide summary' : 'Resumo do guia' }}">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
            <span>{{ $en ? 'Printable material' : 'Material exportável' }}</span>
            <strong>{{ $en ? 'Diagnosis + decision guide' : 'Diagnóstico + guia de decisão' }}</strong>
            <small>{{ $en ? 'Prepared for leaders who want better people decisions.' : 'Preparado para líderes que querem melhores decisões sobre pessoas.' }}</small>
        </aside>
    </div>
</section>

<article class="guide-document" aria-label="{{ $guide['title'] }}">
    <div class="container guide-paper">
        <header class="guide-paper-header">
            <div><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"><span>Business Diversity CE, SA</span></div>
            <small>{{ $en ? 'Advisory guide' : 'Guia prático' }}</small>
        </header>

        <section class="guide-print-title">
            <span>{{ $en ? 'Advisory guide' : 'Guia prático' }}</span>
            <h1>{{ $guide['title'] }}</h1>
            <p>{{ $guide['short'] }}</p>
        </section>

        <section class="guide-section guide-brief">
            <div class="guide-section-heading">
                <span class="guide-kicker">01</span>
                <h2>{{ $en ? 'Executive brief' : 'Resumo executivo' }}</h2>
                <p>{{ $en ? 'This material gives leadership a practical first reading before requesting a proposal or starting an internal project.' : 'Este material dá à liderança uma primeira leitura prática antes de pedir uma proposta ou iniciar um projecto interno.' }}</p>
            </div>
            <div class="guide-brief-grid">
                <article>
                    <span>{{ $en ? 'Decision context' : 'Contexto de decisão' }}</span>
                    <p>{{ $guide['value'] }}</p>
                </article>
                <article>
                    <span>{{ $en ? 'Who should be involved' : 'Quem deve estar envolvido' }}</span>
                    <p>{{ $guide['audience'] }}</p>
                </article>
                <article>
                    <span>{{ $en ? 'Risk of postponing' : 'Risco de adiar' }}</span>
                    <ul class="guide-mini-list">
                        @foreach ($primaryAlerts as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            </div>
        </section>

        <section class="guide-section guide-intro-grid">
            <div><span class="guide-kicker">02</span><h2>{{ $en ? 'What should become clearer' : 'O que deve ficar mais claro' }}</h2></div>
            <p class="lead">{{ $en ? 'A useful intervention should not only produce documents. It should improve the quality of decisions, reduce ambiguity and create routines that managers and teams can actually use.' : 'Uma intervenção útil não deve produzir apenas documentos. Deve melhorar a qualidade das decisões, reduzir ambiguidade e criar rotinas que gestores e equipas consigam usar na prática.' }}</p>
        </section>

        <section class="guide-section guide-two-columns">
            <div class="guide-panel guide-dark">
                <span class="guide-kicker">03</span>
                <h2>{{ $en ? 'Who should use this guide' : 'Para quem é este guia' }}</h2>
                <p>{{ $guide['audience'] }}</p>
            </div>
            <div class="guide-panel">
                <span class="guide-kicker">04</span>
                <h2>{{ $en ? 'Warning signs' : 'Sinais de alerta' }}</h2>
                <ul class="guide-list">
                    @foreach ($guide['alerts'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="guide-section guide-roadmap">
            <div class="guide-section-heading">
                <span class="guide-kicker">05</span>
                <h2>{{ $en ? 'Practical implementation route' : 'Roteiro prático de implementação' }}</h2>
                <p>{{ $en ? 'Use this route to move from intention to execution with less improvisation and clearer ownership.' : 'Use este roteiro para passar da intenção à execução com menos improviso e responsabilidades mais claras.' }}</p>
            </div>
            <div class="guide-roadmap-grid">
                @foreach ($methodSteps as $step)
                    <article>
                        <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="guide-section guide-diagnostic"
            data-diagnostic
            data-empty-title="{{ $en ? 'Start the diagnostic' : 'Comece o diagnóstico' }}"
            data-empty-text="{{ $en ? 'Mark the points that are not yet clear, consistent or fully implemented in your organisation.' : 'Marque os pontos que ainda não estão claros, consistentes ou totalmente implementados na sua organização.' }}"
            data-low-title="{{ $en ? 'Good starting point' : 'Bom ponto de partida' }}"
            data-low-text="{{ $en ? 'Few risk points are visible. This may be the right moment to document the practice, protect consistency and prepare the next stage of maturity.' : 'Há poucos pontos de risco visíveis. Pode ser o momento certo para documentar a prática, proteger consistência e preparar a próxima fase de maturidade.' }}"
            data-medium-title="{{ $en ? 'Moderate priority' : 'Prioridade moderada' }}"
            data-medium-text="{{ $en ? 'There are relevant gaps. A focused diagnostic conversation can help separate quick wins from structural decisions.' : 'Existem lacunas relevantes. Uma conversa de diagnóstico pode ajudar a separar ganhos rápidos de decisões estruturais.' }}"
            data-high-title="{{ $en ? 'High priority' : 'Prioridade alta' }}"
            data-high-text="{{ $en ? 'Several critical points need attention. A structured intervention is recommended to reduce people, performance and governance risk.' : 'Vários pontos críticos precisam de atenção. Recomenda-se uma intervenção estruturada para reduzir riscos de pessoas, desempenho e governação.' }}">
            <div class="guide-section-heading">
                <span class="guide-kicker">06</span>
                <h2>{{ $en ? 'Quick diagnosis checklist' : 'Checklist rápido de diagnóstico' }}</h2>
                <p>{{ $en ? 'Mark the areas that are not yet clear, consistent or fully implemented. The result gives a practical reading of urgency and next steps.' : 'Marque as áreas que ainda não estão claras, consistentes ou totalmente implementadas. O resultado dá uma leitura prática da urgência e do próximo passo.' }}</p>
            </div>

            <div class="guide-diagnostic-grid">
                <div class="guide-checklist">
                    @foreach ($guide['checklist'] as $item)
                        <label class="guide-check-item">
                            <input type="checkbox" data-diagnostic-check value="{{ $loop->iteration }}">
                            <span aria-hidden="true"></span>
                            <p>{{ $item }}</p>
                            <small>{{ $en ? 'Needs attention' : 'Precisa de atenção' }}</small>
                        </label>
                    @endforeach
                </div>

                <aside class="guide-diagnostic-summary" aria-live="polite">
                    <div class="diagnostic-score">
                        <span data-diagnostic-count>0</span><small>/ {{ count($guide['checklist']) }}</small>
                    </div>
                    <div class="diagnostic-meter" aria-hidden="true"><i data-diagnostic-meter></i></div>
                    <strong data-diagnostic-title>{{ $en ? 'Start the diagnostic' : 'Comece o diagnóstico' }}</strong>
                    <p data-diagnostic-text>{{ $en ? 'Mark the points that are not yet clear, consistent or fully implemented in your organisation.' : 'Marque os pontos que ainda não estão claros, consistentes ou totalmente implementados na sua organização.' }}</p>
                    <div class="diagnostic-actions">
                        <button class="button button-light" type="button" data-print-guide>{{ $en ? 'Export with result' : 'Exportar com resultado' }} <span>↗</span></button>
                        <a class="text-link" data-diagnostic-contact href="{{ route($en ? 'en.contact' : 'contact', ['service' => $guide['title']]) }}">{{ $en ? 'Discuss result' : 'Discutir resultado' }} <span>→</span></a>
                    </div>
                </aside>
            </div>
        </section>

        <section class="guide-section guide-deliverables">
            <div>
                <span class="guide-kicker">07</span>
                <h2>{{ $en ? 'What BD can deliver' : 'O que a BD pode entregar' }}</h2>
                <p>{{ $en ? 'A practical engagement is normally translated into concrete tools, decisions and transfer of capability.' : 'Uma intervenção prática deve traduzir-se em ferramentas concretas, decisões e transferência de capacidade.' }}</p>
            </div>
            <div class="guide-deliverable-grid">
                @foreach ($guide['deliverables'] as $item)
                    <div><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $item }}</strong></div>
                @endforeach
            </div>
        </section>

        <section class="guide-section guide-questions">
            <div class="guide-section-heading">
                <span class="guide-kicker">08</span>
                <h2>{{ $en ? 'Questions for leadership alignment' : 'Perguntas para alinhar a liderança' }}</h2>
                <p>{{ $en ? 'Before moving forward, align the leadership team around the decision, the evidence and the expected change.' : 'Antes de avançar, alinhe a liderança sobre a decisão, as evidências e a mudança esperada.' }}</p>
            </div>
            <div class="guide-question-grid">
                @foreach ($decisionQuestions as $question)
                    <article><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><p>{{ $question }}</p></article>
                @endforeach
            </div>
        </section>

        <section class="guide-section guide-next-step">
            <div>
                <span class="guide-kicker">09</span>
                <h2>{{ $en ? 'Suggested next step' : 'Próximo passo recomendado' }}</h2>
                <p>{{ $en ? 'If two or more warning signs are present, schedule a short diagnostic conversation. The goal is not to sell a package; it is to understand the decision your organisation needs to make.' : 'Se dois ou mais sinais de alerta estiverem presentes, agende uma conversa curta de diagnóstico. O objectivo não é vender um pacote; é compreender a decisão que a sua organização precisa tomar.' }}</p>
            </div>
            <a class="button button-primary" href="{{ route($en ? 'en.contact' : 'contact', ['service' => $guide['title']]) }}">{{ $en ? 'Request diagnostic conversation' : 'Pedir conversa de diagnóstico' }} <span>→</span></a>
        </section>

        <footer class="guide-paper-footer">
            <span>Business Diversity CE, SA</span>
            <span>info@bdiversity.co.mz · +258 87 605 2013</span>
            <span>bdiversity.co.mz</span>
        </footer>
    </div>
</article>
@endsection
