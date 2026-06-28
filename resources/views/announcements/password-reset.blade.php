@extends('announcements.layout')
@section('title', 'Restaurar acesso')

@section('content')
<main class="announcement-login">
    <div class="bd-login-orb" data-bd-login-orb aria-hidden="true"><span>BD</span></div>
    <a class="bd-access-back" href="{{ route('announcements.login') }}">← Voltar ao login</a>

    <section class="bd-access-card" aria-label="Restaurar acesso">
        <div class="bd-access-top">
            <a class="bd-access-logo" href="{{ route('home') }}" aria-label="Business Diversity">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
            </a>
        </div>

        <div class="bd-access-copy">
            <span class="eyebrow">RESTAURO SEGURO</span>
        </div>

        @if (session('status'))
            <div class="alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error" role="alert">Não foi possível enviar o link agora.</div>
        @endif

        <form method="POST" action="{{ route('announcements.password.expired.store') }}" class="bd-access-form" autocomplete="off">
            @csrf
            <label>
                <span>Email autorizado</span>
                <input
                    type="text"
                    inputmode="email"
                    name="bd_access_email"
                    value="{{ old('bd_access_email') }}"
                    required
                    autocomplete="off"
                    autocapitalize="none"
                    spellcheck="false"
                    data-lpignore="true"
                    data-1p-ignore="true"
                    placeholder="Email autorizado">
                @error('bd_access_email')<small>{{ $message }}</small>@enderror
            </label>

            <button class="button button-primary" type="submit">Enviar link seguro <span>→</span></button>
        </form>

        <div class="bd-access-meta">
            <a href="{{ route('announcements.login') }}">Login <span>↗</span></a>
            <a href="{{ config('announcements.intranet_url') }}">Interno BD <span>↗</span></a>
        </div>
    </section>
</main>
@include('announcements.partials.login-orb-script')
@endsection
