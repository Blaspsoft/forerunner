<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Providers;

use Blaspsoft\Forerunner\Schema\Struct;

/**
 * Formats a struct for Gemini structured output via `responseJsonSchema`.
 */
class GeminiFormatter implements Formatter
{
    /**
     * Keywords Gemini accepts but does not enforce. Their values are kept as a description hint instead.
     *
     * @var array<int, string>
     */
    public const UNENFORCED_KEYWORDS = [
        'minLength',
        'maxLength',
        'uniqueItems',
    ];

    /**
     * Build the `generationConfig` fields for a JSON response.
     *
     * @return array<string, mixed>
     */
    public function format(Struct $struct): array
    {
        $schema = SchemaTransformer::withoutMetaKeywords($struct->toSchema());

        return [
            'responseMimeType' => 'application/json',
            'responseJsonSchema' => SchemaTransformer::moveToDescription($schema, self::UNENFORCED_KEYWORDS),
        ];
    }
}
