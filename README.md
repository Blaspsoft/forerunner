<p align="center">
    <img src="assets/icon.png" alt="Forerunner Logo" width="150">
</p>

<p align="center">
    <a href="https://github.com/blaspsoft/forerunner/actions?query=workflow%3Amain+branch%3Amain"><img src="https://img.shields.io/github/actions/workflow/status/blaspsoft/forerunner/main.yml?branch=main&label=tests&style=flat-square" alt="Tests"></a>
    <a href="https://packagist.org/packages/blaspsoft/forerunner"><img src="https://img.shields.io/packagist/dt/blaspsoft/forerunner.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/blaspsoft/forerunner"><img src="https://img.shields.io/packagist/v/blaspsoft/forerunner.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/blaspsoft/forerunner"><img src="https://img.shields.io/packagist/l/blaspsoft/forerunner.svg?style=flat-square" alt="License"></a>
    <a href="https://coderabbit.ai"><img src="https://img.shields.io/coderabbit/prs/github/Blaspsoft/forerunner?utm_source=oss&utm_medium=github&utm_campaign=Blaspsoft%2Fforerunner&labelColor=171717&color=FF570A&label=CodeRabbit+Reviews" alt="CodeRabbit Pull Request Reviews"></a>
</p>

# Forerunner - Build structured LLM outputs the Laravel way

<p>
A Laravel package that provides an elegant, migration-inspired API for defining JSON schemas that ensure your LLM responses are perfectly structured every time.
</p>

## Installation

You can install the package via composer:

```bash
composer require blaspsoft/forerunner:^0.1
```

> **Note**: This is a pre-release version (0.x). The API may change as we gather feedback and iterate towards 1.0.0.

The package will automatically register its service provider.

## Quick Start

### Using the Artisan Command

Generate a new structure class:

```bash
php artisan make:struct UserProfile
```

This creates a structure class at `app/Structures/UserProfile.php`:

```php
<?php

namespace App\Structures;

use Blaspsoft\Forerunner\Schema\Struct;
use Blaspsoft\Forerunner\Schema\Property;

class UserProfile
{
    public static function schema(): array
    {
        return Struct::define('user_profile', 'Description of user_profile', function (Property $property) {
            $property->string('example_field');
            // Add your fields here

            $property->strict(); // All fields required + no additional properties
        })->toArray();
    }
}
```

### Basic Usage

Define a schema using the `Struct` class or `Schema` facade:

```php
use Blaspsoft\Forerunner\Schema\Struct;
use Blaspsoft\Forerunner\Schema\Property;

$schema = Struct::define('User', 'A user schema', function (Property $property) {
    $property->string('name', 'The user\'s full name')->required();
    $property->string('email', 'The user\'s email address')->required();
    $property->int('age', 'The user\'s age')->min(0)->max(150);
    $property->boolean('is_active', 'Is the user account active?')->default(true);
})->toArray();
```

Or using the facade for a cleaner syntax:

```php
use Blaspsoft\Forerunner\Facades\Schema;
use Blaspsoft\Forerunner\Schema\Property;

$schema = Schema::define('User', 'A user schema', function (Property $property) {
    $property->string('name', 'The user\'s full name')->required();
    $property->string('email', 'The user\'s email address')->required();
})->toArray();
```

## Available Field Types

### String Fields

```php
$property->string('username', 'The username')
    ->minLength(3)
    ->maxLength(50)
    ->pattern('^[a-zA-Z0-9_]+$')
    ->required();
```

### Integer Fields

```php
$property->int('age', 'User age')
    ->min(0)
    ->max(150)
    ->default(18);

// Alias
$property->integer('count');
```

### Float/Number Fields

```php
$property->float('price', 'Product price')
    ->min(0.0)
    ->max(9999.99);

// Alias
$property->number('rating')->min(0)->max(5);
```

### Boolean Fields

```php
$property->boolean('is_active', 'Account status')
    ->default(true);

// Alias
$property->bool('verified');
```

### Array Fields

```php
// Simple array
$property->array('tags', 'User tags')
    ->items('string')
    ->minItems(1)
    ->maxItems(10);

// Array of objects
$property->array('addresses')->items('object', function (Property $item) {
    $item->string('street')->required();
    $item->string('city')->required();
    $item->string('zip')->required();
});
```

### Enum Fields

```php
$property->enum('role', ['admin', 'user', 'guest'], 'User role')
    ->default('user');

$property->enum('status', ['draft', 'published', 'archived']);
```

### Object Fields

```php
$property->object('address', function (Property $nested) {
    $nested->string('street', 'Street address')->required();
    $nested->string('city', 'City name')->required();
    $nested->string('zip', 'ZIP code')->required();
    $nested->object('coordinates', function (Property $coords) {
        $coords->float('latitude')->required();
        $coords->float('longitude')->required();
    });
}, 'User address');
```

## Field Constraints

### String Constraints

```php
$property->string('username')
    ->minLength(3)              // Minimum length
    ->maxLength(50)             // Maximum length
    ->pattern('^[a-zA-Z0-9]+$') // Regex pattern
    ->required();               // Mark as required
```

### Numeric Constraints

```php
$property->int('age')
    ->min(0)          // Minimum value
    ->max(150)        // Maximum value
    ->default(18);    // Default value
```

### Array Constraints

```php
$property->array('tags')
    ->items('string')  // Type of array items
    ->minItems(1)      // Minimum array length
    ->maxItems(10);    // Maximum array length
```

### General Constraints

```php
$property->string('field')
    ->required()                    // Mark as required
    ->optional()                    // Mark as optional (default)
    ->default('value')              // Set default value
    ->description('Field description'); // Add description
```

## Advanced Features

### Helper Methods for Common Formats

Forerunner provides convenient helper methods for commonly used field formats:

```php
// Email field with automatic format validation
$property->email('email')->required();

// URL field
$property->url('website');

// UUID field
$property->uuid('id')->required();

// Date-time field (ISO 8601)
$property->datetime('created_at');

// Date field
$property->date('birth_date');

// Time field
$property->time('start_time');

// IPv4 address
$property->ipv4('ip_address');

// IPv6 address
$property->ipv6('ipv6_address');

// Hostname
$property->hostname('server_name');
```

### String Format Validation

You can also set custom formats on string fields:

```php
$property->string('email')->format('email');
$property->string('website')->format('uri');
$property->string('id')->format('uuid');
```

Supported formats: `email`, `uri`, `url`, `uuid`, `date`, `date-time`, `time`, `ipv4`, `ipv6`, `hostname`, and more.

### Nullable Fields

Mark fields as nullable to allow both the specified type and null:

```php
$property->string('middle_name')->nullable();
// Generates: {"type": ["string", "null"]}

$property->object('address', function (Property $nested) {
    $nested->string('street')->required();
    $nested->string('city')->required();
})->nullable();
// Generates: {"type": ["object", "null"], "properties": {...}}
```

### Unique Array Items

Ensure array items are unique:

```php
$property->array('tags')
    ->items('string')
    ->uniqueItems();
```

### Additional Properties Control

Control whether objects can have properties not defined in the schema:

```php
// Allow additional properties
$property->additionalProperties(true);

// Disallow additional properties
$property->additionalProperties(false); // This is the default

// Or use the convenient strict() helper
$property->strict(); // Disallows additional properties AND marks all fields as required
```

#### Strict Mode for LLM APIs

The `strict()` method is particularly useful for LLM APIs like **OpenAI Structured Outputs** which require:
1. `additionalProperties: false`
2. All properties in the `required` array

**Important:** Call `strict()` after defining all your fields to ensure all of them are marked as required.

```php
// Perfect for OpenAI Structured Outputs
$schema = Struct::define('User', 'A user schema', function (Property $property) {
    $property->string('fullname');
    $property->email('email');
    $property->int('age')->min(0)->max(120);
    $property->string('location');

    // Call strict() at the end to mark all fields as required
    $property->strict(); // Makes all fields required + disallows extra properties
})->toArray();
```

This generates:
```json
{
    "type": "object",
    "properties": {...},
    "required": ["fullname", "email", "age", "location"],
    "additionalProperties": false
}
```

> **Note**: By default, `additionalProperties` is already set to `false`. Use `strict()` when you also need all fields to be required (like for OpenAI). Call it after defining fields to ensure all are marked as required.

### Schema Metadata

Add metadata to your schemas:

```php
$property->title('User Schema');
$property->description('Schema for user data validation');
$property->schemaVersion('https://json-schema.org/draft/2020-12/schema');
```

You can also add titles to individual fields:

```php
$property->string('email')
    ->title('Email Address')
    ->description('User\'s primary email address')
    ->format('email')
    ->required();
```

### Complete Advanced Example

```php
use Blaspsoft\Forerunner\Schema\Struct;
use Blaspsoft\Forerunner\Schema\Property;

$schema = Struct::define('AdvancedUser', 'Comprehensive user data structure', function (Property $property) {
    // Schema metadata
    $property->schemaVersion();
    $property->title('Advanced User Schema');

    // Helper methods
    $property->uuid('id')->required();
    $property->email('email')->required();
    $property->url('website')->nullable();
    $property->datetime('created_at')->required();

    // Nullable nested object
    $property->object('profile', function (Property $profile) {
        $profile->string('bio')->maxLength(500);
        $profile->string('avatar_url')->format('uri');
    })->nullable();

    // Array with unique items
    $property->array('tags')
        ->items('string')
        ->uniqueItems()
        ->minItems(1)
        ->maxItems(10);

    // Advanced field configuration
    $property->string('username')
        ->title('Username')
        ->description('Unique username for the account')
        ->minLength(3)
        ->maxLength(30)
        ->pattern('^[a-zA-Z0-9_]+$')
        ->required();

    // Call strict() after all fields to mark them all as required
    $property->strict(); // Disallow extras + mark all defined fields as required
})->toArray();
```

This generates:

```json
{
    "$schema": "https://json-schema.org/draft/2020-12/schema",
    "type": "object",
    "title": "Advanced User Schema",
    "description": "Comprehensive user data structure",
    "properties": {
        "id": {
            "type": "string",
            "format": "uuid"
        },
        "email": {
            "type": "string",
            "format": "email"
        },
        "website": {
            "type": ["string", "null"],
            "format": "uri"
        },
        "created_at": {
            "type": "string",
            "format": "date-time"
        },
        "profile": {
            "type": ["object", "null"],
            "properties": {
                "bio": {
                    "type": "string",
                    "maxLength": 500
                },
                "avatar_url": {
                    "type": "string",
                    "format": "uri"
                }
            }
        },
        "tags": {
            "type": "array",
            "items": {
                "type": "string"
            },
            "uniqueItems": true,
            "minItems": 1,
            "maxItems": 10
        },
        "username": {
            "type": "string",
            "title": "Username",
            "description": "Unique username for the account",
            "minLength": 3,
            "maxLength": 30,
            "pattern": "^[a-zA-Z0-9_]+$"
        }
    },
    "required": ["id", "email", "created_at", "username"],
    "additionalProperties": false
}
```

## Complex Examples

### User Profile with Nested Objects

```php
use Blaspsoft\Forerunner\Schema\Struct;
use Blaspsoft\Forerunner\Schema\Property;

$schema = Struct::define('UserProfile', 'A complete user profile schema', function (Property $property) {
    $property->string('name', 'The user\'s full name')
        ->minLength(1)
        ->maxLength(100)
        ->required();

    $property->string('email', 'The user\'s email')
        ->pattern('^[^\s@]+@[^\s@]+\.[^\s@]+$')
        ->required();

    $property->int('age', 'The user\'s age')
        ->min(0)
        ->max(150);

    $property->boolean('is_active', 'Is the account active?')
        ->default(true);

    $property->array('tags', 'User tags')
        ->items('string')
        ->minItems(0)
        ->maxItems(10);

    $property->object('address', function (Property $address) {
        $address->string('street', 'Street name')->required();
        $address->string('city', 'City name')->required();
        $address->string('state', 'State/Province')->required();
        $address->string('zip', 'ZIP/Postal code')->required();
        $address->string('country', 'Country code')->required();
    }, 'User\'s address');

    $property->enum('role', ['admin', 'moderator', 'user'], 'User role')
        ->default('user');
})->toArray();
```

### Blog Post with Comments

```php
$schema = Struct::define('BlogPost', 'A blog post with author and comments', function (Property $property) {
    $property->string('title')->required();
    $property->string('content')->required();
    $property->string('slug')->pattern('^[a-z0-9-]+$')->required();

    $property->object('author', function (Property $author) {
        $author->string('name')->required();
        $author->string('email')->required();
        $author->string('bio');
    })->required();

    $property->array('comments')->items('object', function (Property $comment) {
        $comment->string('text')->required();
        $comment->string('author_name')->required();
        $comment->string('author_email')->required();
        $comment->int('timestamp')->required();
    });

    $property->array('tags')->items('string')->minItems(1);

    $property->enum('status', ['draft', 'published', 'archived'])
        ->default('draft');

    $property->int('views')->min(0)->default(0);
})->toArray();
```

## Working with Generated Schemas

The `Struct::define()` method returns a `Struct` object that can be converted to an array or JSON.

### Convert to Array

```php
$struct = Struct::define('User', 'A user schema', function (Property $property) {
    $property->string('name')->required();
    $property->string('email')->required();
});

// Convert to array
$array = $struct->toArray();
```

### JSON Serialization

The `Struct` object implements `JsonSerializable`, so you can use it directly with `json_encode()`:

```php
$struct = Struct::define('User', 'A user schema', function (Property $property) {
    $property->string('name')->required();
});

// Automatic JSON serialization
$json = json_encode($struct, JSON_PRETTY_PRINT);
```

### Using Structure Classes

When using the `make:struct` command, you can create reusable schema classes:

```php
// In your structure class (generated by make:struct)
class UserProfile
{
    public static function schema(): array
    {
        return Struct::define('user_profile', 'A user profile schema', function (Property $property) {
            $property->string('name')->required();
            $property->string('email')->required();
        })->toArray();
    }
}

// Using the structure
$array = UserProfile::schema();  // Returns array

// For JSON, use json_encode
$json = json_encode(UserProfile::schema(), JSON_PRETTY_PRINT);
```

## Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

## Code Quality

Run PHPStan analysis:

```bash
composer analyse
```

Format code with Laravel Pint:

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Blaspsoft](https://github.com/blaspsoft)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
