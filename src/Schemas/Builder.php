<?php

namespace Blaspsoft\Forerunner\Schemas;

class Builder
{
    protected string $name;

    /** @var array<string, PropertyBuilder> */
    protected array $properties = [];

    /** @var array<int, string> */
    protected array $required = [];

    protected ?string $description = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Add a string field.
     */
    public function string(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description);
    }

    /**
     * Add an integer field.
     */
    public function int(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'integer', $description);
    }

    /**
     * Add an integer field (alias).
     */
    public function integer(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'integer', $description);
    }

    /**
     * Add a float/number field.
     */
    public function float(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'number', $description);
    }

    /**
     * Add a number field (alias).
     */
    public function number(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'number', $description);
    }

    /**
     * Add a boolean field.
     */
    public function boolean(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'boolean', $description);
    }

    /**
     * Add a boolean field (alias).
     */
    public function bool(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'boolean', $description);
    }

    /**
     * Add an array field.
     */
    public function array(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'array', $description);
    }

    /**
     * Add a nested object field.
     */
    public function object(string $name, callable $callback, ?string $description = null): PropertyBuilder
    {
        $nestedBuilder = new Builder($name);
        $callback($nestedBuilder);

        $builder = new PropertyBuilder($name, 'object', $description);
        $builder->setNestedBuilder($nestedBuilder);

        $this->properties[$name] = $builder;

        return $builder;
    }

    /**
     * Add an enum field.
     *
     * @param  array<int, mixed>  $values
     */
    public function enum(string $name, array $values, ?string $description = null): PropertyBuilder
    {
        $builder = $this->addProperty($name, 'string', $description);
        $builder->enum($values);

        return $builder;
    }

    /**
     * Set the description for the entire schema.
     */
    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Add a property to the schema.
     */
    protected function addProperty(string $name, string $type, ?string $description): PropertyBuilder
    {
        $builder = new PropertyBuilder($name, $type, $description);
        $this->properties[$name] = $builder;

        return $builder;
    }

    /**
     * Mark a field as required.
     */
    public function markRequired(string $name): void
    {
        if (! in_array($name, $this->required)) {
            $this->required[] = $name;
        }
    }

    /**
     * Convert the builder to a JSON schema array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [],
        ];

        if ($this->description) {
            $schema['description'] = $this->description;
        }

        foreach ($this->properties as $name => $builder) {
            $schema['properties'][$name] = $builder->toArray();

            if ($builder->isRequired()) {
                $this->markRequired($name);
            }
        }

        if (! empty($this->required)) {
            $schema['required'] = $this->required;
        }

        return $schema;
    }

    /**
     * Convert the builder to a JSON string.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT) ?: '';
    }
}
