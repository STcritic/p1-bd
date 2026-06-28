<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcement_admins', function (Blueprint $table): void {
            $table->timestamp('password_changed_at')->nullable()->after('password');
            $table->timestamp('password_expires_at')->nullable()->after('password_changed_at');
        });

        DB::table('announcement_admins')
            ->whereNull('password_changed_at')
            ->update([
                'password_changed_at' => now(),
                'password_expires_at' => now()->addMonths(6),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('announcement_admins', function (Blueprint $table): void {
            $table->dropColumn(['password_changed_at', 'password_expires_at']);
        });
    }
};
