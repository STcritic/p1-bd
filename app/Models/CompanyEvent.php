<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'announcement_admin_id',
    'announcement_id',
    'title',
    'slug',
    'summary',
    'description',
    'audience',
    'format',
    'location',
    'image_url',
    'external_url',
    'seats_total',
    'starts_at',
    'ends_at',
    'registration_deadline',
    'is_active',
    'is_featured',
])]
class CompanyEvent extends Model
{
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_deadline' => 'datetime',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'seats_total' => 'integer',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AnnouncementAdmin::class, 'announcement_admin_id');
    }

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereNull('starts_at')->orWhere('starts_at', '>=', now()->startOfDay());
        });
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereNotNull('starts_at')->where('starts_at', '<', now()->startOfDay());
    }

    public function reservedSeats(): int
    {
        return (int) $this->registrations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('seats_requested');
    }

    public function remainingSeats(): ?int
    {
        if (! $this->seats_total) {
            return null;
        }

        return max(0, $this->seats_total - $this->reservedSeats());
    }

    public function isFull(): bool
    {
        return $this->remainingSeats() !== null && $this->remainingSeats() <= 0;
    }

    public function registrationOpen(): bool
    {
        return $this->is_active
            && (! $this->registration_deadline || $this->registration_deadline->endOfDay()->isFuture())
            && (! $this->starts_at || $this->starts_at->isFuture());
    }

    public function displayDate(): string
    {
        if (! $this->starts_at) {
            return 'Data a anunciar';
        }

        return $this->starts_at->format('d/m/Y · H:i');
    }

    public function publicUrl(string $locale = 'pt'): string
    {
        return route($locale === 'en' ? 'en.events.show' : 'events.show', $this);
    }
}
