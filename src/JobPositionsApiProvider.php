<?php

declare(strict_types=1);

namespace SharpAPI\JobPositionsApi;

use Illuminate\Support\ServiceProvider;

/**
 * @api
 */
class JobPositionsApiProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sharpapi-job-positions-api.php' => config_path('sharpapi-job-positions-api.php'),
            ], 'sharpapi-job-positions-api');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Merge the package configuration with the app configuration.
        $this->mergeConfigFrom(
            __DIR__.'/../config/sharpapi-job-positions-api.php', 'sharpapi-job-positions-api'
        );
    }
}