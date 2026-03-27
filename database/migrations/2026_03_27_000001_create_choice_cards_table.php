<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('choice_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journey_block_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->json('options'); // ["Option A", "Option B", "Option C", "Option D"]
            $table->unsignedTinyInteger('correct_index');
            $table->text('explanation');
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->index(['journey_block_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('choice_cards');
    }
};
