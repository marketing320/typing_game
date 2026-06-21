<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('typing_challenges', function (Blueprint $table) {
            $table->boolean('require_unique_email')->default(false)->after('max_attempts_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('typing_challenges', function (Blueprint $table) {
            $table->dropColumn('require_unique_email');
        });
    }
};
