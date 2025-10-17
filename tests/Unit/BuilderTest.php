<?php

declare(strict_types=1);
use Blaspsoft\Forerunner\Schema\Property;
use Blaspsoft\Forerunner\Schema\PropertyBuilder;

describe('Builder', function () {
    it('can be instantiated with a name', function () {
        $property = new Property('TestSchema');

        expect($property)->toBeInstanceOf(Property::class);
    });

    it('can add a string property', function () {
        $property = new Property('TestSchema');
        $property = $property->string('username');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add an integer property', function () {
        $property = new Property('TestSchema');
        $property = $property->int('age');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add an integer property using integer alias', function () {
        $property = new Property('TestSchema');
        $property = $property->integer('count');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a float property', function () {
        $property = new Property('TestSchema');
        $property = $property->float('price');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a number property using number alias', function () {
        $property = new Property('TestSchema');
        $property = $property->number('amount');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a boolean property', function () {
        $property = new Property('TestSchema');
        $property = $property->boolean('isActive');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a boolean property using bool alias', function () {
        $property = new Property('TestSchema');
        $property = $property->bool('enabled');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add an array property', function () {
        $property = new Property('TestSchema');
        $property = $property->array('tags');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add an enum property', function () {
        $property = new Property('TestSchema');
        $property = $property->enum('status', ['active', 'inactive', 'pending']);

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a nested object property', function () {
        $property = new Property('TestSchema');
        $property = $property->object('user', function (Property $nested) {
            $nested->string('name');
            $nested->string('email');
        });

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can set a description on the schema', function () {
        $property = new Property('TestSchema');
        $result = $property->description('Test schema description');

        expect($result)->toBe($property);
    });

    it('generates correct JSON schema array for simple properties', function () {
        $property = new Property('TestSchema');
        $property->string('name');
        $property->int('age');

        $schema = $property->toArray();

        expect($schema)->toHaveKey('type', 'object')
            ->and($schema)->toHaveKey('properties')
            ->and($schema['properties'])->toHaveKey('name')
            ->and($schema['properties'])->toHaveKey('age')
            ->and($schema['properties']['name']['type'])->toBe('string')
            ->and($schema['properties']['age']['type'])->toBe('integer');
    });

    it('includes description in schema when set', function () {
        $property = new Property('TestSchema');
        $property->description('User schema');
        $property->string('name');

        $schema = $property->toArray();

        expect($schema)->toHaveKey('description', 'User schema');
    });

    it('includes required fields in schema', function () {
        $property = new Property('TestSchema');
        $property->string('name')->required();
        $property->string('email')->required();
        $property->int('age');

        $schema = $property->toArray();

        expect($schema)->toHaveKey('required')
            ->and($schema['required'])->toContain('name')
            ->and($schema['required'])->toContain('email')
            ->and($schema['required'])->not->toContain('age');
    });

    it('does not include required key when no fields are required', function () {
        $property = new Property('TestSchema');
        $property->string('name');
        $property->int('age');

        $schema = $property->toArray();

        expect($schema)->not->toHaveKey('required');
    });

    it('generates correct schema for nested objects', function () {
        $property = new Property('TestSchema');
        $property->object('user', function (Property $nested) {
            $nested->string('name');
            $nested->string('email');
        });

        $schema = $property->toArray();

        expect($schema['properties']['user']['type'])->toBe('object')
            ->and($schema['properties']['user']['properties'])->toHaveKey('name')
            ->and($schema['properties']['user']['properties'])->toHaveKey('email');
    });

    it('generates correct schema for enum fields', function () {
        $property = new Property('TestSchema');
        $property->enum('status', ['active', 'inactive']);

        $schema = $property->toArray();

        expect($schema['properties']['status']['type'])->toBe('string')
            ->and($schema['properties']['status']['enum'])->toBe(['active', 'inactive']);
    });

    it('can convert schema to JSON string', function () {
        $property = new Property('TestSchema');
        $property->string('name')->required();

        $json = $property->toJson();

        expect($json)->toBeString()
            ->and($json)->toContain('"type": "object"')
            ->and($json)->toContain('"name"')
            ->and($json)->toContain('"required"');
    });

    it('supports method chaining for properties', function () {
        $property = new Property('TestSchema');
        $property->string('username')
            ->required()
            ->minLength(3)
            ->maxLength(50);

        $schema = $property->toArray();

        expect($schema['properties']['username'])->toHaveKey('minLength', 3)
            ->and($schema['properties']['username'])->toHaveKey('maxLength', 50)
            ->and($schema['required'])->toContain('username');
    });

    it('can handle multiple properties with mixed types', function () {
        $property = new Property('TestSchema');
        $property->string('name')->required();
        $property->int('age')->min(0)->max(150);
        $property->boolean('isActive')->default(true);
        $property->array('tags');
        $property->enum('role', ['admin', 'user', 'guest']);

        $schema = $property->toArray();

        expect($schema['properties'])->toHaveCount(5)
            ->and($schema['properties']['name']['type'])->toBe('string')
            ->and($schema['properties']['age']['type'])->toBe('integer')
            ->and($schema['properties']['isActive']['type'])->toBe('boolean')
            ->and($schema['properties']['tags']['type'])->toBe('array')
            ->and($schema['properties']['role']['type'])->toBe('string');
    });

    it('avoids duplicate required fields', function () {
        $property = new Property('TestSchema');
        $property->string('name')->required();

        // Manually mark as required again
        $property->markRequired('name');
        $property->markRequired('name');

        $schema = $property->toArray();

        expect($schema['required'])->toHaveCount(1)
            ->and($schema['required'])->toContain('name');
    });
});
