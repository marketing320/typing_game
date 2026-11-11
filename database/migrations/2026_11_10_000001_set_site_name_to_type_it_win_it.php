<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'site_name'],
            ['setting_value' => 'TYPE IT. WIN IT']
        );
        Cache::forget('setting:site_name');
    }

    public function down(): void
    {
        DB::table('system_settings')
            ->where('setting_key', 'site_name')
            ->where('setting_value', 'TYPE IT. WIN IT')
            ->update(['setting_value' => 'WordSprint']);
        Cache::forget('setting:site_name');
    }
};
