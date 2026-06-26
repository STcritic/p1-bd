@extends('layouts.app')
@section('title', $locale === 'en' ? 'Our services' : 'Os nossos serviços')
@section('description', $locale === 'en' ? 'Human resources consulting, recruitment, performance, career and organisational development.' : 'Consultoria de recursos humanos, recrutamento, desempenho, carreiras e desenvolvimento organizacional.')

@section('content')
@php
    $en = $locale === 'en';
@endphp
<section class="page-hero inner-hero inner-hero-services"><div class="container inner-hero-grid">
    <div class="inner-hero-copy"><span class="eyebrow light">{{ $en ? 'OUR EXPERTISE' : 'AS NOSSAS COMPETÊNCIAS' }}</span><h1>{{ $en ? 'Connected solutions for stronger organisations.' : 'Soluções conectadas para organizações mais fortes.' }}</h1><p>{{ $en ? 'Strategy and execution brought together to solve the whole people challenge.' : 'Estratégia e execução reunidas para resolver o desafio completo das pessoas.' }}</p></div>
    <div class="inner-hero-cards expertise-map" aria-label="{{ $en ? 'Expertise map' : 'Mapa de competências' }}">
        @foreach (($en ? ['Strategy', 'Talent', 'Performance', 'Organisation'] : ['Estratégia', 'Talento', 'Desempenho', 'Organização']) as $index => $area)
            <article class="expertise-chip"><span>0{{ $index + 1 }}</span><strong>{{ $area }}</strong><i>↗</i></article>
        @endforeach
    </div>
</div></section>

<section class="section resource-teaser-section"><div class="container insight-gateway-grid">
    <div class="insight-copy">
        <span class="eyebrow">{{ $en ? 'ADVISORY INTELLIGENCE' : 'INTELIGÊNCIA CONSULTIVA' }}</span>
        <h2>{{ $en ? 'Turn an HR concern into a practical decision route.' : 'Transforme uma dúvida de RH num caminho claro de decisão.' }}</h2>
        <p>{{ $en ? 'Before asking for a proposal, explore BD advisory guides: quick diagnosis, decision criteria and practical actions your leadership team can discuss immediately.' : 'Antes de pedir uma proposta, explore guias consultivos da BD: diagnóstico rápido, critérios de decisão e acções práticas para discutir já com a sua liderança.' }}</p>

        <div class="insight-benefits" aria-label="{{ $en ? 'Guide benefits' : 'Benefícios dos guias' }}">
            <div><strong>01</strong><span>{{ $en ? 'Identify warning signs before they become structural problems.' : 'Identifique sinais de alerta antes que virem problemas estruturais.' }}</span></div>
            <div><strong>02</strong><span>{{ $en ? 'Understand what BD delivers in each engagement.' : 'Perceba o que a BD entrega em cada intervenção.' }}</span></div>
            <div><strong>03</strong><span>{{ $en ? 'Export a clean PDF to share with decision makers.' : 'Exporte um PDF limpo para partilhar com decisores.' }}</span></div>
        </div>

        <div class="insight-actions">
            <a class="button button-primary" href="#catalogo-servicos">{{ $en ? 'View all services' : 'Ver todos os serviços' }} <span>↓</span></a>
            <a class="text-link" href="{{ route($en ? 'en.contact' : 'contact') }}">{{ $en ? 'Talk to a consultant' : 'Falar com um consultor' }} <span>→</span></a>
        </div>
    </div>

    <div class="insight-panel">
        <div class="insight-panel-head">
            <span>{{ $en ? 'Start with a guide' : 'Comece por um guia' }}</span>
            <small>{{ $en ? 'PDF-ready content' : 'Conteúdo pronto para PDF' }}</small>
        </div>
        <div class="resource-teaser-grid">
            @foreach (array_slice($guides, 0, 3) as $guide)
                <a class="resource-teaser-card" href="{{ route($en ? 'en.resource.show' : 'resource.show', $guide['slug']) }}">
                    <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <strong>{{ $guide['title'] }}</strong>
                    <p>{{ $guide['short'] }}</p>
                    <small>{{ $en ? 'Open guide and export PDF' : 'Abrir guia e exportar PDF' }} ↗</small>
                </a>
            @endforeach
        </div>
    </div>
</div></section>

<section class="section services-catalogue" id="catalogo-servicos"><div class="container"><div class="services-list">
@foreach ($guides as $index => $service)
    <article>
        <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
        <div><h2>{{ $service['title'] }}</h2><p>{{ $service['short'] }}</p></div>
        <div class="service-card-actions">
            <a class="service-guide-link" href="{{ route($en ? 'en.resource.show' : 'resource.show', $service['slug']) }}">{{ $en ? 'View guide' : 'Ver guia' }} <span>↗</span></a>
            <a class="service-contact-link" href="{{ route($en ? 'en.contact' : 'contact', ['service' => $service['title']]) }}">{{ $en ? 'Talk to BD' : 'Fale connosco' }} <span>→</span></a>
        </div>
    </article>
@endforeach
</div></div></section>

<section class="section service-detail-band"><div class="container approach-grid"><div><span class="eyebrow light">{{ $en ? 'BUILT FOR YOUR CONTEXT' : 'CONSTRUÍDO PARA O SEU CONTEXTO' }}</span><h2>{{ $en ? 'The service adapts. The standard does not.' : 'O serviço adapta-se. O padrão de qualidade não.' }}</h2></div><div><p class="lead">{{ $en ? 'Every engagement starts with a clear diagnosis, agreed outcomes and a realistic implementation path.' : 'Cada trabalho começa com um diagnóstico claro, resultados acordados e um caminho realista de implementação.' }}</p><a class="button button-light" href="{{ route($en ? 'en.contact' : 'contact') }}">{{ $en ? 'Discuss your challenge' : 'Converse sobre o seu desafio' }} →</a></div></div></section>
@endsection
