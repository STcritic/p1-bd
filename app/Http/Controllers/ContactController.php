<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): RedirectResponse
    {
        return $this->save($request, 'pt');
    }

    public function storeEn(StoreContactRequest $request): RedirectResponse
    {
        return $this->save($request, 'en');
    }

    private function save(StoreContactRequest $request, string $locale): RedirectResponse
    {
        $message = ContactMessage::create([
            ...$request->safe()->except('website'),
            'locale' => $locale,
            'ip_address' => $request->ip(),
        ]);

        try {
            Mail::raw(
                "Nome: {$message->name}\nEmail: {$message->email}\nTelefone: {$message->phone}\nEmpresa: {$message->company}\n\n{$message->message}",
                fn ($mail) => $mail
                    ->to(config('mail.contact_to', 'info@bdiversity.co.mz'))
                    ->replyTo($message->email, $message->name)
                    ->subject("Website BD: {$message->subject}")
            );
        } catch (\Throwable $exception) {
            Log::warning('Contact email could not be sent.', ['exception' => $exception->getMessage()]);
        }

        return back()->with('status', $locale === 'pt'
            ? 'Obrigado. A sua mensagem foi recebida e entraremos em contacto brevemente.'
            : 'Thank you. Your message has been received and we will be in touch shortly.');
    }
}
