@extends('layouts.app')
@section('title', $locale === 'en' ? 'Our services' : 'Os nossos serviços')
@section('description', $locale === 'en' ? 'Human resources consulting, recruitment, performance, career and organisational development.' : 'Consultoria de recursos humanos, recrutamento, desempenho, carreiras e desenvolvimento organizacional.')

@section('content')
@php($en = $locale === 'en')
<section class="page-hero inner-hero inner-hero-services"><div class="container inner-hero-grid">
    <div class="inner-hero-copy"><span class="eyebrow light">{{ $en ? 'OUR EXPERTISE' : 'AS NOSSAS COMPETÊNCIAS' }}</span><h1>{{ $en ? 'Connected solutions for stronger organisations.' : 'Soluções conectadas para organizações mais fortes.' }}</h1><p>{{ $en ? 'Strategy and execution brought together to solve the whole people challenge.' : 'Estratégia e execução reunidas para resolver o desafio completo das pessoas.' }}</p></div>
    <div class="inner-hero-cards expertise-map" aria-label="{{ $en ? 'Expertise map' : 'Mapa de competências' }}">
        @foreach (($en ? ['Strategy', 'Talent', 'Performance', 'Organisation'] : ['Estratégia', 'Talento', 'Desempenho', 'Organização']) as $index => $area)
            <article class="expertise-chip"><span>0{{ $index + 1 }}</span><strong>{{ $area }}</strong><i>↗</i></article>
        @endforeach
    </div>
</div></section>

<section class="section resource-teaser-section"><div class="container">
    <div class="section-header premium-heading">
        <div><span class="eyebrow">{{ $en ? 'PRACTICAL KNOWLEDGE' : 'CONHECIMENTO PRÁTICO' }}</span><h2>{{ $en ? 'Use the guides before you request a proposal.' : 'Consulte os guias antes de pedir uma proposta.' }}</h2></div>
        <p>{{ $en ? 'Each service includes a short advisory guide designed to help visitors diagnose their challenge and understand how BD can support implementation.' : 'Cada serviço inclui um guia prático para ajudar o visitante a diagnosticar o desafio e perceber como a BD pode apoiar a implementação.' }}</p>
    </div>
    <div class="resource-teaser-grid">
        @foreach (array_slice($guides, 0, 3) as $guide)
            <a class="resource-teaser-card" href="{{ route($en ? 'en.resource.show' : 'resource.show', $guide['slug']) }}">
                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <strong>{{ $guide['title'] }}</strong>
                <small>{{ $en ? 'Open advisory guide' : 'Abrir guia prático' }} ↗</small>
            </a>
        @endforeach
    </div>
</div></section>

<section class="section services-catalogue"><div class="container"><div class="services-list">
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
