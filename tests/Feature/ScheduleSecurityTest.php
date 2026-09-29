<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\MeetingSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ScheduleSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-05 08:00:00', 'Africa/Maputo'));
        Mail::fake();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_schedule_human_booking_is_saved_when_turnstile_is_disabled(): void
    {
        $this->enableScheduling();
        config()->set('services.turnstile.enabled', false);

        $this->post('/agenda', $this->validPayload())
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas(Appointment::class, [
            'email' => 'cliente@example.com',
            'scheduled_for' => '2026-10-05 09:00:00',
            'status' => 'scheduled',
        ]);
    }

    public function test_schedule_booking_accepts_valid_turnstile_token(): void
    {
        $this->enableScheduling();
        $this->enableTurnstile();
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/*' => Http::response(['success' => true]),
        ]);

        $this->post('/agenda', $this->validPayload([
            'cf-turnstile-response' => 'valid-token',
        ]))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseCount(Appointment::class, 1);
    }

    public function test_schedule_booking_rejects_missing_turnstile_token(): void
    {
        $this->enableScheduling();
        $this->enableTurnstile();

        $this->post('/agenda', $this->validPayload())
            ->assertRedirect()
            ->assertSessionHasErrors(['schedule_security']);

        $this->assertDatabaseCount(Appointment::class, 0);
        Mail::assertNothingSent();
    }

    public function test_schedule_booking_rejects_invalid_turnstile_token(): void
    {
        $this->enableScheduling();
        $this->enableTurnstile();
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/*' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ]),
        ]);

        $this->post('/agenda', $this->validPayload([
            'cf-turnstile-response' => 'bad-token',
        ]))
            ->assertRedirect()
            ->assertSessionHasErrors(['schedule_security']);

        $this->assertDatabaseCount(Appointment::class, 0);
        Mail::assertNothingSent();
    }

    public function test_schedule_booking_honeypot_is_ignored_without_creating_appointment(): void
    {
        $this->enableScheduling();

        $this->post('/agenda', $this->validPayload([
            'website' => 'https://spam.example',
        ]))
            ->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount(Appointment::class, 0);
        Mail::assertNothingSent();
    }

    public function test_schedule_booking_rate_limit_is_enforced(): void
    {
        $this->enableScheduling();
        config()->set('services.turnstile.enabled', false);

        foreach (['09:00', '09:30', '10:00', '10:30', '11:00'] as $time) {
            $this->post('/agenda', $this->validPayload([
                'scheduled_for' => '2026-10-05T'.$time,
                'email' => str_replace(':', '', $time).'@example.com',
            ]))->assertRedirect();
        }

        $this->post('/agenda', $this->validPayload([
            'scheduled_for' => '2026-10-05T11:30',
            'email' => 'sixth@example.com',
        ]))
            ->assertRedirect()
            ->assertSessionHasErrors(['schedule_security']);

        $this->assertDatabaseCount(Appointment::class, 5);
    }

    public function test_schedule_booking_rejects_already_occupied_time(): void
    {
        $setting = $this->enableScheduling();
        Appointment::query()->create([
            'meeting_setting_id' => $setting->id,
            'name' => 'Existing Client',
            'email' => 'existing@example.com',
            'scheduled_for' => '2026-10-05 09:00:00',
            'duration_minutes' => 30,
            'timezone' => 'Africa/Maputo',
            'status' => 'scheduled',
        ]);

        $this->post('/agenda', $this->validPayload())
            ->assertRedirect()
            ->assertSessionHasErrors(['scheduled_for']);

        $this->assertDatabaseCount(Appointment::class, 1);
        Mail::assertNothingSent();
    }

    public function test_schedule_booking_allows_invalid_turnstile_configuration_to_fail_open(): void
    {
        $this->enableScheduling();
        config()->set('services.turnstile.enabled', true);
        config()->set('services.turnstile.site_key', '...');
        config()->set('services.turnstile.secret_key', '...');

        $this->post('/agenda', $this->validPayload())
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseCount(Appointment::class, 1);
    }

    private function enableScheduling(): MeetingSetting
    {
        $setting = MeetingSetting::current();
        $setting->forceFill([
            'platform_name' => 'Google Meet',
            'meeting_url' => 'https://meet.example.com/test',
            'standard_subject' => 'Conversa de diagnóstico BD',
            'default_duration_minutes' => 30,
            'timezone' => 'Africa/Maputo',
            'availability_rules' => [
                '1' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                '2' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                '3' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                '4' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                '5' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                '6' => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'],
                '7' => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'],
            ],
            'slot_interval_minutes' => 30,
            'minimum_notice_minutes' => 0,
            'notification_emails' => ['info@bdiversity.co.mz'],
            'is_active' => true,
        ])->save();

        return $setting->refresh();
    }

    private function enableTurnstile(): void
    {
        config()->set('services.turnstile.enabled', true);
        config()->set('services.turnstile.site_key', 'site-key');
        config()->set('services.turnstile.secret_key', 'secret-key');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'phone' => '+258 84 000 0000',
            'organization' => 'Empresa Teste',
            'position' => 'Director Geral',
            'subject' => 'Conversa de diagnóstico BD',
            'message' => 'Gostaria de falar sobre uma necessidade da empresa.',
            'scheduled_for' => '2026-10-05T09:00',
            'website' => '',
        ], $overrides);
    }
}
