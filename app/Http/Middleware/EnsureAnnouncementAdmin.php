<?php

namespace App\Http\Middleware;

use App\Models\AnnouncementAdmin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAnnouncementAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminId = $request->session()->get('announcement_admin_id');
        $admin = $adminId ? AnnouncementAdmin::query()->where('is_active', true)->find($adminId) : null;

        if (! $admin) {
            $request->session()->forget('announcement_admin_id');

            return redirect()
                ->route('announcements.login')
                ->with('status', 'Entre para gerir os anúncios do site.');
        }

        if ($admin->passwordExpired()) {
            $request->session()->forget('announcement_admin_id');

            return redirect()
                ->route('announcements.password.expired')
                ->with('status', 'A palavra-passe expirou. Solicite um link seguro de restauro por email.');
        }

        view()->share('announcementAdmin', $admin);

        return $next($request);
    }
}
