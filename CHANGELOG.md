# Changelog

All notable changes to `forerunner` will be documented in this file.

## [Unreleased]

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
