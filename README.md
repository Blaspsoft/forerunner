# Forerunner

A Laravel package that provides an elegant, migration-inspired API for defining JSON schemas that ensure your LLM responses are perfectly structured every time.

## Installation

You can install the package via composer:

```bash
composer require blaspsoft/forerunner
```

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

- [Blaspsoft](https://github.com/blaspsoft)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
