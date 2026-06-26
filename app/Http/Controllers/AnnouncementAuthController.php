<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AnnouncementAuthController extends Controller
{
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

        $this->ensureMasterAdmin();

        $admin = AnnouncementAdmin::query()
            ->where('email', strtolower($credentials['bd_access_email']))
            ->where('is_active', true)
            ->first();

        if (! $admin || ! Hash::check($credentials['bd_access_secret'], $admin->password)) {
            throw ValidationException::withMessages([
                'bd_access_email' => 'Credenciais inválidas para gerir anúncios.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('announcement_admin_id', $admin->id);

        $admin->update(['last_login_at' => now()]);

        return redirect()->route('announcements.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('announcement_admin_id');
        $request->session()->regenerateToken();

        return redirect()->route('announcements.login')->with('status', 'Sessão terminada com segurança.');
    }

    private function ensureMasterAdmin(): void
    {
        $email = strtolower((string) config('announcements.master_email'));
        $password = (string) config('announcements.master_password');

        if ($email === '' || $password === '') {
            return;
        }

        AnnouncementAdmin::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Business Diversity',
                'password' => Hash::make($password),
                'is_master' => true,
                'is_active' => true,
            ]
        );
    }
}
