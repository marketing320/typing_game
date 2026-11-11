<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'layouts.admin', 'admin.auth.login'], function (): void {
            config(['app.name' => SystemSetting::get('site_name') ?: config('app.name')]);
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
