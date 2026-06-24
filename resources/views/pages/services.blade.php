@extends('layouts.app')
@section('title', $locale === 'en' ? 'Our services' : 'Os nossos serviços')
@section('description', $locale === 'en' ? 'Human resources consulting, recruitment, performance, career and organisational development.' : 'Consultoria de recursos humanos, recrutamento, desempenho, carreiras e desenvolvimento organizacional.')

@section('content')
@php
    $en = $locale === 'en';
    $services = $en ? [
        ['Performance Management', 'Design and implementation of clear performance systems, objectives, feedback cycles and development plans.'],
        ['Career & Succession Planning', 'Career paths and succession structures that retain knowledge, prepare leaders and make growth visible.'],
        ['Job Evaluation & Classification', 'Consistent job architecture, role descriptions and grading to support fair, informed decisions.'],
        ['Behavioural Profile Mapping', 'Evidence-based profiles that improve selection, team composition and individual development.'],
        ['Recruitment & Selection', 'End-to-end recruitment with rigorous assessment and strong alignment to role and culture.'],
        ['Policies & Procedures', 'Practical HR policies and operating procedures aligned with organisational needs and legal compliance.'],
        ['Compensation & Benefits', 'Market-informed remuneration analysis and frameworks that balance competitiveness and sustainability.'],
        ['Training & Development', 'Tailored learning programmes that build relevant capability and support measurable application.'],
        ['HR Advisory & Outsourcing', 'Flexible senior HR support for projects, transformation or ongoing back-office operations.'],
        ['HR Digitalisation & Internal Branding', 'Modern employee experiences through digital processes, technology adoption and stronger internal communication.'],
    ] : [
        ['Gestão de Desempenho', 'Desenho e implementação de sistemas claros de desempenho, objectivos, ciclos de feedback e planos de desenvolvimento.'],
        ['Planos de Carreira e Sucessão', 'Percursos de carreira e estruturas de sucessão que retêm conhecimento, preparam líderes e tornam o crescimento visível.'],
        ['Avaliação e Classificação de Cargos', 'Arquitectura de cargos, descrições de funções e enquadramento consistente para apoiar decisões justas.'],
        ['Mapeamento do Perfil Comportamental', 'Perfis baseados em evidências que melhoram a selecção, a composição de equipas e o desenvolvimento individual.'],
        ['Recrutamento e Selecção', 'Recrutamento integral com avaliação rigorosa e forte alinhamento à função e à cultura organizacional.'],
        ['Políticas e Procedimentos', 'Políticas de RH e procedimentos operacionais práticos, alinhados às necessidades e à conformidade legal.'],
        ['Remuneração e Benefícios', 'Análise salarial e modelos informados pelo mercado, equilibrando competitividade e sustentabilidade.'],
        ['Formação e Desenvolvimento', 'Programas personalizados que desenvolvem competências relevantes e promovem aplicação mensurável.'],
        ['Assessoria e Outsourcing de RH', 'Apoio sénior flexível para projectos, transformação ou operações contínuas de back office.'],
        ['Digitalização de RH e Endomarketing', 'Experiências modernas para colaboradores através de processos digitais, adopção tecnológica e comunicação interna mais forte.'],
    ];
@endphp
<section class="page-hero inner-hero inner-hero-services"><div class="container inner-hero-grid">
    <div class="inner-hero-copy"><span class="eyebrow light">{{ $en ? 'OUR EXPERTISE' : 'AS NOSSAS COMPETÊNCIAS' }}</span><h1>{{ $en ? 'Connected solutions for stronger organisations.' : 'Soluções conectadas para organizações mais fortes.' }}</h1><p>{{ $en ? 'Strategy and execution brought together to solve the whole people challenge.' : 'Estratégia e execução reunidas para resolver o desafio completo das pessoas.' }}</p></div>
    <div class="inner-hero-cards expertise-map" aria-label="{{ $en ? 'Expertise map' : 'Mapa de competências' }}">
        @foreach (($en ? ['Strategy', 'Talent', 'Performance', 'Organisation'] : ['Estratégia', 'Talento', 'Desempenho', 'Organização']) as $index => $area)
            <article class="expertise-chip"><span>0{{ $index + 1 }}</span><strong>{{ $area }}</strong><i>↗</i></article>
        @endforeach
    </div>
</div></section>

<section class="section"><div class="container"><div class="services-list">
@foreach ($services as $index => [$title, $description])
    <article><span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><div><h2>{{ $title }}</h2><p>{{ $description }}</p></div><a href="{{ route($en ? 'en.contact' : 'contact', ['service' => $title]) }}" aria-label="{{ ($en ? 'Enquire about ' : 'Pedir informação sobre ').$title }}">↗</a></article>
@endforeach
</div></div></section>

<section class="section service-detail-band"><div class="container approach-grid"><div><span class="eyebrow light">{{ $en ? 'BUILT FOR YOUR CONTEXT' : 'CONSTRUÍDO PARA O SEU CONTEXTO' }}</span><h2>{{ $en ? 'The service adapts. The standard does not.' : 'O serviço adapta-se. O padrão de qualidade não.' }}</h2></div><div><p class="lead">{{ $en ? 'Every engagement starts with a clear diagnosis, agreed outcomes and a realistic implementation path.' : 'Cada trabalho começa com um diagnóstico claro, resultados acordados e um caminho realista de implementação.' }}</p><a class="button button-light" href="{{ route($en ? 'en.contact' : 'contact') }}">{{ $en ? 'Discuss your challenge' : 'Converse sobre o seu desafio' }} →</a></div></div></section>
@endsection
