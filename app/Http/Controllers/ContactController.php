<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\ContactMessage;
use App\Services\WebsiteNotificationService;
use Illuminate\Http\RedirectResponse;

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

        app(WebsiteNotificationService::class)->contactReceived($message);

        return back()->with('status', $locale === 'pt'
            ? 'Obrigado. A sua mensagem foi recebida e entraremos em contacto brevemente.'
            : 'Thank you. Your message has been received and we will be in touch shortly.');
    }
}
