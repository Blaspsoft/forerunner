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

    protected bool $additionalProperties = false;

    protected ?string $schemaVersion = null;

    protected ?string $title = null;

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
     * Add an email field.
     */
    public function email(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('email');
    }

    /**
     * Add a URL field.
     */
    public function url(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('uri');
    }

    /**
     * Add a UUID field.
     */
    public function uuid(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('uuid');
    }

    /**
     * Add a date-time field.
     */
    public function datetime(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('date-time');
    }

    /**
     * Add a date field.
     */
    public function date(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('date');
    }

    /**
     * Add a time field.
     */
    public function time(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('time');
    }

    /**
     * Add an IPv4 address field.
     */
    public function ipv4(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('ipv4');
    }

    /**
     * Add an IPv6 address field.
     */
    public function ipv6(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('ipv6');
    }

    /**
     * Add a hostname field.
     */
    public function hostname(string $name, ?string $description = null): PropertyBuilder
    {
        return $this->addProperty($name, 'string', $description)->format('hostname');
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
     * Set the title for the entire schema.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set whether additional properties are allowed.
     */
    public function additionalProperties(bool $allowed = true): self
    {
        $this->additionalProperties = $allowed;

        return $this;
    }

    /**
     * Enable strict mode: disallow additional properties and mark all fields as required.
     * This is useful for LLM APIs like OpenAI that require all properties to be required.
     */
    public function strict(): self
    {
        $this->additionalProperties = false;

        // Mark all properties as required
        foreach ($this->properties as $property) {
            $property->required();
        }

        return $this;
    }

    /**
     * Set the JSON Schema version.
     */
    public function schemaVersion(string $version = 'https://json-schema.org/draft/2020-12/schema'): self
    {
        $this->schemaVersion = $version;

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

        if ($this->schemaVersion) {
            $schema['$schema'] = $this->schemaVersion;
        }

        if ($this->title) {
            $schema['title'] = $this->title;
        }

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

        $schema['additionalProperties'] = $this->additionalProperties;

        return $schema;
    }

    /**
     * Convert the builder to a JSON string.
     *
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }
}
