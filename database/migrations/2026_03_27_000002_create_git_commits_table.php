<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_commits', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 40)->unique();
            $table->string('short_hash', 7);
            $table->string('message');
            $table->timestamp('committed_at');
            $table->unsignedInteger('minutes_in')->default(0);
            $table->timestamps();

            $table->index('committed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_commits');
    }
};
