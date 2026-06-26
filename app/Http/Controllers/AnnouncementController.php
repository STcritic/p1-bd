<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $this->currentAdmin($request);

        return view('announcements.dashboard', [
            'admin' => $admin,
            'announcements' => Announcement::query()
                ->latest()
                ->get(),
            'admins' => $admin->is_master
                ? AnnouncementAdmin::query()->orderByDesc('is_master')->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $admin = $this->currentAdmin($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'body' => ['nullable', 'string', 'max:1200'],
            'media_type' => ['required', Rule::in(['none', 'image', 'video', 'document'])],
            'media_url' => ['nullable', 'required_unless:media_type,none', 'url', 'max:1000'],
            'media' => ['prohibited'],
            'button_label' => ['nullable', 'string', 'max:60'],
            'button_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'show_once_per_session' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:99'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ], [
            'media.prohibited' => 'Ficheiros grandes não devem ser carregados directamente. Use um link externo.',
            'media_url.required_unless' => 'Informe o link externo do media ou escolha “Sem media”.',
        ]);

        Announcement::query()->create([
            'announcement_admin_id' => $admin->id,
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'media_type' => $data['media_type'],
            'media_path' => null,
            'media_original_name' => null,
            'media_url' => $data['media_type'] === 'none' ? null : ($data['media_url'] ?? null),
            'button_label' => $data['button_label'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'show_once_per_session' => $request->boolean('show_once_per_session', true),
            'priority' => (int) ($data['priority'] ?? 10),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);

        return back()->with('status', 'Anúncio criado com link externo.');
    }

    public function toggle(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);

        return back()->with('status', $announcement->is_active ? 'Anúncio activado.' : 'Anúncio desactivado.');
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        if ($announcement->media_path) {
            Storage::disk('public')->delete($announcement->media_path);
        }

        $announcement->delete();

        return back()->with('status', 'Anúncio apagado.');
    }

    private function currentAdmin(Request $request): AnnouncementAdmin
    {
        return AnnouncementAdmin::query()->findOrFail($request->session()->get('announcement_admin_id'));
    }
}
