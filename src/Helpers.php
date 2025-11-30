<?php

namespace Masum\JsonSchema;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Helpers
{
    /**
     * Validate request data against a schema
     */
    public static function validateRequest(Request $request, $schema): array
    {
        $rule = Schema::rule($schema);

        $validator = Validator::make($request->all(), [
            'data' => [$rule],
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        return [
            'valid' => true,
            'data' => $request->input('data'),
        ];
    }

    /**
     * Create a FormRequest-like validation with schema
     */
    public static function createValidationRules($schema): array
    {
        // Convert schema to Laravel validation rules
        return self::schemaToRules($schema);
    }

    /**
     * Convert JSON Schema to Laravel validation rules
     */
    public static function schemaToRules($schema, string $prefix = ''): array
    {
        $rules = [];
        $schemaArray = $schema->toArray();

        if (isset($schemaArray['properties'])) {
            foreach ($schemaArray['properties'] as $property => $propertySchema) {
                $field = $prefix ? "{$prefix}.{$property}" : $property;
                $rules[$field] = self::propertyToRules($propertySchema);
            }
        }

        return $rules;
    }

    protected static function propertyToRules(array $schema): array
    {
        $rules = [];

        // Type rules
        if (isset($schema['type'])) {
            $type = is_array($schema['type']) ? $schema['type'][0] : $schema['type'];

            $rules[] = match($type) {
                'string' => 'string',
                'number', 'integer' => 'numeric',
                'boolean' => 'boolean',
                'array' => 'array',
                'object' => 'array', // objects as arrays in Laravel
                default => 'string'
            };
        }

        // Required check
        if (isset($schema['nullable']) && $schema['nullable']) {
            $rules[] = 'nullable';
        }

        // String rules
        if (isset($schema['minLength'])) {
            $rules[] = "min:{$schema['minLength']}";
        }

        if (isset($schema['maxLength'])) {
            $rules[] = "max:{$schema['maxLength']}";
        }

        if (isset($schema['format'])) {
            $rules[] = match($schema['format']) {
                'email' => 'email',
                'uri' => 'url',
                'uuid' => 'uuid',
                default => null
            };
        }

        if (isset($schema['enum'])) {
            $rules[] = 'in:' . implode(',', $schema['enum']);
        }

        // Number rules
        if (isset($schema['minimum'])) {
            $rules[] = "min:{$schema['minimum']}";
        }

        if (isset($schema['maximum'])) {
            $rules[] = "max:{$schema['maximum']}";
        }

        return array_filter($rules);
    }
}