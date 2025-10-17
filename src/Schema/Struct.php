<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Schema;

class Struct implements \JsonSerializable
{
    protected Builder $builder;

    protected string $name;

    /** @var array<string, mixed>|null */
    protected ?array $cache = null;

    protected function __construct(string $name, Builder $builder)
    {
        $this->name = $name;
        $this->builder = $builder;
    }

    /**
     * Define a new structure schema.
     */
    public static function define(string $name, ?string $description, callable $callback): self
    {
        $builder = new Builder($name);

        if ($description !== null) {
            $builder->description($description);
        }

        $callback($builder);

        return new self($name, $builder);
    }

    /**
     * Convert the schema to an array.
     * If strict mode is enabled, wraps the schema in OpenAI's format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->cache === null) {
            $builderArray = $this->builder->toArray();

            // If strict mode is enabled, wrap in OpenAI's format
            if ($this->builder->isStrict()) {
                $this->cache = [
                    'name' => $this->name,
                    'strict' => true,
                    'schema' => $builderArray,
                ];
            } else {
                $this->cache = $builderArray;
            }
        }

        return $this->cache;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
