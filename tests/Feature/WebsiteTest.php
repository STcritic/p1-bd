<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response = $this->post('/contactos', [
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'phone' => '+258 84 000 0000',
            'company' => 'Empresa Teste',
            'subject' => 'Pedido de consultoria',
            'message' => 'Gostaria de conversar sobre uma solução para a nossa empresa.',
            'website' => '',
        ]);

        $response->assertRedirect()->assertSessionHas('status');
        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'cliente@example.com',
            'locale' => 'pt',
        ]);
    }

    public function test_contact_rejects_invalid_submissions(): void
    {
        $this->post('/contactos', ['name' => ''])->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $this->assertDatabaseCount(ContactMessage::class, 0);
    }
}
