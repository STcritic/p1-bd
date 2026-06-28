<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'password_changed_at', 'password_expires_at', 'is_master', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class AnnouncementAdmin extends Model
{
    protected function casts(): array
    {
        return [
            'is_master' => 'boolean',
            'is_active' => 'boolean',
            'password_changed_at' => 'datetime',
            'password_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function passwordExpired(): bool
    {
        return $this->password_expires_at !== null && $this->password_expires_at->isPast();
    }

    public function renewPasswordExpiry(): void
    {
        $this->forceFill([
            'password_changed_at' => now(),
            'password_expires_at' => now()->addMonths((int) config('announcements.password_expires_months', 6)),
        ])->save();
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
