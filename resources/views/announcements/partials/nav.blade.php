@php $en = ($collabLang ?? 'pt') === 'en'; @endphp
<header class="announcement-admin-header">
    <div>
        <a class="announcement-admin-logo" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
        </a>
        <div>
            <span class="eyebrow">{{ $en ? 'BD PORTAL' : 'PORTAL BD' }}</span>
            <h1>{{ $pageTitle ?? 'Portal BD' }}</h1>
        </div>
    </div>
    <nav class="announcement-admin-user">
        <a class="announcement-admin-link {{ ($active ?? '') === 'anuncios'      ? 'is-active' : '' }}"
           href="{{ route('announcements.dashboard') }}">{{ $en ? 'Announcements' : 'Anúncios' }}</a>
        <a class="announcement-admin-link {{ ($active ?? '') === 'eventos'       ? 'is-active' : '' }}"
           href="{{ route('collaborator.events.index') }}">{{ $en ? 'Events' : 'Eventos' }}</a>
        <a class="announcement-admin-link {{ ($active ?? '') === 'agenda'        ? 'is-active' : '' }}"
           href="{{ route('collaborator.schedule.index') }}">{{ $en ? 'Schedule' : 'Agenda' }}</a>
        <a class="announcement-admin-link {{ ($active ?? '') === 'propostas'     ? 'is-active' : '' }}"
           href="{{ route('collaborator.proposals.index') }}">{{ $en ? 'Proposals' : 'Propostas' }}</a>
        <a class="announcement-admin-link {{ ($active ?? '') === 'oportunidades' ? 'is-active' : '' }}"
           href="{{ route('collaborator.opportunities.index') }}">{{ $en ? 'Opportunities' : 'Oportunidades' }}</a>
        <span>{{ $announcementAdmin->name }}</span>
        @if($announcementAdmin->password_expires_at)
            <span class="pw-expiry">{{ $en ? 'Password valid until' : 'Senha válida até' }} {{ $announcementAdmin->password_expires_at->format('d/m/Y') }}</span>
        @endif
        <form method="POST" action="{{ route('collaborator.set-language') }}" style="display:inline">
            @csrf
            <input type="hidden" name="lang" value="{{ $en ? 'pt' : 'en' }}">
            <button type="submit" class="collab-lang-toggle" title="{{ $en ? 'Switch to Portuguese' : 'Switch to English' }}">
                {{ $en ? 'PT' : 'EN' }}
            </button>
        </form>
        <form method="POST" action="{{ route('announcements.logout') }}">
            @csrf
            <button type="submit">{{ $en ? 'Sign out' : 'Sair' }}</button>
        </form>
    </nav>
</header>
