@extends('layouts.app')
@section('title', $locale === 'en' ? 'Schedule a meeting' : 'Agendar reunião')
@section('description', $locale === 'en' ? 'Schedule a conversation with Business Diversity.' : 'Agende uma conversa com a Business Diversity.')

@section('content')
@php
    $en = $locale === 'en';
    $minimumDate = now($setting->timezoneName())->addMinutes($setting->minimum_notice_minutes ?: 0)->format('Y-m-d');
    $oldScheduledFor = old('scheduled_for');
    $oldDate = $oldScheduledFor ? \Illuminate\Support\Carbon::parse($oldScheduledFor)->format('Y-m-d') : '';
    $oldTime = $oldScheduledFor ? \Illuminate\Support\Carbon::parse($oldScheduledFor)->format('H:i') : '';
@endphp

<section class="schedule-hero">
    <div class="container schedule-hero-grid">
        <div>
            <span class="eyebrow light">{{ $en ? 'SCHEDULE' : 'MARCAR REUNIÃO' }}</span>
            <h1>{{ $en ? 'Choose a time to talk.' : 'Escolha um horário para falarmos.' }}</h1>
            <p>{{ $en ? 'Fill in the form and we will send the meeting details by email.' : 'Preencha o formulário e enviaremos os dados da reunião por email.' }}</p>
        </div>
        <div class="schedule-card">
            <span class="eyebrow">{{ $en ? 'MEETING DETAILS' : 'DADOS DA MARCAÇÃO' }}</span>

            @if (session('status'))
                <div class="alert-success" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert-error" role="alert">{{ $en ? 'Please review the form fields.' : 'Por favor, reveja os campos do formulário.' }}</div>
            @endif

            @if ($setting->is_active && $setting->meeting_url)
                <form method="POST" action="{{ route($en ? 'en.schedule.store' : 'schedule.store') }}" class="contact-form schedule-form" data-schedule-form data-slots-url="{{ route($en ? 'en.schedule.slots' : 'schedule.slots') }}" data-old-time="{{ $oldTime }}">
                    @csrf
                    <input class="honeypot" name="website" tabindex="-1" autocomplete="off">
                    <div class="field-row">
                        <label><span>{{ $en ? 'Name' : 'Nome' }} *</span><input name="name" value="{{ old('name') }}" required autocomplete="name">@error('name')<small>{{ $message }}</small>@enderror</label>
                        <label><span>Email *</span><input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<small>{{ $message }}</small>@enderror</label>
                    </div>
                    <div class="field-row">
                        <label><span>{{ $en ? 'Phone / WhatsApp' : 'Telefone / WhatsApp' }}</span><input name="phone" value="{{ old('phone') }}" autocomplete="tel">@error('phone')<small>{{ $message }}</small>@enderror</label>
                        <label><span>{{ $en ? 'Organisation' : 'Organização' }}</span><input name="organization" value="{{ old('organization') }}" autocomplete="organization">@error('organization')<small>{{ $message }}</small>@enderror</label>
                    </div>
                    <label><span>{{ $en ? 'Role' : 'Cargo' }}</span><input name="position" value="{{ old('position') }}">@error('position')<small>{{ $message }}</small>@enderror</label>
                    <div class="field-row">
                        <label><span>{{ $en ? 'Date' : 'Data' }} *</span><input type="date" min="{{ $minimumDate }}" value="{{ $oldDate }}" required data-schedule-date>@error('scheduled_for')<small>{{ $message }}</small>@enderror</label>
                        <label><span>{{ $en ? 'Available time' : 'Hora disponível' }} *</span><select required data-schedule-time><option value="">{{ $en ? 'Choose a date first' : 'Escolha primeiro a data' }}</option></select></label>
                    </div>
                    <input type="hidden" name="scheduled_for" value="{{ old('scheduled_for') }}" data-scheduled-for>
                    <p class="schedule-form-note">{{ $en ? 'Only available times are shown.' : 'Apenas aparecem horários disponíveis.' }}</p>
                    <label><span>{{ $en ? 'Subject' : 'Assunto' }}</span><input name="subject" value="{{ old('subject', $setting->standard_subject) }}">@error('subject')<small>{{ $message }}</small>@enderror</label>
                    <label><span>{{ $en ? 'Context' : 'Contexto da conversa' }}</span><textarea name="message" rows="4">{{ old('message') }}</textarea>@error('message')<small>{{ $message }}</small>@enderror</label>
                    <button class="button button-primary" type="submit">{{ $en ? 'Schedule meeting' : 'Agendar reunião' }} <span>→</span></button>
                </form>
            @else
                <div class="announcement-empty">{{ $en ? 'Online scheduling is not available yet.' : 'A agenda online ainda não está disponível.' }}</div>
            @endif
        </div>
    </div>
</section>
<script>
(() => {
    const form = document.querySelector('[data-schedule-form]');
    if (!form) return;

    const dateField = form.querySelector('[data-schedule-date]');
    const timeField = form.querySelector('[data-schedule-time]');
    const scheduledForField = form.querySelector('[data-scheduled-for]');
    const slotsUrl = form.dataset.slotsUrl;
    const oldTime = form.dataset.oldTime;
    const texts = {
        loading: @json($en ? 'Loading times...' : 'A carregar horários...'),
        chooseDate: @json($en ? 'Choose a date first' : 'Escolha primeiro a data'),
        chooseTime: @json($en ? 'Choose a time' : 'Escolha um horário'),
        empty: @json($en ? 'No times available for this date' : 'Sem horários disponíveis nesta data'),
        error: @json($en ? 'Unable to load times' : 'Não foi possível carregar horários'),
    };

    const setOptions = (options, placeholder) => {
        timeField.innerHTML = '';
        const first = document.createElement('option');
        first.value = '';
        first.textContent = placeholder;
        timeField.appendChild(first);

        options.forEach((slot) => {
            const option = document.createElement('option');
            option.value = slot.value;
            option.textContent = slot.label;
            if (oldTime && slot.time === oldTime) option.selected = true;
            timeField.appendChild(option);
        });

        timeField.disabled = options.length === 0;
        scheduledForField.value = timeField.value;
    };

    const loadSlots = async () => {
        scheduledForField.value = '';

        if (!dateField.value) {
            setOptions([], texts.chooseDate);
            return;
        }

        timeField.disabled = true;
        setOptions([], texts.loading);

        try {
            const response = await fetch(`${slotsUrl}?date=${encodeURIComponent(dateField.value)}`, { headers: { 'Accept': 'application/json' } });
            const payload = await response.json();
            const slots = payload.slots || [];
            setOptions(slots, slots.length ? texts.chooseTime : (payload.empty || texts.empty));
        } catch (error) {
            setOptions([], texts.error);
        }
    };

    dateField.addEventListener('change', loadSlots);
    timeField.addEventListener('change', () => {
        scheduledForField.value = timeField.value;
    });

    if (dateField.value) loadSlots();
})();
</script>
@endsection
