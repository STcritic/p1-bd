@extends('announcements.layout')
@section('title', 'Agenda')

@section('content')
@php
    $availabilityRules = old('availability', $setting->availabilityRules());
@endphp
<main class="announcement-dashboard">
    <header class="announcement-admin-header">
        <div>
            <a class="announcement-admin-logo" href="{{ route('home') }}"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"></a>
            <div><span class="eyebrow">PORTAL BD</span><h1>Agenda</h1></div>
        </div>
        <div class="announcement-admin-user">
            <a class="announcement-admin-link" href="{{ route('announcements.dashboard') }}">Anúncios</a>
            <a class="announcement-admin-link" href="{{ route('collaborator.events.index') }}">Eventos</a>
            <a class="announcement-admin-link is-active" href="{{ route('collaborator.schedule.index') }}">Agenda</a>
            <span>{{ $admin->name }}</span>
            <form method="POST" action="{{ route('announcements.logout') }}">@csrf<button type="submit">Sair</button></form>
        </div>
    </header>

    <div class="announcement-admin-shell">
        @if (session('status'))
            <div class="alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error" role="alert">Há campos por corrigir.</div>
        @endif

        <section class="announcement-admin-grid schedule-admin-grid">
            <section class="announcement-panel">
                <div class="announcement-panel-heading">
                    <span class="eyebrow">CONFIGURAÇÃO</span>
                    <h2>Dados da reunião</h2>
                    <p>Defina o link da reunião e os emails que recebem aviso quando alguém marca pelo website.</p>
                </div>

                @if ($admin->is_master)
                    <form method="POST" action="{{ route('collaborator.schedule.setting.update') }}" class="announcement-form">
                        @csrf @method('PATCH')
                        <div class="field-row">
                            <label><span>Plataforma / local</span><input name="platform_name" value="{{ old('platform_name', $setting->platform_name) }}" placeholder="Google Meet, Teams, Zoom...">@error('platform_name')<small>{{ $message }}</small>@enderror</label>
                            <label><span>Duração</span><input type="number" name="default_duration_minutes" min="15" max="240" value="{{ old('default_duration_minutes', $setting->default_duration_minutes) }}">@error('default_duration_minutes')<small>{{ $message }}</small>@enderror</label>
                        </div>
                        <div class="field-row">
                            <label><span>Fuso horário</span><input name="timezone" value="{{ old('timezone', $setting->timezoneName()) }}" placeholder="Africa/Maputo">@error('timezone')<small>{{ $message }}</small>@enderror</label>
                            <label><span>Intervalo dos horários</span><select name="slot_interval_minutes">
                                @foreach ([15, 30, 45, 60] as $interval)
                                    <option value="{{ $interval }}" @selected((int) old('slot_interval_minutes', $setting->slot_interval_minutes ?: 30) === $interval)>{{ $interval }} min</option>
                                @endforeach
                            </select>@error('slot_interval_minutes')<small>{{ $message }}</small>@enderror</label>
                        </div>
                        <label><span>Antecedência mínima</span><input type="number" name="minimum_notice_minutes" min="0" max="10080" value="{{ old('minimum_notice_minutes', $setting->minimum_notice_minutes ?: 120) }}"><small>Em minutos. Ex.: 120 impede marcações para as próximas 2 horas.</small>@error('minimum_notice_minutes')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Link da reunião</span><input type="url" name="meeting_url" value="{{ old('meeting_url', $setting->meeting_url) }}" placeholder="https://meet.google.com/...">@error('meeting_url')<small>{{ $message }}</small>@enderror</label>
                        <div class="field-row">
                            <label><span>ID da reunião</span><input name="meeting_id" value="{{ old('meeting_id', $setting->meeting_id) }}">@error('meeting_id')<small>{{ $message }}</small>@enderror</label>
                            <label><span>Senha / passcode</span><input name="meeting_password" value="{{ old('meeting_password', $setting->meeting_password) }}">@error('meeting_password')<small>{{ $message }}</small>@enderror</label>
                        </div>
                        <label><span>Notas para o participante</span><textarea name="location_notes" rows="4" placeholder="Ex.: entrar 5 minutos antes; usar nome completo; contactos de apoio...">{{ old('location_notes', $setting->location_notes) }}</textarea>@error('location_notes')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Emails que recebem aviso</span><textarea name="notification_emails" rows="3" placeholder="um email por linha ou separado por vírgula">{{ old('notification_emails', implode(', ', $setting->notificationEmailList())) }}</textarea>@error('notification_emails')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Assunto</span><input name="standard_subject" value="{{ old('standard_subject', $setting->standard_subject) }}" required>@error('standard_subject')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Mensagem no email</span><textarea name="standard_message" rows="4">{{ old('standard_message', $setting->standard_message) }}</textarea>@error('standard_message')<small>{{ $message }}</small>@enderror</label>
                        <div class="availability-editor">
                            <div>
                                <span class="eyebrow">DISPONIBILIDADE</span>
                                <h3>Dias e horas de atendimento</h3>
                            </div>
                            @foreach (\App\Models\MeetingSetting::WEEK_DAYS as $day => $label)
                                @php
                                    $rule = $availabilityRules[(string) $day] ?? $availabilityRules[$day] ?? ['enabled' => false, 'start' => '09:00', 'end' => '17:00'];
                                @endphp
                                <div class="availability-row">
                                    <label class="availability-toggle">
                                        <input type="hidden" name="availability[{{ $day }}][enabled]" value="0">
                                        <input type="checkbox" name="availability[{{ $day }}][enabled]" value="1" @checked((bool) ($rule['enabled'] ?? false))>
                                        <span>{{ $label }}</span>
                                    </label>
                                    <input type="time" name="availability[{{ $day }}][start]" value="{{ $rule['start'] ?? '09:00' }}">
                                    <input type="time" name="availability[{{ $day }}][end]" value="{{ $rule['end'] ?? '17:00' }}">
                                </div>
                                @error("availability.{$day}.end")<small>{{ $message }}</small>@enderror
                            @endforeach
                        </div>
                        <div class="announcement-check-row">
                            <label><input type="checkbox" name="is_active" value="1" @checked($setting->is_active)> <span>Agenda pública activa</span></label>
                        </div>
                        <button class="button button-primary" type="submit">Guardar configuração <span>→</span></button>
                    </form>
                @else
                    <div class="schedule-setting-readonly">
                        <p>Apenas a conta master pode alterar estes dados.</p>
                        <strong>{{ $setting->platform_name ?: 'Plataforma não definida' }}</strong>
                        <span>{{ $setting->meeting_url ?: 'Link não definido' }}</span>
                    </div>
                @endif
            </section>

            <section class="announcement-panel">
                <div class="announcement-panel-heading">
                    <span class="eyebrow">MARCAÇÕES</span>
                    <h2>Pedidos feitos no website</h2>
                    <p>As marcações feitas no site aparecem aqui.</p>
                </div>

                <div class="schedule-blocks">
                    <div class="announcement-panel-heading compact">
                        <span class="eyebrow">BLOQUEIOS</span>
                        <h3>Horários ocupados</h3>
                    </div>

                    @if ($admin->is_master)
                        <form method="POST" action="{{ route('collaborator.schedule.blocks.store') }}" class="schedule-block-form">
                            @csrf
                            <label><span>Título</span><input name="title" value="{{ old('title', 'Indisponível') }}" required>@error('title')<small>{{ $message }}</small>@enderror</label>
                            <div class="field-row">
                                <label><span>Início</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required>@error('starts_at')<small>{{ $message }}</small>@enderror</label>
                                <label><span>Fim</span><input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" required>@error('ends_at')<small>{{ $message }}</small>@enderror</label>
                            </div>
                            <label><span>Nota</span><input name="notes" value="{{ old('notes') }}" placeholder="Ex.: reunião externa, feriado, deslocação...">@error('notes')<small>{{ $message }}</small>@enderror</label>
                            <button type="submit">Bloquear horário</button>
                        </form>
                    @endif

                    <div class="schedule-block-list">
                        @forelse ($blocks as $block)
                            <article>
                                <div>
                                    <strong>{{ $block->title }}</strong>
                                    <span>{{ $block->starts_at->timezone($setting->timezoneName())->format('d/m/Y H:i') }} — {{ $block->ends_at->timezone($setting->timezoneName())->format('d/m/Y H:i') }}</span>
                                    @if ($block->notes)<small>{{ $block->notes }}</small>@endif
                                </div>
                                @if ($admin->is_master)
                                    <form method="POST" action="{{ route('collaborator.schedule.blocks.destroy', $block) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit">Remover</button>
                                    </form>
                                @endif
                            </article>
                        @empty
                            <div class="announcement-empty">Sem bloqueios activos.</div>
                        @endforelse
                    </div>
                </div>

                <div class="appointment-admin-list">
                    @forelse ($appointments as $appointment)
                        <article class="appointment-admin-card">
                            <div>
                                <span @class(['appointment-status', 'is-completed' => $appointment->status === 'completed', 'is-cancelled' => $appointment->status === 'cancelled'])>{{ $appointment->statusLabel() }}</span>
                                <h3>{{ $appointment->subject ?: 'Conversa BD' }}</h3>
                                <p>{{ $appointment->name }} · {{ $appointment->email }}{{ $appointment->phone ? ' · '.$appointment->phone : '' }}</p>
                                <small>{{ $appointment->scheduledLocal()->format('d/m/Y H:i') }} · {{ $appointment->duration_minutes }} min · {{ $appointment->meeting_platform ?: 'Plataforma não definida' }}</small>
                                @if ($appointment->organization || $appointment->position)
                                    <small>{{ $appointment->organization }}{{ $appointment->position ? ' · '.$appointment->position : '' }}</small>
                                @endif
                                @if ($appointment->message)
                                    <p>{{ $appointment->message }}</p>
                                @endif
                                @if ($appointment->meeting_url)
                                    <a class="text-link" href="{{ $appointment->meeting_url }}" target="_blank" rel="noopener">Abrir reunião <span>↗</span></a>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('collaborator.schedule.appointments.update', $appointment) }}" class="appointment-update-form">
                                @csrf @method('PATCH')
                                <select name="status">
                                    <option value="scheduled" @selected($appointment->status === 'scheduled')>Agendada</option>
                                    <option value="completed" @selected($appointment->status === 'completed')>Realizada</option>
                                    <option value="cancelled" @selected($appointment->status === 'cancelled')>Cancelada</option>
                                </select>
                                <textarea name="internal_notes" rows="3" placeholder="Nota interna">{{ $appointment->internal_notes }}</textarea>
                                <button type="submit">Actualizar</button>
                            </form>
                        </article>
                    @empty
                        <div class="announcement-empty">Ainda não há marcações feitas no website.</div>
                    @endforelse
                </div>
            </section>
        </section>
    </div>
</main>
@endsection
