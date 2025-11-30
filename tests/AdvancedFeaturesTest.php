<?php

namespace Masum\JsonSchema\Tests;

use Masum\JsonSchema\Schema;

class AdvancedFeaturesTest extends TestCase
{
    public function test_can_export_schema_to_json(): void
    {
        $schema = Schema::object()
            ->property('name', Schema::string()->minLength(1)->maxLength(255))
            ->property('age', Schema::number()->minimum(0))
            ->required(['name']);

        $json = $schema->toJson();
        $array = json_decode($json, true);

        $this->assertJson($json);
        $this->assertEquals('object', $array['type']);
        $this->assertArrayHasKey('name', $array['properties']);
        $this->assertArrayHasKey('age', $array['properties']);
    }

    public function test_can_create_schema_with_compound_types(): void
    {
        $schema = Schema::compound()
            ->anyOf([
                Schema::string()->minLength(5),
                Schema::number()->minimum(10)
            ]);

        $array = $schema->toArray();

        $this->assertArrayHasKey('anyOf', $array);
        $this->assertCount(2, $array['anyOf']);
    }

    public function test_can_create_schema_from_json(): void
    {
        $json = '{
            "type": "object",
            "properties": {
                "email": {
                    "type": "string",
                    "format": "email"
                },
                "age": {
                    "type": "integer",
                    "minimum": 0
                }
            },
            "required": ["email"]
        }';

        $schema = Schema::fromJson($json);
        $array = $schema->toArray();

        $this->assertEquals('object', $array['type']);
        $this->assertArrayHasKey('email', $array['properties']);
        $this->assertArrayHasKey('age', $array['properties']);
        $this->assertContains('email', $array['required']);
    }

    public function test_can_create_schema_from_array(): void
    {
        $data = [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'minLength' => 1
                ]
            ],
            'required' => ['name']
        ];

        $schema = Schema::fromArray($data);
        $array = $schema->toArray();

        $this->assertEquals('object', $array['type']);
        $this->assertArrayHasKey('name', $array['properties']);
        $this->assertContains('name', $array['required']);
    }

    public function test_can_use_custom_properties(): void
    {
        $schema = Schema::object()
            ->property('id', Schema::string())
            ->custom('$schema', 'http://json-schema.org/draft-07/schema#')
            ->custom('x-custom-property', 'custom-value');

        $array = $schema->toArray();

        $this->assertEquals('http://json-schema.org/draft-07/schema#', $array['$schema']);
        $this->assertEquals('custom-value', $array['x-custom-property']);
    }

    public function test_can_create_nullable_fields(): void
    {
        $schema = Schema::string()
            ->minLength(1)
            ->nullable();

        $array = $schema->toArray();

        $this->assertEquals(['string', 'null'], $array['type']);
        $this->assertEquals(1, $array['minLength']);
    }

    public function test_can_use_comments(): void
    {
        $schema = Schema::string()
            ->comment('This is a test field')
            ->minLength(1);

        $array = $schema->toArray();

        $this->assertEquals('This is a test field', $array['$comment']);
    }
}