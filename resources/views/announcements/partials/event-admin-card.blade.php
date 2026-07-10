@php
    $remainingSeats = $event->remainingSeats();
    $reservedSeats = $event->reservedSeats();
@endphp
<article class="event-admin-card">
    <div class="event-admin-main">
        <span @class(['announcement-status', 'is-active' => $event->is_active])>{{ $event->is_active ? 'Activo' : 'Inactivo' }}</span>
        <h3>{{ $event->title }}</h3>
        <p>{{ \Illuminate\Support\Str::limit($event->summary ?: $event->description, 150) ?: 'Sem resumo definido.' }}</p>
        <div class="event-admin-meta">
            <span>{{ $event->displayDate() }}</span>
            <span>{{ ucfirst($event->format) }}</span>
            @if ($event->location)<span>{{ $event->location }}</span>@endif
            @if ($event->seats_total)
                <span>{{ $reservedSeats }}/{{ $event->seats_total }} vagas reservadas</span>
            @else
                <span>Sem limite de vagas</span>
            @endif
            @if ($remainingSeats !== null)
                <span>{{ $remainingSeats }} vagas disponíveis</span>
            @endif
        </div>
        <div class="event-admin-actions-row">
            <a class="button button-light" href="{{ route('events.show', $event) }}" target="_blank" rel="noopener">Abrir página <span>↗</span></a>
            <form method="POST" action="{{ route('collaborator.events.toggle', $event) }}">
                @csrf @method('PATCH')
                <button type="submit">{{ $event->is_active ? 'Desactivar' : 'Activar' }}</button>
            </form>
        </div>
    </div>

    <details class="event-admin-details">
        <summary>Editar evento</summary>
        <form method="POST" action="{{ route('collaborator.events.update', $event) }}" class="announcement-form event-edit-form">
            @csrf @method('PATCH')
            <label><span>Título</span><input name="title" value="{{ old('title', $event->title) }}" required maxlength="190"></label>
            <label><span>Resumo</span><textarea name="summary" rows="3" maxlength="500">{{ old('summary', $event->summary) }}</textarea></label>
            <label><span>Descrição / programa</span><textarea name="description" rows="4" maxlength="3000">{{ old('description', $event->description) }}</textarea></label>
            <div class="field-row">
                <label><span>Público-alvo</span><input name="audience" value="{{ old('audience', $event->audience) }}"></label>
                <label>
                    <span>Formato</span>
                    <select name="format">
                        <option value="presencial" @selected($event->format === 'presencial')>Presencial</option>
                        <option value="online" @selected($event->format === 'online')>Online</option>
                        <option value="hibrido" @selected($event->format === 'hibrido')>Híbrido</option>
                    </select>
                </label>
            </div>
            <div class="field-row">
                <label><span>Local / plataforma</span><input name="location" value="{{ old('location', $event->location) }}"></label>
                <label><span>Número de vagas</span><input type="number" name="seats_total" min="1" max="5000" value="{{ old('seats_total', $event->seats_total) }}"></label>
            </div>
            <div class="field-row">
                <label><span>Início</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}"></label>
                <label><span>Fim</span><input type="datetime-local" name="ends_at" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}"></label>
            </div>
            <label><span>Prazo de inscrição</span><input type="datetime-local" name="registration_deadline" value="{{ old('registration_deadline', $event->registration_deadline?->format('Y-m-d\TH:i')) }}"></label>
            <div class="field-row">
                <label><span>Imagem externa</span><input type="url" name="image_url" value="{{ old('image_url', $event->image_url) }}"></label>
                <label><span>Link externo complementar</span><input type="url" name="external_url" value="{{ old('external_url', $event->external_url) }}"></label>
            </div>
            <div class="announcement-check-row">
                <label><input type="checkbox" name="is_active" value="1" @checked($event->is_active)> <span>Activo</span></label>
                <label><input type="checkbox" name="is_featured" value="1" @checked($event->is_featured)> <span>Destacado</span></label>
                @unless ($event->announcement_id)
                    <label><input type="checkbox" name="create_announcement" value="1"> <span>Criar anúncio de abertura</span></label>
                @endunless
            </div>
            <button class="button button-primary" type="submit">Guardar alterações</button>
        </form>
    </details>

    <details class="event-admin-details" open>
        <summary>Inscrições: {{ $event->registrations->count() }}</summary>
        <div class="event-registration-list">
            @forelse ($event->registrations as $registration)
                <article>
                    <div>
                        <strong>{{ $registration->name }}</strong>
                        <span>{{ $registration->email }}{{ $registration->phone ? ' · '.$registration->phone : '' }}</span>
                        <small>{{ $registration->organization ?: 'Sem organização' }}{{ $registration->position ? ' · '.$registration->position : '' }} · {{ $registration->seats_requested }} vaga(s)</small>
                        @if ($registration->notes)<p>{{ $registration->notes }}</p>@endif
                    </div>
                    <form method="POST" action="{{ route('collaborator.events.registrations.update', $registration) }}" class="event-registration-actions">
                        @csrf @method('PATCH')
                        <select name="status">
                            <option value="pending" @selected($registration->status === 'pending')>Pendente</option>
                            <option value="confirmed" @selected($registration->status === 'confirmed')>Confirmado</option>
                            <option value="waitlist" @selected($registration->status === 'waitlist')>Lista de espera</option>
                            <option value="cancelled" @selected($registration->status === 'cancelled')>Cancelado</option>
                        </select>
                        <input name="internal_notes" value="{{ $registration->internal_notes }}" placeholder="Nota interna">
                        <button type="submit">Actualizar</button>
                    </form>
                </article>
            @empty
                <div class="announcement-empty">Ainda não há inscrições para este evento.</div>
            @endforelse
        </div>
    </details>
</article>
