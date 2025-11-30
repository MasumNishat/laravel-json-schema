<?php

namespace Masum\JsonSchema\Facades;

use Illuminate\Support\Facades\Facade;
use Masum\JsonSchema\Validation\JsonSchemaRule;
use Masum\JsonSchema\Types\Type;

/**
 * @method static \Masum\JsonSchema\Types\ObjectType object()
 * @method static \Masum\JsonSchema\Types\StringType string()
 * @method static \Masum\JsonSchema\Types\NumberType number()
 * @method static \Masum\JsonSchema\Types\ArrayType array()
 * @method static \Masum\JsonSchema\Types\BooleanType boolean()
 * @method static \Masum\JsonSchema\Types\NullType null()
 * @method static \Masum\JsonSchema\Types\CompoundType compound()
 * @method static \Masum\JsonSchema\Schema make()
 * @method static JsonSchemaRule rule(Type $schema)
 * @method static array validate($data, Type $schema)
 * @method static \Masum\JsonSchema\Types\ObjectType fromJson(string $json)
 * @method static \Masum\JsonSchema\Types\ObjectType fromArray(array $data)
 */
class Schema extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'json-schema';
    }
}