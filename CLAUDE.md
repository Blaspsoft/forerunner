# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Forerunner is a Laravel package (blaspsoft/forerunner) that provides a migration-inspired fluent API for building JSON schemas. The package is designed to generate structured JSON schemas for LLM responses.

## Development Commands

### Testing
```bash
composer test              # Run Pest test suite
composer test-coverage     # Run tests with coverage report
```

### Code Quality
```bash
composer format            # Format code with Laravel Pint
composer analyse           # Run PHPStan static analysis (level 9)
```

### Installation
```bash
composer install           # Install dependencies
```

## Architecture

### Core Schema Builder Pattern

The package uses a fluent builder pattern with three main classes in `src/Schema/`:

1. **Struct** - Entry point with a static `define()` method; renders the schema (`toArray()`, `toSchema()`) and provider payloads
2. **Property** - The schema builder that manages fields and generates JSON schema objects
3. **PropertyBuilder** - Handles individual field definitions with chainable constraint methods

#### Builder Flow
```
Struct::define() → creates Property → adds PropertyBuilders → toArray() → JSON schema
```

#### Key Architectural Decisions

- **Property stores PropertyBuilder instances**: `Property::$properties` holds `PropertyBuilder` objects, converted to arrays only in `toArray()`
- **Nested objects use recursive Property instances**: `object()` and `items('object', fn)` keep a nested `Property` that is rendered lazily, so strict mode can reach every level
- **Strict mode is applied at render time**: `toArray(bool $strict)` passes strictness down to nested schemas; every field is then listed in `required`
- **Struct::toArray() returns OpenAI's wrapper when strict** (kept for backwards compatibility); `toSchema()` always returns the plain schema

### Providers (`src/Providers/`)

- `Provider` enum (`OpenAI`, `Anthropic`, `Gemini`) maps to a `Formatter`
- Each formatter takes `Struct::toSchema()` and rewrites it with `SchemaTransformer` helpers (`requireAll`, `closeObjects`, `moveToDescription`, `withoutMetaKeywords`, `name`)
- Formatters never mutate the struct; they work on the rendered array

### Type System

Property provides methods for JSON Schema types:
- Primitives: `string()`, `int()`/`integer()`, `float()`/`number()`, `boolean()`/`bool()`
- Complex: `array()`, `object()`, `enum()` (type inferred from the values)
- Formats: `email()`, `url()`, `uuid()`, `datetime()`, `date()`, `time()`, `ipv4()`, `ipv6()`, `hostname()`

PropertyBuilder provides constraint methods:
- String: `minLength()`, `maxLength()`, `pattern()`, `format()`
- Numeric: `min()`, `max()`
- Array: `minItems()`, `maxItems()`, `items()`, `uniqueItems()`
- General: `required()`, `optional()`, `nullable()`, `default()`, `description()`, `title()`, `enum()`

## PHPStan Configuration

- Level 9 strictness enabled
- All array types must have PHPDoc annotations with generics (e.g., `@var array<string, PropertyBuilder>`)
- The `phpstan-baseline.neon` file tracks accepted issues
- Never use `new static()` - use `new self()` instead

## Laravel Integration

- Service provider: `ForerunnerServiceProvider` (auto-discovered)
- Config file published via: `php artisan vendor:publish --tag="forerunner-config"`
- Supports Laravel 12 & 13
- PHP 8.2+ required
