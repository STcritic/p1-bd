<?php

namespace App\Http\Controllers;

use App\Models\CompanyEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventRegistrationController extends Controller
{
    public function store(Request $request, CompanyEvent $event): RedirectResponse
    {
        abort_unless($event->is_active, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'organization' => ['nullable', 'string', 'max:190'],
            'position' => ['nullable', 'string', 'max:190'],
            'seats_requested' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1200'],
            'website' => ['nullable', 'max:0'],
        ]);

        $requestedSeats = (int) $data['seats_requested'];
        $remainingSeats = $event->remainingSeats();
        $status = $remainingSeats !== null && $requestedSeats > $remainingSeats ? 'waitlist' : 'pending';

        $event->registrations()->create([
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

        return back()->with(
            'status',
            $status === 'waitlist'
                ? 'Recebemos o seu pedido. As vagas directas estão preenchidas; ficou em lista de espera.'
                : 'Inscrição recebida. A equipa BD entrará em contacto para confirmação.'
        );
    }
}
