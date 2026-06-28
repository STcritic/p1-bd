<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable([
    'platform_name',
    'meeting_url',
    'meeting_id',
    'meeting_password',
    'location_notes',
    'notification_emails',
    'standard_subject',
    'standard_message',
    'default_duration_minutes',
    'timezone',
    'availability_rules',
    'slot_interval_minutes',
    'minimum_notice_minutes',
    'is_active',
])]
class MeetingSetting extends Model
{
    public const WEEK_DAYS = [
        1 => 'Segunda',
        2 => 'Terça',
        3 => 'Quarta',
        4 => 'Quinta',
        5 => 'Sexta',
        6 => 'Sábado',
        7 => 'Domingo',
    ];

    protected function casts(): array
    {
        return [
            'notification_emails' => 'array',
            'availability_rules' => 'array',
            'default_duration_minutes' => 'integer',
            'slot_interval_minutes' => 'integer',
            'minimum_notice_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            [
                'platform_name' => 'Google Meet',
                'standard_subject' => 'Conversa de diagnóstico BD',
                'standard_message' => 'Use os dados abaixo para entrar na reunião.',
                'default_duration_minutes' => 30,
                'timezone' => 'Africa/Maputo',
                'availability_rules' => self::defaultAvailabilityRules(),
                'slot_interval_minutes' => 30,
                'minimum_notice_minutes' => 120,
                'notification_emails' => [config('mail.contact_to', 'info@bdiversity.co.mz')],
                'is_active' => false,
            ]
        );
    }

    public static function defaultAvailabilityRules(): array
    {
        $rules = [];

        foreach (self::WEEK_DAYS as $day => $label) {
            $rules[(string) $day] = [
                'enabled' => $day <= 5,
                'start' => '09:00',
                'end' => '17:00',
            ];
        }

        return $rules;
    }

    public function notificationEmailList(): array
    {
        return collect($this->notification_emails ?: [])
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    public function timezoneName(): string
    {
        return $this->timezone ?: config('app.timezone', 'Africa/Maputo');
    }

    public function availabilityRules(): array
    {
        $rules = $this->availability_rules ?: self::defaultAvailabilityRules();

        return collect(self::WEEK_DAYS)
            ->mapWithKeys(function (string $label, int $day) use ($rules): array {
                $rule = $rules[(string) $day] ?? $rules[$day] ?? [];

                return [(string) $day => [
                    'enabled' => (bool) ($rule['enabled'] ?? false),
                    'start' => $rule['start'] ?? '09:00',
                    'end' => $rule['end'] ?? '17:00',
                ]];
            })
            ->all();
    }

    public function workingWindowFor(Carbon|string $date): ?array
    {
        $day = $date instanceof Carbon
            ? $date->copy()->timezone($this->timezoneName())->startOfDay()
            : Carbon::parse($date, $this->timezoneName())->startOfDay();

        $rule = $this->availabilityRules()[(string) $day->isoWeekday()] ?? null;

        if (! $rule || ! $rule['enabled']) {
            return null;
        }

        $start = Carbon::parse($day->format('Y-m-d').' '.$rule['start'], $this->timezoneName());
        $end = Carbon::parse($day->format('Y-m-d').' '.$rule['end'], $this->timezoneName());

        if ($end->lessThanOrEqualTo($start)) {
            return null;
        }

        return [$start, $end];
    }

    public function isInsideWorkingWindow(Carbon $start, Carbon $end): bool
    {
        $window = $this->workingWindowFor($start);

        if (! $window) {
            return false;
        }

        [$workStart, $workEnd] = $window;

        return $start->greaterThanOrEqualTo($workStart) && $end->lessThanOrEqualTo($workEnd);
    }

    public function hasBusyConflict(Carbon $start, Carbon $end): bool
    {
        $appointments = Appointment::query()
            ->where('status', 'scheduled')
            ->where('scheduled_for', '<', $end->format('Y-m-d H:i:s'))
            ->where('scheduled_for', '>=', $start->copy()->subDay()->format('Y-m-d H:i:s'))
            ->get();

        foreach ($appointments as $appointment) {
            $appointmentStart = $appointment->scheduled_for->copy()->timezone($this->timezoneName());
            $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration_minutes);

            if ($appointmentStart->lessThan($end) && $appointmentEnd->greaterThan($start)) {
                return true;
            }
        }

        $blocks = MeetingBlock::query()
            ->where('starts_at', '<', $end->format('Y-m-d H:i:s'))
            ->where('ends_at', '>', $start->format('Y-m-d H:i:s'))
            ->exists();

        return $blocks;
    }

    public function acceptsAppointmentAt(Carbon $start): bool
    {
        if (! $this->is_active || ! $this->meeting_url) {
            return false;
        }

        $start = $start->copy()->timezone($this->timezoneName());
        $end = $start->copy()->addMinutes($this->default_duration_minutes);
        $minimumStart = now($this->timezoneName())->addMinutes($this->minimum_notice_minutes ?: 0);

        return $start->greaterThanOrEqualTo($minimumStart)
            && $this->isInsideWorkingWindow($start, $end)
            && ! $this->hasBusyConflict($start, $end);
    }

    public function availableSlotsForDate(Carbon|string $date): array
    {
        if (! $this->is_active || ! $this->meeting_url) {
            return [];
        }

        $window = $this->workingWindowFor($date);

        if (! $window) {
            return [];
        }

        [$workStart, $workEnd] = $window;
        $duration = max(15, $this->default_duration_minutes ?: 30);
        $interval = max(15, $this->slot_interval_minutes ?: 30);
        $minimumStart = now($this->timezoneName())->addMinutes($this->minimum_notice_minutes ?: 0);
        $slots = [];

        for ($slot = $workStart->copy(); $slot->copy()->addMinutes($duration)->lessThanOrEqualTo($workEnd); $slot->addMinutes($interval)) {
            $slotEnd = $slot->copy()->addMinutes($duration);

            if ($slot->lessThan($minimumStart)) {
                continue;
            }

            if ($this->hasBusyConflict($slot, $slotEnd)) {
                continue;
            }

            $slots[] = [
                'value' => $slot->format('Y-m-d\TH:i'),
                'time' => $slot->format('H:i'),
                'label' => $slot->format('H:i'),
            ];
        }

        return $slots;
    }
}
