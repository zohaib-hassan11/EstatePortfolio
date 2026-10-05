<?php

namespace App\Providers;

use App\Support\Ai\AiConnector;
use App\Support\Ai\ClaudeConnector;
use App\Support\Ai\NullConnector;
use App\Support\SiteSettings;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SiteSettings::class);

        // No API key means no AI, and the app says so honestly rather than
        // failing at the point of use. Tests bind their own fake over this.
        $this->app->singleton(AiConnector::class, fn () => filled(config('ai.key'))
            ? new ClaudeConnector(
                apiKey: (string) config('ai.key'),
                model: (string) config('ai.model'),
                maxTokens: (int) config('ai.max_tokens'),
                timeout: (int) config('ai.timeout'),
            )
            : new NullConnector());
    }

    public function boot(): void
    {
        // Overlay the admin-editable settings onto config/agent.php so every
        // existing config('agent.*') call picks them up without changes.
        $this->app->make(SiteSettings::class)->apply();
    }
}
