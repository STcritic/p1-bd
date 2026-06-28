<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementAdmin;
use App\Models\Appointment;
use App\Models\MeetingBlock;
use App\Models\MeetingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CollaboratorScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $this->currentAdmin($request);

        return view('announcements.schedule', [
            'admin' => $admin,
            'setting' => MeetingSetting::current(),
            'appointments' => Appointment::query()
                ->orderByDesc('scheduled_for')
                ->latest()
                ->get(),
            'blocks' => MeetingBlock::query()
                ->where('ends_at', '>=', now()->subDay())
                ->orderBy('starts_at')
                ->get(),
        ]);
    }

    public function updateSetting(Request $request): RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        abort_unless($admin->is_master, 403);

        $data = $request->validate([
            'platform_name' => ['nullable', 'string', 'max:120'],
            'meeting_url' => ['nullable', 'url', 'max:1000'],
            'meeting_id' => ['nullable', 'string', 'max:190'],
            'meeting_password' => ['nullable', 'string', 'max:190'],
            'location_notes' => ['nullable', 'string', 'max:1200'],
            'notification_emails' => ['nullable', 'string', 'max:2000'],
            'standard_subject' => ['required', 'string', 'max:190'],
            'standard_message' => ['nullable', 'string', 'max:1200'],
            'default_duration_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'timezone' => ['required', 'string', 'max:80'],
            'slot_interval_minutes' => ['required', 'integer', Rule::in([15, 30, 45, 60])],
            'minimum_notice_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
            'availability' => ['required', 'array'],
            'availability.*.enabled' => ['nullable', 'boolean'],
            'availability.*.start' => ['nullable', 'date_format:H:i'],
            'availability.*.end' => ['nullable', 'date_format:H:i'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $availability = [];

        foreach (MeetingSetting::WEEK_DAYS as $day => $label) {
            $rule = $data['availability'][(string) $day] ?? [];
            $enabled = (bool) ($rule['enabled'] ?? false);
            $start = $rule['start'] ?? '09:00';
            $end = $rule['end'] ?? '17:00';

            if ($enabled && $start >= $end) {
                return back()
                    ->withInput()
                    ->withErrors(["availability.{$day}.end" => "No dia {$label}, a hora final deve ser posterior à hora inicial."]);
            }

            $availability[(string) $day] = [
                'enabled' => $enabled,
                'start' => $start,
                'end' => $end,
            ];
        }

        $emails = collect(preg_split('/[\s,;]+/', (string) ($data['notification_emails'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        if ($request->boolean('is_active') && empty($data['meeting_url'])) {
            return back()
                ->withInput()
                ->withErrors(['meeting_url' => 'Para activar a agenda pública, informe o link da reunião.']);
        }

        MeetingSetting::current()->update([
            'platform_name' => $data['platform_name'] ?? null,
            'meeting_url' => $data['meeting_url'] ?? null,
            'meeting_id' => $data['meeting_id'] ?? null,
            'meeting_password' => $data['meeting_password'] ?? null,
            'location_notes' => $data['location_notes'] ?? null,
            'notification_emails' => $emails,
            'standard_subject' => $data['standard_subject'],
            'standard_message' => $data['standard_message'] ?? null,
            'default_duration_minutes' => (int) $data['default_duration_minutes'],
            'timezone' => $data['timezone'],
            'availability_rules' => $availability,
            'slot_interval_minutes' => (int) $data['slot_interval_minutes'],
            'minimum_notice_minutes' => (int) $data['minimum_notice_minutes'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Configuração da agenda actualizada.');
    }

    public function storeBlock(Request $request): RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        abort_unless($admin->is_master, 403);

        $setting = MeetingSetting::current();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'is_full_day' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1200'],
        ]);

        MeetingBlock::query()->create([
            'title' => $data['title'],
            'starts_at' => Carbon::parse($data['starts_at'], $setting->timezoneName()),
            'ends_at' => Carbon::parse($data['ends_at'], $setting->timezoneName()),
            'is_full_day' => $request->boolean('is_full_day'),
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('status', 'Horário bloqueado.');
    }

    public function destroyBlock(Request $request, MeetingBlock $block): RedirectResponse
    {
        $admin = $this->currentAdmin($request);
        abort_unless($admin->is_master, 403);

        $block->delete();

        return back()->with('status', 'Bloqueio removido.');
    }

    public function updateAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Appointment::STATUSES)],
            'internal_notes' => ['nullable', 'string', 'max:1200'],
        ]);

        $appointment->update($data);

        return back()->with('status', 'Marcação actualizada.');
    }

    private function currentAdmin(Request $request): AnnouncementAdmin
    {
        return AnnouncementAdmin::query()->findOrFail($request->session()->get('announcement_admin_id'));
    }
}
