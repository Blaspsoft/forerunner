<?php

namespace Blaspsoft\Forerunner\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, mixed> define(string $name, callable $callback)
 *
 * @see \Blaspsoft\Forerunner\Schemas\Struct
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
