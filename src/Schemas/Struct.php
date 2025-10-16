<?php

namespace Blaspsoft\Forerunner\Schemas;

class Struct
{
    /** @var array<string, mixed> */
    protected array $schema = [];

    protected string $name;

    /**
     * Define a new structure schema.
     *
     * @return array<string, mixed>
     */
    public static function define(string $name, callable $callback): array
    {
        $instance = new self;
        $instance->name = $name;

        $builder = new Builder($name);
        $callback($builder);

        return $builder->toArray();
    }
}
