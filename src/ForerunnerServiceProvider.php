<?php

namespace Blaspsoft\Forerunner;

use Blaspsoft\Forerunner\Commands\MakeStructCommand;
use Blaspsoft\Forerunner\Schema\Struct;
use Illuminate\Support\ServiceProvider;

class ForerunnerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('forerunner.schema', function () {
            return new class
            {
                /**
                 * @param  array<int, mixed>  $args
                 */
                public static function __callStatic(string $method, array $args): mixed
                {
                    return Struct::$method(...$args);
                }

                /**
                 * @param  array<int, mixed>  $args
                 */
                public function __call(string $method, array $args): mixed
                {
                    return Struct::$method(...$args);
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeStructCommand::class,
            ]);
        }
    }
}
