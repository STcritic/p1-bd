<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TurnstileVerifier
{
    public function shouldRender(): bool
    {
        return $this->enabled() && $this->configured();
    }

    public function siteKey(): ?string
    {
        $siteKey = trim((string) config('services.turnstile.site_key'));

        return $this->usableValue($siteKey) ? $siteKey : null;
    }

    public function verify(Request $request, string $context): ?string
    {
        if (! $this->enabled()) {
            return null;
        }

        if (! $this->configured()) {
            Log::warning('Turnstile is enabled but not configured with usable keys.', [
                'context' => $context,
                'site_key_present' => $this->present(config('services.turnstile.site_key')),
                'secret_key_present' => $this->present(config('services.turnstile.secret_key')),
            ]);

            return null;
        }

        $token = $request->input('cf-turnstile-response');

        if (! is_string($token) || trim($token) === '') {
            Log::notice('Turnstile token missing.', $this->logContext($request, $context));

            return 'missing_turnstile';
        }

        try {
            $response = Http::asForm()
                ->timeout((float) config('services.turnstile.timeout', 4))
                ->post(config('services.turnstile.verify_url'), [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (Throwable $exception) {
            Log::warning('Turnstile verification unavailable.', $this->logContext($request, $context, [
                'message' => $exception->getMessage(),
            ]));

            return null;
        }

        if (! $response->ok() || ! (bool) $response->json('success')) {
            Log::notice('Turnstile verification rejected.', $this->logContext($request, $context, [
                'status' => $response->status(),
                'errors' => $response->json('error-codes'),
            ]));

            return 'turnstile_failed';
        }

        return null;
    }

    private function enabled(): bool
    {
        return (bool) config('services.turnstile.enabled', false);
    }

    private function configured(): bool
    {
        return $this->usableValue(config('services.turnstile.site_key'))
            && $this->usableValue(config('services.turnstile.secret_key'));
    }

    private function usableValue(mixed $value): bool
    {
        $value = trim((string) $value);

        return $value !== '' && $value !== '...';
    }

    private function present(mixed $value): bool
    {
        return trim((string) $value) !== '';
    }

    private function logContext(Request $request, string $context, array $extra = []): array
    {
        return $extra + [
            'context' => $context,
            'ip' => $request->ip(),
            'route' => $request->route()?->getName(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ];
    }
}
