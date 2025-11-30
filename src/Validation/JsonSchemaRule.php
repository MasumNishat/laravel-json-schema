<?php

namespace Masum\JsonSchema\Validation;

use Illuminate\Contracts\Validation\Rule;
use Masum\JsonSchema\Types\Type;

class JsonSchemaRule implements Rule
{
    protected Type $schema;
    protected array $errors = [];

    public function __construct(Type $schema)
    {
        $this->schema = $schema;
    }

    public function passes($attribute, $value): bool
    {
        $data = is_string($value) ? json_decode($value, true) : $value;

        if (is_string($value) && json_last_error() !== JSON_ERROR_NONE) {
            $this->errors[] = "The $attribute must be valid JSON";
            return false;
        }

        return $this->validateAgainstSchema($data, $this->schema->toArray(), $attribute);
    }

    protected function validateAgainstSchema($data, array $schema, string $path = ''): bool
    {
        $valid = true;

        // Compound validation
        if (!$this->validateCompound($data, $schema, $path)) {
            $valid = false;
        }

        // Type validation
        if (isset($schema['type'])) {
            if (!$this->validateType($data, $schema['type'], $path)) {
                $valid = false;
            }
        }

        // String validation
        if (isset($schema['type']) && $schema['type'] === 'string') {
            if (!$this->validateString($data, $schema, $path)) {
                $valid = false;
            }
        }

        // Number validation
        if (isset($schema['type']) && in_array($schema['type'], ['number', 'integer'])) {
            if (!$this->validateNumber($data, $schema, $path)) {
                $valid = false;
            }
        }

        // Array validation
        if (isset($schema['type']) && $schema['type'] === 'array') {
            if (!$this->validateArray($data, $schema, $path)) {
                $valid = false;
            }
        }

        // Object validation
        if (isset($schema['type']) && $schema['type'] === 'object') {
            if (!$this->validateObject($data, $schema, $path)) {
                $valid = false;
            }
        }

        return $valid;
    }

    protected function validateType($data, string $type, string $path): bool
    {
        $valid = match($type) {
            'string' => is_string($data),
            'number' => is_int($data) || is_float($data),
            'integer' => is_int($data),
            'boolean' => is_bool($data),
            'array' => is_array($data),
            'object' => is_array($data) || is_object($data),
            'null' => is_null($data),
            default => true
        };

        if (!$valid) {
            $this->errors[] = "The field $path must be of type $type";
        }

        return $valid;
    }

    protected function validateString($data, array $schema, string $path): bool
    {
        $valid = true;

        if (isset($schema['minLength']) && strlen($data) < $schema['minLength']) {
            $this->errors[] = "The field $path must be at least {$schema['minLength']} characters";
            $valid = false;
        }

        if (isset($schema['maxLength']) && strlen($data) > $schema['maxLength']) {
            $this->errors[] = "The field $path may not be greater than {$schema['maxLength']} characters";
            $valid = false;
        }

        if (isset($schema['pattern']) && !preg_match("/{$schema['pattern']}/", $data)) {
            $this->errors[] = "The field $path format is invalid";
            $valid = false;
        }

        if (isset($schema['format'])) {
            if (!$this->validateFormat($data, $schema['format'], $path)) {
                $valid = false;
            }
        }

        if (isset($schema['enum']) && !in_array($data, $schema['enum'])) {
            $this->errors[] = "The field $path must be one of: " . implode(', ', $schema['enum']);
            $valid = false;
        }

        return $valid;
    }

    protected function validateNumber($data, array $schema, string $path): bool
    {
        $valid = true;

        if (isset($schema['minimum']) && $data < $schema['minimum']) {
            $this->errors[] = "The field $path must be at least {$schema['minimum']}";
            $valid = false;
        }

        if (isset($schema['maximum']) && $data > $schema['maximum']) {
            $this->errors[] = "The field $path may not be greater than {$schema['maximum']}";
            $valid = false;
        }

        if (isset($schema['exclusiveMinimum']) && $data <= $schema['exclusiveMinimum']) {
            $this->errors[] = "The field $path must be greater than {$schema['exclusiveMinimum']}";
            $valid = false;
        }

        if (isset($schema['exclusiveMaximum']) && $data >= $schema['exclusiveMaximum']) {
            $this->errors[] = "The field $path must be less than {$schema['exclusiveMaximum']}";
            $valid = false;
        }

        if (isset($schema['multipleOf']) && fmod($data, $schema['multipleOf']) != 0) {
            $this->errors[] = "The field $path must be a multiple of {$schema['multipleOf']}";
            $valid = false;
        }

        return $valid;
    }

    protected function validateArray($data, array $schema, string $path): bool
    {
        $valid = true;

        if (isset($schema['minItems']) && count($data) < $schema['minItems']) {
            $this->errors[] = "The field $path must have at least {$schema['minItems']} items";
            $valid = false;
        }

        if (isset($schema['maxItems']) && count($data) > $schema['maxItems']) {
            $this->errors[] = "The field $path may not have more than {$schema['maxItems']} items";
            $valid = false;
        }

        if (isset($schema['uniqueItems']) && $schema['uniqueItems'] && count($data) !== count(array_unique($data))) {
            $this->errors[] = "The field $path must have unique items";
            $valid = false;
        }

        // Validate array items if schema is defined
        if (isset($schema['items']) && is_array($data)) {
            foreach ($data as $index => $item) {
                $itemPath = $path ? "{$path}[{$index}]" : "[{$index}]";
                if (!$this->validateAgainstSchema($item, $schema['items'], $itemPath)) {
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    protected function validateObject($data, array $schema, string $path): bool
    {
        $valid = true;
        $data = (array) $data;

        // Check required fields
        if (isset($schema['required'])) {
            foreach ($schema['required'] as $requiredField) {
                if (!array_key_exists($requiredField, $data)) {
                    $fieldPath = $path ? "{$path}.{$requiredField}" : $requiredField;
                    $this->errors[] = "The field $fieldPath is required";
                    $valid = false;
                }
            }
        }

        // Validate properties
        if (isset($schema['properties'])) {
            foreach ($schema['properties'] as $property => $propertySchema) {
                $fieldPath = $path ? "{$path}.{$property}" : $property;

                if (array_key_exists($property, $data)) {
                    if (!$this->validateAgainstSchema($data[$property], $propertySchema, $fieldPath)) {
                        $valid = false;
                    }
                }
            }
        }

        // Check additional properties
        if (isset($schema['additionalProperties']) && $schema['additionalProperties'] === false) {
            $allowedProperties = array_keys($schema['properties'] ?? []);
            foreach (array_keys($data) as $property) {
                if (!in_array($property, $allowedProperties)) {
                    $fieldPath = $path ? "{$path}.{$property}" : $property;
                    $this->errors[] = "The field $fieldPath is not allowed";
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    protected function validateFormat($data, string $format, string $path): bool
    {
        $valid = match($format) {
            'email' => filter_var($data, FILTER_VALIDATE_EMAIL) !== false,
            'uri' => filter_var($data, FILTER_VALIDATE_URL) !== false,
            'date' => strtotime($data) !== false,
            'date-time' => strtotime($data) !== false,
            default => true
        };

        if (!$valid) {
            $this->errors[] = "The field $path must be a valid $format";
        }

        return $valid;
    }

    public function message(): string
    {
        return implode(', ', $this->errors);
    }

    protected function validateCompound($data, array $schema, string $path): bool
    {
        $valid = true;

        // anyOf validation
        if (isset($schema['anyOf'])) {
            $anyValid = false;
            foreach ($schema['anyOf'] as $index => $subSchema) {
                if ($this->validateAgainstSchema($data, $subSchema, $path)) {
                    $anyValid = true;
                    break;
                }
            }
            if (!$anyValid) {
                $this->errors[] = "The field $path must match at least one of the defined schemas";
                $valid = false;
            }
        }

        // allOf validation
        if (isset($schema['allOf'])) {
            foreach ($schema['allOf'] as $index => $subSchema) {
                if (!$this->validateAgainstSchema($data, $subSchema, $path)) {
                    $this->errors[] = "The field $path must match all of the defined schemas";
                    $valid = false;
                    break;
                }
            }
        }

        // oneOf validation
        if (isset($schema['oneOf'])) {
            $matchCount = 0;
            foreach ($schema['oneOf'] as $index => $subSchema) {
                if ($this->validateAgainstSchema($data, $subSchema, $path)) {
                    $matchCount++;
                }
            }
            if ($matchCount !== 1) {
                $this->errors[] = "The field $path must match exactly one of the defined schemas";
                $valid = false;
            }
        }

        // not validation
        if (isset($schema['not'])) {
            if ($this->validateAgainstSchema($data, $schema['not'], $path)) {
                $this->errors[] = "The field $path must not match the defined schema";
                $valid = false;
            }
        }

        return $valid;
    }
}