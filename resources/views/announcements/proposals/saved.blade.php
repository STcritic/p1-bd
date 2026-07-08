@extends('announcements.layout')
@section('title', 'Propostas guardadas')

@section('content')
<main class="announcement-dashboard">
    <header class="announcement-admin-header">
        <div>
            <a class="announcement-admin-logo" href="{{ route('home') }}"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"></a>
            <div><span class="eyebrow">PORTAL BD</span><h1>Propostas guardadas</h1></div>
        </div>
        <div class="announcement-admin-user">
            <a class="announcement-admin-link" href="{{ route('announcements.dashboard') }}">Anúncios</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.events.index') }}">Eventos</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.schedule.index') }}">Agenda</a>
            <a class="announcement-admin-link is-active" href="{{ route('collaborator.proposals.index') }}">Propostas</a>
            <span>{{ $admin->name }}</span>
            <form method="POST" action="{{ route('announcements.logout') }}">@csrf<button type="submit">Sair</button></form>
        </div>
    </header>

    <div class="announcement-admin-shell">
        @if (session('success'))
            <div class="alert-success" role="alert">{{ session('success') }}</div>
        @endif

        <div class="proposals-saved-header">
            <div>
                <h2>Histórico de propostas</h2>
                <p>{{ $proposals->total() }} proposta(s) gerada(s)</p>
            </div>
            <a href="{{ route('collaborator.proposals.index') }}" class="button button-primary">+ Nova proposta</a>
        </div>

        @if ($proposals->isEmpty())
            <div class="proposals-saved-empty">
                <p>Ainda não tem propostas guardadas.</p>
                <a href="{{ route('collaborator.proposals.index') }}" class="button button-primary">Gerar primeira proposta</a>
            </div>
        @else
            <div class="proposals-saved-filters no-print">
                @foreach (\App\Models\Proposal::statuses() as $key => $label)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}"
                       class="proposal-filter-chip {{ request('status') === $key ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
                @if (request('status'))
                    <a href="{{ route('collaborator.proposals.saved') }}" class="proposal-filter-chip">Todos</a>
                @endif
            </div>

            <div class="proposals-saved-table">
                <div class="proposals-saved-table-head">
                    <span>Referência</span>
                    <span>Cliente</span>
                    <span>Serviço</span>
                    <span>Estado</span>
                    <span>Validade</span>
                    <span>Data</span>
                    <span></span>
                </div>
                @foreach ($proposals as $proposal)
                    <div class="proposals-saved-row {{ $proposal->isExpired() ? 'is-expired' : '' }}">
                        <span class="proposals-saved-ref">{{ $proposal->reference }}</span>
                        <div class="proposals-saved-client">
                            <strong>{{ $proposal->client_name }}</strong>
                            @if ($proposal->client_contact)
                                <small>{{ $proposal->client_contact }}</small>
                            @endif
                        </div>
                        <span>{{ $proposal->service_title }}</span>
                        <span>
                            <span class="proposal-status-badge proposal-status-{{ $proposal->statusColor() }}">{{ $proposal->statusLabel() }}</span>
                        </span>
                        <span class="{{ $proposal->isExpired() ? 'text-danger' : '' }}">
                            {{ $proposal->expires_at ? $proposal->expires_at->format('d/m/Y') : '—' }}
                        </span>
                        <span>{{ $proposal->created_at->format('d/m/Y') }}</span>
                        <div class="proposals-saved-actions">
                            <a href="{{ route('collaborator.proposals.show', $proposal) }}" class="proposals-saved-view">Ver</a>
                            <form method="POST" action="{{ route('collaborator.proposals.destroy', $proposal) }}"
                                  onsubmit="return confirm('Eliminar esta proposta?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="proposals-saved-delete">×</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="proposals-saved-pagination">
                {{ $proposals->links() }}
            </div>
        @endif
    </div>
</main>
@endsection
