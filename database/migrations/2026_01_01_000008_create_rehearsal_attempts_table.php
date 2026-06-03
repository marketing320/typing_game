<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rehearsal_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('typing_text_id')->constrained('typing_texts')->cascadeOnDelete();
            $table->string('anonymous_id')->nullable();
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
            $table->longText('user_input')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rehearsal_attempts');
    }
};
