<?php

namespace Masum\JsonSchema;

use Masum\JsonSchema\Types\ObjectType;
use Masum\JsonSchema\Types\StringType;
use Masum\JsonSchema\Types\NumberType;
use Masum\JsonSchema\Types\ArrayType;
use Masum\JsonSchema\Types\BooleanType;
use Masum\JsonSchema\Types\NullType;
use Masum\JsonSchema\Types\CompoundType;
use Masum\JsonSchema\Types\Type;
use Masum\JsonSchema\Validation\JsonSchemaRule;

class Schema
{
    public static function object(): ObjectType
    {
        return new ObjectType();
    }

    public static function string(): StringType
    {
        return new StringType();
    }

    public static function number(): NumberType
    {
        return new NumberType();
    }

    public static function array(): ArrayType
    {
        return new ArrayType();
    }

    public static function boolean(): BooleanType
    {
        return new BooleanType();
    }

    public static function null(): NullType
    {
        return new NullType();
    }

    public static function compound(): CompoundType
    {
        return new CompoundType();
    }

    public static function make(): static
    {
        return new static();
    }

    /**
     * Create a Laravel validation rule from a schema
     */
    public static function rule(Type $schema): JsonSchemaRule
    {
        return new JsonSchemaRule($schema);
    }

    /**
     * Validate data against a schema
     */
    public static function validate($data, Type $schema): array
    {
        $rule = new JsonSchemaRule($schema);
        $rule->passes('data', $data);

        return [
            'valid' => empty($rule->message()),
            'errors' => $rule->message() ? explode(', ', $rule->message()) : [],
        ];
    }

    /**
     * Create schema from JSON string
     */
    public static function fromJson(string $json): ObjectType
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON string');
        }

        return self::fromArray($data);
    }

    /**
     * Create schema from array
     */
    public static function fromArray(array $data): ObjectType
    {
        $schema = self::object();

        foreach ($data as $key => $value) {
            $schema = self::processSchemaValue($schema, $key, $value);
        }

        return $schema;
    }

    private static function processSchemaValue(ObjectType $schema, string $key, $value): ObjectType
    {
        if ($key === 'properties' && is_array($value)) {
            foreach ($value as $propName => $propSchema) {
                $type = self::createTypeFromSchema($propSchema);
                $schema->property($propName, $type);
            }
        } elseif ($key === 'required' && is_array($value)) {
            $schema->required($value);
        } else {
            // Handle other schema properties
            $schema->custom($key, $value);
        }

        return $schema;
    }

    private static function createTypeFromSchema(array $schema): Types\Type
    {
        $type = $schema['type'] ?? 'string';

        return match($type) {
            'string' => self::buildStringType($schema),
            'number', 'integer' => self::buildNumberType($schema),
            'array' => self::buildArrayType($schema),
            'boolean' => self::buildBooleanType($schema),
            'object' => self::buildObjectType($schema),
            default => self::string() // fallback
        };
    }

    private static function buildStringType(array $schema): StringType
    {
        $type = self::string();

        if (isset($schema['minLength'])) $type->minLength($schema['minLength']);
        if (isset($schema['maxLength'])) $type->maxLength($schema['maxLength']);
        if (isset($schema['pattern'])) $type->pattern($schema['pattern']);
        if (isset($schema['format'])) $type->format($schema['format']);
        if (isset($schema['enum'])) $type->enum($schema['enum']);

        return $type;
    }

    private static function buildNumberType(array $schema): NumberType
    {
        $type = self::number();

        if (isset($schema['minimum'])) $type->minimum($schema['minimum']);
        if (isset($schema['maximum'])) $type->maximum($schema['maximum']);
        if (isset($schema['multipleOf'])) $type->multipleOf($schema['multipleOf']);

        return $type;
    }

    private static function buildArrayType(array $schema): ArrayType
    {
        $type = self::array();

        if (isset($schema['items'])) {
            $itemType = self::createTypeFromSchema($schema['items']);
            $type->items($itemType);
        }
        if (isset($schema['minItems'])) $type->minItems($schema['minItems']);
        if (isset($schema['maxItems'])) $type->maxItems($schema['maxItems']);

        return $type;
    }

    private static function buildBooleanType(array $schema): BooleanType
    {
        return self::boolean();
    }

    private static function buildObjectType(array $schema): ObjectType
    {
        $type = self::object();

        if (isset($schema['properties'])) {
            foreach ($schema['properties'] as $propName => $propSchema) {
                $propType = self::createTypeFromSchema($propSchema);
                $type->property($propName, $propType);
            }
        }
        if (isset($schema['required'])) {
            $type->required($schema['required']);
        }

        return $type;
    }
}