<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MeetingSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Throwable;

class AppointmentController extends Controller
{
    public function show(Request $request): View
    {
        $locale = $request->routeIs('en.*') ? 'en' : 'pt';
        app()->setLocale($locale);

        return view('pages.schedule', [
            'locale' => $locale,
            'setting' => MeetingSetting::current(),
        ]);
    }

    public function slots(Request $request): JsonResponse
    {
        $locale = $request->routeIs('en.*') ? 'en' : 'pt';
        $setting = MeetingSetting::current();

        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        return response()->json([
            'slots' => $setting->availableSlotsForDate($data['date']),
            'empty' => $locale === 'en' ? 'No times available for this date.' : 'Sem horários disponíveis nesta data.',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $locale = $request->routeIs('en.*') ? 'en' : 'pt';
        app()->setLocale($locale);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'organization' => ['nullable', 'string', 'max:190'],
            'position' => ['nullable', 'string', 'max:190'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['nullable', 'string', 'max:1200'],
            'scheduled_for' => ['required', 'date', 'after:now'],
            'website' => ['nullable', 'max:0'],
        ]);

        $setting = MeetingSetting::current();

        if (! $setting->is_active || ! $setting->meeting_url) {
            return back()
                ->withInput()
                ->withErrors(['scheduled_for' => $locale === 'en' ? 'Online scheduling is not available yet.' : 'A agenda online ainda não está disponível.']);
        }

        $requestedStart = Carbon::parse($data['scheduled_for'], $setting->timezoneName());

        if (! $setting->acceptsAppointmentAt($requestedStart)) {
            return back()
                ->withInput()
                ->withErrors(['scheduled_for' => $locale === 'en' ? 'This time is not available. Please choose another one.' : 'Este horário não está disponível. Escolha outro horário.']);
        }

        $appointment = Appointment::query()->create([
            'meeting_setting_id' => $setting->id,
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'organization' => $data['organization'] ?? null,
            'position' => $data['position'] ?? null,
            'subject' => $data['subject'] ?: $setting->standard_subject,
            'message' => $data['message'] ?? null,
            'scheduled_for' => $requestedStart,
            'duration_minutes' => $setting->default_duration_minutes,
            'timezone' => $setting->timezoneName(),
            'status' => 'scheduled',
            'meeting_platform' => $setting->platform_name,
            'meeting_url' => $setting->meeting_url,
            'meeting_id' => $setting->meeting_id,
            'meeting_password' => $setting->meeting_password,
            'location_notes' => $setting->location_notes,
            'ip_address' => $request->ip(),
        ]);

        $this->sendAppointmentEmails($appointment, $setting);

        return back()->with('status', $locale === 'en'
            ? 'Meeting scheduled. We sent the details by email.'
            : 'Reunião marcada. Enviámos os detalhes por email.');
    }

    private function sendAppointmentEmails(Appointment $appointment, MeetingSetting $setting): void
    {
        try {
            Mail::send('emails.appointment-confirmation', [
                'appointment' => $appointment,
                'setting' => $setting,
            ], function ($message) use ($appointment): void {
                $message
                    ->to($appointment->email, $appointment->name)
                    ->subject('Confirmação de reunião | Business Diversity');
            });
        } catch (Throwable $exception) {
            Log::warning('Appointment confirmation email failed.', [
                'appointment_id' => $appointment->id,
                'message' => $exception->getMessage(),
            ]);
        }

        $recipients = $setting->notificationEmailList();

        if ($recipients === []) {
            $recipients = [config('mail.contact_to', 'info@bdiversity.co.mz')];
        }

        try {
            Mail::send('emails.appointment-notification', [
                'appointment' => $appointment,
                'setting' => $setting,
            ], function ($message) use ($appointment, $recipients): void {
                $message
                    ->to($recipients)
                    ->replyTo($appointment->email, $appointment->name)
                    ->subject('Nova marcação no website BD');
            });
        } catch (Throwable $exception) {
            Log::warning('Appointment collaborator notification failed.', [
                'appointment_id' => $appointment->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
