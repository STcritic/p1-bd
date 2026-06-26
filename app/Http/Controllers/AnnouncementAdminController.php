<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnnouncementAdminController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $master = $this->currentAdmin($request);
        abort_unless($master->is_master, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:announcement_admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        AnnouncementAdmin::query()->create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'is_master' => false,
            'is_active' => true,
        ]);

        return back()->with('status', 'Acesso secundário criado.');
    }

    public function destroy(Request $request, AnnouncementAdmin $admin): RedirectResponse
    {
        $master = $this->currentAdmin($request);
        abort_unless($master->is_master, 403);
        abort_if($admin->is_master, 403, 'A conta master não pode ser apagada aqui.');

        $admin->delete();

        return back()->with('status', 'Acesso secundário apagado.');
    }

    private function currentAdmin(Request $request): AnnouncementAdmin
    {
        return AnnouncementAdmin::query()->findOrFail($request->session()->get('announcement_admin_id'));
    }
}
