<?php

/**
 * Example User Schema - Comprehensive Feature Showcase
 *
 * Demonstrates ALL supported features of the JSON Schema package.
 */

use Masum\JsonSchema\Schema;

return Schema::object()
    // Basic Types with all constraints
    ->property('id', Schema::string()
        ->format('uuid')
        ->description('Unique identifier for the user')
        ->comment('Must be a valid UUID v4')
    )
    ->property('username', Schema::string()
        ->minLength(3)
        ->maxLength(50)
        ->pattern('^[a-zA-Z0-9_]+$')
        ->description('Unique username for login')
    )
    ->property('email', Schema::string()
        ->format('email')
        ->description('Primary email address')
    )
    ->property('age', Schema::number()
        ->integer()
        ->minimum(0)
        ->maximum(150)
        ->multipleOf(1)
        ->nullable()
        ->description('Age in years')
    )
    ->property('height', Schema::number()
        ->minimum(0.5)
        ->maximum(3.0)
        ->exclusiveMinimum(0)
        ->exclusiveMaximum(3.5)
        ->description('Height in meters')
    )
    ->property('is_active', Schema::boolean()
        ->default(true)
        ->description('Whether the user account is active')
    )
    ->property('score', Schema::number()
        ->minimum(0)
        ->maximum(100)
        ->description('User performance score')
    )

    // Array Types with various item schemas
    ->property('tags', Schema::array()
        ->items(Schema::string()->minLength(1)->maxLength(20))
        ->minItems(0)
        ->maxItems(10)
        ->uniqueItems(true)
        ->description('User tags for categorization')
    )
    ->property('scores', Schema::array()
        ->items(Schema::number()->minimum(0)->maximum(100))
        ->minItems(1)
        ->maxItems(100)
        ->description('Historical scores')
    )
    ->property('preferences', Schema::array()
        ->items(Schema::object()
            ->property('key', Schema::string())
            ->property('value', Schema::string())
            ->required(['key', 'value'])
        )
        ->description('User preference key-value pairs')
    )

    // Complex Nested Objects
    ->property('address', Schema::object()
        ->property('street', Schema::string()->minLength(1))
        ->property('city', Schema::string()->minLength(1))
        ->property('state', Schema::string()->minLength(2)->maxLength(2))
        ->property('zip_code', Schema::string()->pattern('^[0-9]{5}(-[0-9]{4})?$'))
        ->property('country', Schema::string()->enum(['US', 'CA', 'UK', 'AU']))
        ->required(['street', 'city', 'state', 'country'])
        ->additionalProperties(false)
        ->description('Primary physical address')
    )

    ->property('profile', Schema::object()
        ->property('bio', Schema::string()->maxLength(1000)->nullable())
        ->property('avatar_url', Schema::string()->format('uri')->nullable())
        ->property('birth_date', Schema::string()->format('date'))
        ->property('company', Schema::string()->maxLength(100)->nullable())
        ->property('job_title', Schema::string()->maxLength(100)->nullable())
        ->required(['birth_date'])
        ->description('Extended profile information')
    )

    // Advanced Compound Types
    ->property('contact_method', Schema::compound()
        ->oneOf(
            Schema::object()
                ->property('type', Schema::string()->const('email'))
                ->property('value', Schema::string()->format('email'))
                ->required(['type', 'value']),
            Schema::object()
                ->property('type', Schema::string()->const('phone'))
                ->property('value', Schema::string()->pattern('^\+?[1-9]\d{1,14}$'))
                ->required(['type', 'value']),
            Schema::object()
                ->property('type', Schema::string()->const('sms'))
                ->property('value', Schema::string()->pattern('^\+?[1-9]\d{1,14}$'))
                ->required(['type', 'value'])
        )
        ->description('Primary contact method - exactly one must match')
    )

    ->property('user_type', Schema::compound()
        ->anyOf(
            Schema::object()
                ->property('role', Schema::string()->const('admin'))
                ->property('permissions', Schema::array()->minItems(1)),
            Schema::object()
                ->property('role', Schema::string()->const('user'))
                ->property('subscription', Schema::string()->enum(['free', 'premium']))
        )
        ->description('User type - at least one must match')
    )

    ->property('advanced_requirements', Schema::compound()
        ->allOf(
            Schema::object()
                ->property('age', Schema::number()->minimum(18)),
            Schema::object()
                ->property('verified', Schema::boolean()->const(true)),
            Schema::object()
                ->property('consent', Schema::boolean()->const(true))
        )
        ->description('All conditions must be satisfied')
    )

    ->property('restricted_status', Schema::compound()
        ->not(Schema::string()->enum(['banned', 'suspended']))
        ->description('Status cannot be banned or suspended')
    )

    // Enum and Const Examples
    ->property('status', Schema::string()
        ->enum(['active', 'inactive', 'pending', 'suspended'])
        ->default('pending')
        ->description('Current account status')
    )

    ->property('account_type', Schema::string()
        ->const('standard')
        ->description('Fixed account type')
    )

    // Nullable and Optional Fields
    ->property('middle_name', Schema::string()
        ->minLength(1)
        ->maxLength(50)
        ->nullable()
        ->description('Optional middle name')
    )

    ->property('phone_number', Schema::string()
        ->pattern('^\+?[1-9]\d{1,14}$')
        ->nullable()
        ->description('Optional phone number')
    )

    // Required Fields
    ->required([
        'id',
        'username',
        'email',
        'is_active',
        'status',
        'account_type',
        'profile'
    ])

    // Custom Keywords and Metadata
    ->custom('$schema', 'http://json-schema.org/draft-07/schema#')
    ->custom('$id', 'https://example.com/schemas/user.json')
    ->custom('title', 'Comprehensive User Schema')
    ->custom('description', 'A complete user schema demonstrating all JSON Schema features supported by the package')
    ->custom('definitions', [
        'address' => [
            'type' => 'object',
            'properties' => [
                'street' => ['type' => 'string'],
                'city' => ['type' => 'string']
            ]
        ]
    ])

    // Laravel-specific custom properties
    ->custom('x-laravel-table', 'users')
    ->custom('x-laravel-model', 'App\\Models\\User')
    ->custom('x-laravel-casts', [
        'metadata' => 'array',
        'preferences' => 'collection'
    ])

    // Validation configuration
    ->custom('x-validation-messages', [
        'email.format' => 'The email address must be valid.',
        'age.minimum' => 'Age must be a positive number.'
    ])

    // Additional schema constraints
    ->minProperties(5)
    ->maxProperties(25)
    ->additionalProperties(false)

    // Schema-level metadata
    ->comment('This comprehensive schema demonstrates all features of the masum/laravel-json-schema package')
    ->examples([
        [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'age' => 30,
            'is_active' => true,
            'status' => 'active'
        ]
    ]);