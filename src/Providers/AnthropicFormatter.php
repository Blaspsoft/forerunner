<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Providers;

use Blaspsoft\Forerunner\Schema\Struct;

/**
 * Formats a struct for Claude structured outputs, which require closed objects and do not
 * support numeric, string length or array size constraints.
 */
class AnthropicFormatter implements Formatter
{
    /**
     * Keywords Claude structured outputs reject. Their values are kept as a description hint instead.
     *
     * @var array<int, string>
     */
    public const UNSUPPORTED_KEYWORDS = [
        'minimum',
        'maximum',
        'exclusiveMinimum',
        'exclusiveMaximum',
        'multipleOf',
        'minLength',
        'maxLength',
        'minItems',
        'maxItems',
        'uniqueItems',
    ];

    /**
     * Build the Messages API `output_config.format` payload.
     *
     * @return array<string, mixed>
     */
    public function format(Struct $struct): array
    {
        return [
            'type' => 'json_schema',
            'schema' => $this->schema($struct),
        ];
    }

    /**
     * Build a strict tool definition whose input is the struct.
     *
     * @return array<string, mixed>
     */
    public function tool(Struct $struct): array
    {
        $schema = $this->schema($struct);

        $description = $schema['description'] ?? null;
        unset($schema['description']);

        return array_filter([
            'name' => SchemaTransformer::name($struct->name()),
            'description' => $description,
            'input_schema' => $schema,
            'strict' => true,
        ], fn (mixed $value): bool => $value !== null);
    }

    /**
     * Rewrite the struct's schema to fit Claude's supported JSON Schema subset.
     *
     * @return array<string, mixed>
     */
    protected function schema(Struct $struct): array
    {
        $schema = SchemaTransformer::withoutMetaKeywords($struct->toSchema());
        $schema = SchemaTransformer::closeObjects($schema);

        return SchemaTransformer::moveToDescription($schema, self::UNSUPPORTED_KEYWORDS);
    }
}
