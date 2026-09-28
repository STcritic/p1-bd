<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_available_in_portuguese_and_english(): void
    {
        foreach (['/', '/sobre-nos', '/servicos', '/eventos', '/contactos', '/en', '/en/about', '/en/services', '/en/events', '/en/contact'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_homepage_links_to_the_existing_intranet(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Área do Colaborador')
            ->assertSee('https://bdiversity.co.mz/intranet', false);
    }

    public function test_sitemap_is_available(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('/servicos');
    }

    public function test_contact_message_is_validated_and_saved(): void
    {
        $response = $this->post('/contactos', $this->validContactPayload());

        $response->assertRedirect()->assertSessionHas('status');
        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'cliente@example.com',
            'locale' => 'pt',
        ]);
    }

    public function test_contact_rejects_invalid_submissions(): void
    {
        $this->post('/contactos', [
            'name' => '',
            'form_started_at' => Crypt::encryptString((string) (microtime(true) - 2)),
        ])->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $this->assertDatabaseCount(ContactMessage::class, 0);
    }

    public function test_contact_honeypot_submission_is_silently_ignored(): void
    {
        Mail::fake();

        $this->post('/contactos', $this->validContactPayload(['website' => 'https://spam.example']))
            ->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount(ContactMessage::class, 0);
        Mail::assertNothingSent();
    }

    public function test_contact_fast_submission_is_silently_ignored(): void
    {
        Mail::fake();

        $this->post('/contactos', $this->validContactPayload([
            'form_started_at' => Crypt::encryptString((string) microtime(true)),
        ]))
            ->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount(ContactMessage::class, 0);
        Mail::assertNothingSent();
    }

    public function test_contact_turnstile_failure_is_silently_ignored(): void
    {
        Mail::fake();
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/*' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ]),
        ]);
        config()->set('services.turnstile.enabled', true);
        config()->set('services.turnstile.site_key', 'site-key');
        config()->set('services.turnstile.secret_key', 'secret-key');

        $this->post('/contactos', $this->validContactPayload([
            'cf-turnstile-response' => 'bad-token',
        ]))
            ->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount(ContactMessage::class, 0);
        Mail::assertNothingSent();
    }

    private function validContactPayload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'phone' => '+258 84 000 0000',
            'company' => 'Empresa Teste',
            'subject' => 'Pedido de consultoria',
            'message' => 'Gostaria de conversar sobre uma solução para a nossa empresa.',
            'website' => '',
            'form_started_at' => Crypt::encryptString((string) (microtime(true) - 2)),
        ], $overrides);
    }
}
