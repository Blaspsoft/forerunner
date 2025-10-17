<?php

use Blaspsoft\Forerunner\Schema\Builder;
use Blaspsoft\Forerunner\Schema\PropertyBuilder;

describe('Builder', function () {
    it('can be instantiated with a name', function () {
        $builder = new Builder('TestSchema');

        expect($builder)->toBeInstanceOf(Builder::class);
    });

    it('can add a string property', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->string('username');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add an integer property', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->int('age');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add an integer property using integer alias', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->integer('count');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a float property', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->float('price');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a number property using number alias', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->number('amount');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a boolean property', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->boolean('isActive');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a boolean property using bool alias', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->bool('enabled');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add an array property', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->array('tags');

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add an enum property', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->enum('status', ['active', 'inactive', 'pending']);

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can add a nested object property', function () {
        $builder = new Builder('TestSchema');
        $property = $builder->object('user', function (Builder $nested) {
            $nested->string('name');
            $nested->string('email');
        });

        expect($property)->toBeInstanceOf(PropertyBuilder::class);
    });

    it('can set a description on the schema', function () {
        $builder = new Builder('TestSchema');
        $result = $builder->description('Test schema description');

        expect($result)->toBe($builder);
    });

    it('generates correct JSON schema array for simple properties', function () {
        $builder = new Builder('TestSchema');
        $builder->string('name');
        $builder->int('age');

        $schema = $builder->toArray();

        expect($schema)->toHaveKey('type', 'object')
            ->and($schema)->toHaveKey('properties')
            ->and($schema['properties'])->toHaveKey('name')
            ->and($schema['properties'])->toHaveKey('age')
            ->and($schema['properties']['name']['type'])->toBe('string')
            ->and($schema['properties']['age']['type'])->toBe('integer');
    });

    it('includes description in schema when set', function () {
        $builder = new Builder('TestSchema');
        $builder->description('User schema');
        $builder->string('name');

        $schema = $builder->toArray();

        expect($schema)->toHaveKey('description', 'User schema');
    });

    it('includes required fields in schema', function () {
        $builder = new Builder('TestSchema');
        $builder->string('name')->required();
        $builder->string('email')->required();
        $builder->int('age');

        $schema = $builder->toArray();

        expect($schema)->toHaveKey('required')
            ->and($schema['required'])->toContain('name')
            ->and($schema['required'])->toContain('email')
            ->and($schema['required'])->not->toContain('age');
    });

    it('does not include required key when no fields are required', function () {
        $builder = new Builder('TestSchema');
        $builder->string('name');
        $builder->int('age');

        $schema = $builder->toArray();

        expect($schema)->not->toHaveKey('required');
    });

    it('generates correct schema for nested objects', function () {
        $builder = new Builder('TestSchema');
        $builder->object('user', function (Builder $nested) {
            $nested->string('name');
            $nested->string('email');
        });

        $schema = $builder->toArray();

        expect($schema['properties']['user']['type'])->toBe('object')
            ->and($schema['properties']['user']['properties'])->toHaveKey('name')
            ->and($schema['properties']['user']['properties'])->toHaveKey('email');
    });

    it('generates correct schema for enum fields', function () {
        $builder = new Builder('TestSchema');
        $builder->enum('status', ['active', 'inactive']);

        $schema = $builder->toArray();

        expect($schema['properties']['status']['type'])->toBe('string')
            ->and($schema['properties']['status']['enum'])->toBe(['active', 'inactive']);
    });

    it('can convert schema to JSON string', function () {
        $builder = new Builder('TestSchema');
        $builder->string('name')->required();

        $json = $builder->toJson();

        expect($json)->toBeString()
            ->and($json)->toContain('"type": "object"')
            ->and($json)->toContain('"name"')
            ->and($json)->toContain('"required"');
    });

    it('supports method chaining for properties', function () {
        $builder = new Builder('TestSchema');
        $builder->string('username')
            ->required()
            ->minLength(3)
            ->maxLength(50);

        $schema = $builder->toArray();

        expect($schema['properties']['username'])->toHaveKey('minLength', 3)
            ->and($schema['properties']['username'])->toHaveKey('maxLength', 50)
            ->and($schema['required'])->toContain('username');
    });

    it('can handle multiple properties with mixed types', function () {
        $builder = new Builder('TestSchema');
        $builder->string('name')->required();
        $builder->int('age')->min(0)->max(150);
        $builder->boolean('isActive')->default(true);
        $builder->array('tags');
        $builder->enum('role', ['admin', 'user', 'guest']);

        $schema = $builder->toArray();

        expect($schema['properties'])->toHaveCount(5)
            ->and($schema['properties']['name']['type'])->toBe('string')
            ->and($schema['properties']['age']['type'])->toBe('integer')
            ->and($schema['properties']['isActive']['type'])->toBe('boolean')
            ->and($schema['properties']['tags']['type'])->toBe('array')
            ->and($schema['properties']['role']['type'])->toBe('string');
    });

    it('avoids duplicate required fields', function () {
        $builder = new Builder('TestSchema');
        $builder->string('name')->required();

        // Manually mark as required again
        $builder->markRequired('name');
        $builder->markRequired('name');

        $schema = $builder->toArray();

        expect($schema['required'])->toHaveCount(1)
            ->and($schema['required'])->toContain('name');
    });
});
