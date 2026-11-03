<?php

declare(strict_types=1);
use Blaspsoft\Forerunner\Providers\Provider;
use Blaspsoft\Forerunner\Providers\SchemaTransformer;
use Blaspsoft\Forerunner\Schema\Property;
use Blaspsoft\Forerunner\Schema\Struct;

function orderStruct(): Struct
{
    return Struct::define('Order Summary', 'An order', function (Property $property) {
        $property->schemaVersion();
        $property->id('https://example.com/order.json');
        $property->string('id')->required();
        $property->string('note')->maxLength(200);
        $property->int('quantity')->min(1)->max(10)->required();
        $property->enum('status', ['open', 'closed']);
        $property->object('customer', function (Property $customer) {
            $customer->string('name')->required();
            $customer->email('email');
        }, 'The customer');
        $property->array('tags')->items('string')->minItems(1)->uniqueItems();
    });
}

describe('toSchema', function () {
    it('returns the plain schema without a provider wrapper', function () {
        $struct = Struct::define('User', 'A user', function (Property $property) {
            $property->string('name');
            $property->strict();
        });

        expect($struct->toSchema()['type'])->toBe('object')
            ->and($struct->toSchema()['required'])->toBe(['name'])
            ->and($struct->toSchema())->not->toHaveKey('strict');
    });
});

describe('OpenAI', function () {
    it('builds a Chat Completions response_format', function () {
        $format = orderStruct()->toOpenAI();

        expect($format['type'])->toBe('json_schema')
            ->and($format['json_schema']['name'])->toBe('Order_Summary')
            ->and($format['json_schema']['description'])->toBe('An order')
            ->and($format['json_schema']['strict'])->toBeTrue()
            ->and($format['json_schema']['schema'])->not->toHaveKeys(['description', '$schema', '$id']);
    });

    it('builds a Responses API text.format', function () {
        $format = orderStruct()->toOpenAIResponses();

        expect($format['type'])->toBe('json_schema')
            ->and($format['name'])->toBe('Order_Summary')
            ->and($format['strict'])->toBeTrue()
            ->and($format['schema']['type'])->toBe('object');
    });

    it('requires every property at every level', function () {
        $schema = orderStruct()->toOpenAI()['json_schema']['schema'];

        expect($schema['required'])->toBe(['id', 'note', 'quantity', 'status', 'customer', 'tags'])
            ->and($schema['properties']['customer']['required'])->toBe(['name', 'email']);
    });

    it('makes optional properties nullable', function () {
        $schema = orderStruct()->toOpenAI()['json_schema']['schema'];

        expect($schema['properties']['id']['type'])->toBe('string')
            ->and($schema['properties']['note']['type'])->toBe(['string', 'null'])
            ->and($schema['properties']['status']['enum'])->toBe(['open', 'closed', null])
            ->and($schema['properties']['customer']['properties']['email']['type'])->toBe(['string', 'null']);
    });

    it('closes every object even when additional properties were allowed', function () {
        $schema = Struct::define('Open', 'd', function (Property $property) {
            $property->string('a');
            $property->additionalProperties(true);
        })->toOpenAI()['json_schema']['schema'];

        expect($schema['additionalProperties'])->toBeFalse();
    });

    it('does not double up null on already nullable properties', function () {
        $schema = Struct::define('N', 'd', fn (Property $p) => $p->string('a')->nullable())
            ->toOpenAI()['json_schema']['schema'];

        expect($schema['properties']['a']['type'])->toBe(['string', 'null']);
    });
});

describe('Anthropic', function () {
    it('builds an output_config.format', function () {
        $format = orderStruct()->toAnthropic();

        expect($format['type'])->toBe('json_schema')
            ->and($format['schema']['type'])->toBe('object')
            ->and($format['schema']['description'])->toBe('An order')
            ->and($format['schema'])->not->toHaveKeys(['$schema', '$id']);
    });

    it('moves unsupported constraints into the description', function () {
        $schema = orderStruct()->toAnthropic()['schema'];

        expect($schema['properties']['quantity'])->not->toHaveKeys(['minimum', 'maximum'])
            ->and($schema['properties']['quantity']['description'])->toBe('(minimum: 1, maximum: 10)')
            ->and($schema['properties']['tags'])->not->toHaveKeys(['minItems', 'uniqueItems'])
            ->and($schema['properties']['tags']['description'])->toBe('(minItems: 1, uniqueItems: true)');
    });

    it('appends constraint hints to an existing description', function () {
        $schema = Struct::define('T', 'd', fn (Property $p) => $p->string('code', 'A code')->minLength(3))
            ->toAnthropic()['schema'];

        expect($schema['properties']['code']['description'])->toBe('A code (minLength: 3)');
    });

    it('keeps optional properties optional', function () {
        $schema = orderStruct()->toAnthropic()['schema'];

        expect($schema['required'])->toBe(['id', 'quantity'])
            ->and($schema['properties']['note']['type'])->toBe('string');
    });

    it('builds a strict tool definition', function () {
        $tool = orderStruct()->toAnthropicTool();

        expect($tool['name'])->toBe('Order_Summary')
            ->and($tool['description'])->toBe('An order')
            ->and($tool['strict'])->toBeTrue()
            ->and($tool['input_schema']['type'])->toBe('object')
            ->and($tool['input_schema'])->not->toHaveKey('description');
    });
});

describe('Gemini', function () {
    it('builds generationConfig fields', function () {
        $config = orderStruct()->toGemini();

        expect($config['responseMimeType'])->toBe('application/json')
            ->and($config['responseJsonSchema']['type'])->toBe('object')
            ->and($config['responseJsonSchema'])->not->toHaveKeys(['$schema', '$id']);
    });

    it('keeps enforced constraints and moves unenforced ones into the description', function () {
        $schema = orderStruct()->toGemini()['responseJsonSchema'];

        expect($schema['properties']['quantity']['minimum'])->toBe(1)
            ->and($schema['properties']['tags']['minItems'])->toBe(1)
            ->and($schema['properties']['tags'])->not->toHaveKey('uniqueItems')
            ->and($schema['properties']['note'])->not->toHaveKey('maxLength')
            ->and($schema['properties']['note']['description'])->toBe('(maxLength: 200)');
    });
});

describe('for()', function () {
    it('formats for a provider enum or name', function () {
        $struct = orderStruct();

        expect($struct->for(Provider::OpenAI))->toBe($struct->toOpenAI())
            ->and($struct->for('anthropic'))->toBe($struct->toAnthropic())
            ->and($struct->for('Gemini'))->toBe($struct->toGemini());
    });

    it('rejects unknown providers', function () {
        orderStruct()->for('mistral');
    })->throws(ValueError::class);
});

describe('Provider-safe names', function () {
    it('replaces invalid characters and limits the length', function () {
        expect(SchemaTransformer::name('User Profile (v2)'))->toBe('User_Profile_v2')
            ->and(strlen(SchemaTransformer::name(str_repeat('a', 100))))->toBe(64)
            ->and(SchemaTransformer::name('!!!'))->toBe('schema');
    });
});

describe('Struct output is unchanged', function () {
    it('does not mutate the struct when formatting', function () {
        $struct = orderStruct();
        $before = $struct->toArray();

        $struct->toOpenAI();
        $struct->toAnthropic();
        $struct->toGemini();

        expect($struct->toArray())->toBe($before);
    });
});
