<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('full_name', 100)->nullable()->after('username');
            $table->string('phone', 30)->nullable()->after('full_name');
            $table->string('referral_source', 100)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'phone', 'referral_source']);
        });
    }
};
