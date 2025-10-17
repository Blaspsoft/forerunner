<?php

declare(strict_types=1);
use Blaspsoft\Forerunner\Schema\Property;
use Blaspsoft\Forerunner\Schema\Struct;

describe('Struct', function () {
    it('can define a simple schema', function () {
        $struct = Struct::define('User', 'A user schema', function (Property $property) {
            $property->string('name');
            $property->string('email');
        });

        expect($struct)->toBeInstanceOf(Struct::class);

        $schema = $struct->toArray();
        expect($schema)->toBeArray()
            ->and($schema['type'])->toBe('object')
            ->and($schema['properties'])->toHaveKey('name')
            ->and($schema['properties'])->toHaveKey('email');
    });

    it('returns a Struct instance from define method', function () {
        $struct = Struct::define('Simple', 'A simple schema', function (Property $property) {
            $property->string('field');
        });

        expect($struct)->toBeInstanceOf(Struct::class);
    });

    it('can define schema with required fields', function () {
        $schema = Struct::define('User', 'A user with required fields', function (Property $property) {
            $property->string('name')->required();
            $property->string('email')->required();
            $property->int('age');
        })->toArray();

        expect($schema['required'])->toContain('name')
            ->and($schema['required'])->toContain('email')
            ->and($schema['required'])->not->toContain('age');
    });

    it('can define schema with nested objects', function () {
        $schema = Struct::define('Post', 'A blog post schema', function (Property $property) {
            $property->string('title')->required();
            $property->object('author', function (Property $nested) {
                $nested->string('name')->required();
                $nested->string('email')->required();
            })->required();
        })->toArray();

        expect($schema['properties'])->toHaveKey('author')
            ->and($schema['properties']['author']['type'])->toBe('object')
            ->and($schema['properties']['author']['properties'])->toHaveKey('name')
            ->and($schema['properties']['author']['properties'])->toHaveKey('email')
            ->and($schema['required'])->toContain('title')
            ->and($schema['required'])->toContain('author');
    });

    it('can define schema with array fields', function () {
        $schema = Struct::define('Product', 'A product schema', function (Property $property) {
            $property->string('name');
            $property->array('tags')->items('string');
        })->toArray();

        expect($schema['properties'])->toHaveKey('tags')
            ->and($schema['properties']['tags']['type'])->toBe('array')
            ->and($schema['properties']['tags']['items'])->toBe(['type' => 'string']);
    });

    it('can define schema with enum fields', function () {
        $schema = Struct::define('User', 'A user schema', function (Property $property) {
            $property->string('name');
            $property->enum('role', ['admin', 'user', 'guest']);
        })->toArray();

        expect($schema['properties'])->toHaveKey('role')
            ->and($schema['properties']['role']['type'])->toBe('string')
            ->and($schema['properties']['role']['enum'])->toBe(['admin', 'user', 'guest']);
    });

    it('can define schema with all primitive types', function () {
        $schema = Struct::define('AllTypes', 'Schema with all types', function (Property $property) {
            $property->string('text');
            $property->int('count');
            $property->float('price');
            $property->boolean('isActive');
            $property->array('items');
        })->toArray();

        expect($schema['properties']['text']['type'])->toBe('string')
            ->and($schema['properties']['count']['type'])->toBe('integer')
            ->and($schema['properties']['price']['type'])->toBe('number')
            ->and($schema['properties']['isActive']['type'])->toBe('boolean')
            ->and($schema['properties']['items']['type'])->toBe('array');
    });

    it('can define schema with property constraints', function () {
        $schema = Struct::define('User', 'A user schema', function (Property $property) {
            $property->string('username')
                ->required()
                ->minLength(3)
                ->maxLength(50);
            $property->int('age')
                ->min(0)
                ->max(150);
        })->toArray();

        expect($schema['properties']['username'])->toHaveKey('minLength', 3)
            ->and($schema['properties']['username'])->toHaveKey('maxLength', 50)
            ->and($schema['properties']['age'])->toHaveKey('minimum', 0.0)
            ->and($schema['properties']['age'])->toHaveKey('maximum', 150.0);
    });

    it('can define schema with description', function () {
        $schema = Struct::define('User', 'A user schema', function (Property $property) {
            $property->description('A user object');
            $property->string('name');
        })->toArray();

        expect($schema)->toHaveKey('description', 'A user object');
    });

    it('can define complex nested schema', function () {
        $schema = Struct::define('BlogPost', 'A blog post with comments', function (Property $property) {
            $property->string('title')->required();
            $property->string('content')->required();
            $property->object('author', function (Property $author) {
                $author->string('name')->required();
                $author->string('email')->required();
                $author->string('bio');
            })->required();
            $property->array('comments')->items('object', function (Property $comment) {
                $comment->string('text')->required();
                $comment->string('authorName')->required();
                $comment->int('timestamp')->required();
            });
            $property->array('tags')->items('string');
            $property->enum('status', ['draft', 'published', 'archived'])->default('draft');
        })->toArray();

        expect($schema['type'])->toBe('object')
            ->and($schema['properties'])->toHaveKey('title')
            ->and($schema['properties'])->toHaveKey('author')
            ->and($schema['properties'])->toHaveKey('comments')
            ->and($schema['properties']['author']['type'])->toBe('object')
            ->and($schema['properties']['comments']['type'])->toBe('array')
            ->and($schema['properties']['comments']['items']['type'])->toBe('object')
            ->and($schema['properties']['status']['default'])->toBe('draft')
            ->and($schema['required'])->toContain('title')
            ->and($schema['required'])->toContain('content')
            ->and($schema['required'])->toContain('author');
    });

    it('can define empty schema', function () {
        $schema = Struct::define('Empty', 'An empty schema', function (Property $property) {
            // No properties
        })->toArray();

        expect($schema['type'])->toBe('object')
            ->and($schema['properties'])->toBeArray()
            ->and($schema['properties'])->toBeEmpty()
            ->and($schema)->not->toHaveKey('required');
    });

    it('can define schema with default values', function () {
        $schema = Struct::define('Settings', 'Application settings', function (Property $property) {
            $property->boolean('notifications')->default(true);
            $property->string('theme')->default('light');
            $property->int('pageSize')->default(10);
        })->toArray();

        expect($schema['properties']['notifications']['default'])->toBe(true)
            ->and($schema['properties']['theme']['default'])->toBe('light')
            ->and($schema['properties']['pageSize']['default'])->toBe(10);
    });

    it('can define schema with pattern validation', function () {
        $schema = Struct::define('Contact', 'Contact information', function (Property $property) {
            $property->string('email')->pattern('^[^@]+@[^@]+\.[^@]+$');
            $property->string('phone')->pattern('^\d{3}-\d{3}-\d{4}$');
        })->toArray();

        expect($schema['properties']['email'])->toHaveKey('pattern')
            ->and($schema['properties']['phone'])->toHaveKey('pattern');
    });

    it('can define schema with deeply nested objects', function () {
        $schema = Struct::define('Company', 'Company information', function (Property $property) {
            $property->string('name');
            $property->object('address', function (Property $address) {
                $address->string('street');
                $address->string('city');
                $address->object('coordinates', function (Property $coords) {
                    $coords->float('latitude');
                    $coords->float('longitude');
                });
            });
        })->toArray();

        expect($schema['properties']['address']['type'])->toBe('object')
            ->and($schema['properties']['address']['properties'])->toHaveKey('coordinates')
            ->and($schema['properties']['address']['properties']['coordinates']['type'])->toBe('object')
            ->and($schema['properties']['address']['properties']['coordinates']['properties'])->toHaveKey('latitude')
            ->and($schema['properties']['address']['properties']['coordinates']['properties'])->toHaveKey('longitude');
    });

    it('can define schema with array constraints', function () {
        $schema = Struct::define('List', 'A list of items', function (Property $property) {
            $property->array('items')
                ->minItems(1)
                ->maxItems(10)
                ->items('string');
        })->toArray();

        expect($schema['properties']['items'])->toHaveKey('minItems', 1)
            ->and($schema['properties']['items'])->toHaveKey('maxItems', 10)
            ->and($schema['properties']['items']['items'])->toBe(['type' => 'string']);
    });

    it('can be serialized with json_encode', function () {
        $schema = Struct::define('User', 'A user schema', function (Property $property) {
            $property->string('name')->required();
        });

        $json = json_encode($schema, JSON_PRETTY_PRINT);
        expect($json)->toBeString()
            ->and($json)->toContain('"type": "object"');
    });
});
