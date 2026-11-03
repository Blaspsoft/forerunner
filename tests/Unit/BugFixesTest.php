<?php

declare(strict_types=1);
use Blaspsoft\Forerunner\Schema\Property;
use Blaspsoft\Forerunner\Schema\Struct;

describe('Strict mode nesting', function () {
    it('requires every property of nested objects', function () {
        $schema = Struct::define('Order', 'An order', function (Property $property) {
            $property->object('customer', function (Property $customer) {
                $customer->string('name');
                $customer->string('email');
            });
            $property->strict();
        })->toArray()['schema'];

        expect($schema['properties']['customer']['required'])->toBe(['name', 'email']);
    });

    it('requires every property of array item objects', function () {
        $schema = Struct::define('Order', 'An order', function (Property $property) {
            $property->array('items')->items('object', function (Property $item) {
                $item->string('sku');
                $item->int('quantity');
            });
            $property->strict();
        })->toArray()['schema'];

        expect($schema['properties']['items']['items']['required'])->toBe(['sku', 'quantity']);
    });

    it('requires deeply nested properties', function () {
        $schema = Struct::define('Deep', 'A deep schema', function (Property $property) {
            $property->object('a', function (Property $a) {
                $a->object('b', function (Property $b) {
                    $b->string('c');
                });
            });
            $property->strict();
        })->toArray()['schema'];

        expect($schema['properties']['a']['properties']['b']['required'])->toBe(['c']);
    });

    it('leaves nested properties optional when not strict', function () {
        $schema = Struct::define('Order', 'An order', function (Property $property) {
            $property->object('customer', function (Property $customer) {
                $customer->string('name');
            });
        })->toArray();

        expect($schema['properties']['customer'])->not->toHaveKey('required');
    });
});

describe('Enum types', function () {
    it('keeps string enums as strings', function () {
        $schema = Struct::define('T', 'd', fn (Property $p) => $p->enum('status', ['a', 'b']))->toArray();

        expect($schema['properties']['status']['type'])->toBe('string');
    });

    it('types integer enums as integers', function () {
        $schema = Struct::define('T', 'd', fn (Property $p) => $p->enum('priority', [1, 2, 3]))->toArray();

        expect($schema['properties']['priority']['type'])->toBe('integer');
    });

    it('types mixed integer and float enums as numbers', function () {
        $schema = Struct::define('T', 'd', fn (Property $p) => $p->enum('rating', [1, 1.5, 2]))->toArray();

        expect($schema['properties']['rating']['type'])->toBe('number');
    });

    it('types boolean enums as booleans', function () {
        $schema = Struct::define('T', 'd', fn (Property $p) => $p->enum('flag', [true]))->toArray();

        expect($schema['properties']['flag']['type'])->toBe('boolean');
    });

    it('makes enums containing null nullable', function () {
        $schema = Struct::define('T', 'd', fn (Property $p) => $p->enum('size', ['s', 'm', null]))->toArray();

        expect($schema['properties']['size']['type'])->toBe(['string', 'null'])
            ->and($schema['properties']['size']['enum'])->toBe(['s', 'm', null]);
    });
});

describe('Nested object metadata', function () {
    it('prefers the field description over the nested schema description', function () {
        $schema = Struct::define('T', 'd', function (Property $property) {
            $property->object('customer', function (Property $customer) {
                $customer->description('Inner description');
                $customer->string('name');
            }, 'Outer description');
        })->toArray();

        expect($schema['properties']['customer']['description'])->toBe('Outer description');
    });

    it('falls back to the nested schema description', function () {
        $schema = Struct::define('T', 'd', function (Property $property) {
            $property->object('customer', function (Property $customer) {
                $customer->description('Inner description');
                $customer->string('name');
            });
        })->toArray();

        expect($schema['properties']['customer']['description'])->toBe('Inner description');
    });
});

describe('Numeric constraints', function () {
    it('keeps integer bounds as integers', function () {
        $schema = Struct::define('T', 'd', fn (Property $p) => $p->int('age')->min(0)->max(150))->toArray();

        expect($schema['properties']['age']['minimum'])->toBe(0)
            ->and($schema['properties']['age']['maximum'])->toBe(150);
    });

    it('keeps float bounds as floats', function () {
        $schema = Struct::define('T', 'd', fn (Property $p) => $p->float('score')->min(0.5))->toArray();

        expect($schema['properties']['score']['minimum'])->toBe(0.5);
    });
});

describe('Facade alias', function () {
    it('does not override the Laravel Schema alias', function () {
        $composer = json_decode((string) file_get_contents(__DIR__.'/../../composer.json'), true);

        expect($composer['extra']['laravel'])->not->toHaveKey('aliases');
    });
});
