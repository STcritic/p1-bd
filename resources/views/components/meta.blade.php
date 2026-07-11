@props([
    'locale' => app()->getLocale(),
    'canonicalUrl' => url()->current(),
    'defaultUrl' => url()->current(),
])

@php
    $title = trim($__env->yieldContent('title', 'Business Diversity'));
    $fullTitle = str_ends_with($title, 'Business Diversity') ? $title : $title.' | Business Diversity';
    $description = trim($__env->yieldContent('description', $locale === 'en'
        ? 'Business consulting and human capital solutions in Mozambique.'
        : 'Consultoria empresarial e soluções de capital humano em Moçambique.'
    ));
    $canonical = trim($__env->yieldContent('canonical', $canonicalUrl));
    $robots = trim($__env->yieldContent('robots', 'index,follow,max-image-preview:large'));
    $ogType = trim($__env->yieldContent('og_type', 'website'));
    $ogImage = trim($__env->yieldContent('image', asset('assets/img/og/business-diversity-og.jpg')));
    $ogImageAlt = trim($__env->yieldContent('image_alt', 'Business Diversity'));
    $assetVersion = rawurlencode((string) config('app.version', '1.0.0'));
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => 'Business Diversity CE, SA',
        'url' => url('/'),
        'telephone' => '+258876052013',
        'email' => 'info@bdiversity.co.mz',
        'logo' => asset('assets/img/logo/logo.png'),
        'image' => $ogImage,
        'inLanguage' => $locale === 'en' ? 'en' : 'pt',
        'sameAs' => [
            'https://www.linkedin.com/company/business-diversity/',
            'https://www.facebook.com/people/Business-Diversity/100064670336347/',
        ],
        'contactPoint' => [
            [
                '@type' => 'ContactPoint',
                'telephone' => '+258876052013',
                'contactType' => 'customer service',
                'availableLanguage' => ['Portuguese', 'English'],
            ],
        ],
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Mozambique',
        ],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Beleluane, Matola Rio',
            'addressLocality' => 'Maputo',
            'addressCountry' => 'MZ',
        ],
    ];
@endphp

<meta name="theme-color" content="#073b73">
<meta name="author" content="Business Diversity CE, SA">
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $defaultUrl }}">

<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:site_name" content="Business Diversity">
<meta property="og:locale" content="{{ $locale === 'en' ? 'en_US' : 'pt_PT' }}">
@if ($locale === 'pt')
    <meta property="og:locale:alternate" content="en_US">
@else
    <meta property="og:locale:alternate" content="pt_PT">
@endif
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:secure_url" content="{{ $ogImage }}">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $ogImageAlt }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">
<meta name="twitter:image:alt" content="{{ $ogImageAlt }}">

<meta name="msapplication-TileColor" content="#073b73">
<link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}?v={{ $assetVersion }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}?v={{ $assetVersion }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}?v={{ $assetVersion }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}?v={{ $assetVersion }}">
<link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}?v={{ $assetVersion }}">

<script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
