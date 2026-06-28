<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementAdmin;
use App\Models\CompanyEvent;
use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CollaboratorEventController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $this->currentAdmin($request);

        return view('announcements.events', [
            'admin' => $admin,
            'upcomingEvents' => CompanyEvent::query()
                ->with(['registrations' => fn ($query) => $query->latest()])
                ->upcoming()
                ->orderBy('starts_at')
                ->latest()
                ->get(),
            'pastEvents' => CompanyEvent::query()
                ->with(['registrations' => fn ($query) => $query->latest()])
                ->past()
                ->orderByDesc('starts_at')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        $data = $this->validatedEventData($request);

        $event = CompanyEvent::query()->create([
            ...$data,
            'announcement_admin_id' => $admin->id,
            'slug' => $this->uniqueSlug($data['title']),
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->boolean('create_announcement')) {
            $event->forceFill([
                'announcement_id' => $this->createAnnouncementForEvent($event, $admin)->id,
            ])->save();
        }

        return back()->with('status', 'Evento criado e disponível para gestão.');
    }

    public function update(Request $request, CompanyEvent $event): RedirectResponse
    {
        $data = $this->validatedEventData($request);

        $event->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->boolean('create_announcement') && ! $event->announcement_id) {
            $event->forceFill([
                'announcement_id' => $this->createAnnouncementForEvent($event, $this->currentAdmin($request))->id,
            ])->save();
        }

        return back()->with('status', 'Evento actualizado.');
    }

    public function toggle(Request $request, CompanyEvent $event): RedirectResponse
    {
        $event->update(['is_active' => ! $event->is_active]);

        return back()->with('status', $event->is_active ? 'Evento activado.' : 'Evento desactivado.');
    }

    public function updateRegistration(Request $request, EventRegistration $registration): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(EventRegistration::STATUSES)],
            'internal_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $registration->update($data);

        return back()->with('status', 'Inscrição actualizada.');
    }

    private function validatedEventData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:3000'],
            'audience' => ['nullable', 'string', 'max:255'],
            'format' => ['required', Rule::in(['presencial', 'online', 'hibrido'])],
            'location' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'external_url' => ['nullable', 'url', 'max:1000'],
            'seats_total' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'registration_deadline' => ['nullable', 'date'],
        ]);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'evento';
        $slug = $base;
        $counter = 2;

        while (CompanyEvent::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function createAnnouncementForEvent(CompanyEvent $event, AnnouncementAdmin $admin): Announcement
    {
        return Announcement::query()->create([
            'announcement_admin_id' => $admin->id,
            'title' => 'Novo evento: '.$event->title,
            'body' => $event->summary ?: 'Inscrições abertas para o próximo evento da Business Diversity.',
            'media_type' => $event->image_url ? 'image' : 'none',
            'media_url' => $event->image_url,
            'button_label' => 'Ver evento',
            'button_url' => $event->publicUrl('pt'),
            'is_active' => true,
            'show_once_per_session' => true,
            'priority' => 5,
            'starts_at' => now(),
            'ends_at' => $event->registration_deadline ?: $event->starts_at,
        ]);
    }

    private function currentAdmin(Request $request): AnnouncementAdmin
    {
        return AnnouncementAdmin::query()->findOrFail($request->session()->get('announcement_admin_id'));
    }
}
