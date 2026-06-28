<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementAdmin;
use App\Support\AnnouncementMasterAccess;
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

    public function showLogin(): View
    {
        return view('announcements.login');
    }

    public function login(Request $request): RedirectResponse
    {
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
        $request->session()->put('announcement_admin_id', $admin->id);

        $admin->update(['last_login_at' => now()]);

        return redirect()->route('announcements.dashboard');
    }

    public function showPasswordResetRequest(): View
    {
        app(AnnouncementMasterAccess::class)->ensure();

        return view('announcements.password-reset');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
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

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('announcement_admin_id');
        $request->session()->regenerateToken();

        return redirect()->route('announcements.login')->with('status', 'Sessão terminada.');
    }
}
