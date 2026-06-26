@extends('layouts.app')
@section('title', $locale === 'en' ? 'Events and training' : 'Eventos e formações')
@section('description', $locale === 'en' ? 'Business Diversity events, workshops and professional training.' : 'Eventos, workshops e formações profissionais da Business Diversity.')

@section('content')
@php
    $en = $locale === 'en';
@endphp
<section class="page-hero inner-hero inner-hero-events"><div class="container inner-hero-grid">
    <div class="inner-hero-copy"><span class="eyebrow light">{{ $en ? 'EVENTS & LEARNING' : 'EVENTOS E APRENDIZAGEM' }}</span><h1>{{ $en ? 'Learning designed to change how work gets done.' : 'Aprendizagem desenhada para transformar a forma de trabalhar.' }}</h1><p>{{ $en ? 'Practical experiences built around real organisational challenges.' : 'Experiências práticas construídas em torno de desafios reais das organizações.' }}</p></div>
    <div class="inner-hero-cards learning-formats">
        <article class="learning-main-card"><span>{{ $en ? 'LEARNING THAT MOVES' : 'APRENDIZAGEM QUE MOVE' }}</span><div class="learning-pulse"><i></i><i></i><b>BD</b></div></article>
        <article><span>01</span><strong>Workshops</strong></article>
        <article><span>02</span><strong>{{ $en ? 'In-company' : 'In-company' }}</strong></article>
        <article><span>03</span><strong>{{ $en ? 'Leadership' : 'Liderança' }}</strong></article>
    </div>
</div></section>

<section class="section"><div class="container empty-events"><div class="event-visual"><img src="{{ asset('assets/images/service_03.jpg') }}" alt="{{ $en ? 'Professionals collaborating during a working session' : 'Profissionais a colaborar numa sessão de trabalho' }}"></div><div><span class="eyebrow">{{ $en ? 'UPCOMING EVENTS' : 'PRÓXIMOS EVENTOS' }}</span><h2>{{ $en ? 'New dates are being prepared.' : 'Estamos a preparar novas datas.' }}</h2><p class="lead">{{ $en ? 'Our programmes combine theory, practice and specialist guidance so participants leave equipped to make a difference.' : 'Os nossos programas combinam teoria, prática e orientação de especialistas, para que os participantes saiam preparados para fazer a diferença.' }}</p><p>{{ $en ? 'Talk to us about in-company training or ask to be notified about the next public session.' : 'Fale connosco sobre formação in-company ou peça para ser informado sobre a próxima sessão pública.' }}</p><a class="button button-primary" href="{{ route($en ? 'en.contact' : 'contact', ['subject' => $en ? 'Events and training' : 'Eventos e formações']) }}">{{ $en ? 'Register your interest' : 'Registar interesse' }} →</a></div></div></section>
@endsection
