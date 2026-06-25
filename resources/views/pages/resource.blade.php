@extends('layouts.app')
@section('title', $guide['title'].' - '.($locale === 'en' ? 'Advisory guide' : 'Guia prático'))
@section('description', $guide['short'])

@section('content')
@php($en = $locale === 'en')
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
            <div><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"><span>{{ $en ? 'Business Diversity CE, SA' : 'Business Diversity CE, SA' }}</span></div>
            <small>{{ $en ? 'Advisory guide' : 'Guia prático' }}</small>
        </header>

        <section class="guide-print-title">
            <span>{{ $en ? 'Advisory guide' : 'Guia prático' }}</span>
            <h1>{{ $guide['title'] }}</h1>
            <p>{{ $guide['short'] }}</p>
        </section>

        <section class="guide-section guide-intro-grid">
            <div><span class="guide-kicker">01</span><h2>{{ $en ? 'Why this matters' : 'Porque isto importa' }}</h2></div>
            <p class="lead">{{ $guide['value'] }}</p>
        </section>

        <section class="guide-section guide-two-columns">
            <div class="guide-panel guide-dark">
                <span class="guide-kicker">02</span>
                <h2>{{ $en ? 'Who should use this guide' : 'Para quem é este guia' }}</h2>
                <p>{{ $guide['audience'] }}</p>
            </div>
            <div class="guide-panel">
                <span class="guide-kicker">03</span>
                <h2>{{ $en ? 'Warning signs' : 'Sinais de alerta' }}</h2>
                <ul class="guide-list">
                    @foreach ($guide['alerts'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="guide-section">
            <div class="guide-section-heading">
                <span class="guide-kicker">04</span>
                <h2>{{ $en ? 'Quick diagnosis checklist' : 'Checklist rápido de diagnóstico' }}</h2>
                <p>{{ $en ? 'Use these questions before deciding whether the organisation needs a structured intervention.' : 'Use estas perguntas antes de decidir se a organização precisa de uma intervenção estruturada.' }}</p>
            </div>
            <div class="guide-checklist">
                @foreach ($guide['checklist'] as $item)
                    <label><span></span><p>{{ $item }}</p></label>
                @endforeach
            </div>
        </section>

        <section class="guide-section guide-deliverables">
            <div>
                <span class="guide-kicker">05</span>
                <h2>{{ $en ? 'What BD can deliver' : 'O que a BD pode entregar' }}</h2>
                <p>{{ $en ? 'A practical engagement is normally translated into concrete tools, decisions and transfer of capability.' : 'Uma intervenção prática deve traduzir-se em ferramentas concretas, decisões e transferência de capacidade.' }}</p>
            </div>
            <div class="guide-deliverable-grid">
                @foreach ($guide['deliverables'] as $item)
                    <div><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $item }}</strong></div>
                @endforeach
            </div>
        </section>

        <section class="guide-section guide-next-step">
            <div>
                <span class="guide-kicker">06</span>
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
