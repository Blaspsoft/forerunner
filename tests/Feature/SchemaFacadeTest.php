<?php

declare(strict_types=1);
use Blaspsoft\Forerunner\Facades\Schema;
use Blaspsoft\Forerunner\Schema\Property;

describe('Schema Facade', function () {
    it('can define a schema using the facade', function () {
        $schema = Schema::define('User', 'A user schema', function (Property $property) {
            $property->string('name')->required();
            $property->string('email')->required();
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['type'])->toBe('object')
            ->and($schema['properties'])->toHaveKey('name')
            ->and($schema['properties'])->toHaveKey('email')
            ->and($schema['required'])->toContain('name')
            ->and($schema['required'])->toContain('email');
    });

    it('can define complex schemas using the facade', function () {
        $schema = Schema::define('BlogPost', 'A blog post schema', function (Property $property) {
            $property->string('title')->required();
            $property->string('content')->required();
            $property->object('author', function (Property $author) {
                $author->string('name')->required();
                $author->string('email')->required();
            })->required();
            $property->array('tags')->items('string');
            $property->enum('status', ['draft', 'published', 'archived'])->default('draft');
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['type'])->toBe('object')
            ->and($schema['properties'])->toHaveKey('title')
            ->and($schema['properties'])->toHaveKey('author')
            ->and($schema['properties']['author']['type'])->toBe('object')
            ->and($schema['properties']['tags']['type'])->toBe('array')
            ->and($schema['properties']['status']['enum'])->toBe(['draft', 'published', 'archived']);
    });

    it('can define schemas with all field types', function () {
        $schema = Schema::define('CompleteExample', 'Complete example schema', function (Property $property) {
            $property->string('text');
            $property->int('count');
            $property->float('price');
            $property->boolean('isActive');
            $property->array('items');
            $property->enum('role', ['admin', 'user']);
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['properties']['text']['type'])->toBe('string')
            ->and($schema['properties']['count']['type'])->toBe('integer')
            ->and($schema['properties']['price']['type'])->toBe('number')
            ->and($schema['properties']['isActive']['type'])->toBe('boolean')
            ->and($schema['properties']['items']['type'])->toBe('array')
            ->and($schema['properties']['role']['type'])->toBe('string')
            ->and($schema['properties']['role']['enum'])->toBe(['admin', 'user']);
    });

    it('can define schemas with validation constraints', function () {
        $schema = Schema::define('ValidationExample', 'Validation example schema', function (Property $property) {
            $property->string('username')
                ->required()
                ->minLength(3)
                ->maxLength(50)
                ->pattern('^[a-zA-Z0-9_]+$');
            $property->int('age')
                ->min(0)
                ->max(150);
            $property->array('tags')
                ->minItems(1)
                ->maxItems(10);
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['properties']['username'])->toHaveKey('minLength', 3)
            ->and($schema['properties']['username'])->toHaveKey('maxLength', 50)
            ->and($schema['properties']['username'])->toHaveKey('pattern')
            ->and($schema['properties']['age'])->toHaveKey('minimum', 0.0)
            ->and($schema['properties']['age'])->toHaveKey('maximum', 150.0)
            ->and($schema['properties']['tags'])->toHaveKey('minItems', 1)
            ->and($schema['properties']['tags'])->toHaveKey('maxItems', 10);
    });

    it('can define schemas with nested objects', function () {
        $schema = Schema::define('Company', 'Company information', function (Property $property) {
            $property->string('name')->required();
            $property->object('address', function (Property $address) {
                $address->string('street');
                $address->string('city');
                $address->object('coordinates', function (Property $coords) {
                    $coords->float('latitude');
                    $coords->float('longitude');
                });
            });
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['properties']['address']['type'])->toBe('object')
            ->and($schema['properties']['address']['properties'])->toHaveKey('coordinates')
            ->and($schema['properties']['address']['properties']['coordinates']['type'])->toBe('object')
            ->and($schema['properties']['address']['properties']['coordinates']['properties'])->toHaveKey('latitude')
            ->and($schema['properties']['address']['properties']['coordinates']['properties'])->toHaveKey('longitude');
    });

    it('can define schemas with array of objects', function () {
        $schema = Schema::define('UserList', 'A list of users', function (Property $property) {
            $property->array('users')->items('object', function (Property $user) {
                $user->string('name')->required();
                $user->string('email')->required();
                $user->int('age');
            });
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['properties']['users']['type'])->toBe('array')
            ->and($schema['properties']['users']['items']['type'])->toBe('object')
            ->and($schema['properties']['users']['items']['properties'])->toHaveKey('name')
            ->and($schema['properties']['users']['items']['properties'])->toHaveKey('email')
            ->and($schema['properties']['users']['items']['properties'])->toHaveKey('age');
    });

    it('can define schemas with descriptions', function () {
        $schema = Schema::define('DescribedSchema', 'A schema with descriptions', function (Property $property) {
            $property->description('A schema with descriptions');
            $property->string('name')->description('The user name');
            $property->string('email')->description('The user email address');
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema)->toHaveKey('description', 'A schema with descriptions')
            ->and($schema['properties']['name'])->toHaveKey('description', 'The user name')
            ->and($schema['properties']['email'])->toHaveKey('description', 'The user email address');
    });

    it('can define schemas with default values', function () {
        $schema = Schema::define('DefaultsExample', 'Schema with defaults', function (Property $property) {
            $property->boolean('notifications')->default(true);
            $property->string('theme')->default('light');
            $property->int('pageSize')->default(10);
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['properties']['notifications']['default'])->toBe(true)
            ->and($schema['properties']['theme']['default'])->toBe('light')
            ->and($schema['properties']['pageSize']['default'])->toBe(10);
    });
});
