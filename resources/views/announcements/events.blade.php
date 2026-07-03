@extends('announcements.layout')
@section('title', 'Gestão de eventos')

@section('content')
<main class="announcement-dashboard">
    <header class="announcement-admin-header">
        <div>
            <a class="announcement-admin-logo" href="{{ route('home') }}"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"></a>
            <div><span class="eyebrow">PORTAL BD</span><h1>Eventos e inscrições</h1></div>
        </div>
        <div class="announcement-admin-user">
            <a class="announcement-admin-link" href="{{ route('announcements.dashboard') }}">Anúncios</a>
            <a class="announcement-admin-link is-active" href="{{ route('collaborator.events.index') }}">Eventos</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.schedule.index') }}">Agenda</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.proposals.index') }}">Propostas</a>
            <span>{{ $admin->name }}</span>
            <form method="POST" action="{{ route('announcements.logout') }}">@csrf<button type="submit">Sair</button></form>
        </div>
    </header>

    <div class="announcement-admin-shell">
        @if (session('status'))
            <div class="alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error" role="alert">Há campos por corrigir. Veja as mensagens no formulário.</div>
        @endif

        <section class="announcement-admin-grid event-admin-grid">
            <form method="POST" action="{{ route('collaborator.events.store') }}" class="announcement-panel announcement-form">
                @csrf
                <div class="announcement-panel-heading">
                    <span class="eyebrow">NOVO EVENTO</span>
                    <h2>Criar evento público</h2>
                    <p>Defina vagas, datas, imagem externa e, se fizer sentido, gere também um anúncio de abertura ligado ao evento.</p>
                </div>

                <label><span>Título *</span><input name="title" value="{{ old('title') }}" required maxlength="190">@error('title')<small>{{ $message }}</small>@enderror</label>
                <label><span>Resumo</span><textarea name="summary" rows="3" maxlength="500">{{ old('summary') }}</textarea>@error('summary')<small>{{ $message }}</small>@enderror</label>
                <label><span>Descrição / programa</span><textarea name="description" rows="5" maxlength="3000">{{ old('description') }}</textarea>@error('description')<small>{{ $message }}</small>@enderror</label>

                <div class="field-row">
                    <label><span>Público-alvo</span><input name="audience" value="{{ old('audience') }}" placeholder="Ex.: gestores, RH, líderes de equipa">@error('audience')<small>{{ $message }}</small>@enderror</label>
                    <label>
                        <span>Formato</span>
                        <select name="format" required>
                            <option value="presencial" @selected(old('format') === 'presencial')>Presencial</option>
                            <option value="online" @selected(old('format') === 'online')>Online</option>
                            <option value="hibrido" @selected(old('format') === 'hibrido')>Híbrido</option>
                        </select>
                        @error('format')<small>{{ $message }}</small>@enderror
                    </label>
                </div>

                <div class="field-row">
                    <label><span>Local / plataforma</span><input name="location" value="{{ old('location') }}" placeholder="Matola, Maputo, Zoom...">@error('location')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Número de vagas</span><input type="number" name="seats_total" min="1" max="5000" value="{{ old('seats_total') }}">@error('seats_total')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Início</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}">@error('starts_at')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Fim</span><input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}">@error('ends_at')<small>{{ $message }}</small>@enderror</label>
                </div>

                <label><span>Prazo de inscrição</span><input type="datetime-local" name="registration_deadline" value="{{ old('registration_deadline') }}">@error('registration_deadline')<small>{{ $message }}</small>@enderror</label>

                <div class="field-row">
                    <label><span>Imagem externa</span><input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://...">@error('image_url')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Link externo complementar</span><input type="url" name="external_url" value="{{ old('external_url') }}" placeholder="https://...">@error('external_url')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="announcement-check-row">
                    <label><input type="checkbox" name="is_active" value="1" checked> <span>Publicar na página Eventos</span></label>
                    <label><input type="checkbox" name="is_featured" value="1"> <span>Destacar evento</span></label>
                    <label><input type="checkbox" name="create_announcement" value="1"> <span>Criar anúncio de abertura ligado ao evento</span></label>
                </div>

                <button class="button button-primary" type="submit">Criar evento <span>→</span></button>
            </form>

            <section class="announcement-panel">
                <div class="announcement-panel-heading">
                    <span class="eyebrow">AGENDA</span>
                    <h2>Próximos eventos</h2>
                    <p>Eventos activos aparecem publicamente; eventos desactivados ficam guardados para histórico interno.</p>
                </div>

                <div class="event-admin-list">
                    @forelse ($upcomingEvents as $event)
                        @include('announcements.partials.event-admin-card', ['event' => $event])
                    @empty
                        <div class="announcement-empty">Ainda não há eventos futuros criados.</div>
                    @endforelse
                </div>
            </section>
        </section>

        <section class="announcement-panel event-history-panel">
            <div class="announcement-panel-heading">
                <span class="eyebrow">HISTÓRICO</span>
                <h2>Eventos realizados ou sem nova data</h2>
                <p>Mantenha o histórico visível na página pública quando o evento estiver activo, ou desactive para arquivo interno.</p>
            </div>

            <div class="event-admin-list event-admin-list-compact">
                @forelse ($pastEvents as $event)
                    @include('announcements.partials.event-admin-card', ['event' => $event])
                @empty
                    <div class="announcement-empty">Ainda não há histórico de eventos.</div>
                @endforelse
            </div>
        </section>
    </div>
</main>
@endsection
