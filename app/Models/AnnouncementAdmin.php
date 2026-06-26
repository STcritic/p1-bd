<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'is_master', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class AnnouncementAdmin extends Model
{
    protected function casts(): array
    {
        return [
            'is_master' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
