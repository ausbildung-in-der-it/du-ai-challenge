<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_leads', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('linkedin_url')->nullable();
            $table->string('source', 50)->index();
            $table->string('journey_slug')->nullable()->index();
            $table->string('journey_session_nanoid', 21)->nullable()->index();
            $table->boolean('completed_challenge')->default(false)->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_leads');
    }
};
