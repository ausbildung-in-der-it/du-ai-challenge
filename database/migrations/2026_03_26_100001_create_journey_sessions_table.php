<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journey_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('nanoid', 21)->unique();
            $table->foreignId('learning_journey_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('current_block')->default(0);
            $table->unsignedInteger('current_item')->default(0);
            $table->json('answers')->nullable();
            $table->string('persona_style')->nullable();
            $table->boolean('persona_prompt_shown')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journey_sessions');
    }
};
