<?php

namespace Blaspsoft\Forerunner\Schema;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class Struct implements \ArrayAccess, \JsonSerializable
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
    public static function define(string $name, callable $callback): self
    {
        $builder = new Builder($name);
        $callback($builder);

        return new self($name, $builder);
    }

    /**
     * Convert the schema to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->cache === null) {
            $this->cache = $this->builder->toArray();
        }

        return $this->cache;
    }

    /**
     * Convert the schema to a JSON string.
     *
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return $this->builder->toJson();
    }

    /**
     * Check if an offset exists (ArrayAccess).
     *
     * @param  string  $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    /**
     * Get an offset value (ArrayAccess).
     *
     * @param  string  $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset];
    }

    /**
     * Set an offset value (ArrayAccess) - not supported.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('Struct schemas are immutable');
    }

    /**
     * Unset an offset (ArrayAccess) - not supported.
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('Struct schemas are immutable');
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
