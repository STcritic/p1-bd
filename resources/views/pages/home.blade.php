@extends('layouts.app')

@section('title', $locale === 'en' ? 'Business consulting and human capital' : 'Consultoria empresarial e capital humano')
@section('description', $locale === 'en' ? 'Strategic human resources, recruitment and organisational development solutions in Mozambique.' : 'Soluções estratégicas de recursos humanos, recrutamento e desenvolvimento organizacional em Moçambique.')

@section('content')
@php
    $en = $locale === 'en';
@endphp
<section class="hero hero-premium">
    <div class="hero-media"><img src="{{ asset('assets/images/hero-consulting-team.png') }}" alt="{{ $en ? 'African business leaders in a strategic consulting session' : 'Líderes empresariais africanos numa sessão de consultoria estratégica' }}"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <span class="eyebrow">{{ $en ? 'HUMAN CAPITAL ADVISORY · MOZAMBIQUE' : 'CONSULTORIA DE CAPITAL HUMANO · MOÇAMBIQUE' }}</span>
        <h1>{{ $en ? 'Human strategy for businesses that lead.' : 'Estratégia humana para empresas que lideram.' }}</h1>
        <p>{{ $en ? 'We connect people, performance and organisational intelligence to turn ambition into sustainable business results.' : 'Ligamos pessoas, desempenho e inteligência organizacional para transformar ambição em resultados empresariais sustentáveis.' }}</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ route($en ? 'en.contact' : 'contact') }}">{{ $en ? 'Start a conversation' : 'Iniciar uma conversa' }} <span>→</span></a>
            <a class="text-link" href="{{ route($en ? 'en.services' : 'services') }}">{{ $en ? 'Explore our expertise' : 'Explorar competências' }} <span>↗</span></a>
        </div>
    </div>
</section>

<div class="trust-ribbon" aria-label="{{ $en ? 'Areas of expertise' : 'Áreas de competência' }}">
    <div class="trust-ribbon-track">
        @foreach (($en ? ['Human Capital Strategy', 'Organisational Development', 'Talent Intelligence', 'Performance Systems', 'Executive Advisory'] : ['Estratégia de Capital Humano', 'Desenvolvimento Organizacional', 'Inteligência de Talento', 'Sistemas de Desempenho', 'Assessoria Executiva']) as $item)
            <span>{{ $item }}</span><i>✦</i>
        @endforeach
        @foreach (($en ? ['Human Capital Strategy', 'Organisational Development', 'Talent Intelligence', 'Performance Systems', 'Executive Advisory'] : ['Estratégia de Capital Humano', 'Desenvolvimento Organizacional', 'Inteligência de Talento', 'Sistemas de Desempenho', 'Assessoria Executiva']) as $item)
            <span aria-hidden="true">{{ $item }}</span><i aria-hidden="true">✦</i>
        @endforeach
    </div>
</div>

<section class="section executive-intro">
    <div class="container executive-grid">
        <div class="executive-statement">
            <span class="eyebrow">{{ $en ? 'EXPERIENCE + INNOVATION' : 'EXPERIÊNCIA + INOVAÇÃO' }}</span>
            <h2>{{ $en ? 'Experience that guides. Talent that transforms.' : 'Experiência que orienta. Talento que transforma.' }}</h2>
        </div>
        <div class="executive-copy">
            <p class="lead">{{ $en ? 'We combine senior judgement with a new generation of specialists to solve today’s people and business challenges.' : 'Combinamos o discernimento de profissionais séniores com uma nova geração de especialistas para responder aos desafios actuais das pessoas e dos negócios.' }}</p>
            <p>{{ $en ? 'Professionals with more than 22 years of experience work alongside young talent skilled in HR digitalisation, internal branding, technology and new ways of working.' : 'Profissionais com mais de 22 anos de experiência trabalham lado a lado com talentos jovens especializados em digitalização de RH, endomarketing, tecnologia e novas formas de trabalhar.' }}</p>
            <a class="arrow-link" href="{{ route($en ? 'en.about' : 'about') }}">{{ $en ? 'Our firm and leadership' : 'A nossa empresa e liderança' }} <span>→</span></a>
        </div>
        <div class="metric-tile metric-dark"><span>{{ $en ? 'Trust built' : 'Confiança construída' }}</span><strong>8+</strong><small>{{ $en ? 'years delivering consulting solutions' : 'anos a entregar soluções de consultoria' }}</small></div>
        <div class="metric-tile"><span>{{ $en ? 'Experience that guides' : 'Experiência que orienta' }}</span><strong>22+</strong><small>{{ $en ? 'years brought by senior professionals' : 'anos trazidos por profissionais séniores' }}</small></div>
        <div class="metric-tile metric-accent"><span>{{ $en ? 'Contemporary talent' : 'Talento contemporâneo' }}</span><strong class="metric-word">{{ $en ? 'NEXT' : 'FUTURO' }}</strong><small>{{ $en ? 'digitalisation, internal branding and technology' : 'digitalização, endomarketing e tecnologia' }}</small></div>
    </div>
</section>

<section class="section capability-section">
    <div class="container">
        <div class="section-header premium-heading">
            <div><span class="eyebrow">{{ $en ? 'OUR EXPERTISE' : 'AS NOSSAS COMPETÊNCIAS' }}</span><h2>{{ $en ? 'One partner. An integrated view of your organisation.' : 'Um parceiro. Uma visão integrada da sua organização.' }}</h2></div>
            <div><p>{{ $en ? 'Connected solutions designed to solve the whole challenge, not just one visible symptom.' : 'Soluções conectadas para resolver o desafio completo, não apenas um sintoma visível.' }}</p><a class="arrow-link" href="{{ route($en ? 'en.services' : 'services') }}">{{ $en ? 'All capabilities' : 'Todas as competências' }} <span>↗</span></a></div>
        </div>

        <div class="capability-bento">
            <article class="capability-card capability-featured">
                <img src="{{ asset('assets/images/service_01.jpg') }}" alt="" loading="lazy">
                <div class="capability-shade"></div>
                <div class="capability-content"><span class="card-index">01 / {{ $en ? 'STRATEGY' : 'ESTRATÉGIA' }}</span><div><h3>{{ $en ? 'Human Capital Strategy' : 'Estratégia de Capital Humano' }}</h3><p>{{ $en ? 'Align structure, talent and leadership with the ambition of the business.' : 'Alinhar estrutura, talento e liderança com a ambição do negócio.' }}</p></div><a href="{{ route($en ? 'en.services' : 'services') }}" aria-label="{{ $en ? 'Explore Human Capital Strategy' : 'Explorar Estratégia de Capital Humano' }}">↗</a></div>
            </article>
            <article class="capability-card capability-blue">
                <span class="card-index">02 / {{ $en ? 'PERFORMANCE' : 'DESEMPENHO' }}</span><div class="capability-symbol">◎</div><div><h3>{{ $en ? 'Performance & Rewards' : 'Desempenho e Recompensas' }}</h3><p>{{ $en ? 'Systems that make expectations clear, contribution visible and decisions fair.' : 'Sistemas que tornam expectativas claras, contribuição visível e decisões justas.' }}</p></div><a href="{{ route($en ? 'en.services' : 'services') }}">↗</a>
            </article>
            <article class="capability-card capability-light">
                <span class="card-index">03 / {{ $en ? 'TALENT' : 'TALENTO' }}</span><div class="capability-symbol">◇</div><div><h3>{{ $en ? 'Talent Intelligence' : 'Inteligência de Talento' }}</h3><p>{{ $en ? 'Recruitment, behavioural insight and succession for the capabilities you need next.' : 'Recrutamento, perfis comportamentais e sucessão para as capacidades de que precisará.' }}</p></div><a href="{{ route($en ? 'en.services' : 'services') }}">↗</a>
            </article>
            <article class="capability-card capability-ink">
                <span class="card-index">04 / {{ $en ? 'ORGANISATION' : 'ORGANIZAÇÃO' }}</span><div class="capability-orbit" aria-hidden="true"><i></i><i></i><i></i></div><div><h3>{{ $en ? 'Organisation & Culture' : 'Organização e Cultura' }}</h3><p>{{ $en ? 'Operating models, policies and culture that turn strategy into consistent action.' : 'Modelos operacionais, políticas e cultura que transformam estratégia em acção consistente.' }}</p></div><a href="{{ route($en ? 'en.services' : 'services') }}">↗</a>
            </article>
        </div>
    </div>
</section>

<section class="section advisory-lab">
    <div class="advisory-glow" aria-hidden="true"></div>
    <div class="container advisory-grid">
        <div class="advisory-heading"><span class="eyebrow light">{{ $en ? 'ADVISORY LENS' : 'LENTE DE ASSESSORIA' }}</span><h2>{{ $en ? 'Complex decisions need a clearer view.' : 'Decisões complexas exigem uma visão mais clara.' }}</h2><p>{{ $en ? 'Explore how we approach the moments that shape an organisation.' : 'Explore como abordamos os momentos que definem uma organização.' }}</p></div>
        <div class="expertise-tabs" role="tablist" aria-label="{{ $en ? 'Advisory areas' : 'Áreas de assessoria' }}">
            <button class="expertise-tab is-active" type="button" role="tab" aria-selected="true" data-expertise-tab="growth"><span>01</span>{{ $en ? 'Growth' : 'Crescimento' }}</button>
            <button class="expertise-tab" type="button" role="tab" aria-selected="false" data-expertise-tab="change"><span>02</span>{{ $en ? 'Change' : 'Mudança' }}</button>
            <button class="expertise-tab" type="button" role="tab" aria-selected="false" data-expertise-tab="leadership"><span>03</span>{{ $en ? 'Leadership' : 'Liderança' }}</button>
        </div>
        <div class="advisory-panels">
            <article class="advisory-panel is-active" data-expertise-panel="growth"><span>01</span><h3>{{ $en ? 'Scale without losing clarity.' : 'Crescer sem perder clareza.' }}</h3><p>{{ $en ? 'We align roles, capabilities and decision rights so growth strengthens the organisation instead of stretching it.' : 'Alinhamos funções, capacidades e direitos de decisão para que o crescimento fortaleça a organização em vez de a fragilizar.' }}</p><ul><li>{{ $en ? 'Workforce planning' : 'Planeamento da força de trabalho' }}</li><li>{{ $en ? 'Organisation design' : 'Desenho organizacional' }}</li><li>{{ $en ? 'Capability roadmaps' : 'Roteiros de capacidades' }}</li></ul></article>
            <article class="advisory-panel" data-expertise-panel="change"><span>02</span><h3>{{ $en ? 'Turn change into adoption.' : 'Transformar mudança em adopção.' }}</h3><p>{{ $en ? 'We translate strategic change into the behaviours, communication and operating rhythms people can act on.' : 'Traduzimos mudança estratégica em comportamentos, comunicação e ritmos operacionais sobre os quais as pessoas podem agir.' }}</p><ul><li>{{ $en ? 'Change readiness' : 'Prontidão para mudança' }}</li><li>{{ $en ? 'Stakeholder alignment' : 'Alinhamento de stakeholders' }}</li><li>{{ $en ? 'Culture activation' : 'Activação da cultura' }}</li></ul></article>
            <article class="advisory-panel" data-expertise-panel="leadership"><span>03</span><h3>{{ $en ? 'Build leadership depth.' : 'Construir profundidade de liderança.' }}</h3><p>{{ $en ? 'We help identify, prepare and support leaders who can make sound decisions in increasingly complex contexts.' : 'Ajudamos a identificar, preparar e apoiar líderes capazes de tomar boas decisões em contextos cada vez mais complexos.' }}</p><ul><li>{{ $en ? 'Succession architecture' : 'Arquitectura de sucessão' }}</li><li>{{ $en ? 'Executive assessment' : 'Avaliação executiva' }}</li><li>{{ $en ? 'Leadership development' : 'Desenvolvimento de liderança' }}</li></ul></article>
        </div>
    </div>
</section>

<section class="section method-section">
    <div class="container">
        <div class="center-heading"><span class="eyebrow">{{ $en ? 'THE BD METHOD' : 'O MÉTODO BD' }}</span><h2>{{ $en ? 'From insight to measurable movement.' : 'Do diagnóstico ao movimento mensurável.' }}</h2></div>
        <div class="method-grid">
            <article class="method-card"><span>01</span><div class="method-line"></div><h3>{{ $en ? 'Listen deeply' : 'Escutar profundamente' }}</h3><p>{{ $en ? 'Understand the business context, not only the stated request.' : 'Compreender o contexto do negócio, não apenas o pedido apresentado.' }}</p></article>
            <article class="method-card"><span>02</span><div class="method-line"></div><h3>{{ $en ? 'Diagnose clearly' : 'Diagnosticar com clareza' }}</h3><p>{{ $en ? 'Find the patterns, causes and leverage points that matter.' : 'Encontrar padrões, causas e pontos de alavancagem relevantes.' }}</p></article>
            <article class="method-card"><span>03</span><div class="method-line"></div><h3>{{ $en ? 'Design together' : 'Desenhar em conjunto' }}</h3><p>{{ $en ? 'Build practical solutions with the people who will use them.' : 'Construir soluções práticas com as pessoas que irão utilizá-las.' }}</p></article>
            <article class="method-card"><span>04</span><div class="method-line"></div><h3>{{ $en ? 'Make it work' : 'Fazer funcionar' }}</h3><p>{{ $en ? 'Implement, transfer capability and measure what changed.' : 'Implementar, transferir capacidade e medir o que mudou.' }}</p></article>
        </div>
    </div>
</section>

<section class="section leadership-feature">
    <div class="container leadership-card">
        <div class="leadership-image"><img src="{{ asset('assets/images/team_02.png') }}" alt="Sandra Nhachale" loading="lazy"><span>{{ $en ? 'Leadership perspective' : 'Perspectiva de liderança' }}</span></div>
        <div class="leadership-copy"><span class="eyebrow">{{ $en ? 'EXPERIENCE WITH PURPOSE' : 'EXPERIÊNCIA COM PROPÓSITO' }}</span><h2>{{ $en ? 'Senior counsel, grounded in the reality of your business.' : 'Assessoria sénior, ancorada na realidade do seu negócio.' }}</h2><p class="lead">{{ $en ? 'Our approach brings together more than two decades of human resources experience and a practical commitment to helping organisations perform through people.' : 'A nossa abordagem reúne mais de duas décadas de experiência em Recursos Humanos e um compromisso prático de ajudar organizações a alcançar resultados através das pessoas.' }}</p><div class="leadership-signature"><strong>Sandra Nhachale</strong><span>{{ $en ? 'Managing Director' : 'Directora Geral' }}</span></div><a class="arrow-link" href="{{ route($en ? 'en.about' : 'about') }}">{{ $en ? 'Meet our team' : 'Conheça a equipa' }} <span>→</span></a></div>
    </div>
</section>

<section class="section partners-section">
    <div class="container">
        <div class="center-heading">
            <span class="eyebrow">{{ $en ? 'TRUSTED RELATIONSHIPS' : 'RELAÇÕES DE CONFIANÇA' }}</span>
            <h2>{{ $en ? 'Some of our valued partners' : 'Alguns dos nossos estimados parceiros' }}</h2>
        </div>

        <div class="partners-grid">
            @foreach (config('proposal_identity.clients', []) as $p)
                <div class="partner-card">
                    <img src="{{ asset($p['logo']) }}"
                         alt="{{ $p['name'] }}" loading="lazy"
                         onerror="this.style.display='none'">
                    <span>{{ $p['name'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="membership-block">
            <div class="membership-label">
                <span class="eyebrow">{{ $en ? 'MEMBERSHIP' : 'MEMBRESIA' }}</span>
            </div>
            <div class="membership-card">
                <img src="{{ asset('assets/logo/patnersLogo/Clients/ameprh.png') }}"
                     alt="AMEPRH" loading="lazy"
                     onerror="this.style.display='none'">
                <div>
                    <strong>AMEPRH</strong>
                    <span>{{ $en ? 'Mozambican Association of HR Companies and Professionals' : 'Associação Moçambicana de Empresas e Profissionais de RH' }}</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
