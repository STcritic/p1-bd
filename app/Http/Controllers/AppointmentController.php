<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MeetingSetting;
use App\Services\TurnstileVerifier;
use App\Services\WebsiteNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
            'empty' => $locale === 'en'
                ? 'No times available for this date.'
                : 'Sem horários disponíveis nesta data.',
        ]);
    }

    public function store(Request $request, TurnstileVerifier $turnstile): RedirectResponse
    {
        $locale = $request->routeIs('en.*') ? 'en' : 'pt';
        app()->setLocale($locale);

        if (filled($request->input('website'))) {
            $this->logScheduleSecurityEvent($request, 'honeypot');

            return back()->with('status', $locale === 'en'
                ? 'Thank you. If the meeting can be confirmed, we will send the details by email.'
                : 'Obrigado. Se a marcação puder ser confirmada, enviaremos os detalhes por email.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'organization' => ['nullable', 'string', 'max:190'],
            'position' => ['nullable', 'string', 'max:190'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['nullable', 'string', 'max:1200'],
            'scheduled_for' => ['required', 'date', 'after:now'],
        ]);


        $turnstileReason = $turnstile->verify($request, 'schedule_booking');

        if ($turnstileReason !== null) {
            return back()
                ->withInput()
                ->withErrors([
                    'schedule_security' => $this->securityMessage($locale, $turnstileReason),
                ]);
        }

        $setting = MeetingSetting::current();

        if (! $setting->is_active || ! $setting->meeting_url) {
            return back()
                ->withInput()
                ->withErrors([
                    'scheduled_for' => $locale === 'en'
                        ? 'Online scheduling is not available yet.'
                        : 'A agenda online ainda não está disponível.',
                ]);
        }

        [$appointment, $lockedSetting] = DB::transaction(function () use ($setting, $data, $locale): array {
            $lockedSetting = MeetingSetting::query()
                ->whereKey($setting->id)
                ->lockForUpdate()
                ->first() ?? MeetingSetting::current();

            $requestedStart = Carbon::parse($data['scheduled_for'], $lockedSetting->timezoneName());

            if (! $lockedSetting->acceptsAppointmentAt($requestedStart)) {
                throw ValidationException::withMessages([
                    'scheduled_for' => $locale === 'en'
                        ? 'This time is not available. Please choose another one.'
                        : 'Este horário não está disponível. Escolha outro horário.',
                ]);
            }

            $appointment = Appointment::query()->create([
                'meeting_setting_id' => $lockedSetting->id,
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'phone' => $data['phone'] ?? null,
                'organization' => $data['organization'] ?? null,
                'position' => $data['position'] ?? null,
                'subject' => ($data['subject'] ?? null) ?: $lockedSetting->standard_subject,
                'message' => $data['message'] ?? null,
                'scheduled_for' => $requestedStart,
                'duration_minutes' => $lockedSetting->default_duration_minutes,
                'timezone' => $lockedSetting->timezoneName(),
                'status' => 'scheduled',
                'meeting_platform' => $lockedSetting->platform_name,
                'meeting_url' => $lockedSetting->meeting_url,
                'meeting_id' => $lockedSetting->meeting_id,
                'meeting_password' => $lockedSetting->meeting_password,
                'location_notes' => $lockedSetting->location_notes,
                'ip_address' => request()->ip(),
            ]);

            return [$appointment, $lockedSetting];
        });

        app(WebsiteNotificationService::class)->appointmentBooked($appointment, $lockedSetting);

        return back()->with('status', $locale === 'en'
            ? 'Meeting scheduled. We sent the details by email.'
            : 'Reunião marcada. Enviámos os detalhes por email.');
    }

    private function securityMessage(string $locale, string $reason): string
    {
        $en = $locale === 'en';

        return match ($reason) {
            'submitted_too_fast' => $en
                ? 'Please wait a few seconds before scheduling the meeting.'
                : 'Aguarde alguns segundos antes de agendar a reunião.',
            'missing_turnstile', 'turnstile_failed' => $en
                ? 'We could not validate the anti-spam check. Please reload the page and try again.'
                : 'Não foi possível validar a protecção anti-spam. Recarregue a página e tente novamente.',
            default => $en
                ? 'We could not validate this submission. Please try again.'
                : 'Não foi possível validar esta submissão. Tente novamente.',
        };
    }

    private function logScheduleSecurityEvent(Request $request, string $reason): void
    {
        Log::notice('Schedule form submission blocked as suspicious.', [
            'reason' => $reason,
            'ip' => $request->ip(),
            'route' => $request->route()?->getName(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }
}
