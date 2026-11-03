<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Providers;

/**
 * Helpers that rewrite a JSON schema array to fit a provider's structured output rules.
 */
class SchemaTransformer
{
    /**
     * Apply a callback to every schema node, children first.
     *
     * @param  array<string, mixed>  $schema
     * @param  callable(array<string, mixed>): array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    public static function walk(array $schema, callable $callback): array
    {
        if (isset($schema['properties']) && is_array($schema['properties'])) {
            foreach ($schema['properties'] as $name => $property) {
                if (is_array($property)) {
                    /** @var array<string, mixed> $property */
                    $schema['properties'][$name] = self::walk($property, $callback);
                }
            }
        }

        if (isset($schema['items']) && is_array($schema['items'])) {
            /** @var array<string, mixed> $items */
            $items = $schema['items'];
            $schema['items'] = self::walk($items, $callback);
        }

        return $callback($schema);
    }

    /**
     * Remove the root-level `$schema` and `$id` keywords, which only describe the schema document.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public static function withoutMetaKeywords(array $schema): array
    {
        unset($schema['$schema'], $schema['$id']);

        return $schema;
    }

    /**
     * Disallow additional properties on every object.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public static function closeObjects(array $schema): array
    {
        return self::walk($schema, function (array $node): array {
            if (self::isObject($node)) {
                $node['additionalProperties'] = false;
            }

            return $node;
        });
    }

    /**
     * List every property as required, making previously optional properties nullable instead.
     *
     * Providers with strict schemas (such as OpenAI) require every key to be listed in `required`,
     * so an optional field is expressed as one that may be `null`.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public static function requireAll(array $schema): array
    {
        return self::walk($schema, function (array $node): array {
            if (! self::isObject($node) || ! isset($node['properties']) || ! is_array($node['properties'])) {
                return $node;
            }

            $required = isset($node['required']) && is_array($node['required']) ? $node['required'] : [];

            foreach ($node['properties'] as $name => $property) {
                if (! in_array($name, $required, true) && is_array($property)) {
                    /** @var array<string, mixed> $property */
                    $node['properties'][$name] = self::makeNullable($property);
                }
            }

            $node['required'] = array_keys($node['properties']);

            return $node;
        });
    }

    /**
     * Remove unsupported keywords, keeping their values as a hint in each node's description.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<int, string>  $keywords
     * @return array<string, mixed>
     */
    public static function moveToDescription(array $schema, array $keywords): array
    {
        return self::walk($schema, function (array $node) use ($keywords): array {
            $hints = [];

            foreach ($keywords as $keyword) {
                if (array_key_exists($keyword, $node)) {
                    $hints[] = $keyword.': '.json_encode($node[$keyword]);
                    unset($node[$keyword]);
                }
            }

            if ($hints !== []) {
                $description = isset($node['description']) && is_string($node['description']) ? $node['description'].' ' : '';
                $node['description'] = $description.'('.implode(', ', $hints).')';
            }

            return $node;
        });
    }

    /**
     * Make a name safe for providers that only accept letters, numbers, underscores and dashes (max 64).
     */
    public static function name(string $name): string
    {
        $name = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $name) ?? $name;

        return substr(trim($name, '_') ?: 'schema', 0, 64);
    }

    /**
     * Allow `null` as a value for a schema node.
     *
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    protected static function makeNullable(array $node): array
    {
        $types = isset($node['type']) ? (array) $node['type'] : [];

        if ($types !== [] && ! in_array('null', $types, true)) {
            $types[] = 'null';
            $node['type'] = $types;
        }

        if (isset($node['enum']) && is_array($node['enum']) && ! in_array(null, $node['enum'], true)) {
            $node['enum'][] = null;
        }

        return $node;
    }

    /**
     * Determine whether a schema node describes an object.
     *
     * @param  array<string, mixed>  $node
     */
    protected static function isObject(array $node): bool
    {
        return in_array('object', isset($node['type']) ? (array) $node['type'] : [], true);
    }
}
