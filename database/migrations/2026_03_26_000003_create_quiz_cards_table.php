<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journey_block_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->string('headline');
            $table->text('story');
            $table->string('date_label');
            $table->boolean('is_real');
            $table->text('explanation');
            $table->json('sources')->nullable();
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->index(['journey_block_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_cards');
    }
};
