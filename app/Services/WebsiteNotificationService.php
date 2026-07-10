<?php

namespace App\Services;

use App\Mail\BdNotificationMail;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\EventRegistration;
use App\Models\MeetingSetting;
use App\Modules\Collaborator\Opportunity\Domain\DiagnosticSession;
use App\Modules\Collaborator\Opportunity\Domain\Opportunity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class WebsiteNotificationService
{
    public function diagnosticLinkGenerated(DiagnosticSession $session): void
    {
        $session->loadMissing('opportunity.admin');
        $opportunity = $session->opportunity;

        if (! $opportunity?->client_email) {
            return;
        }

        $this->send([$opportunity->client_email], [
            'subject' => 'Diagnóstico BD: '.$this->serviceTitle($session, $opportunity),
            'eyebrow' => 'Business Diversity',
            'title' => 'Diagnóstico preparado',
            'intro' => 'Preparámos um diagnóstico rápido para compreender melhor o contexto da sua organização e orientar uma proposta mais ajustada.',
            'details' => [
                'Serviço' => $this->serviceTitle($session, $opportunity),
                'Cliente' => $opportunity->client_name,
                'Validade' => $session->expires_at ? $session->expires_at->format('d/m/Y') : 'Sem prazo definido',
            ],
            'action' => [
                'label' => 'Abrir diagnóstico',
                'url' => $session->portalUrl(),
            ],
            'notes' => [
                'O preenchimento pode ser retomado enquanto o link estiver válido.',
                'As respostas ajudam a BD a preparar recomendações mais objectivas.',
            ],
            'reply_to' => $this->companyReplyTo(),
            'footer' => 'Business Diversity · Consultoria Empresarial',
        ], 'diagnostic_link_generated');
    }

    public function diagnosticSubmitted(DiagnosticSession $session): void
    {
        $session->loadMissing('opportunity.admin');
        $opportunity = $session->opportunity;

        if (! $opportunity) {
            return;
        }

        if ($opportunity->client_email) {
            $this->send([$opportunity->client_email], [
                'subject' => 'Diagnóstico recebido | Business Diversity',
                'eyebrow' => 'Business Diversity',
                'title' => 'Diagnóstico recebido',
                'intro' => 'Obrigado. Recebemos as suas respostas e a equipa BD vai analisar o contexto para definir os próximos passos.',
                'details' => [
                    'Serviço' => $this->serviceTitle($session, $opportunity),
                    'Cliente' => $opportunity->client_name,
                    'Empresa' => $opportunity->client_company ?: '—',
                    'Referência' => $opportunity->reference ?: '—',
                ],
                'notes' => [
                    'Se houver necessidade de esclarecimentos, entraremos em contacto.',
                ],
                'reply_to' => $this->companyReplyTo(),
                'footer' => 'Business Diversity · Consultoria Empresarial',
            ], 'diagnostic_client_confirmation');
        }

        $this->send($this->opportunityRecipients($opportunity), [
            'subject' => 'Diagnóstico submetido: '.$opportunity->client_name,
            'eyebrow' => 'Portal de diagnóstico',
            'title' => 'Diagnóstico submetido pelo cliente',
            'intro' => 'O cliente submeteu o diagnóstico. A oportunidade já pode ser revista no painel.',
            'details' => [
                'Cliente' => $opportunity->client_name,
                'Empresa' => $opportunity->client_company ?: '—',
                'Email' => $opportunity->client_email ?: '—',
                'Serviço' => $this->serviceTitle($session, $opportunity),
                'Colaborador' => $opportunity->admin?->name ?: '—',
                'Referência' => $opportunity->reference ?: '—',
            ],
            'action' => [
                'label' => 'Abrir oportunidade',
                'url' => route('collaborator.opportunities.show', $opportunity),
            ],
            'reply_to' => $this->clientReplyTo($opportunity->client_email, $opportunity->client_contact ?: $opportunity->client_name),
            'footer' => 'Business Diversity · Portal BD',
        ], 'diagnostic_collaborator_notification');
    }

    public function contactReceived(ContactMessage $message): void
    {
        $this->send([$message->email], [
            'subject' => 'Mensagem recebida | Business Diversity',
            'eyebrow' => 'Business Diversity',
            'title' => 'Mensagem recebida',
            'intro' => 'Obrigado pelo contacto. A equipa BD vai rever a sua mensagem e responder brevemente.',
            'details' => [
                'Assunto' => $message->subject,
                'Empresa' => $message->company ?: '—',
            ],
            'reply_to' => $this->companyReplyTo(),
            'footer' => 'Business Diversity · Consultoria Empresarial',
        ], 'contact_client_confirmation');

        $this->send($this->companyRecipients(), [
            'subject' => 'Novo contacto no website: '.$message->subject,
            'eyebrow' => 'Website BD',
            'title' => 'Novo contacto recebido',
            'intro' => 'Foi submetida uma nova mensagem no formulário de contactos.',
            'details' => [
                'Nome' => $message->name,
                'Email' => $message->email,
                'Telefone' => $message->phone ?: '—',
                'Empresa' => $message->company ?: '—',
                'Assunto' => $message->subject,
                'Mensagem' => $message->message,
            ],
            'reply_to' => $this->clientReplyTo($message->email, $message->name),
            'footer' => 'Business Diversity · Website',
        ], 'contact_company_notification');
    }

    public function eventRegistrationReceived(EventRegistration $registration): void
    {
        $registration->loadMissing('event.admin');
        $event = $registration->event;

        if (! $event) {
            return;
        }

        $statusText = $registration->status === 'waitlist'
            ? 'Recebemos o seu pedido. As vagas directas estão preenchidas e o seu contacto ficou em lista de espera.'
            : 'Recebemos a sua inscrição. A equipa BD entrará em contacto para confirmação.';

        $this->send([$registration->email], [
            'subject' => 'Inscrição recebida | '.$event->title,
            'eyebrow' => 'Eventos BD',
            'title' => 'Inscrição recebida',
            'intro' => $statusText,
            'details' => [
                'Evento' => $event->title,
                'Data' => $event->displayDate(),
                'Formato' => $event->format ?: '—',
                'Vagas solicitadas' => (string) $registration->seats_requested,
                'Estado' => $registration->statusLabel(),
            ],
            'reply_to' => $this->companyReplyTo(),
            'footer' => 'Business Diversity · Eventos',
        ], 'event_registration_client_confirmation');

        $this->send($this->companyRecipients([$event->admin?->email]), [
            'subject' => 'Nova inscrição: '.$event->title,
            'eyebrow' => 'Eventos BD',
            'title' => 'Nova inscrição recebida',
            'intro' => 'Há uma nova inscrição no evento publicado no website.',
            'details' => [
                'Evento' => $event->title,
                'Participante' => $registration->name,
                'Email' => $registration->email,
                'Telefone' => $registration->phone ?: '—',
                'Organização' => $registration->organization ?: '—',
                'Cargo' => $registration->position ?: '—',
                'Vagas solicitadas' => (string) $registration->seats_requested,
                'Estado' => $registration->statusLabel(),
                'Notas' => $registration->notes ?: '—',
            ],
            'action' => [
                'label' => 'Gerir eventos',
                'url' => route('collaborator.events.index'),
            ],
            'reply_to' => $this->clientReplyTo($registration->email, $registration->name),
            'footer' => 'Business Diversity · Portal BD',
        ], 'event_registration_company_notification');
    }

    public function appointmentBooked(Appointment $appointment, MeetingSetting $setting): void
    {
        $meetingDetails = [
            'Data' => $appointment->scheduledLocal()->format('d/m/Y H:i'),
            'Duração' => $appointment->duration_minutes.' minutos',
            'Assunto' => $appointment->subject,
            'Plataforma' => $appointment->meeting_platform ?: '—',
            'Link' => $appointment->meeting_url ?: '—',
            'ID' => $appointment->meeting_id ?: '—',
            'Senha' => $appointment->meeting_password ?: '—',
        ];

        $this->send([$appointment->email], [
            'subject' => 'Reunião marcada | Business Diversity',
            'eyebrow' => 'Agenda BD',
            'title' => 'Reunião marcada',
            'intro' => 'A sua reunião com a Business Diversity foi marcada.',
            'details' => $meetingDetails,
            'action' => [
                'label' => 'Adicionar ao Google Calendar',
                'url' => $appointment->googleCalendarUrl(),
            ],
            'secondary_action' => $appointment->meeting_url ? [
                'label' => 'Entrar na reunião',
                'url' => $appointment->meeting_url,
            ] : null,
            'notes' => array_values(array_filter([
                $setting->standard_message,
                $appointment->location_notes,
            ])),
            'reply_to' => $this->companyReplyTo(),
            'footer' => 'Business Diversity · Agenda',
        ], 'appointment_client_confirmation');

        $recipients = $setting->notificationEmailList();
        $recipients = $recipients === [] ? $this->companyRecipients() : $this->companyRecipients($recipients);

        $this->send($recipients, [
            'subject' => 'Nova marcação no website BD',
            'eyebrow' => 'Agenda BD',
            'title' => 'Nova reunião marcada',
            'intro' => 'Uma nova reunião foi marcada pelo website.',
            'details' => [
                'Nome' => $appointment->name,
                'Email' => $appointment->email,
                'Telefone' => $appointment->phone ?: '—',
                'Organização' => $appointment->organization ?: '—',
                'Cargo' => $appointment->position ?: '—',
                'Data' => $appointment->scheduledLocal()->format('d/m/Y H:i'),
                'Assunto' => $appointment->subject,
                'Contexto' => $appointment->message ?: '—',
                'Link' => $appointment->meeting_url ?: '—',
            ],
            'action' => [
                'label' => 'Ver agenda',
                'url' => route('collaborator.schedule.index'),
            ],
            'reply_to' => $this->clientReplyTo($appointment->email, $appointment->name),
            'footer' => 'Business Diversity · Portal BD',
        ], 'appointment_company_notification');
    }

    private function send(array|string|null $recipients, array $payload, string $context): void
    {
        if (! (bool) config('bd_notifications.enabled', true)) {
            return;
        }

        $recipients = $this->cleanEmails((array) $recipients);

        if ($recipients === []) {
            return;
        }

        try {
            Mail::to($recipients)->send(new BdNotificationMail($payload));
        } catch (Throwable $exception) {
            Log::warning('BD notification email failed.', [
                'context' => $context,
                'recipients' => $recipients,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function companyRecipients(array|string|null $extra = null): array
    {
        return $this->cleanEmails([
            config('bd_notifications.company_email', config('mail.contact_to', 'info@bdiversity.co.mz')),
            $extra,
        ]);
    }

    private function opportunityRecipients(Opportunity $opportunity): array
    {
        $opportunity->loadMissing('admin');

        return $this->companyRecipients([
            $opportunity->admin?->email,
        ]);
    }

    private function cleanEmails(array $emails): array
    {
        return collect($emails)
            ->flatten()
            ->filter()
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    private function companyReplyTo(): array
    {
        return [
            'email' => config('bd_notifications.reply_to.address', config('mail.contact_to', 'info@bdiversity.co.mz')),
            'name' => config('bd_notifications.reply_to.name', 'Business Diversity'),
        ];
    }

    private function clientReplyTo(?string $email, ?string $name): array
    {
        return [
            'email' => $email,
            'name' => $name ?: $email,
        ];
    }

    private function serviceTitle(DiagnosticSession $session, ?Opportunity $opportunity = null): string
    {
        if ($opportunity?->service_title) {
            return $opportunity->service_title;
        }

        return collect(config('service_guides.pt', []))
            ->firstWhere('slug', $session->service_slug)['title']
            ?? $session->service_slug;
    }
}
