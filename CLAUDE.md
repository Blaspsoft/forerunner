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

The package uses a fluent builder pattern with three main classes in `src/Schemas/`:

1. **Struct** - Entry point that provides a static `define()` method to create schemas
2. **Builder** - The main schema builder that manages properties and generates JSON schema objects
3. **PropertyBuilder** - Handles individual property definitions with chainable constraint methods

#### Builder Flow
```
Struct::define() → creates Builder → adds PropertyBuilders → toArray() → JSON schema
```

#### Key Architectural Decisions

- **Builder stores PropertyBuilder instances**: The `Builder::$properties` array stores `PropertyBuilder` objects (not raw arrays), which are converted to arrays only during `toArray()` serialization
- **Nested objects use recursive Builders**: The `object()` method creates a nested `Builder` instance and passes it via callback, which is then attached to a `PropertyBuilder` via `setNestedBuilder()`
- **Array items support callbacks**: The `PropertyBuilder::items()` method can accept a callback for object-type arrays, creating nested schemas
- **Required fields tracked separately**: Fields marked as `required()` are tracked in both `PropertyBuilder::$required` flag and `Builder::$required` array during serialization

### Type System

The Builder provides methods for JSON Schema types:
- Primitives: `string()`, `int()`/`integer()`, `float()`/`number()`, `boolean()`/`bool()`
- Complex: `array()`, `object()`, `enum()`

PropertyBuilder provides constraint methods:
- String: `minLength()`, `maxLength()`, `pattern()`
- Numeric: `min()`, `max()`
- Array: `minItems()`, `maxItems()`, `items()`
- General: `required()`, `optional()`, `default()`, `description()`, `enum()`

## PHPStan Configuration

- Level 9 strictness enabled
- All array types must have PHPDoc annotations with generics (e.g., `@var array<string, PropertyBuilder>`)
- The `phpstan-baseline.neon` file tracks accepted issues
- Never use `new static()` - use `new self()` instead

## Laravel Integration

- Service provider: `ForerunnerServiceProvider` (auto-discovered)
- Config file published via: `php artisan vendor:publish --tag="forerunner-config"`
- Supports Laravel 11 & 12
- PHP 8.2+ required
