@extends('layouts.app')
@section('title', $locale === 'en' ? 'Contact us' : 'Contacte-nos')
@section('description', $locale === 'en' ? 'Talk to Business Diversity about your human capital and organisational challenges.' : 'Fale com a Business Diversity sobre os seus desafios de capital humano e organização.')

@section('content')
@php
    $en = $locale === 'en';
    $diagnostic = request('diagnostico');
    $selectedService = request('service') ?? request('subject');
    $serviceLabel = $selectedService ?: ($en ? 'this service' : 'este serviço');
    $diagnosticMessage = $diagnostic
        ? ($en
            ? "Hello BD,\n\nI completed the quick diagnostic for {$serviceLabel} and the result was: {$diagnostic}.\n\nI would like to discuss the next steps."
            : "Olá BD,\n\nFiz o diagnóstico rápido para {$serviceLabel} e o resultado foi: {$diagnostic}.\n\nGostaria de discutir os próximos passos.")
        : '';
@endphp
<section class="page-hero inner-hero inner-hero-contact"><div class="container inner-hero-grid">
    <div class="inner-hero-copy"><span class="eyebrow light">{{ $en ? 'START HERE' : 'COMECE AQUI' }}</span><h1>{{ $en ? 'Your next people decision can start with one conversation.' : 'A sua próxima decisão sobre pessoas pode começar com uma conversa.' }}</h1><p>{{ $en ? 'Choose the easiest way to reach us. We are ready to understand the challenge.' : 'Escolha a forma mais simples de falar connosco. Estamos prontos para compreender o desafio.' }}</p></div>
    <div class="inner-hero-cards contact-actions">
        <a href="{{ route($en ? 'en.schedule.show' : 'schedule.show') }}"><span>01</span><div><small>{{ $en ? '30-minute conversation' : 'Conversa de 30 minutos' }}</small><strong>{{ $en ? 'Schedule a meeting' : 'Agendar reunião' }}</strong></div><i>→</i></a>
        <a href="https://wa.me/258876052013" target="_blank" rel="noopener"><span>02</span><div><small>{{ $en ? 'Direct channel' : 'Canal directo' }}</small><strong>WhatsApp</strong></div><i>↗</i></a>
        <a href="mailto:info@bdiversity.co.mz"><span>03</span><div><small>{{ $en ? 'Send your brief' : 'Envie o seu pedido' }}</small><strong>Email</strong></div><i>↗</i></a>
    </div>
</div></section>

<section class="section contact-section"><div class="container contact-grid"><div class="contact-details"><span class="eyebrow">{{ $en ? 'GET IN TOUCH' : 'FALE CONNOSCO' }}</span><h2>{{ $en ? 'We are ready to listen.' : 'Estamos prontos para escutar.' }}</h2><p>{{ $en ? 'Use the form or choose the channel that works best for you.' : 'Use o formulário ou escolha o canal mais conveniente para si.' }}</p>
    <div class="contact-method"><span>01</span><div><small>{{ $en ? 'Phone / WhatsApp' : 'Telefone / WhatsApp' }}</small><a href="https://wa.me/258876052013">+258 87 605 2013</a></div></div>
    <div class="contact-method"><span>02</span><div><small>Email</small><a href="mailto:info@bdiversity.co.mz">info@bdiversity.co.mz</a></div></div>
    <div class="contact-method"><span>03</span><div><small>{{ $en ? 'Office' : 'Escritório' }}</small><a href="https://maps.app.goo.gl/TPeqy9imfq2xwMyt7" target="_blank" rel="noopener">Rua da Mozal, Matola-Rio ↗</a></div></div>
    <a class="arrow-link" href="{{ route($en ? 'en.schedule.show' : 'schedule.show') }}">{{ __('site.common.schedule') }} →</a>
</div>
<div class="form-panel">
    @if (session('status'))<div class="alert-success" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert-error" role="alert">{{ $en ? 'Please review the highlighted fields.' : 'Por favor, reveja os campos assinalados.' }}</div>@endif
    <form method="POST" action="{{ route($en ? 'en.contact.store' : 'contact.store') }}" class="contact-form">
        @csrf
        <div class="honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
        <div class="field-row"><label><span>{{ $en ? 'Name' : 'Nome' }} *</span><input name="name" value="{{ old('name') }}" required autocomplete="name" @class(['invalid' => $errors->has('name')])>@error('name')<small>{{ $message }}</small>@enderror</label><label><span>Email *</span><input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" @class(['invalid' => $errors->has('email')])>@error('email')<small>{{ $message }}</small>@enderror</label></div>
        <div class="field-row"><label><span>{{ $en ? 'Phone' : 'Telefone' }}</span><input name="phone" value="{{ old('phone') }}" autocomplete="tel"></label><label><span>{{ $en ? 'Company' : 'Empresa' }}</span><input name="company" value="{{ old('company') }}" autocomplete="organization"></label></div>
        <label><span>{{ $en ? 'Subject' : 'Assunto' }} *</span><input name="subject" value="{{ old('subject', request('subject') ?? request('service')) }}" required @class(['invalid' => $errors->has('subject')])>@error('subject')<small>{{ $message }}</small>@enderror</label>
        <label><span>{{ $en ? 'How can we help?' : 'Como podemos ajudar?' }} *</span><textarea name="message" rows="6" required @class(['invalid' => $errors->has('message')])>{{ old('message', $diagnosticMessage) }}</textarea>@error('message')<small>{{ $message }}</small>@enderror</label>
        <button class="button button-primary" type="submit">{{ $en ? 'Send message' : 'Enviar mensagem' }} →</button>
    </form>
</div></div></section>
@endsection
