<?php

use Blaspsoft\Forerunner\Schemas\Builder;
use Blaspsoft\Forerunner\Schemas\Struct;

it('works with the intended API usage', function () {
    $schema = Struct::define('User', function (Builder $builder) {
        $builder->string('name', 'The name of the user')->minLength(1)->maxLength(100)->required();
        $builder->int('age', 'The age of the user')->min(0)->max(150);
        $builder->boolean('is_active', 'Is the user active?')->default(true);
        $builder->array('tags', 'Tags associated with the user')
            ->items('string')
            ->minItems(0)
            ->maxItems(10);
        $builder->object('address', function (Builder $table) {
            $table->string('street', 'Street name')->required();
            $table->string('city', 'City name')->required();
            $table->string('zip', 'ZIP code')->required();
        }, 'The address of the user');
    });

    expect($schema)->toBeInstanceOf(Struct::class)
        ->and($schema['type'])->toBe('object')
        ->and($schema['properties'])->toHaveKey('name')
        ->and($schema['properties'])->toHaveKey('age')
        ->and($schema['properties'])->toHaveKey('is_active')
        ->and($schema['properties'])->toHaveKey('tags')
        ->and($schema['properties'])->toHaveKey('address')
        ->and($schema['properties']['name']['description'])->toBe('The name of the user')
        ->and($schema['properties']['name']['minLength'])->toBe(1)
        ->and($schema['properties']['name']['maxLength'])->toBe(100)
        ->and($schema['properties']['age']['description'])->toBe('The age of the user')
        ->and($schema['properties']['age']['minimum'])->toBe(0.0)
        ->and($schema['properties']['age']['maximum'])->toBe(150.0)
        ->and($schema['properties']['is_active']['description'])->toBe('Is the user active?')
        ->and($schema['properties']['is_active']['default'])->toBe(true)
        ->and($schema['properties']['tags']['description'])->toBe('Tags associated with the user')
        ->and($schema['properties']['tags']['items'])->toBe(['type' => 'string'])
        ->and($schema['properties']['tags']['minItems'])->toBe(0)
        ->and($schema['properties']['tags']['maxItems'])->toBe(10)
        ->and($schema['properties']['address']['description'])->toBe('The address of the user')
        ->and($schema['properties']['address']['type'])->toBe('object')
        ->and($schema['properties']['address']['properties'])->toHaveKey('street')
        ->and($schema['properties']['address']['properties'])->toHaveKey('city')
        ->and($schema['properties']['address']['properties'])->toHaveKey('zip')
        ->and($schema['properties']['address']['properties']['street']['description'])->toBe('Street name')
        ->and($schema['properties']['address']['properties']['city']['description'])->toBe('City name')
        ->and($schema['properties']['address']['properties']['zip']['description'])->toBe('ZIP code')
        ->and($schema['required'])->toContain('name')
        ->and($schema['properties']['address']['required'])->toContain('street')
        ->and($schema['properties']['address']['required'])->toContain('city')
        ->and($schema['properties']['address']['required'])->toContain('zip');
});
