<?php
// examples/RealWorldUsage.php

use Masum\JsonSchema\Schema;
use Masum\JsonSchema\Helpers;

// API Request Validation
class UserRegistrationRequest extends FormRequest
{
    public function rules()
    {
        $schema = Schema::object()
            ->property('user', Schema::object()
                ->property('email', Schema::string()->format('email'))
                ->property('password', Schema::string()->minLength(8)->pattern('^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$'))
                ->property('profile', Schema::object()
                    ->property('first_name', Schema::string()->minLength(1))
                    ->property('last_name', Schema::string()->minLength(1))
                    ->property('birth_date', Schema::string()->format('date'))
                    ->required(['first_name', 'last_name', 'birth_date'])
                )
                ->required(['email', 'password', 'profile'])
            )
            ->property('consent', Schema::object()
                ->property('terms', Schema::boolean()->const(true))
                ->property('marketing', Schema::boolean())
                ->required(['terms'])
            )
            ->required(['user', 'consent']);

        return Helpers::createValidationRules($schema);
    }
}

// Database Configuration Schema
$databaseConfigSchema = Schema::object()
    ->property('connections', Schema::object()
        ->property('mysql', Schema::object()
            ->property('driver', Schema::string()->const('mysql'))
            ->property('host', Schema::string())
            ->property('port', Schema::number()->integer()->minimum(1)->maximum(65535))
            ->property('database', Schema::string())
            ->property('username', Schema::string())
            ->property('password', Schema::string()->nullable())
            ->property('charset', Schema::string()->default('utf8mb4'))
            ->property('collation', Schema::string()->default('utf8mb4_unicode_ci'))
            ->required(['driver', 'host', 'database', 'username'])
        )
        ->property('redis', Schema::object()
            ->property('host', Schema::string())
            ->property('port', Schema::number()->integer()->minimum(1)->maximum(65535))
            ->property('password', Schema::string()->nullable())
            ->required(['host', 'port'])
        )
        ->required(['mysql'])
    )
    ->custom('x-config-file', 'config/database.php')
    ->custom('x-environment-variables', [
        'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'
    ]);

// Feature Flag Configuration
$featureFlagSchema = Schema::object()
    ->property('features', Schema::object()
        ->property('new_ui', Schema::object()
            ->property('enabled', Schema::boolean())
            ->property('rollout_percentage', Schema::number()->minimum(0)->maximum(100))
            ->property('user_ids', Schema::array()->items(Schema::string()))
            ->required(['enabled'])
        )
        ->property('beta_features', Schema::object()
            ->property('enabled', Schema::boolean())
            ->property('allowed_emails', Schema::array()->items(Schema::string()->format('email')))
            ->required(['enabled'])
        )
        ->additionalProperties(false)
    )
    ->custom('x-feature-toggles', true)
    ->custom('x-cache-duration', '5 minutes');