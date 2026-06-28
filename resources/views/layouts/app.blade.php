<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#073b73">
    <title>@yield('title', 'Business Diversity') | Business Diversity</title>
    <meta name="description" content="@yield('description', 'Consultoria empresarial e soluções de capital humano em Moçambique.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Business Diversity') | Business Diversity">
    <meta property="og:description" content="@yield('description', 'Consultoria empresarial e soluções de capital humano em Moçambique.')">
    <meta property="og:image" content="{{ asset('assets/img/logo/logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <script>document.documentElement.classList.add('js')</script>
    @vite('resources/js/app.js')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => 'Business Diversity CE, SA',
        'url' => config('app.url'),
        'telephone' => '+258876052013',
        'email' => 'info@bdiversity.co.mz',
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Rua da Mozal, Matola-Rio', 'addressCountry' => 'MZ'],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
</head>
<body>
@php
    $isEnglish = $locale === 'en';
    $routes = $isEnglish
        ? ['home' => 'en.home', 'about' => 'en.about', 'services' => 'en.services', 'events' => 'en.events', 'contact' => 'en.contact']
        : ['home' => 'home', 'about' => 'about', 'services' => 'services', 'events' => 'events', 'contact' => 'contact'];
    $alternate = $isEnglish
        ? ['en.home' => 'home', 'en.about' => 'about', 'en.services' => 'services', 'en.events' => 'events', 'en.contact' => 'contact', 'en.schedule.show' => 'schedule.show']
        : ['home' => 'en.home', 'about' => 'en.about', 'services' => 'en.services', 'events' => 'en.events', 'contact' => 'en.contact', 'schedule.show' => 'en.schedule.show'];
    $currentRoute = request()->route()?->getName();
    $currentGuide = request()->route('guide');
    $currentEvent = request()->route('event');
    $alternateRoute = $alternate[$currentRoute] ?? ($isEnglish ? 'home' : 'en.home');
    $alternateUrl = $currentRoute === 'resource.show'
        ? route('en.resource.show', $currentGuide)
        : ($currentRoute === 'en.resource.show'
            ? route('resource.show', $currentGuide)
            : ($currentRoute === 'events.show'
                ? route('en.events.show', $currentEvent)
                : ($currentRoute === 'en.events.show'
                    ? route('events.show', $currentEvent)
                    : route($alternateRoute))));
    $siteAnnouncement = \Illuminate\Support\Facades\Schema::hasTable('announcements')
        ? \App\Models\Announcement::query()->visible()->orderBy('priority')->latest()->first()
        : null;
@endphp
<a class="skip-link" href="#main">{{ $isEnglish ? 'Skip to content' : 'Saltar para o conteúdo' }}</a>
<div class="scroll-progress" aria-hidden="true"><span data-scroll-progress></span></div>

<div class="topbar">
    <div class="container topbar-inner">
        <span>{{ $isEnglish ? 'Monday–Friday, 09:00–17:00' : 'Segunda–Sexta, 09:00–17:00' }}</span>
        <div><a href="tel:+258876052013">+258 87 605 2013</a><a href="mailto:info@bdiversity.co.mz">info@bdiversity.co.mz</a></div>
    </div>
</div>

<header class="site-header" data-header>
    <div class="container nav-wrap">
        <a href="{{ route($routes['home']) }}" class="brand" aria-label="Business Diversity">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-menu" data-menu-toggle>
            <span></span><span></span><span></span><span class="sr-only">Menu</span>
        </button>
        <nav id="site-menu" class="site-nav" aria-label="{{ $isEnglish ? 'Main navigation' : 'Navegação principal' }}" data-menu>
            @foreach ($routes as $key => $routeName)
                <a href="{{ route($routeName) }}" @class(['active' => request()->routeIs($routeName) || ($key === 'services' && request()->routeIs($isEnglish ? 'en.resource.show' : 'resource.show')) || ($key === 'events' && request()->routeIs($isEnglish ? 'en.events.show' : 'events.show'))])>{{ __('site.nav.'.$key) }}</a>
            @endforeach
            <a class="language-link" href="{{ $alternateUrl }}" hreflang="{{ $isEnglish ? 'pt' : 'en' }}">{{ $isEnglish ? 'PT' : 'EN' }}</a>
            <a class="button button-intranet" href="{{ route('announcements.login') }}" aria-label="{{ __('site.nav.intranet') }}">{{ __('site.nav.intranet') }} <span aria-hidden="true">↗</span></a>
        </nav>
    </div>
</header>

<main id="main">
    @yield('content')
</main>

@if ($siteAnnouncement)
    @php
        $announcementMediaUrl = $siteAnnouncement->mediaUrl();
        $announcementEmbedUrl = $siteAnnouncement->mediaEmbedUrl();
    @endphp
    <div class="site-announcement-modal"
        data-announcement-modal
        data-announcement-key="bd-announcement-{{ $siteAnnouncement->id }}-{{ $siteAnnouncement->updated_at?->timestamp }}"
        data-show-once="{{ $siteAnnouncement->show_once_per_session ? '1' : '0' }}"
        hidden>
        <div class="site-announcement-backdrop" data-announcement-close></div>
        <section class="site-announcement-card" role="dialog" aria-modal="true" aria-labelledby="site-announcement-title">
            <button class="site-announcement-close" type="button" data-announcement-close aria-label="{{ $isEnglish ? 'Close announcement' : 'Fechar anúncio' }}">×</button>
            @if ($announcementMediaUrl)
                <div class="site-announcement-media">
                    @if ($siteAnnouncement->media_type === 'image')
                        <img src="{{ $announcementMediaUrl }}" alt="">
                    @elseif ($announcementEmbedUrl)
                        <iframe src="{{ $announcementEmbedUrl }}" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    @else
                        <a class="site-announcement-link-card" href="{{ $announcementMediaUrl }}" target="_blank" rel="noopener">
                            <span>{{ $siteAnnouncement->mediaPlatform() }}</span>
                            <strong>{{ $isEnglish ? 'Open external media' : 'Abrir media externo' }}</strong>
                            <small>{{ $isEnglish ? 'Content is hosted outside this website.' : 'O conteúdo está alojado fora deste website.' }}</small>
                        </a>
                    @endif
                </div>
            @endif
            <div class="site-announcement-copy">
                <span class="eyebrow">{{ $isEnglish ? 'BD ANNOUNCEMENT' : 'ANÚNCIO BD' }}</span>
                <h2 id="site-announcement-title">{{ $siteAnnouncement->title }}</h2>
                @if ($siteAnnouncement->body)
                    <p>{{ $siteAnnouncement->body }}</p>
                @endif
                <div class="site-announcement-actions">
                    @if ($siteAnnouncement->button_label && $siteAnnouncement->button_url)
                        <a class="button button-primary" href="{{ $siteAnnouncement->button_url }}">{{ $siteAnnouncement->button_label }} <span>↗</span></a>
                    @endif
                    <button class="text-link" type="button" data-announcement-close>{{ $isEnglish ? 'Continue to website' : 'Continuar no website' }} <span>→</span></button>
                </div>
            </div>
        </section>
    </div>
@endif

<div class="floating-actions" aria-label="{{ $isEnglish ? 'Quick actions' : 'Acções rápidas' }}">
    <a class="floating-whatsapp" href="https://wa.me/258876052013" target="_blank" rel="noopener" aria-label="WhatsApp">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.6 4.2 1.6 6L.2 24l6.4-1.7a11.8 11.8 0 0 0 5.5 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4Zm-8.4 18.2c-1.7 0-3.5-.5-5-1.4l-.4-.2-3.8 1 1-3.7-.2-.4a9.8 9.8 0 1 1 8.4 4.7Zm5.4-7.3c-.3-.2-1.8-.9-2.1-1-.3-.1-.5-.2-.7.2-.2.3-.8 1-.9 1.2-.2.2-.3.2-.7.1-1.7-.8-2.8-1.5-3.9-3.4-.3-.5.3-.5.8-1.6.1-.2 0-.4 0-.6l-1-2.4c-.3-.6-.6-.5-.8-.5H7c-.2 0-.6.1-.9.4-.3.4-1.2 1.2-1.2 2.9s1.3 3.4 1.5 3.6c.2.2 2.5 3.8 6 5.3 2.2.9 3 .9 4.1.8.7-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.2-.1-.5-.2-.8-.3Z"/></svg>
        <span>{{ $isEnglish ? 'Chat with us' : 'Fale connosco' }}</span>
    </a>
    <button class="back-to-top" type="button" data-back-to-top aria-label="{{ $isEnglish ? 'Back to top' : 'Voltar ao topo' }}">↑</button>
</div>

<section class="cta-band">
    <div class="container cta-band-inner">
        <div><span class="eyebrow light">{{ $isEnglish ? 'LET’S BUILD TOGETHER' : 'VAMOS CONSTRUIR JUNTOS' }}</span><h2>{{ $isEnglish ? 'Ready to strengthen your organisation?' : 'Pronto para fortalecer a sua organização?' }}</h2></div>
        <a class="button button-light" href="{{ route($isEnglish ? 'en.schedule.show' : 'schedule.show') }}">{{ __('site.common.schedule') }} <span aria-hidden="true">→</span></a>
    </div>
</section>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"><p>{{ $isEnglish ? 'We help organisations focus on their core business while we strengthen people, processes and performance.' : 'Ajudamos organizações a concentrarem-se no seu negócio principal enquanto fortalecemos pessoas, processos e desempenho.' }}</p></div>
        <div><h3>{{ $isEnglish ? 'Navigate' : 'Navegar' }}</h3>@foreach ($routes as $key => $routeName)<a href="{{ route($routeName) }}">{{ __('site.nav.'.$key) }}</a>@endforeach</div>
        <div><h3>{{ $isEnglish ? 'Contact' : 'Contactos' }}</h3><a href="tel:+258876052013">+258 87 605 2013</a><a href="mailto:info@bdiversity.co.mz">info@bdiversity.co.mz</a><a href="https://maps.app.goo.gl/TPeqy9imfq2xwMyt7" target="_blank" rel="noopener">Rua da Mozal, Matola-Rio</a></div>
        <div><h3>{{ $isEnglish ? 'Connect' : 'Redes sociais' }}</h3><a href="https://bit.ly/LinkedInBDiversity" target="_blank" rel="noopener">LinkedIn ↗</a><a href="https://bit.ly/Business-Diversity-fb" target="_blank" rel="noopener">Facebook ↗</a><a href="https://wa.me/258876052013" target="_blank" rel="noopener">WhatsApp ↗</a></div>
    </div>
    <div class="container footer-bottom"><span>© {{ date('Y') }} Business Diversity CE, SA.</span><a href="{{ route('announcements.login') }}">{{ __('site.nav.intranet') }}</a></div>
</footer>
</body>
</html>
