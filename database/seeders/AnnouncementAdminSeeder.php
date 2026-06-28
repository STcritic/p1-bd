<?php

namespace Database\Seeders;

use App\Support\AnnouncementMasterAccess;
use Illuminate\Database\Seeder;

class AnnouncementAdminSeeder extends Seeder
{
    public function run(): void
    {
        app(AnnouncementMasterAccess::class)->ensure();
    }
}
