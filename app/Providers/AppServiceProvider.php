<?php

namespace App\Providers;

use App\Support\SiteSettings;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SiteSettings::class);
    }

    public function boot(): void
    {
        // Overlay the admin-editable settings onto config/agent.php so every
        // existing config('agent.*') call picks them up without changes.
        $this->app->make(SiteSettings::class)->apply();
    }
}
