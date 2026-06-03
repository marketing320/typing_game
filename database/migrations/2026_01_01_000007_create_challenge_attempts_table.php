<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('typing_challenges')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('typing_text_id')->constrained('typing_texts')->cascadeOnDelete();
            $table->enum('status', ['started', 'completed', 'failed', 'disqualified'])->default('started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('duration_seconds', 10, 3)->nullable();
            $table->unsignedInteger('total_words')->default(0);
            $table->unsignedInteger('correct_words')->default(0);
            $table->unsignedInteger('wrong_words')->default(0);
            $table->unsignedInteger('total_characters')->default(0);
            $table->unsignedInteger('correct_characters')->default(0);
            $table->unsignedInteger('wrong_characters')->default(0);
            $table->decimal('wpm', 8, 2)->default(0);
            $table->decimal('accuracy', 5, 2)->default(0);
            $table->unsignedInteger('mistake_count')->default(0);
            $table->unsignedInteger('remaining_lives')->default(1);
            $table->longText('user_input')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('distance_from_allowed_meters', 10, 2)->nullable();
            $table->boolean('is_within_geofence')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_attempts');
    }
};
