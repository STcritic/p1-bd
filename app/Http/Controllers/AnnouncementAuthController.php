<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementAdmin;
use App\Support\AnnouncementMasterAccess;
use App\Services\TurnstileVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class AnnouncementAuthController extends Controller
{
    private const RESET_TOKEN_MINUTES = 60;
    private const LOGIN_TURNSTILE_AFTER_FAILURES = 2;

    public function showLogin(Request $request): View
    {
        return view('announcements.login', [
            'requiresTurnstile' => $this->loginRequiresTurnstile($request),
        ]);
    }

    public function login(Request $request, TurnstileVerifier $turnstile): RedirectResponse
    {
        if (filled($request->input('website'))) {
            $this->logAuthSecurityEvent($request, 'login_honeypot');

            throw ValidationException::withMessages([
                'bd_access_email' => 'Credenciais inválidas para gerir anúncios.',
            ]);
        }

        if ($this->loginRequiresTurnstile($request)) {
            $turnstileReason = $turnstile->verify($request, 'announcement_login');

            if ($turnstileReason !== null) {
                throw ValidationException::withMessages([
                    'bd_access_email' => $this->securityMessage('pt', $turnstileReason),
                ]);
            }
        }

        $credentials = $request->validate([
            'bd_access_email' => ['required', 'email'],
            'bd_access_secret' => ['required', 'string'],
        ]);

        app(AnnouncementMasterAccess::class)->ensure();

        $admin = AnnouncementAdmin::query()
            ->where('email', strtolower($credentials['bd_access_email']))
            ->where('is_active', true)
            ->first();

        if (! $admin || ! Hash::check($credentials['bd_access_secret'], $admin->password)) {
            $this->registerFailedLogin($request);

            throw ValidationException::withMessages([
                'bd_access_email' => 'Credenciais inválidas para gerir anúncios.',
            ]);
        }

        if ($admin->passwordExpired()) {
            return redirect()
                ->route('announcements.password.expired')
                ->with('status', 'A palavra-passe expirou. Solicite um link de restauro por email.');
        }

        $request->session()->regenerate();
        $request->session()->forget('announcement_login_failures');
        $request->session()->put('announcement_admin_id', $admin->id);

        $admin->update(['last_login_at' => now()]);

        return redirect()->route('announcements.dashboard');
    }

    public function showPasswordResetRequest(TurnstileVerifier $turnstile): View
    {
        app(AnnouncementMasterAccess::class)->ensure();

        return view('announcements.password-reset', [
            'showTurnstile' => $turnstile->shouldRender(),
            'turnstileSiteKey' => $turnstile->siteKey(),
        ]);
    }

    public function sendPasswordResetLink(Request $request, TurnstileVerifier $turnstile): RedirectResponse
    {
        if (filled($request->input('website'))) {
            $this->logAuthSecurityEvent($request, 'password_reset_honeypot');

            return back()->with('status', 'Se existir uma conta para este email, receberá um link de restauro.');
        }

        $turnstileReason = $turnstile->verify($request, 'announcement_password_reset');

        if ($turnstileReason !== null) {
            throw ValidationException::withMessages([
                'bd_access_email' => $this->securityMessage('pt', $turnstileReason),
            ]);
        }

        $data = $request->validate([
            'bd_access_email' => ['required', 'email'],
        ]);

        app(AnnouncementMasterAccess::class)->ensure();

        $email = strtolower($data['bd_access_email']);
        $admin = AnnouncementAdmin::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->first();

        if ($admin) {
            $plainToken = Str::random(72);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($plainToken),
                    'created_at' => now(),
                ]
            );

            $resetUrl = route('announcements.password.reset', [
                'token' => $plainToken,
                'email' => $email,
            ]);

            try {
                Mail::send('emails.announcement-password-reset', [
                    'admin' => $admin,
                    'resetUrl' => $resetUrl,
                    'expiresMinutes' => self::RESET_TOKEN_MINUTES,
                ], function ($message) use ($admin): void {
                    $message
                        ->to($admin->email, $admin->name)
                        ->subject('Restauro de acesso | Business Diversity');
                });
            } catch (Throwable $exception) {
                Log::error('Announcement password reset email failed.', [
                    'admin_id' => $admin->id,
                    'email' => $admin->email,
                    'message' => $exception->getMessage(),
                ]);

                throw ValidationException::withMessages([
                    'bd_access_email' => 'Não foi possível enviar o email de restauro agora. Verifique a configuração SMTP.',
                ]);
            }
        }

        return back()->with('status', 'Se existir uma conta para este email, receberá um link de restauro.');
    }

    public function showPasswordResetForm(Request $request, string $token): View
    {
        return view('announcements.password-new', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function updatePasswordFromToken(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        app(AnnouncementMasterAccess::class)->ensure();

        $email = strtolower($data['email']);
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record || ! Hash::check($data['token'], $record->token)) {
            throw ValidationException::withMessages([
                'email' => 'O link de restauro é inválido. Solicite um novo link.',
            ]);
        }

        if (Carbon::parse($record->created_at)->addMinutes(self::RESET_TOKEN_MINUTES)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            throw ValidationException::withMessages([
                'email' => 'O link de restauro expirou. Solicite um novo link.',
            ]);
        }

        $admin = AnnouncementAdmin::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->first();

        if (! $admin) {
            throw ValidationException::withMessages([
                'email' => 'Este acesso já não está activo.',
            ]);
        }

        $admin->forceFill([
            'password' => $data['password'],
            'password_changed_at' => now(),
            'password_expires_at' => now()->addMonths((int) config('announcements.password_expires_months', 6)),
            'last_login_at' => now(),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $request->session()->regenerate();
        $request->session()->put('announcement_admin_id', $admin->id);

        return redirect()
            ->route('announcements.dashboard')
            ->with('status', 'Palavra-passe actualizada. O novo prazo de validade é de 6 meses.');
    }

    private function loginRequiresTurnstile(Request $request): bool
    {
        return (int) $request->session()->get('announcement_login_failures', 0) >= self::LOGIN_TURNSTILE_AFTER_FAILURES;
    }

    private function registerFailedLogin(Request $request): void
    {
        $failures = (int) $request->session()->get('announcement_login_failures', 0) + 1;
        $request->session()->put('announcement_login_failures', $failures);

        Log::notice('Announcement login failed.', [
            'ip' => $request->ip(),
            'failures' => $failures,
            'email_hash' => hash('sha256', strtolower(trim((string) $request->input('bd_access_email')))),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }

    private function securityMessage(string $locale, string $reason): string
    {
        return match ($reason) {
            'missing_turnstile', 'turnstile_failed' => 'Não foi possível validar a protecção anti-spam. Recarregue a página e tente novamente.',
            default => 'Não foi possível validar esta submissão. Tente novamente.',
        };
    }

    private function logAuthSecurityEvent(Request $request, string $reason): void
    {
        Log::notice('Announcement auth submission blocked as suspicious.', [
            'reason' => $reason,
            'ip' => $request->ip(),
            'route' => $request->route()?->getName(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('announcement_admin_id');
        $request->session()->regenerateToken();

        return redirect()->route('announcements.login')->with('status', 'Sessão terminada.');
    }
}
