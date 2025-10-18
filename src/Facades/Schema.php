<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Blaspsoft\Forerunner\Schema\Struct define(string $name, string $description, callable $callback)
 *
 * @see \Blaspsoft\Forerunner\Schema\Struct
 */
class Schema extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'forerunner.schema';
    }
}
