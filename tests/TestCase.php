<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Tests;

use Blaspsoft\Forerunner\ForerunnerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ForerunnerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default configuration here
    }
}
