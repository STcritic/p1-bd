<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('announcement_admin_id')
                ->constrained('announcement_admins')
                ->cascadeOnDelete();
            $table->string('reference', 80);
            $table->string('service_slug', 80);
            $table->string('service_title', 190);
            $table->string('client_name', 190);
            $table->string('client_contact', 190)->nullable();
            $table->string('status', 30)->default('rascunho');
            $table->json('form_data');
            $table->text('notes')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $table->index(['announcement_admin_id', 'status']);
            $table->index(['announcement_admin_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
