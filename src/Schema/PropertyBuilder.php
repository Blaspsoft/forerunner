<?php

namespace Blaspsoft\Forerunner\Schema;

class PropertyBuilder
{
    protected string $name;

    protected string $type;

    protected ?string $description;

    protected bool $required = false;

    /** @var array<int, mixed>|null */
    protected ?array $enum = null;

    protected ?Builder $nestedBuilder = null;

    /** @var array<string, mixed>|null */
    protected ?array $items = null;

    protected mixed $default = null;

    protected ?int $minLength = null;

    protected ?int $maxLength = null;

    protected ?int $minItems = null;

    protected ?int $maxItems = null;

    protected ?float $minimum = null;

    protected ?float $maximum = null;

    protected ?string $pattern = null;

    protected ?bool $uniqueItems = null;

    protected ?string $format = null;

    protected bool $nullable = false;

    protected ?string $title = null;

    public function __construct(string $name, string $type, ?string $description = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * Mark this field as required.
     */
    public function required(): self
    {
        $this->required = true;

        return $this;
    }

    /**
     * Mark this field as optional.
     */
    public function optional(): self
    {
        $this->required = false;

        return $this;
    }

    /**
     * Set the description for this field.
     */
    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set enum values for this field.
     *
     * @param  array<int, mixed>  $values
     */
    public function enum(array $values): self
    {
        $this->enum = $values;

        return $this;
    }

    /**
     * Define the items type for array fields.
     */
    public function items(string $type, ?callable $callback = null): self
    {
        if ($callback && $type === 'object') {
            $nestedBuilder = new Builder($this->name.'_item');
            $callback($nestedBuilder);
            $this->items = $nestedBuilder->toArray();
        } else {
            $this->items = ['type' => $type];
        }

        return $this;
    }

    /**
     * Set a default value for this field.
     */
    public function default(mixed $value): self
    {
        $this->default = $value;

        return $this;
    }

    /**
     * Set minimum length for string fields.
     */
    public function minLength(int $length): self
    {
        $this->minLength = $length;

        return $this;
    }

    /**
     * Set maximum length for string fields.
     */
    public function maxLength(int $length): self
    {
        $this->maxLength = $length;

        return $this;
    }

    /**
     * Set minimum items for array fields.
     */
    public function minItems(int $count): self
    {
        $this->minItems = $count;

        return $this;
    }

    /**
     * Set maximum items for array fields.
     */
    public function maxItems(int $count): self
    {
        $this->maxItems = $count;

        return $this;
    }

    /**
     * Set minimum value for numeric fields.
     */
    public function min(float $value): self
    {
        $this->minimum = $value;

        return $this;
    }

    /**
     * Set maximum value for numeric fields.
     */
    public function max(float $value): self
    {
        $this->maximum = $value;

        return $this;
    }

    /**
     * Set a regex pattern for string validation.
     */
    public function pattern(string $regex): self
    {
        $this->pattern = $regex;

        return $this;
    }

    /**
     * Set the title for this field.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Ensure array items are unique.
     */
    public function uniqueItems(bool $unique = true): self
    {
        $this->uniqueItems = $unique;

        return $this;
    }

    /**
     * Set the format for string validation.
     */
    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Mark this field as nullable.
     */
    public function nullable(bool $nullable = true): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * Set a nested builder for object types.
     */
    public function setNestedBuilder(Builder $builder): void
    {
        $this->nestedBuilder = $builder;
    }

    /**
     * Check if this field is required.
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Get the field name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Convert the property to a JSON schema array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        // Handle nullable types
        if ($this->nullable) {
            $property = ['type' => [$this->type, 'null']];
        } else {
            $property = ['type' => $this->type];
        }

        if ($this->title) {
            $property['title'] = $this->title;
        }

        if ($this->description) {
            $property['description'] = $this->description;
        }

        if ($this->format) {
            $property['format'] = $this->format;
        }

        if ($this->enum !== null) {
            $property['enum'] = $this->enum;
        }

        if ($this->nestedBuilder !== null) {
            $nestedArray = $this->nestedBuilder->toArray();
            // When nullable is set on an object, keep the nullable type but merge nested properties
            if ($this->nullable) {
                // Preserve the nullable type array, merge everything except 'type'
                unset($nestedArray['type']);
                $property = array_merge($property, $nestedArray);
            } else {
                $property = array_merge($property, $nestedArray);
            }
        }

        if ($this->items !== null) {
            $property['items'] = $this->items;
        }

        if ($this->uniqueItems !== null) {
            $property['uniqueItems'] = $this->uniqueItems;
        }

        if ($this->default !== null) {
            $property['default'] = $this->default;
        }

        if ($this->minLength !== null) {
            $property['minLength'] = $this->minLength;
        }

        if ($this->maxLength !== null) {
            $property['maxLength'] = $this->maxLength;
        }

        if ($this->minItems !== null) {
            $property['minItems'] = $this->minItems;
        }

        if ($this->maxItems !== null) {
            $property['maxItems'] = $this->maxItems;
        }

        if ($this->minimum !== null) {
            $property['minimum'] = $this->minimum;
        }

        if ($this->maximum !== null) {
            $property['maximum'] = $this->maximum;
        }

        if ($this->pattern !== null) {
            $property['pattern'] = $this->pattern;
        }

        return $property;
    }
}
