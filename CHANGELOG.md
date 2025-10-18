# Changelog

All notable changes to `forerunner` will be documented in this file.

## [Unreleased]

## 0.2.0 - 2025-01-18

### Breaking Changes

- **Renamed `Builder` class to `Property`** for better API semantics
  - `Blaspsoft\Forerunner\Schemas\Builder` → `Blaspsoft\Forerunner\Schema\Property`
  - Better reflects the class's purpose of building properties within a schema
- **Renamed namespace from `Schemas` to `Schema`** for cleaner imports
  - `Blaspsoft\Forerunner\Schemas\*` → `Blaspsoft\Forerunner\Schema\*`
- **Made `description` parameter required** in `Struct::define()`
  - Old: `Struct::define($name, $callback)`
  - New: `Struct::define($name, $description, $callback)`
  - Aligns with LLM provider requirements (Anthropic Claude requires description)
- **Removed `toJson()` method** from `Struct` class
  - Use `json_encode($struct)` instead (implements `JsonSerializable`)
- **Removed config file** (`config/forerunner.php`)
  - Package now uses sensible defaults
  - Simplifies setup and reduces configuration overhead

### Added

- **OpenAI Structured Outputs format support**
  - `strict()` mode now wraps schema in `{name, strict: true, schema: {...}}` format
  - Perfect for OpenAI's Structured Outputs API
- **Inline property descriptions**
  - Properties can now have descriptions as second parameter
  - Example: `$property->string('name', 'The user\'s full name')`
- **Cached schema generation** in `Struct::toArray()`
  - Improves performance by caching the generated schema array
- **Strict type declarations** (`declare(strict_types=1)`) added to all PHP files

### Changed

- **Updated callback parameter** from `$builder` to `$property` throughout codebase
  - More intuitive and consistent with the renamed class
- **Improved `strict()` behavior**
  - Now automatically marks fields added *after* `strict()` is called as required
  - `toArray()` made non-mutating to prevent side effects
- **Parameter order updated** for better ergonomics
  - Description moved to second parameter in `Struct::define()`

### Removed

- Removed dead code: `$this->required` property and `markRequired()` method from `Property` class
  - Required fields now derived directly from `PropertyBuilder::isRequired()` states

### Fixed

- PHPStan configuration no longer references deleted config directory
- Laravel Pint formatting issues resolved
- README examples updated to call `strict()` after defining all fields (best practice)
- Facade PHPDoc updated to include required description parameter

### Documentation

- Comprehensive README updates with all new API patterns
- Added guidance on calling `strict()` after field definitions
- Fixed examples to use new `Property` class and variable naming
- Added CodeRabbit badge for automated code review integration

### Technical Details

- 118 tests passing with 402 assertions
- PHPStan Level 9 compliance maintained
- All CI checks passing (tests, PHPStan, Laravel Pint)
- Full backward compatibility for PHP 8.2+, Laravel 11 & 12

## 0.1.4 - 2024-10-17

### Changed
- **Improved stub template**: Generated struct classes now include `$builder->strict()` by default
  - New structs are OpenAI-ready out of the box
  - Removed `->required()` from example field (handled by `strict()`)

### Removed
- **Removed `named()` method** from stub template
  - Confusing method that wasn't commonly needed
  - Simplifies generated struct classes

### Technical Details
- 120 tests passing with 403 assertions
- Cleaner, more focused generated code

## 0.1.3 - 2024-10-17

### Changed
- **Enhanced `strict()` method**: Now marks all fields as required in addition to setting `additionalProperties: false`
  - Perfect for OpenAI Structured Outputs which requires all properties in the `required` array
  - Simplifies schema creation for strict LLM APIs
  - One method call handles both requirements

### Added
- Test for new `strict()` behavior
- README documentation for strict mode and OpenAI compatibility

### Technical Details
- 120 tests passing with 403 assertions
- PHPStan Level 9 compliance maintained

## 0.1.2 - 2024-10-17

### Changed
- **IMPORTANT**: `additionalProperties` now defaults to `false` and is always included in schema output
  - Required by LLM APIs (OpenAI, Anthropic) for structured output
  - Provides stricter validation by default
  - Can still be enabled with `additionalProperties(true)` if needed
- Stub file now uses `$builder` variable instead of `$table` for better clarity

### Technical Details
- 119 tests passing with 400 assertions
- PHPStan Level 9 compliance maintained
- All schemas now explicitly include `"additionalProperties": false`

## 0.1.1 - 2024-10-17

### Added
- Method chaining support: `Struct::define()->toJson()` now works
- `toArray()` method on Struct with internal caching for performance
- `toJson()` method on Struct for direct JSON conversion
- `ArrayAccess` implementation for backward-compatible array-like access
- `JsonSerializable` implementation for `json_encode()` support

### Changed
- **BREAKING (minor)**: `Struct::define()` now returns a `Struct` object instead of an array
  - Maintains backward compatibility through `ArrayAccess` - existing code using `$schema['type']` still works
  - Enables new method chaining syntax: `Struct::define()->toJson()`
- Service provider updated to proxy static calls to Struct via anonymous class
- README documentation significantly expanded with method chaining examples
- Generated struct classes now recommended to return `Struct` type hint

### Technical Details
- 119 tests passing with 399 assertions
- All existing tests updated to work with new Struct object API
- Full backward compatibility maintained through ArrayAccess interface

## 0.1.0 - 2024-10-17 (Initial Pre-release)

### Added
- `additionalProperties` support for controlling extra properties in objects
- `strict()` helper method to disallow additional properties
- `format` validation for strings (email, url, uuid, date-time, etc.)
- `uniqueItems` constraint for arrays
- `nullable` support for optional null values (generates `["type", "null"]` syntax)
- `title` field support for both schemas and individual fields
- Helper methods for common patterns:
  - `email()` - Email field with format validation
  - `url()` - URL field with uri format
  - `uuid()` - UUID field with format validation
  - `datetime()` - ISO 8601 date-time field
  - `date()` - Date-only field
  - `time()` - Time-only field
  - `ipv4()` - IPv4 address field
  - `ipv6()` - IPv6 address field
  - `hostname()` - Hostname field
- JSON Schema draft version declaration support via `schemaVersion()`
- CONTRIBUTING.md with comprehensive contribution guidelines
- SECURITY.md with security vulnerability reporting process
- .editorconfig for consistent coding styles across editors
- Comprehensive test suite for advanced features (31 new tests)

### Changed
- Improved error handling in `Builder::toJson()` to throw `JsonException` on errors
- Enhanced .gitignore with better library best practices
- Updated README.md with extensive documentation for all new features

### Fixed
- JSON encoding now uses `JSON_THROW_ON_ERROR` flag for proper error handling
- Nullable objects now correctly preserve type array when merged with nested builder

### Technical Details
- 116 tests passing with 377 assertions
- PHPStan Level 9 compliance maintained
- Full compatibility with PHP 8.2, 8.3, 8.4
- Full compatibility with Laravel 11 & 12

---

**Note**: This is an initial pre-release version. Expect frequent updates and potential API changes as we gather feedback and add features towards the 1.0.0 stable release.
