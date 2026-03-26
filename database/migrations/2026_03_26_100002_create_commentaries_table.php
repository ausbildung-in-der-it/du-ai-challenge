<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commentaries', function (Blueprint $table) {
            $table->id();
            $table->string('nanoid', 21)->unique();
            $table->foreignId('journey_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('quiz_card_id');
            $table->string('persona_style')->nullable();
            $table->string('status')->default('pending'); // pending, processing, done, failed
            $table->text('text')->nullable();
            $table->text('prompt_used')->nullable();
            $table->timestamps();

            $table->index(['journey_session_id', 'quiz_card_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commentaries');
    }
};
