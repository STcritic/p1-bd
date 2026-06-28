@extends('announcements.layout')
@section('title', 'Nova palavra-passe')

@section('content')
<main class="announcement-login">
    <div class="bd-login-orb" data-bd-login-orb aria-hidden="true"><span>BD</span></div>
    <a class="bd-access-back" href="{{ route('announcements.login') }}">← Voltar ao login</a>

    <section class="bd-access-card" aria-label="Definir nova palavra-passe">
        <div class="bd-access-top">
            <a class="bd-access-logo" href="{{ route('home') }}" aria-label="Business Diversity">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
            </a>
        </div>

        <div class="bd-access-copy">
            <span class="eyebrow">NOVA PALAVRA-PASSE</span>
        </div>

        @if ($errors->any())
            <div class="alert-error" role="alert">O link não é válido ou há campos por corrigir.</div>
        @endif

        <form method="POST" action="{{ route('announcements.password.update') }}" class="bd-access-form" autocomplete="off">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label>
                <span>Email</span>
                <input
                    type="text"
                    inputmode="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    required
                    autocomplete="off"
                    autocapitalize="none"
                    spellcheck="false"
                    data-lpignore="true"
                    data-1p-ignore="true"
                    placeholder="Email">
                @error('email')<small>{{ $message }}</small>@enderror
            </label>

            <label>
                <span>Nova palavra-passe</span>
                <input
                    type="password"
                    name="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    data-lpignore="true"
                    data-1p-ignore="true">
                @error('password')<small>{{ $message }}</small>@enderror
            </label>

            <label>
                <span>Confirmar nova palavra-passe</span>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    data-lpignore="true"
                    data-1p-ignore="true">
            </label>

            <button class="button button-primary" type="submit">Actualizar senha <span>→</span></button>
        </form>
    </section>
</main>
@include('announcements.partials.login-orb-script')
@endsection
