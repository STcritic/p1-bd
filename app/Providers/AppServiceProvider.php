<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ((bool) config('app.force_https')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('contact-form', fn (Request $request): Limit => $this->limitWithBackResponse(
            request: $request,
            name: 'contact_form',
            maxAttempts: 5,
            decayMinutes: 10,
            errorKey: 'contact_security',
            messagePt: 'Demasiadas tentativas de contacto. Aguarde alguns minutos e tente novamente.',
            messageEn: 'Too many contact attempts. Please wait a few minutes and try again.',
        ));

        RateLimiter::for('schedule-booking', fn (Request $request): Limit => $this->limitWithBackResponse(
            request: $request,
            name: 'schedule_booking',
            maxAttempts: 5,
            decayMinutes: 10,
            errorKey: 'schedule_security',
            messagePt: 'Demasiadas tentativas de agendamento. Aguarde alguns minutos e tente novamente.',
            messageEn: 'Too many scheduling attempts. Please wait a few minutes and try again.',
        ));

        RateLimiter::for('diagnostic-save', fn (Request $request): Limit => $this->limitWithJsonResponse(
            request: $request,
            name: 'diagnostic_save',
            maxAttempts: 60,
            decayMinutes: 1,
        ));

        RateLimiter::for('diagnostic-submit', fn (Request $request): Limit => $this->limitWithBackResponse(
            request: $request,
            name: 'diagnostic_submit',
            maxAttempts: 5,
            decayMinutes: 10,
            errorKey: 'form',
            messagePt: 'Demasiadas tentativas de submissão. Aguarde alguns minutos e tente novamente.',
            messageEn: 'Too many submission attempts. Please wait a few minutes and try again.',
        ));

        RateLimiter::for('announcement-login', fn (Request $request): Limit => $this->limitWithBackResponse(
            request: $request,
            name: 'announcement_login',
            maxAttempts: 6,
            decayMinutes: 1,
            errorKey: 'bd_access_email',
            messagePt: 'Demasiadas tentativas de login. Aguarde um minuto e tente novamente.',
            messageEn: 'Too many login attempts. Please wait a minute and try again.',
        ));

        RateLimiter::for('announcement-password-reset', fn (Request $request): Limit => $this->limitWithBackResponse(
            request: $request,
            name: 'announcement_password_reset',
            maxAttempts: 5,
            decayMinutes: 10,
            errorKey: 'bd_access_email',
            messagePt: 'Demasiados pedidos de restauro. Aguarde alguns minutos e tente novamente.',
            messageEn: 'Too many reset requests. Please wait a few minutes and try again.',
        ));

        RateLimiter::for('event-registration', fn (Request $request): Limit => $this->limitWithBackResponse(
            request: $request,
            name: 'event_registration',
            maxAttempts: 6,
            decayMinutes: 10,
            errorKey: 'event_security',
            messagePt: 'Demasiadas tentativas de inscrição. Aguarde alguns minutos e tente novamente.',
            messageEn: 'Too many registration attempts. Please wait a few minutes and try again.',
        ));
    }

    private function limitWithBackResponse(
        Request $request,
        string $name,
        int $maxAttempts,
        int $decayMinutes,
        string $errorKey,
        string $messagePt,
        string $messageEn,
    ): Limit {
        return Limit::perMinutes($decayMinutes, $maxAttempts)
            ->by((string) $request->ip())
            ->response(function (Request $request, array $headers) use ($name, $errorKey, $messagePt, $messageEn): RedirectResponse {
                $this->logRateLimit($request, $name);

                $response = back()
                    ->withInput()
                    ->withErrors([
                        $errorKey => $request->routeIs('en.*') ? $messageEn : $messagePt,
                    ]);

                $response->headers->add($headers);

                return $response;
            });
    }

    private function limitWithJsonResponse(Request $request, string $name, int $maxAttempts, int $decayMinutes): Limit
    {
        return Limit::perMinutes($decayMinutes, $maxAttempts)
            ->by((string) $request->ip())
            ->response(function (Request $request, array $headers) use ($name) {
                $this->logRateLimit($request, $name);

                return response()->json([
                    'error' => 'Too many attempts.',
                ], 429, $headers);
            });
    }

    private function logRateLimit(Request $request, string $name): void
    {
        Log::notice('Public form rate limit exceeded.', [
            'limiter' => $name,
            'ip' => $request->ip(),
            'route' => $request->route()?->getName(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }
}
