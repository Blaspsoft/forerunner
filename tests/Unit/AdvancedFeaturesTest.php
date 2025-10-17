<?php

declare(strict_types=1);
use Blaspsoft\Forerunner\Schema\Property;
use Blaspsoft\Forerunner\Schema\PropertyBuilder;
use Blaspsoft\Forerunner\Schema\Struct;

describe('Advanced Features', function () {
    describe('additionalProperties', function () {
        it('can set additionalProperties to true', function () {
            $property = new Property('Test');
            $property->string('name');
            $property->additionalProperties(true);

            $schema = $property->toArray();

            expect($schema)->toHaveKey('additionalProperties', true);
        });

        it('can set additionalProperties to false', function () {
            $property = new Property('Test');
            $property->string('name');
            $property->additionalProperties(false);

            $schema = $property->toArray();

            expect($schema)->toHaveKey('additionalProperties', false);
        });

        it('can use strict() helper for disallowing additional properties', function () {
            $property = new Property('Test');
            $property->string('name');
            $property->strict();

            $schema = $property->toArray();

            expect($schema)->toHaveKey('additionalProperties', false);
        });

        it('strict() makes all fields required', function () {
            $property = new Property('Test');
            $property->string('name');
            $property->email('email');
            $property->int('age')->min(0);
            $property->strict();

            $schema = $property->toArray();

            expect($schema)->toHaveKey('additionalProperties', false)
                ->and($schema['required'])->toBe(['name', 'email', 'age']);
        });

        it('defaults additionalProperties to false', function () {
            $property = new Property('Test');
            $property->string('name');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('additionalProperties', false);
        });
    });

    describe('uniqueItems', function () {
        it('can set uniqueItems on array fields', function () {
            $property = new Property('Test');
            $property->array('tags')->items('string')->uniqueItems();

            $schema = $property->toArray();

            expect($schema['properties']['tags'])->toHaveKey('uniqueItems', true);
        });

        it('can explicitly set uniqueItems to false', function () {
            $property = new Property('Test');
            $property->array('tags')->items('string')->uniqueItems(false);

            $schema = $property->toArray();

            expect($schema['properties']['tags'])->toHaveKey('uniqueItems', false);
        });

        it('does not include uniqueItems when not set', function () {
            $property = new Property('Test');
            $property->array('tags')->items('string');

            $schema = $property->toArray();

            expect($schema['properties']['tags'])->not->toHaveKey('uniqueItems');
        });
    });

    describe('format', function () {
        it('can set format on string fields', function () {
            $property = new Property('Test');
            $property->string('email')->format('email');

            $schema = $property->toArray();

            expect($schema['properties']['email'])->toHaveKey('format', 'email');
        });

        it('supports email format', function () {
            $property = new PropertyBuilder('email', 'string');
            $property->format('email');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('format', 'email');
        });

        it('supports uri format', function () {
            $property = new PropertyBuilder('website', 'string');
            $property->format('uri');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('format', 'uri');
        });

        it('supports uuid format', function () {
            $property = new PropertyBuilder('id', 'string');
            $property->format('uuid');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('format', 'uuid');
        });

        it('supports date-time format', function () {
            $property = new PropertyBuilder('created_at', 'string');
            $property->format('date-time');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('format', 'date-time');
        });
    });

    describe('nullable', function () {
        it('can mark fields as nullable', function () {
            $property = new PropertyBuilder('note', 'string');
            $property->nullable();

            $schema = $property->toArray();

            expect($schema['type'])->toBe(['string', 'null']);
        });

        it('does not affect type when nullable is false', function () {
            $property = new PropertyBuilder('note', 'string');
            $property->nullable(false);

            $schema = $property->toArray();

            expect($schema['type'])->toBe('string');
        });

        it('works with integer types', function () {
            $property = new PropertyBuilder('count', 'integer');
            $property->nullable();

            $schema = $property->toArray();

            expect($schema['type'])->toBe(['integer', 'null']);
        });

        it('can be chained with other methods', function () {
            $property = new PropertyBuilder('email', 'string');
            $property->format('email')->nullable()->description('Optional email');

            $schema = $property->toArray();

            expect($schema['type'])->toBe(['string', 'null'])
                ->and($schema['format'])->toBe('email')
                ->and($schema['description'])->toBe('Optional email');
        });
    });

    describe('schema version', function () {
        it('can set JSON Schema version', function () {
            $property = new Property('Test');
            $property->string('name');
            $property->schemaVersion('https://json-schema.org/draft/2020-12/schema');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('$schema', 'https://json-schema.org/draft/2020-12/schema');
        });

        it('uses default version when called without arguments', function () {
            $property = new Property('Test');
            $property->string('name');
            $property->schemaVersion();

            $schema = $property->toArray();

            expect($schema)->toHaveKey('$schema', 'https://json-schema.org/draft/2020-12/schema');
        });

        it('does not include $schema when not set', function () {
            $property = new Property('Test');
            $property->string('name');

            $schema = $property->toArray();

            expect($schema)->not->toHaveKey('$schema');
        });
    });

    describe('title', function () {
        it('can set title on schema', function () {
            $property = new Property('Test');
            $property->title('User Schema');
            $property->string('name');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('title', 'User Schema');
        });

        it('can set title on property', function () {
            $property = new PropertyBuilder('email', 'string');
            $property->title('Email Address');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('title', 'Email Address');
        });

        it('does not include title when not set', function () {
            $property = new Property('Test');
            $property->string('name');

            $schema = $property->toArray();

            expect($schema)->not->toHaveKey('title');
        });
    });

    describe('helper methods', function () {
        it('can add email field', function () {
            $property = new Property('Test');
            $property->email('email', 'User email address');

            $schema = $property->toArray();

            expect($schema['properties']['email']['type'])->toBe('string')
                ->and($schema['properties']['email']['format'])->toBe('email')
                ->and($schema['properties']['email']['description'])->toBe('User email address');
        });

        it('can add url field', function () {
            $property = new Property('Test');
            $property->url('website');

            $schema = $property->toArray();

            expect($schema['properties']['website']['type'])->toBe('string')
                ->and($schema['properties']['website']['format'])->toBe('uri');
        });

        it('can add uuid field', function () {
            $property = new Property('Test');
            $property->uuid('id');

            $schema = $property->toArray();

            expect($schema['properties']['id']['type'])->toBe('string')
                ->and($schema['properties']['id']['format'])->toBe('uuid');
        });

        it('can add datetime field', function () {
            $property = new Property('Test');
            $property->datetime('created_at');

            $schema = $property->toArray();

            expect($schema['properties']['created_at']['type'])->toBe('string')
                ->and($schema['properties']['created_at']['format'])->toBe('date-time');
        });

        it('can add date field', function () {
            $property = new Property('Test');
            $property->date('birth_date');

            $schema = $property->toArray();

            expect($schema['properties']['birth_date']['type'])->toBe('string')
                ->and($schema['properties']['birth_date']['format'])->toBe('date');
        });

        it('can add time field', function () {
            $property = new Property('Test');
            $property->time('start_time');

            $schema = $property->toArray();

            expect($schema['properties']['start_time']['type'])->toBe('string')
                ->and($schema['properties']['start_time']['format'])->toBe('time');
        });

        it('can add ipv4 field', function () {
            $property = new Property('Test');
            $property->ipv4('ip_address');

            $schema = $property->toArray();

            expect($schema['properties']['ip_address']['type'])->toBe('string')
                ->and($schema['properties']['ip_address']['format'])->toBe('ipv4');
        });

        it('can add ipv6 field', function () {
            $property = new Property('Test');
            $property->ipv6('ipv6_address');

            $schema = $property->toArray();

            expect($schema['properties']['ipv6_address']['type'])->toBe('string')
                ->and($schema['properties']['ipv6_address']['format'])->toBe('ipv6');
        });

        it('can add hostname field', function () {
            $property = new Property('Test');
            $property->hostname('server');

            $schema = $property->toArray();

            expect($schema['properties']['server']['type'])->toBe('string')
                ->and($schema['properties']['server']['format'])->toBe('hostname');
        });

        it('helper methods support chaining', function () {
            $property = new Property('Test');
            $property->email('email')->required();

            $schema = $property->toArray();

            expect($schema['properties']['email']['format'])->toBe('email')
                ->and($schema['required'])->toContain('email');
        });
    });

    describe('complete schema with all features', function () {
        it('generates comprehensive schema with all features in OpenAI format when strict', function () {
            $schema = Struct::define('CompleteExample', 'Complete example with all features', function (Property $property) {
                $property->schemaVersion();
                $property->title('Complete Schema Example');
                $property->description('A comprehensive schema demonstrating all features');
                $property->strict();

                $property->uuid('id')->required();
                $property->email('email')->required();
                $property->url('website')->nullable();
                $property->datetime('created_at')->required();
                $property->string('status')
                    ->enum(['active', 'inactive'])
                    ->default('active');

                $property->array('tags')
                    ->items('string')
                    ->uniqueItems()
                    ->minItems(1)
                    ->maxItems(5);

                $property->object('metadata', function (Property $nested) {
                    $nested->string('version')->required();
                    $nested->int('count')->min(0);
                })->nullable();
            })->toArray();

            // Check OpenAI format wrapper
            expect($schema)->toHaveKey('name', 'CompleteExample')
                ->and($schema)->toHaveKey('strict', true)
                ->and($schema)->toHaveKey('schema');

            // Check the nested schema structure
            $nestedSchema = $schema['schema'];
            expect($nestedSchema)->toHaveKey('$schema')
                ->and($nestedSchema)->toHaveKey('title', 'Complete Schema Example')
                ->and($nestedSchema)->toHaveKey('description')
                ->and($nestedSchema)->toHaveKey('additionalProperties', false)
                ->and($nestedSchema['properties']['id']['format'])->toBe('uuid')
                ->and($nestedSchema['properties']['email']['format'])->toBe('email')
                ->and($nestedSchema['properties']['website']['type'])->toBe(['string', 'null'])
                ->and($nestedSchema['properties']['tags']['uniqueItems'])->toBeTrue()
                ->and($nestedSchema['properties']['metadata']['type'])->toBe(['object', 'null'])
                ->and($nestedSchema['required'])->toContain('id', 'email', 'created_at');
        });

        it('generates normal schema without strict mode', function () {
            $schema = Struct::define('NormalExample', 'Normal example without strict mode', function (Property $property) {
                $property->title('Normal Schema Example');
                $property->uuid('id')->required();
                $property->email('email')->required();
            })->toArray();

            // Without strict(), should return normal flat schema
            expect($schema)->toHaveKey('type', 'object')
                ->and($schema)->toHaveKey('title', 'Normal Schema Example')
                ->and($schema)->not->toHaveKey('name')
                ->and($schema)->not->toHaveKey('strict')
                ->and($schema['properties']['id']['format'])->toBe('uuid')
                ->and($schema['properties']['email']['format'])->toBe('email');
        });
    });
});
