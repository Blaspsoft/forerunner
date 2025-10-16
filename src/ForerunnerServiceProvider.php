<?php

namespace Blaspsoft\Forerunner;

use Illuminate\Support\ServiceProvider;

class ForerunnerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/forerunner.php',
            'forerunner'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/forerunner.php' => config_path('forerunner.php'),
            ], 'forerunner-config');
        }
    }
}
