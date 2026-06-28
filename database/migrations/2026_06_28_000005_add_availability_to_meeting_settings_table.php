<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meeting_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('meeting_settings', 'timezone')) {
                $table->string('timezone', 80)->default('Africa/Maputo');
            }

            if (! Schema::hasColumn('meeting_settings', 'availability_rules')) {
                $table->json('availability_rules')->nullable();
            }

            if (! Schema::hasColumn('meeting_settings', 'slot_interval_minutes')) {
                $table->unsignedSmallInteger('slot_interval_minutes')->default(30);
            }

            if (! Schema::hasColumn('meeting_settings', 'minimum_notice_minutes')) {
                $table->unsignedSmallInteger('minimum_notice_minutes')->default(120);
            }
        });
    }

    public function down(): void
    {
        Schema::table('meeting_settings', function (Blueprint $table): void {
            foreach (['timezone', 'availability_rules', 'slot_interval_minutes', 'minimum_notice_minutes'] as $column) {
                if (Schema::hasColumn('meeting_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
