<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Schema Settings
    |--------------------------------------------------------------------------
    |
    | This option defines the default settings for JSON Schema generation.
    |
    */

    'default' => [
        'schema' => 'http://json-schema.org/draft-07/schema#',
        'additional_properties' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Settings
    |--------------------------------------------------------------------------
    |
    | Configure how validation should behave.
    |
    */

    'validation' => [
        'strict_types' => true,
        'return_exceptions' => false,
        'format_validators' => [
            'email' => true,
            'uri' => true,
            'date' => true,
            'date-time' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Schema Storage
    |--------------------------------------------------------------------------
    |
    | Configure where schemas should be stored and loaded from.
    |
    */

    'storage' => [
        'path' => storage_path('app/json-schemas'),
        'auto_create' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel Integration
    |--------------------------------------------------------------------------
    |
    | Configure Laravel-specific integration options.
    |
    */

    'laravel' => [
        'use_facade' => true,
        'auto_register' => true,
        'validation_messages' => [
            'default' => 'The :attribute field is invalid.',
            'required' => 'The :attribute field is required.',
            'type' => 'The :attribute must be of type :type.',
        ],
    ],
];