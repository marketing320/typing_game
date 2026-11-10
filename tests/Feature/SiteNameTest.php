<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_updates_headers_and_cached_site_name(): void
    {
        config(['app.name' => 'WordSprint']);
        SystemSetting::set('site_name', 'WordSprint');
        $this->assertSame('WordSprint', SystemSetting::get('site_name'));

        $migration = require database_path('migrations/2026_11_10_000001_set_site_name_to_type_it_win_it.php');
        $migration->up();
        $migration->up();

        $this->assertSame('TYPE IT. WIN IT', SystemSetting::get('site_name'));
        $this->assertDatabaseCount('system_settings', 1);
        foreach (['home', 'admin.login'] as $route) {
            config(['app.name' => 'WordSprint']);
            $this->get(route($route))->assertOk()
                ->assertSee('class="brand-copy">TYPE IT. WIN IT', false)
                ->assertSee(' | TYPE IT. WIN IT</title>', false);
        }

        $migration->down();
        $this->assertSame('WordSprint', SystemSetting::get('site_name'));
    }

    public function test_blank_site_name_falls_back_to_configured_name(): void
    {
        config(['app.name' => 'Configured Name']);
        SystemSetting::set('site_name', '');

        $this->get(route('home'))->assertOk()
            ->assertSee('class="brand-copy">Configured Name', false);
    }
}
