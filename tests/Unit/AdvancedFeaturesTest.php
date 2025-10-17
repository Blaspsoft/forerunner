<?php

declare(strict_types=1);
use Blaspsoft\Forerunner\Schema\Builder;
use Blaspsoft\Forerunner\Schema\PropertyBuilder;
use Blaspsoft\Forerunner\Schema\Struct;

describe('Advanced Features', function () {
    describe('additionalProperties', function () {
        it('can set additionalProperties to true', function () {
            $builder = new Builder('Test');
            $builder->string('name');
            $builder->additionalProperties(true);

            $schema = $builder->toArray();

            expect($schema)->toHaveKey('additionalProperties', true);
        });

        it('can set additionalProperties to false', function () {
            $builder = new Builder('Test');
            $builder->string('name');
            $builder->additionalProperties(false);

            $schema = $builder->toArray();

            expect($schema)->toHaveKey('additionalProperties', false);
        });

        it('can use strict() helper for disallowing additional properties', function () {
            $builder = new Builder('Test');
            $builder->string('name');
            $builder->strict();

            $schema = $builder->toArray();

            expect($schema)->toHaveKey('additionalProperties', false);
        });

        it('strict() makes all fields required', function () {
            $builder = new Builder('Test');
            $builder->string('name');
            $builder->email('email');
            $builder->int('age')->min(0);
            $builder->strict();

            $schema = $builder->toArray();

            expect($schema)->toHaveKey('additionalProperties', false)
                ->and($schema['required'])->toBe(['name', 'email', 'age']);
        });

        it('defaults additionalProperties to false', function () {
            $builder = new Builder('Test');
            $builder->string('name');

            $schema = $builder->toArray();

            expect($schema)->toHaveKey('additionalProperties', false);
        });
    });

    describe('uniqueItems', function () {
        it('can set uniqueItems on array fields', function () {
            $builder = new Builder('Test');
            $builder->array('tags')->items('string')->uniqueItems();

            $schema = $builder->toArray();

            expect($schema['properties']['tags'])->toHaveKey('uniqueItems', true);
        });

        it('can explicitly set uniqueItems to false', function () {
            $builder = new Builder('Test');
            $builder->array('tags')->items('string')->uniqueItems(false);

            $schema = $builder->toArray();

            expect($schema['properties']['tags'])->toHaveKey('uniqueItems', false);
        });

        it('does not include uniqueItems when not set', function () {
            $builder = new Builder('Test');
            $builder->array('tags')->items('string');

            $schema = $builder->toArray();

            expect($schema['properties']['tags'])->not->toHaveKey('uniqueItems');
        });
    });

    describe('format', function () {
        it('can set format on string fields', function () {
            $builder = new Builder('Test');
            $builder->string('email')->format('email');

            $schema = $builder->toArray();

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
            $builder = new Builder('Test');
            $builder->string('name');
            $builder->schemaVersion('https://json-schema.org/draft/2020-12/schema');

            $schema = $builder->toArray();

            expect($schema)->toHaveKey('$schema', 'https://json-schema.org/draft/2020-12/schema');
        });

        it('uses default version when called without arguments', function () {
            $builder = new Builder('Test');
            $builder->string('name');
            $builder->schemaVersion();

            $schema = $builder->toArray();

            expect($schema)->toHaveKey('$schema', 'https://json-schema.org/draft/2020-12/schema');
        });

        it('does not include $schema when not set', function () {
            $builder = new Builder('Test');
            $builder->string('name');

            $schema = $builder->toArray();

            expect($schema)->not->toHaveKey('$schema');
        });
    });

    describe('title', function () {
        it('can set title on schema', function () {
            $builder = new Builder('Test');
            $builder->title('User Schema');
            $builder->string('name');

            $schema = $builder->toArray();

            expect($schema)->toHaveKey('title', 'User Schema');
        });

        it('can set title on property', function () {
            $property = new PropertyBuilder('email', 'string');
            $property->title('Email Address');

            $schema = $property->toArray();

            expect($schema)->toHaveKey('title', 'Email Address');
        });

        it('does not include title when not set', function () {
            $builder = new Builder('Test');
            $builder->string('name');

            $schema = $builder->toArray();

            expect($schema)->not->toHaveKey('title');
        });
    });

    describe('helper methods', function () {
        it('can add email field', function () {
            $builder = new Builder('Test');
            $builder->email('email', 'User email address');

            $schema = $builder->toArray();

            expect($schema['properties']['email']['type'])->toBe('string')
                ->and($schema['properties']['email']['format'])->toBe('email')
                ->and($schema['properties']['email']['description'])->toBe('User email address');
        });

        it('can add url field', function () {
            $builder = new Builder('Test');
            $builder->url('website');

            $schema = $builder->toArray();

            expect($schema['properties']['website']['type'])->toBe('string')
                ->and($schema['properties']['website']['format'])->toBe('uri');
        });

        it('can add uuid field', function () {
            $builder = new Builder('Test');
            $builder->uuid('id');

            $schema = $builder->toArray();

            expect($schema['properties']['id']['type'])->toBe('string')
                ->and($schema['properties']['id']['format'])->toBe('uuid');
        });

        it('can add datetime field', function () {
            $builder = new Builder('Test');
            $builder->datetime('created_at');

            $schema = $builder->toArray();

            expect($schema['properties']['created_at']['type'])->toBe('string')
                ->and($schema['properties']['created_at']['format'])->toBe('date-time');
        });

        it('can add date field', function () {
            $builder = new Builder('Test');
            $builder->date('birth_date');

            $schema = $builder->toArray();

            expect($schema['properties']['birth_date']['type'])->toBe('string')
                ->and($schema['properties']['birth_date']['format'])->toBe('date');
        });

        it('can add time field', function () {
            $builder = new Builder('Test');
            $builder->time('start_time');

            $schema = $builder->toArray();

            expect($schema['properties']['start_time']['type'])->toBe('string')
                ->and($schema['properties']['start_time']['format'])->toBe('time');
        });

        it('can add ipv4 field', function () {
            $builder = new Builder('Test');
            $builder->ipv4('ip_address');

            $schema = $builder->toArray();

            expect($schema['properties']['ip_address']['type'])->toBe('string')
                ->and($schema['properties']['ip_address']['format'])->toBe('ipv4');
        });

        it('can add ipv6 field', function () {
            $builder = new Builder('Test');
            $builder->ipv6('ipv6_address');

            $schema = $builder->toArray();

            expect($schema['properties']['ipv6_address']['type'])->toBe('string')
                ->and($schema['properties']['ipv6_address']['format'])->toBe('ipv6');
        });

        it('can add hostname field', function () {
            $builder = new Builder('Test');
            $builder->hostname('server');

            $schema = $builder->toArray();

            expect($schema['properties']['server']['type'])->toBe('string')
                ->and($schema['properties']['server']['format'])->toBe('hostname');
        });

        it('helper methods support chaining', function () {
            $builder = new Builder('Test');
            $builder->email('email')->required();

            $schema = $builder->toArray();

            expect($schema['properties']['email']['format'])->toBe('email')
                ->and($schema['required'])->toContain('email');
        });
    });

    describe('complete schema with all features', function () {
        it('generates comprehensive schema with all features in OpenAI format when strict', function () {
            $schema = Struct::define('CompleteExample', function (Builder $builder) {
                $builder->schemaVersion();
                $builder->title('Complete Schema Example');
                $builder->description('A comprehensive schema demonstrating all features');
                $builder->strict();

                $builder->uuid('id')->required();
                $builder->email('email')->required();
                $builder->url('website')->nullable();
                $builder->datetime('created_at')->required();
                $builder->string('status')
                    ->enum(['active', 'inactive'])
                    ->default('active');

                $builder->array('tags')
                    ->items('string')
                    ->uniqueItems()
                    ->minItems(1)
                    ->maxItems(5);

                $builder->object('metadata', function (Builder $nested) {
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
            $schema = Struct::define('NormalExample', function (Builder $builder) {
                $builder->title('Normal Schema Example');
                $builder->uuid('id')->required();
                $builder->email('email')->required();
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
