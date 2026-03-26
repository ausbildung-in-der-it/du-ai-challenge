<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journey_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_journey_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // quiz, learn
            $table->unsignedInteger('position');
            $table->json('config')->nullable();
            $table->timestamps();

            $table->index(['learning_journey_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journey_blocks');
    }
};
