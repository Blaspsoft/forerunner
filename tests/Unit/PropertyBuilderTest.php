<?php

use Blaspsoft\Forerunner\Schemas\Builder;
use Blaspsoft\Forerunner\Schemas\PropertyBuilder;

describe('PropertyBuilder', function () {
    it('can be instantiated with name and type', function () {
        $property = new PropertyBuilder('username', 'string');

        expect($property)->toBeInstanceOf(PropertyBuilder::class)
            ->and($property->getName())->toBe('username');
    });

    it('can be instantiated with description', function () {
        $property = new PropertyBuilder('username', 'string', 'User login name');
        $schema = $property->toArray();

        expect($schema)->toHaveKey('description', 'User login name');
    });

    it('can be marked as required', function () {
        $property = new PropertyBuilder('username', 'string');
        $result = $property->required();

        expect($result)->toBe($property)
            ->and($property->isRequired())->toBeTrue();
    });

    it('can be marked as optional', function () {
        $property = new PropertyBuilder('username', 'string');
        $property->required();
        $result = $property->optional();

        expect($result)->toBe($property)
            ->and($property->isRequired())->toBeFalse();
    });

    it('is optional by default', function () {
        $property = new PropertyBuilder('username', 'string');

        expect($property->isRequired())->toBeFalse();
    });

    it('can set description via method', function () {
        $property = new PropertyBuilder('username', 'string');
        $result = $property->description('User login name');

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('description', 'User login name');
    });

    it('can set enum values', function () {
        $property = new PropertyBuilder('status', 'string');
        $result = $property->enum(['active', 'inactive', 'pending']);

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('enum', ['active', 'inactive', 'pending']);
    });

    it('can set default value', function () {
        $property = new PropertyBuilder('isActive', 'boolean');
        $result = $property->default(true);

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('default', true);
    });

    it('can set minLength for strings', function () {
        $property = new PropertyBuilder('username', 'string');
        $result = $property->minLength(3);

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('minLength', 3);
    });

    it('can set maxLength for strings', function () {
        $property = new PropertyBuilder('username', 'string');
        $result = $property->maxLength(50);

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('maxLength', 50);
    });

    it('can set minItems for arrays', function () {
        $property = new PropertyBuilder('tags', 'array');
        $result = $property->minItems(1);

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('minItems', 1);
    });

    it('can set maxItems for arrays', function () {
        $property = new PropertyBuilder('tags', 'array');
        $result = $property->maxItems(10);

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('maxItems', 10);
    });

    it('can set minimum for numbers', function () {
        $property = new PropertyBuilder('age', 'integer');
        $result = $property->min(0);

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('minimum', 0.0);
    });

    it('can set maximum for numbers', function () {
        $property = new PropertyBuilder('age', 'integer');
        $result = $property->max(150);

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('maximum', 150.0);
    });

    it('can set pattern for strings', function () {
        $property = new PropertyBuilder('email', 'string');
        $result = $property->pattern('^[a-z]+@[a-z]+\.[a-z]+$');

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('pattern', '^[a-z]+@[a-z]+\.[a-z]+$');
    });

    it('can set items type for arrays', function () {
        $property = new PropertyBuilder('scores', 'array');
        $result = $property->items('integer');

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('items')
            ->and($schema['items'])->toBe(['type' => 'integer']);
    });

    it('can set items with nested object for arrays', function () {
        $property = new PropertyBuilder('users', 'array');
        $result = $property->items('object', function (Builder $builder) {
            $builder->string('name');
            $builder->string('email');
        });

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('items')
            ->and($schema['items']['type'])->toBe('object')
            ->and($schema['items']['properties'])->toHaveKey('name')
            ->and($schema['items']['properties'])->toHaveKey('email');
    });

    it('can set nested builder for objects', function () {
        $builder = new Builder('nested');
        $builder->string('field');

        $property = new PropertyBuilder('data', 'object');
        $property->setNestedBuilder($builder);

        $schema = $property->toArray();

        expect($schema['type'])->toBe('object')
            ->and($schema['properties'])->toHaveKey('field');
    });

    it('supports method chaining', function () {
        $property = new PropertyBuilder('username', 'string');
        $result = $property
            ->description('User login name')
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->pattern('^[a-zA-Z0-9_]+$');

        expect($result)->toBe($property);

        $schema = $property->toArray();
        expect($schema)->toHaveKey('description')
            ->and($schema)->toHaveKey('minLength', 3)
            ->and($schema)->toHaveKey('maxLength', 50)
            ->and($schema)->toHaveKey('pattern');
    });

    it('generates minimal schema with only type', function () {
        $property = new PropertyBuilder('simple', 'string');
        $schema = $property->toArray();

        expect($schema)->toBe(['type' => 'string']);
    });

    it('does not include null properties in schema', function () {
        $property = new PropertyBuilder('username', 'string');
        // Don't set any optional properties
        $schema = $property->toArray();

        expect($schema)->not->toHaveKey('description')
            ->and($schema)->not->toHaveKey('enum')
            ->and($schema)->not->toHaveKey('default')
            ->and($schema)->not->toHaveKey('minLength')
            ->and($schema)->not->toHaveKey('maxLength')
            ->and($schema)->not->toHaveKey('minimum')
            ->and($schema)->not->toHaveKey('maximum')
            ->and($schema)->not->toHaveKey('pattern')
            ->and($schema)->not->toHaveKey('items');
    });

    it('can handle numeric default values including zero', function () {
        $property = new PropertyBuilder('count', 'integer');
        $property->default(0);

        $schema = $property->toArray();

        expect($schema)->toHaveKey('default', 0);
    });

    it('can handle boolean false as default value', function () {
        $property = new PropertyBuilder('isEnabled', 'boolean');
        $property->default(false);

        $schema = $property->toArray();

        expect($schema)->toHaveKey('default', false);
    });

    it('can handle empty string as default value', function () {
        $property = new PropertyBuilder('note', 'string');
        $property->default('');

        $schema = $property->toArray();

        expect($schema)->toHaveKey('default', '');
    });

    it('merges nested builder schema correctly', function () {
        $builder = new Builder('user');
        $builder->string('name')->required();
        $builder->string('email')->required();

        $property = new PropertyBuilder('user', 'object');
        $property->setNestedBuilder($builder);

        $schema = $property->toArray();

        expect($schema['type'])->toBe('object')
            ->and($schema['properties'])->toHaveKey('name')
            ->and($schema['properties'])->toHaveKey('email')
            ->and($schema['required'])->toContain('name')
            ->and($schema['required'])->toContain('email');
    });

    it('can combine multiple constraints', function () {
        $property = new PropertyBuilder('score', 'number');
        $property
            ->min(0)
            ->max(100)
            ->default(50)
            ->description('Test score');

        $schema = $property->toArray();

        expect($schema['type'])->toBe('number')
            ->and($schema['minimum'])->toBe(0.0)
            ->and($schema['maximum'])->toBe(100.0)
            ->and($schema['default'])->toBe(50)
            ->and($schema['description'])->toBe('Test score');
    });
});
