<?php

namespace App\Support;

use App\Models\AnnouncementAdmin;
use Illuminate\Support\Facades\Hash;

class AnnouncementMasterAccess
{
    public function ensure(): ?AnnouncementAdmin
    {
        $email = strtolower(trim((string) config('announcements.master_email')));
        $passwordHash = $this->configuredPasswordHash();

        if ($email === '' || $passwordHash === '') {
            return null;
        }

        $admin = AnnouncementAdmin::query()->where('email', $email)->first();

        if (! $admin) {
            return AnnouncementAdmin::query()->create([
                'name' => 'Business Diversity',
                'email' => $email,
                'password' => $passwordHash,
                'password_changed_at' => now(),
                'password_expires_at' => $this->nextExpiryDate(),
                'is_master' => true,
                'is_active' => true,
            ]);
        }

        $updates = [
            'name' => $admin->name ?: 'Business Diversity',
            'is_master' => true,
            'is_active' => true,
        ];

        if (! $admin->password_changed_at) {
            $updates['password_changed_at'] = now();
        }

        if (! $admin->password_expires_at) {
            $updates['password_expires_at'] = $this->nextExpiryDate();
        }

        $admin->forceFill($updates)->save();

        return $admin->fresh();
    }

    public function nextExpiryDate()
    {
        return now()->addMonths((int) config('announcements.password_expires_months', 6));
    }

    private function configuredPasswordHash(): string
    {
        $password = (string) config('announcements.master_password');

        if ($password !== '') {
            return Hash::make($password);
        }

        return (string) config('announcements.master_password_hash');
    }
}
