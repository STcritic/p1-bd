<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_event_id',
    'name',
    'email',
    'phone',
    'organization',
    'position',
    'seats_requested',
    'status',
    'notes',
    'internal_notes',
    'source',
])]
class EventRegistration extends Model
{
    public const STATUSES = ['pending', 'confirmed', 'waitlist', 'cancelled'];

    protected function casts(): array
    {
        return [
            'seats_requested' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompanyEvent::class, 'company_event_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'confirmed' => 'Confirmado',
            'waitlist' => 'Lista de espera',
            'cancelled' => 'Cancelado',
            default => 'Pendente',
        };
    }
}
