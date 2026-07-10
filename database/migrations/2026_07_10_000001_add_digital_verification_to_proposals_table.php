<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table): void {
            $table->string('verification_code', 80)->nullable()->unique()->after('reference');
            $table->string('verification_token', 96)->nullable()->unique()->after('verification_code');
            $table->timestamp('certified_at')->nullable()->after('expires_at');
            $table->timestamp('revoked_at')->nullable()->after('certified_at');
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table): void {
            $table->dropUnique(['verification_code']);
            $table->dropUnique(['verification_token']);
            $table->dropColumn([
                'verification_code',
                'verification_token',
                'certified_at',
                'revoked_at',
            ]);
        });
    }
};
