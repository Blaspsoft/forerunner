<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner;

use Blaspsoft\Forerunner\Commands\MakeStructCommand;
use Blaspsoft\Forerunner\Schema\Struct;
use Illuminate\Support\ServiceProvider;

class ForerunnerServiceProvider extends ServiceProvider
{
    /**
     * Register the Forerunner schema service in the application container.
     *
     * Binds a singleton under the container key "forerunner.schema" that resolves to an object
     * which forwards dynamic instance and static calls to the `Struct` class's static methods.
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
     * Register console commands when the application is running in a console.
     *
     * Registers the MakeStructCommand so it becomes available to the application's CLI when executed in a console environment.
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