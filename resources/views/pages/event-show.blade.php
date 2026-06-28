@extends('layouts.app')
@section('title', $event->title)
@section('description', $event->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $event->description), 150))

@section('content')
@php
    $en = $locale === 'en';
    $remainingSeats = $event->remainingSeats();
    $registrationOpen = $event->registrationOpen();
@endphp

<section class="event-detail-hero">
    <div class="container event-detail-grid">
        <div>
            <a class="text-link event-back-link" href="{{ route($en ? 'en.events' : 'events') }}">← {{ $en ? 'Back to events' : 'Voltar aos eventos' }}</a>
            <span class="eyebrow light">{{ $en ? 'BD EVENT' : 'EVENTO BD' }}</span>
            <h1>{{ $event->title }}</h1>
            @if ($event->summary)<p>{{ $event->summary }}</p>@endif
            <div class="event-detail-facts">
                <article><span>01</span><strong>{{ $event->displayDate() }}</strong><small>{{ $en ? 'Date and time' : 'Data e hora' }}</small></article>
                <article><span>02</span><strong>{{ ucfirst($event->format) }}</strong><small>{{ $event->location ?: ($en ? 'Location to confirm' : 'Local a confirmar') }}</small></article>
                <article><span>03</span><strong>{{ $event->seats_total ? $event->seats_total : ($en ? 'Open' : 'Aberto') }}</strong><small>{{ $en ? 'Available seats' : 'Vagas definidas' }}</small></article>
            </div>
        </div>
        <div class="event-detail-cover">
            @if ($event->image_url)
                <img src="{{ $event->image_url }}" alt="">
            @else
                <div><span>BD</span><strong>{{ $en ? 'Learning experience' : 'Experiência de aprendizagem' }}</strong></div>
            @endif
        </div>
    </div>
</section>

<section class="section event-detail-section"><div class="container event-detail-content">
    <article class="event-programme">
        <span class="eyebrow">{{ $en ? 'PROGRAMME' : 'PROGRAMA' }}</span>
        <h2>{{ $en ? 'What participants can expect.' : 'O que os participantes podem esperar.' }}</h2>
        <div class="event-description">
            {!! nl2br(e($event->description ?: ($en ? 'More details will be shared by the BD team.' : 'Mais detalhes serão partilhados pela equipa BD.'))) !!}
        </div>
        @if ($event->audience)
            <div class="event-audience"><strong>{{ $en ? 'Recommended for:' : 'Recomendado para:' }}</strong> {{ $event->audience }}</div>
        @endif
        @if ($event->external_url)
            <a class="text-link" href="{{ $event->external_url }}" target="_blank" rel="noopener">{{ $en ? 'Open complementary information' : 'Abrir informação complementar' }} <span>↗</span></a>
        @endif
    </article>

    <aside class="event-registration-panel" id="inscricao">
        <span class="eyebrow">{{ $en ? 'REGISTRATION' : 'INSCRIÇÃO' }}</span>
        <h2>{{ $en ? 'Reserve your interest.' : 'Reserve o seu interesse.' }}</h2>
        @if ($remainingSeats !== null)
            <p>{{ $remainingSeats > 0 ? ($remainingSeats.' '.($en ? 'seats available.' : 'vagas disponíveis.')) : ($en ? 'Direct seats are filled. New requests enter the waiting list.' : 'As vagas directas estão preenchidas. Novos pedidos entram em lista de espera.') }}</p>
        @endif

        @if (session('status'))
            <div class="alert-success" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert-error" role="alert">{{ $en ? 'Please review the form fields.' : 'Revise os campos do formulário.' }}</div>
        @endif

        @if ($registrationOpen)
            <form method="POST" action="{{ route($en ? 'en.events.register' : 'events.register', $event) }}" class="contact-form event-registration-form">
                @csrf
                <input class="honeypot" name="website" tabindex="-1" autocomplete="off">
                <label><span>{{ $en ? 'Full name' : 'Nome completo' }} *</span><input name="name" value="{{ old('name') }}" required>@error('name')<small>{{ $message }}</small>@enderror</label>
                <div class="field-row">
                    <label><span>Email *</span><input type="email" name="email" value="{{ old('email') }}" required>@error('email')<small>{{ $message }}</small>@enderror</label>
                    <label><span>{{ $en ? 'Phone / WhatsApp' : 'Telefone / WhatsApp' }}</span><input name="phone" value="{{ old('phone') }}">@error('phone')<small>{{ $message }}</small>@enderror</label>
                </div>
                <div class="field-row">
                    <label><span>{{ $en ? 'Organisation' : 'Organização' }}</span><input name="organization" value="{{ old('organization') }}">@error('organization')<small>{{ $message }}</small>@enderror</label>
                    <label><span>{{ $en ? 'Role' : 'Cargo' }}</span><input name="position" value="{{ old('position') }}">@error('position')<small>{{ $message }}</small>@enderror</label>
                </div>
                <label><span>{{ $en ? 'Seats requested' : 'Número de participantes' }} *</span><input type="number" name="seats_requested" min="1" max="20" value="{{ old('seats_requested', 1) }}" required>@error('seats_requested')<small>{{ $message }}</small>@enderror</label>
                <label><span>{{ $en ? 'Notes' : 'Observações' }}</span><textarea name="notes" rows="4">{{ old('notes') }}</textarea>@error('notes')<small>{{ $message }}</small>@enderror</label>
                <button class="button button-primary" type="submit">{{ $en ? 'Submit registration' : 'Enviar inscrição' }} <span>→</span></button>
            </form>
        @else
            <div class="announcement-empty">{{ $en ? 'Registration for this event is closed.' : 'As inscrições para este evento estão encerradas.' }}</div>
        @endif
    </aside>
</div></section>
@endsection
