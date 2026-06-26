<?php

namespace Database\Seeders;

use App\Models\AnnouncementAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AnnouncementAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = strtolower((string) config('announcements.master_email'));
        $password = (string) config('announcements.master_password');

        if ($email === '' || $password === '') {
            return;
        }

        AnnouncementAdmin::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Business Diversity',
                'password' => Hash::make($password),
                'is_master' => true,
                'is_active' => true,
            ]
        );
    }
}
