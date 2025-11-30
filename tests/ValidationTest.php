<?php

namespace Masum\JsonSchema\Tests;

use Masum\JsonSchema\Schema;

class ValidationTest extends TestCase
{
    public function test_validates_string_successfully(): void
    {
        $schema = Schema::string()
            ->minLength(1)
            ->maxLength(255);

        $result = Schema::validate('hello', $schema);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    public function test_validates_string_failure(): void
    {
        $schema = Schema::string()
            ->minLength(5);

        $result = Schema::validate('hi', $schema);

        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }

    public function test_validates_object_successfully(): void
    {
        $schema = Schema::object()
            ->property('name', Schema::string()->minLength(1))
            ->property('email', Schema::string()->format('email'))
            ->required(['name', 'email']);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $result = Schema::validate($data, $schema);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    public function test_validates_object_missing_required(): void
    {
        $schema = Schema::object()
            ->property('name', Schema::string()->minLength(1))
            ->required(['name']);

        $data = [];

        $result = Schema::validate($data, $schema);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('required', $result['errors'][0]);
    }

    public function test_validates_array_successfully(): void
    {
        $schema = Schema::array()
            ->items(Schema::string())
            ->minItems(1)
            ->maxItems(3);

        $data = ['a', 'b', 'c'];

        $result = Schema::validate($data, $schema);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    public function test_can_use_rule_in_laravel_validation(): void
    {
        $schema = Schema::object()
            ->property('email', Schema::string()->format('email'))
            ->required(['email']);

        $rule = Schema::rule($schema);

        $this->assertTrue($rule->passes('data', ['email' => 'test@example.com']));
        $this->assertFalse($rule->passes('data', ['email' => 'invalid-email']));
    }
}