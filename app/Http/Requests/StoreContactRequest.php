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

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'min:2', 'max:120', 'regex:/\A[\pL\pM .\'-]+\z/u'],
            'email' => ['bail', 'required', 'email:rfc,filter', 'max:180'],
            'phone' => ['nullable', 'string', 'min:7', 'max:30', 'regex:/\A[+()0-9\s.-]+\z/'],
            'company' => ['nullable', 'string', 'max:120', 'not_regex:~https?://|www\.~i'],
            'subject' => ['bail', 'required', 'string', 'min:4', 'max:160', 'not_regex:~https?://|www\.~i'],
            'message' => ['bail', 'required', 'string', 'min:20', 'max:3000', 'not_regex:/<[^>]*>/'],
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

            throw new HttpResponseException(
                back()->with('status', $this->genericStatusMessage())
            );
        }

        parent::failedValidation($validator);
    }

    private function checkHoneypot(Validator $validator): void
    {
        if (filled($this->input('website'))) {
            $this->flagSpam($validator, 'honeypot');
        }
    }

    private function checkSubmissionTime(Validator $validator): void
    {
        $encryptedStartedAt = $this->input('form_started_at');

        if (! is_string($encryptedStartedAt) || trim($encryptedStartedAt) === '') {
            $this->flagSpam($validator, 'missing_form_timer');

            return;
        }

        try {
            $startedAt = (float) Crypt::decryptString($encryptedStartedAt);
        } catch (Throwable) {
            $this->flagSpam($validator, 'invalid_form_timer');

            return;
        }

        $minimumSeconds = max(0.0, (float) config('contact_form.minimum_seconds', 1.0));

        if ((microtime(true) - $startedAt) < $minimumSeconds) {
            $this->flagSpam($validator, 'submitted_too_fast');
        }
    }

    private function checkTurnstile(Validator $validator): void
    {
        if (! $this->turnstileEnabled()) {
            return;
        }

        $token = $this->input('cf-turnstile-response');

        if (! is_string($token) || trim($token) === '') {
            $this->flagSpam($validator, 'missing_turnstile');

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
            $this->flagSpam($validator, 'turnstile_unavailable');

            Log::warning('Turnstile verification unavailable.', [
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        if (! $response->ok() || ! (bool) $response->json('success')) {
            $this->flagSpam($validator, 'turnstile_failed');

            Log::notice('Turnstile verification failed.', [
                'ip' => $this->ip(),
                'status' => $response->status(),
                'errors' => $response->json('error-codes'),
            ]);
        }
    }

    private function turnstileEnabled(): bool
    {
        return (bool) config('services.turnstile.enabled', false)
            && filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    private function flagSpam(Validator $validator, string $reason): void
    {
        $this->spamReasons[] = $reason;
        $validator->errors()->add('contact_security', 'Suspicious contact submission.');
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
