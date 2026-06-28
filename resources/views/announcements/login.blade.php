@extends('announcements.layout')
@section('title', 'Área do Colaborador')

@section('content')
<main class="announcement-login">
    <div class="bd-login-orb" data-bd-login-orb aria-hidden="true"><span>BD</span></div>
    <a class="bd-access-back" href="{{ route('home') }}">← Voltar ao website</a>

    <section class="bd-access-card" aria-label="Área do Colaborador">
        <div class="bd-access-top">
            <a class="bd-access-logo" href="{{ route('home') }}" aria-label="Business Diversity">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
            </a>
        </div>

        <div class="bd-access-copy">
            <span class="eyebrow">ÁREA DO COLABORADOR</span>
        </div>

        @if (session('status'))
            <div class="alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error" role="alert">Verifique o email e a palavra-passe.</div>
        @endif

        <form method="POST" action="{{ route('announcements.login.store') }}" class="bd-access-form" autocomplete="off">
            @csrf
            <label>
                <span>Email</span>
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
                    placeholder="Email">
                @error('bd_access_email')<small>{{ $message }}</small>@enderror
            </label>
            <label>
                <span>Palavra-passe</span>
                <input
                    type="password"
                    name="bd_access_secret"
                    required
                    autocomplete="new-password"
                    data-lpignore="true"
                    data-1p-ignore="true"
                    data-clear-secret>
                @error('bd_access_secret')<small>{{ $message }}</small>@enderror
            </label>
            <button class="button button-primary" type="submit">Entrar na gestão <span>→</span></button>
        </form>

        <div class="bd-access-meta">
            <a href="{{ route('announcements.password.expired') }}">Restaurar senha <span>↗</span></a>
            <a href="{{ config('announcements.intranet_url') }}">Interno BD <span>↗</span></a>
        </div>
    </section>
</main>
<script>
    document.querySelectorAll('[data-clear-secret]').forEach((field) => {
        field.value = '';
        window.setTimeout(() => { field.value = ''; }, 250);
        window.setTimeout(() => { field.value = ''; }, 900);
    });

</script>
@include('announcements.partials.login-orb-script')
@endsection
