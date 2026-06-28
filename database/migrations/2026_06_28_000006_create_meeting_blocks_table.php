<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_blocks', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->default('Indisponível');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_full_day')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_blocks');
    }
};
