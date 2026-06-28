@extends('layouts.app')
@section('title', $locale === 'en' ? 'Events and training' : 'Eventos e formações')
@section('description', $locale === 'en' ? 'Business Diversity events, workshops and professional training.' : 'Eventos, workshops e formações profissionais da Business Diversity.')

@section('content')
@php($en = $locale === 'en')
<section class="page-hero inner-hero inner-hero-events"><div class="container inner-hero-grid">
    <div class="inner-hero-copy"><span class="eyebrow light">{{ $en ? 'EVENTS & LEARNING' : 'EVENTOS E APRENDIZAGEM' }}</span><h1>{{ $en ? 'Learning designed to change how work gets done.' : 'Aprendizagem desenhada para transformar a forma de trabalhar.' }}</h1><p>{{ $en ? 'Practical experiences built around real organisational challenges.' : 'Experiências práticas construídas em torno de desafios reais das organizações.' }}</p></div>
    <div class="inner-hero-cards learning-formats">
        <article class="learning-main-card"><span>{{ $en ? 'LEARNING THAT MOVES' : 'APRENDIZAGEM QUE MOVE' }}</span><div class="learning-pulse"><i></i><i></i><b>BD</b></div></article>
        <article><span>01</span><strong>Workshops</strong></article>
        <article><span>02</span><strong>In-company</strong></article>
        <article><span>03</span><strong>{{ $en ? 'Leadership' : 'Liderança' }}</strong></article>
    </div>
</div></section>

@if ($events->isNotEmpty())
    <section class="section events-showcase"><div class="container">
        <div class="section-header">
            <div><span class="eyebrow">{{ $en ? 'UPCOMING EVENTS' : 'PRÓXIMOS EVENTOS' }}</span><h2>{{ $en ? 'Choose the session that fits your agenda.' : 'Escolha a sessão que responde ao seu momento.' }}</h2></div>
            <p>{{ $en ? 'Register directly on the website. The BD team will confirm availability and next steps.' : 'Inscreva-se directamente no website. A equipa BD confirma disponibilidade e próximos passos.' }}</p>
        </div>

        <div class="event-card-grid">
            @foreach ($events as $event)
                @php($remainingSeats = $event->remainingSeats())
                <article @class(['public-event-card', 'is-featured' => $event->is_featured])>
                    @if ($event->image_url)
                        <img src="{{ $event->image_url }}" alt="">
                    @endif
                    <div>
                        <span class="event-date">{{ $event->displayDate() }}</span>
                        <h3>{{ $event->title }}</h3>
                        <p>{{ $event->summary ?: \Illuminate\Support\Str::limit($event->description, 150) }}</p>
                        <ul class="event-facts">
                            <li>{{ ucfirst($event->format) }}</li>
                            @if ($event->location)<li>{{ $event->location }}</li>@endif
                            @if ($remainingSeats !== null)
                                <li>{{ $remainingSeats > 0 ? ($remainingSeats.' '.($en ? 'seats available' : 'vagas disponíveis')) : ($en ? 'Waiting list' : 'Lista de espera') }}</li>
                            @else
                                <li>{{ $en ? 'Open registration' : 'Inscrição aberta' }}</li>
                            @endif
                        </ul>
                        <a class="button button-primary" href="{{ route($en ? 'en.events.show' : 'events.show', $event) }}">{{ $en ? 'View and register' : 'Ver e inscrever-se' }} <span>→</span></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div></section>
@else
    <section class="section events-empty-section">
        <div class="container events-empty-state">
            <span class="eyebrow">{{ $en ? 'UPCOMING EVENTS' : 'PRÓXIMOS EVENTOS' }}</span>
            <h2>{{ $en ? 'No public event is active at the moment.' : 'Nenhum evento público activo no momento.' }}</h2>
            <p>{{ $en ? 'New dates will appear here as soon as they are published by the BD team through the collaborator area.' : 'As próximas datas aparecerão aqui assim que forem publicadas pela equipa BD através da Área do Colaborador.' }}</p>
        </div>
    </section>
@endif

@if ($pastEvents->isNotEmpty())
    <section class="section event-history-section"><div class="container">
        <div class="section-header">
            <div><span class="eyebrow">{{ $en ? 'EVENT HISTORY' : 'HISTÓRICO' }}</span><h2>{{ $en ? 'Sessions already delivered.' : 'Sessões já realizadas.' }}</h2></div>
            <p>{{ $en ? 'A living record of learning initiatives supported by BD.' : 'Um registo vivo das iniciativas de aprendizagem conduzidas pela BD.' }}</p>
        </div>
        <div class="event-history-grid">
            @foreach ($pastEvents as $event)
                <article>
                    <span>{{ $event->starts_at?->format('d/m/Y') }}</span>
                    <h3>{{ $event->title }}</h3>
                    <p>{{ $event->summary ?: \Illuminate\Support\Str::limit($event->description, 110) }}</p>
                    <a class="text-link" href="{{ route($en ? 'en.events.show' : 'events.show', $event) }}">{{ $en ? 'View record' : 'Ver registo' }} <span>→</span></a>
                </article>
            @endforeach
        </div>
    </div></section>
@endif
@endsection
