<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Providers;

use Blaspsoft\Forerunner\Schema\Struct;

/**
 * Formats a struct for OpenAI Structured Outputs, which require every object to be closed
 * and to list every property as required (optional fields become nullable).
 */
class OpenAIFormatter implements Formatter
{
    /**
     * Build the Chat Completions `response_format` payload.
     *
     * @return array<string, mixed>
     */
    public function format(Struct $struct): array
    {
        return [
            'type' => 'json_schema',
            'json_schema' => $this->jsonSchema($struct),
        ];
    }

    /**
     * Build the Responses API `text.format` payload.
     *
     * @return array<string, mixed>
     */
    public function responses(Struct $struct): array
    {
        return ['type' => 'json_schema', ...$this->jsonSchema($struct)];
    }

    /**
     * Build the named, strict JSON schema shared by both OpenAI APIs.
     *
     * @return array<string, mixed>
     */
    protected function jsonSchema(Struct $struct): array
    {
        $schema = SchemaTransformer::withoutMetaKeywords($struct->toSchema());
        $schema = SchemaTransformer::requireAll(SchemaTransformer::closeObjects($schema));

        $description = $schema['description'] ?? null;
        unset($schema['description']);

        return array_filter([
            'name' => SchemaTransformer::name($struct->name()),
            'description' => $description,
            'strict' => true,
            'schema' => $schema,
        ], fn (mixed $value): bool => $value !== null);
    }
}
