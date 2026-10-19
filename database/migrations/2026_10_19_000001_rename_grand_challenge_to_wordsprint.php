<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('typing_challenges')
            ->where('title', 'Typing Monkey Grand Challenge')
            ->update(['title' => 'WordSprint Grand Challenge']);
    }

    public function down(): void
    {
        DB::table('typing_challenges')
            ->where('title', 'WordSprint Grand Challenge')
            ->update(['title' => 'Typing Monkey Grand Challenge']);
    }
};
