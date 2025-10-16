<?php

namespace Blaspsoft\Forerunner;

use Blaspsoft\Forerunner\Commands\MakeStructCommand;
use Blaspsoft\Forerunner\Schemas\Struct;
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

        $this->app->bind('forerunner.schema', function () {
            return new Struct;
        });
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

            $this->commands([
                MakeStructCommand::class,
            ]);
        }
    }
}
