<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'meeting_setting_id',
    'name',
    'email',
    'phone',
    'organization',
    'position',
    'subject',
    'message',
    'scheduled_for',
    'duration_minutes',
    'timezone',
    'status',
    'meeting_platform',
    'meeting_url',
    'meeting_id',
    'meeting_password',
    'location_notes',
    'internal_notes',
    'ip_address',
])]
class Appointment extends Model
{
    public const STATUSES = ['scheduled', 'completed', 'cancelled'];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function meetingSetting(): BelongsTo
    {
        return $this->belongsTo(MeetingSetting::class);
    }

    public function scheduledEnd(): Carbon
    {
        return $this->scheduledLocal()->addMinutes($this->duration_minutes);
    }

    public function scheduledLocal(): Carbon
    {
        return $this->scheduled_for->copy()->timezone($this->timezone ?: 'Africa/Johannesburg');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'completed' => 'Realizada',
            'cancelled' => 'Cancelada',
            default => 'Agendada',
        };
    }

    public function meetingDetailsText(): string
    {
        return collect([
            $this->meeting_platform ? "Plataforma: {$this->meeting_platform}" : null,
            $this->meeting_url ? "Link: {$this->meeting_url}" : null,
            $this->meeting_id ? "ID: {$this->meeting_id}" : null,
            $this->meeting_password ? "Senha: {$this->meeting_password}" : null,
            $this->location_notes,
        ])->filter()->implode("\n");
    }

    public function googleCalendarUrl(): string
    {
        $start = $this->scheduled_for->copy()->utc()->format('Ymd\THis\Z');
        $end = $this->scheduledEnd()->utc()->format('Ymd\THis\Z');
        $details = trim(($this->message ? "Contexto: {$this->message}\n\n" : '').$this->meetingDetailsText());

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $this->subject ?: 'Conversa com Business Diversity',
            'dates' => "{$start}/{$end}",
            'details' => $details,
            'location' => $this->meeting_url ?: $this->meeting_platform,
        ]);
    }
}
