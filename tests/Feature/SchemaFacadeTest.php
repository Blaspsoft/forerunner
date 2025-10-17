<?php

declare(strict_types=1);
use Blaspsoft\Forerunner\Facades\Schema;
use Blaspsoft\Forerunner\Schema\Builder;

describe('Schema Facade', function () {
    it('can define a schema using the facade', function () {
        $schema = Schema::define('User', null, function (Builder $builder) {
            $builder->string('name')->required();
            $builder->string('email')->required();
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['type'])->toBe('object')
            ->and($schema['properties'])->toHaveKey('name')
            ->and($schema['properties'])->toHaveKey('email')
            ->and($schema['required'])->toContain('name')
            ->and($schema['required'])->toContain('email');
    });

    it('can define complex schemas using the facade', function () {
        $schema = Schema::define('BlogPost', null, function (Builder $builder) {
            $builder->string('title')->required();
            $builder->string('content')->required();
            $builder->object('author', function (Builder $author) {
                $author->string('name')->required();
                $author->string('email')->required();
            })->required();
            $builder->array('tags')->items('string');
            $builder->enum('status', ['draft', 'published', 'archived'])->default('draft');
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
        $schema = Schema::define('CompleteExample', null, function (Builder $builder) {
            $builder->string('text');
            $builder->int('count');
            $builder->float('price');
            $builder->boolean('isActive');
            $builder->array('items');
            $builder->enum('role', ['admin', 'user']);
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
        $schema = Schema::define('ValidationExample', null, function (Builder $builder) {
            $builder->string('username')
                ->required()
                ->minLength(3)
                ->maxLength(50)
                ->pattern('^[a-zA-Z0-9_]+$');
            $builder->int('age')
                ->min(0)
                ->max(150);
            $builder->array('tags')
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
        $schema = Schema::define('Company', null, function (Builder $builder) {
            $builder->string('name')->required();
            $builder->object('address', function (Builder $address) {
                $address->string('street');
                $address->string('city');
                $address->object('coordinates', function (Builder $coords) {
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
        $schema = Schema::define('UserList', null, function (Builder $builder) {
            $builder->array('users')->items('object', function (Builder $user) {
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
        $schema = Schema::define('DescribedSchema', null, function (Builder $builder) {
            $builder->description('A schema with descriptions');
            $builder->string('name')->description('The user name');
            $builder->string('email')->description('The user email address');
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema)->toHaveKey('description', 'A schema with descriptions')
            ->and($schema['properties']['name'])->toHaveKey('description', 'The user name')
            ->and($schema['properties']['email'])->toHaveKey('description', 'The user email address');
    });

    it('can define schemas with default values', function () {
        $schema = Schema::define('DefaultsExample', null, function (Builder $builder) {
            $builder->boolean('notifications')->default(true);
            $builder->string('theme')->default('light');
            $builder->int('pageSize')->default(10);
        })->toArray();

        expect($schema)->toBeArray()
            ->and($schema['properties']['notifications']['default'])->toBe(true)
            ->and($schema['properties']['theme']['default'])->toBe('light')
            ->and($schema['properties']['pageSize']['default'])->toBe(10);
    });
});
