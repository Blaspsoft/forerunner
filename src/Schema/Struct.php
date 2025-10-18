<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Schema;

class Struct implements \JsonSerializable
{
    protected Property $builder;

    protected string $name;

    /** @var array<string, mixed>|null */
    protected ?array $cache = null;

    /**
     * Initialize the struct with its identifier and property builder.
     *
     * @param  string  $name  The struct's name used when emitting the schema.
     * @param  Property  $builder  The Property builder responsible for producing this struct's schema.
     */
    protected function __construct(string $name, Property $builder)
    {
        $this->name = $name;
        $this->builder = $builder;
    }

    /**
     * Create a Struct by configuring a Property builder and returning the resulting schema.
     *
     * @param  string  $name  The name of the structure.
     * @param  string  $description  A human-readable description for the structure.
     * @param  callable  $callback  A callable that receives the Property builder (`function(Property $builder): void`) used to configure the structure.
     * @return self A new Struct instance representing the defined schema.
     */
    public static function define(string $name, string $description, callable $callback): self
    {
        $builder = new Property($name);
        $builder->description($description);

        $callback($builder);

        return new self($name, $builder);
    }

    /**
     * Converts the struct to an associative array representation.
     *
     * When the builder is in strict mode the returned array is in OpenAI-compatible format with keys
     * 'name' (the struct name), 'strict' set to true, and 'schema' containing the builder's schema.
     * The result is cached for subsequent calls.
     *
     * @return array<string,mixed> The schema array, or the OpenAI-compatible wrapper when strict mode is enabled.
     */
    public function toArray(): array
    {
        if ($this->cache === null) {
            $builderArray = $this->builder->toArray();

            // If strict mode is enabled, wrap in OpenAI's format
            if ($this->builder->isStrict()) {
                // Extract description from schema and move to top level
                $description = $builderArray['description'] ?? null;
                unset($builderArray['description']);

                $this->cache = [
                    'name' => $this->name,
                    'description' => $description,
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
     * Provide this struct's array representation for JSON serialization.
     *
     * @return array<string,mixed> The struct schema as an associative array suitable for json_encode().
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
