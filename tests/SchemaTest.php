<?php

namespace Masum\JsonSchema\Tests;

use Masum\JsonSchema\Schema;

class SchemaTest extends TestCase
{
    public function test_can_create_object_schema(): void
    {
        $schema = Schema::object();

        $this->assertInstanceOf(\Masum\JsonSchema\Types\ObjectType::class, $schema);
        $this->assertEquals(['type' => 'object', 'properties' => []], $schema->toArray());
    }

    public function test_can_create_string_schema(): void
    {
        $schema = Schema::string()
            ->minLength(1)
            ->maxLength(255)
            ->format('email');

        $expected = [
            'type' => 'string',
            'minLength' => 1,
            'maxLength' => 255,
            'format' => 'email',
        ];

        $this->assertEquals($expected, $schema->toArray());
    }

    public function test_can_create_number_schema(): void
    {
        $schema = Schema::number()
            ->minimum(0)
            ->maximum(100)
            ->multipleOf(5);

        $expected = [
            'type' => 'number',
            'minimum' => 0,
            'maximum' => 100,
            'multipleOf' => 5,
        ];

        $this->assertEquals($expected, $schema->toArray());
    }

    public function test_can_create_array_schema(): void
    {
        $schema = Schema::array()
            ->items(Schema::string())
            ->minItems(1)
            ->maxItems(10);

        $expected = [
            'type' => 'array',
            'items' => ['type' => 'string'],
            'minItems' => 1,
            'maxItems' => 10,
        ];

        $this->assertEquals($expected, $schema->toArray());
    }

    public function test_can_create_boolean_schema(): void
    {
        $schema = Schema::boolean()
            ->const(true);

        $expected = [
            'type' => 'boolean',
            'const' => true,
        ];

        $this->assertEquals($expected, $schema->toArray());
    }
}