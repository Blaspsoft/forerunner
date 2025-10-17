<p align="center">
    <img src="assets/icon.png" alt="Forerunner Logo" width="150">
</p>

<p align="center">
    <a href="https://github.com/blaspsoft/forerunner/actions?query=workflow%3Amain+branch%3Amain"><img src="https://img.shields.io/github/actions/workflow/status/blaspsoft/forerunner/main.yml?branch=main&label=tests&style=flat-square" alt="Tests"></a>
    <a href="https://packagist.org/packages/blaspsoft/forerunner"><img src="https://img.shields.io/packagist/dt/blaspsoft/forerunner.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/blaspsoft/forerunner"><img src="https://img.shields.io/packagist/v/blaspsoft/forerunner.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/blaspsoft/forerunner"><img src="https://img.shields.io/packagist/l/blaspsoft/forerunner.svg?style=flat-square" alt="License"></a>
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

use Blaspsoft\Forerunner\Schemas\Struct;
use Blaspsoft\Forerunner\Schemas\Builder;

class UserProfile
{
    public static function schema(): array
    {
        return Struct::define('user_profile', function (Builder $table) {
            $table->string('example_field')->required();
            // Add your fields here
        });
    }
}
```

### Basic Usage

Define a schema using the `Struct` class or `Schema` facade:

```php
use Blaspsoft\Forerunner\Schemas\Struct;
use Blaspsoft\Forerunner\Schemas\Builder;

$schema = Struct::define('User', function (Builder $builder) {
    $builder->string('name', 'The user\'s full name')->required();
    $builder->string('email', 'The user\'s email address')->required();
    $builder->int('age', 'The user\'s age')->min(0)->max(150);
    $builder->boolean('is_active', 'Is the user account active?')->default(true);
});
```

Or using the facade:

```php
use Blaspsoft\Forerunner\Facades\Schema;
use Blaspsoft\Forerunner\Schemas\Builder;

$schema = Schema::define('User', function (Builder $builder) {
    $builder->string('name')->required();
    $builder->string('email')->required();
});
```

## Available Field Types

### String Fields

```php
$builder->string('username', 'The username')
    ->minLength(3)
    ->maxLength(50)
    ->pattern('^[a-zA-Z0-9_]+$')
    ->required();
```

### Integer Fields

```php
$builder->int('age', 'User age')
    ->min(0)
    ->max(150)
    ->default(18);

// Alias
$builder->integer('count');
```

### Float/Number Fields

```php
$builder->float('price', 'Product price')
    ->min(0.0)
    ->max(9999.99);

// Alias
$builder->number('rating')->min(0)->max(5);
```

### Boolean Fields

```php
$builder->boolean('is_active', 'Account status')
    ->default(true);

// Alias
$builder->bool('verified');
```

### Array Fields

```php
// Simple array
$builder->array('tags', 'User tags')
    ->items('string')
    ->minItems(1)
    ->maxItems(10);

// Array of objects
$builder->array('addresses')->items('object', function (Builder $item) {
    $item->string('street')->required();
    $item->string('city')->required();
    $item->string('zip')->required();
});
```

### Enum Fields

```php
$builder->enum('role', ['admin', 'user', 'guest'], 'User role')
    ->default('user');

$builder->enum('status', ['draft', 'published', 'archived']);
```

### Object Fields

```php
$builder->object('address', function (Builder $nested) {
    $nested->string('street', 'Street address')->required();
    $nested->string('city', 'City name')->required();
    $nested->string('zip', 'ZIP code')->required();
    $nested->object('coordinates', function (Builder $coords) {
        $coords->float('latitude')->required();
        $coords->float('longitude')->required();
    });
}, 'User address');
```

## Field Constraints

### String Constraints

```php
$builder->string('username')
    ->minLength(3)              // Minimum length
    ->maxLength(50)             // Maximum length
    ->pattern('^[a-zA-Z0-9]+$') // Regex pattern
    ->required();               // Mark as required
```

### Numeric Constraints

```php
$builder->int('age')
    ->min(0)          // Minimum value
    ->max(150)        // Maximum value
    ->default(18);    // Default value
```

### Array Constraints

```php
$builder->array('tags')
    ->items('string')  // Type of array items
    ->minItems(1)      // Minimum array length
    ->maxItems(10);    // Maximum array length
```

### General Constraints

```php
$builder->string('field')
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
$builder->email('email')->required();

// URL field
$builder->url('website');

// UUID field
$builder->uuid('id')->required();

// Date-time field (ISO 8601)
$builder->datetime('created_at');

// Date field
$builder->date('birth_date');

// Time field
$builder->time('start_time');

// IPv4 address
$builder->ipv4('ip_address');

// IPv6 address
$builder->ipv6('ipv6_address');

// Hostname
$builder->hostname('server_name');
```

### String Format Validation

You can also set custom formats on string fields:

```php
$builder->string('email')->format('email');
$builder->string('website')->format('uri');
$builder->string('id')->format('uuid');
```

Supported formats: `email`, `uri`, `url`, `uuid`, `date`, `date-time`, `time`, `ipv4`, `ipv6`, `hostname`, and more.

### Nullable Fields

Mark fields as nullable to allow both the specified type and null:

```php
$builder->string('middle_name')->nullable();
// Generates: {"type": ["string", "null"]}

$builder->object('address', function (Builder $nested) {
    $nested->string('street')->required();
    $nested->string('city')->required();
})->nullable();
// Generates: {"type": ["object", "null"], "properties": {...}}
```

### Unique Array Items

Ensure array items are unique:

```php
$builder->array('tags')
    ->items('string')
    ->uniqueItems();
```

### Additional Properties Control

Control whether objects can have properties not defined in the schema:

```php
// Allow additional properties
$builder->additionalProperties(true);

// Disallow additional properties (strict mode)
$builder->additionalProperties(false);

// Or use the convenient strict() helper
$builder->strict();
```

Example:

```php
$schema = Struct::define('StrictUser', function (Builder $builder) {
    $builder->string('name')->required();
    $builder->string('email')->required();
    $builder->strict(); // No other properties allowed
});
```

### Schema Metadata

Add metadata to your schemas:

```php
$builder->title('User Schema');
$builder->description('Schema for user data validation');
$builder->schemaVersion('https://json-schema.org/draft/2020-12/schema');
```

You can also add titles to individual fields:

```php
$builder->string('email')
    ->title('Email Address')
    ->description('User\'s primary email address')
    ->format('email')
    ->required();
```

### Complete Advanced Example

```php
use Blaspsoft\Forerunner\Schemas\Struct;
use Blaspsoft\Forerunner\Schemas\Builder;

$schema = Struct::define('AdvancedUser', function (Builder $builder) {
    // Schema metadata
    $builder->schemaVersion();
    $builder->title('Advanced User Schema');
    $builder->description('Comprehensive user data structure');
    $builder->strict(); // Disallow additional properties

    // Helper methods
    $builder->uuid('id')->required();
    $builder->email('email')->required();
    $builder->url('website')->nullable();
    $builder->datetime('created_at')->required();

    // Nullable nested object
    $builder->object('profile', function (Builder $profile) {
        $profile->string('bio')->maxLength(500);
        $profile->string('avatar_url')->format('uri');
    })->nullable();

    // Array with unique items
    $builder->array('tags')
        ->items('string')
        ->uniqueItems()
        ->minItems(1)
        ->maxItems(10);

    // Advanced field configuration
    $builder->string('username')
        ->title('Username')
        ->description('Unique username for the account')
        ->minLength(3)
        ->maxLength(30)
        ->pattern('^[a-zA-Z0-9_]+$')
        ->required();
});
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
use Blaspsoft\Forerunner\Schemas\Struct;
use Blaspsoft\Forerunner\Schemas\Builder;

$schema = Struct::define('UserProfile', function (Builder $builder) {
    $builder->string('name', 'The user\'s full name')
        ->minLength(1)
        ->maxLength(100)
        ->required();

    $builder->string('email', 'The user\'s email')
        ->pattern('^[^\s@]+@[^\s@]+\.[^\s@]+$')
        ->required();

    $builder->int('age', 'The user\'s age')
        ->min(0)
        ->max(150);

    $builder->boolean('is_active', 'Is the account active?')
        ->default(true);

    $builder->array('tags', 'User tags')
        ->items('string')
        ->minItems(0)
        ->maxItems(10);

    $builder->object('address', function (Builder $address) {
        $address->string('street', 'Street name')->required();
        $address->string('city', 'City name')->required();
        $address->string('state', 'State/Province')->required();
        $address->string('zip', 'ZIP/Postal code')->required();
        $address->string('country', 'Country code')->required();
    }, 'User\'s address');

    $builder->enum('role', ['admin', 'moderator', 'user'], 'User role')
        ->default('user');
});
```

### Blog Post with Comments

```php
$schema = Struct::define('BlogPost', function (Builder $builder) {
    $builder->string('title')->required();
    $builder->string('content')->required();
    $builder->string('slug')->pattern('^[a-z0-9-]+$')->required();

    $builder->object('author', function (Builder $author) {
        $author->string('name')->required();
        $author->string('email')->required();
        $author->string('bio');
    })->required();

    $builder->array('comments')->items('object', function (Builder $comment) {
        $comment->string('text')->required();
        $comment->string('author_name')->required();
        $comment->string('author_email')->required();
        $comment->int('timestamp')->required();
    });

    $builder->array('tags')->items('string')->minItems(1);

    $builder->enum('status', ['draft', 'published', 'archived'])
        ->default('draft');

    $builder->int('views')->min(0)->default(0);
});
```

## Working with Generated Schemas

### Get Schema as Array

```php
$schema = Struct::define('User', function (Builder $builder) {
    $builder->string('name')->required();
});

// $schema is now an array containing the JSON Schema
```

### Get Schema as JSON String

```php
use Blaspsoft\Forerunner\Schemas\Builder;

$builder = new Builder('User');
$builder->string('name')->required();

$jsonSchema = $builder->toJson();
// Returns formatted JSON string
```

### Using Structure Classes

```php
// In your structure class
class UserProfile
{
    public static function schema(): array
    {
        return Struct::define('user_profile', function (Builder $table) {
            $table->string('name')->required();
            $table->string('email')->required();
        });
    }

    public static function toJson(): string
    {
        return json_encode(static::schema(), JSON_PRETTY_PRINT);
    }
}

// Using the structure
$schema = UserProfile::schema();
$json = UserProfile::toJson();
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="forerunner-config"
```

This will create `config/forerunner.php` where you can customize package settings.

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
