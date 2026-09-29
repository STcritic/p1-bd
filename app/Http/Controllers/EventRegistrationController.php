<?php

namespace App\Http\Controllers;

use App\Models\CompanyEvent;
use App\Services\TurnstileVerifier;
use App\Services\WebsiteNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventRegistrationController extends Controller
{
    public function store(Request $request, CompanyEvent $event, TurnstileVerifier $turnstile): RedirectResponse
    {
        abort_unless($event->is_active, 404);

        if (filled($request->input('website'))) {
            Log::notice('Event registration blocked as suspicious.', [
                'reason' => 'honeypot',
                'event_id' => $event->id,
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);

            return back()->with('status', 'Inscrição recebida. A equipa BD entrará em contacto para confirmação.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'organization' => ['nullable', 'string', 'max:190'],
            'position' => ['nullable', 'string', 'max:190'],
            'seats_requested' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1200'],
        ]);


        $turnstileReason = $turnstile->verify($request, 'event_registration');

        if ($turnstileReason !== null) {
            return back()
                ->withInput()
                ->withErrors([
                    'event_security' => $request->routeIs('en.*')
                        ? 'We could not validate the anti-spam check. Please reload the page and try again.'
                        : 'Não foi possível validar a protecção anti-spam. Recarregue a página e tente novamente.',
                ]);
        }

        $requestedSeats = (int) $data['seats_requested'];
        $remainingSeats = $event->remainingSeats();
        $status = $remainingSeats !== null && $requestedSeats > $remainingSeats ? 'waitlist' : 'pending';

        $registration = $event->registrations()->create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'organization' => $data['organization'] ?? null,
            'position' => $data['position'] ?? null,
            'seats_requested' => $requestedSeats,
            'status' => $status,
            'notes' => $data['notes'] ?? null,
            'source' => 'website',
        ]);

        app(WebsiteNotificationService::class)->eventRegistrationReceived($registration);

        return back()->with(
            'status',
            $status === 'waitlist'
                ? 'Recebemos o seu pedido. As vagas directas estão preenchidas; ficou em lista de espera.'
                : 'Inscrição recebida. A equipa BD entrará em contacto para confirmação.'
        );
    }
}
