<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use Throwable;

class StoreContactRequest extends FormRequest
{
    private array $spamReasons = [];

    private bool $silentlyDrop = false;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'min:2', 'max:120', 'regex:/\A[\pL\pM .,\'-]+\z/u'],
            'email' => ['bail', 'required', 'email:rfc,filter', 'max:180'],
            'phone' => ['nullable', 'string', 'min:7', 'max:30', 'regex:/\A[+()0-9\s.-]+\z/'],
            'company' => ['nullable', 'string', 'max:120'],
            'subject' => ['bail', 'required', 'string', 'min:4', 'max:160'],
            'message' => ['bail', 'required', 'string', 'min:10', 'max:3000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->checkHoneypot($validator);
            $this->checkSubmissionTime($validator);
            $this->checkTurnstile($validator);
        });
    }

    protected function failedValidation(ValidationContract $validator): void
    {
        if ($this->spamReasons !== []) {
            $this->logSpamAttempt();
        }

        if ($this->silentlyDrop) {
            throw new HttpResponseException(
                back()->with('status', $this->genericStatusMessage())
            );
        }

        parent::failedValidation($validator);
    }

    private function checkHoneypot(Validator $validator): void
    {
        if (filled($this->input('website'))) {
            $this->flagSpam($validator, 'honeypot', silent: true);
        }
    }

    private function checkSubmissionTime(Validator $validator): void
    {
        $encryptedStartedAt = $this->input('form_started_at');

        if (! is_string($encryptedStartedAt) || trim($encryptedStartedAt) === '') {
            $this->flagSpam($validator, 'missing_form_timer', silent: true);

            return;
        }

        try {
            $startedAt = (float) Crypt::decryptString($encryptedStartedAt);
        } catch (Throwable) {
            $this->flagSpam($validator, 'invalid_form_timer', silent: true);

            return;
        }

        $minimumSeconds = max(0.0, (float) config('contact_form.minimum_seconds', 1.0));

        if ((microtime(true) - $startedAt) < $minimumSeconds) {
            $this->flagSpam($validator, 'submitted_too_fast', $this->securityMessage('submitted_too_fast'));
        }
    }

    private function checkTurnstile(Validator $validator): void
    {
        if (! (bool) config('services.turnstile.enabled', false)) {
            return;
        }

        if (! $this->turnstileConfigured()) {
            Log::warning('Turnstile is enabled but not configured with usable keys.');

            return;
        }

        $token = $this->input('cf-turnstile-response');

        if (! is_string($token) || trim($token) === '') {
            $this->flagSpam($validator, 'missing_turnstile', $this->securityMessage('missing_turnstile'));

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout((float) config('services.turnstile.timeout', 4))
                ->post(config('services.turnstile.verify_url'), [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $this->ip(),
                ]);
        } catch (Throwable $exception) {
            Log::warning('Turnstile verification unavailable.', [
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        if (! $response->ok() || ! (bool) $response->json('success')) {
            $this->flagSpam($validator, 'turnstile_failed', $this->securityMessage('turnstile_failed'));

            Log::notice('Turnstile verification failed.', [
                'ip' => $this->ip(),
                'status' => $response->status(),
                'errors' => $response->json('error-codes'),
            ]);
        }
    }

    private function turnstileConfigured(): bool
    {
        return $this->usableTurnstileValue(config('services.turnstile.site_key'))
            && $this->usableTurnstileValue(config('services.turnstile.secret_key'));
    }

    private function usableTurnstileValue(mixed $value): bool
    {
        $value = trim((string) $value);

        return $value !== '' && $value !== '...';
    }

    private function flagSpam(Validator $validator, string $reason, ?string $message = null, bool $silent = false): void
    {
        $this->spamReasons[] = $reason;
        $this->silentlyDrop = $this->silentlyDrop || $silent;
        $validator->errors()->add('contact_security', $message ?? 'Suspicious contact submission.');
    }

    private function securityMessage(string $reason): string
    {
        $en = $this->routeIs('en.*');

        return match ($reason) {
            'submitted_too_fast' => $en
                ? 'Please wait a few seconds before sending the message.'
                : 'Aguarde alguns segundos antes de enviar a mensagem.',
            'missing_turnstile', 'turnstile_failed' => $en
                ? 'We could not validate the anti-spam check. Please reload the page and try again.'
                : 'Não foi possível validar a protecção anti-spam. Recarregue a página e tente novamente.',
            default => $en
                ? 'We could not validate this submission. Please try again.'
                : 'Não foi possível validar esta submissão. Tente novamente.',
        };
    }

    private function logSpamAttempt(): void
    {
        $email = strtolower(trim((string) $this->input('email')));

        Log::notice('Contact form submission blocked as suspicious.', [
            'ip' => $this->ip(),
            'reasons' => array_values(array_unique($this->spamReasons)),
            'email_hash' => $email !== '' ? hash('sha256', $email) : null,
            'user_agent' => substr((string) $this->userAgent(), 0, 255),
        ]);
    }

    private function genericStatusMessage(): string
    {
        return $this->routeIs('en.*')
            ? 'Thank you. Your message has been received and we will be in touch shortly.'
            : 'Obrigado. A sua mensagem foi recebida e entraremos em contacto brevemente.';
    }
}
