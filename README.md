# Laravel JSON Schema

A lightweight, fluent JSON Schema builder for Laravel with full validation support.

## Installation

```bash
composer require masum/laravel-json-schema
```

## Quick Start

### Basic Schema Definition

```php
use Masum\JsonSchema\Schema;

$userSchema = Schema::object()
    ->property('id', Schema::string()->format('uuid'))
    ->property('name', Schema::string()->minLength(1)->maxLength(255))
    ->property('email', Schema::string()->format('email'))
    ->property('age', Schema::number()->integer()->minimum(0)->nullable())
    ->required(['id', 'name', 'email']);

// Convert to JSON
$json = $userSchema->toJson();

// Convert to array
$array = $userSchema->toArray();
```

## Validation

### Direct Validation

```php
$data = [
    'id' => '550e8400-e29b-41d4-a716-446655440000',
    'name' => 'John Doe',
    'email' => 'john@example.com'
];

$result = Schema::validate($data, $userSchema);

if ($result['valid']) {
    // Data is valid
} else {
    // Handle errors
    $errors = $result['errors'];
}
```

### Laravel Validation Rule

```php
use Illuminate\Http\Request;
use Masum\JsonSchema\Schema;

public function store(Request $request)
{
    $request->validate([
        'user_data' => [Schema::rule($userSchema)],
    ]);
    
    // Data is valid
}
```

### Form Request Integration

```php
use Masum\JsonSchema\Helpers;
use Illuminate\Foundation\Http\FormRequest; // Assuming FormRequest is used

class StoreUserRequest extends FormRequest
{
    public function rules()
    {
        return Helpers::createValidationRules($userSchema);
    }
}
```

## Advanced Usage

### Compound Schemas

```php
$schema = Schema::compound()
    ->anyOf([
        Schema::string()->minLength(5),
        Schema::number()->minimum(10)
    ]);
```

### Array Validation

```php
$schema = Schema::array()
    ->items(Schema::object()
        ->property('id', Schema::string())
        ->property('value', Schema::number())
    )
    ->minItems(1)
    ->maxItems(10)
    ->uniqueItems(true);
```

### Custom Properties

```php
$schema = Schema::object()
    ->property('id', Schema::string())
    ->custom('$schema', 'http://json-schema.org/draft-07/schema#')
    ->custom('x-custom-property', 'custom-value');
```

## Artisan Commands

### Create a New Schema

```bash
php artisan make:schema UserSchema
```

### Create Example Schema

```bash
php artisan make:schema UserSchema --example
```

### Validate Data

```bash
# From argument
php artisan schema:validate UserSchema '{"name": "John", "email": "john@example.com"}'

# From file
php artisan schema:validate UserSchema --file=user-data.json

# From stdin
echo '{"name": "John"}' | php artisan schema:validate UserSchema
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Masum\JsonSchema\SchemaServiceProvider" --tag="json-schema-config"
```

## Testing

```bash
composer test
```

## Features

- ✅ Fluent, expressive API
- ✅ Full JSON Schema Draft-07 support
- ✅ Laravel validation integration
- ✅ Compound types (anyOf, oneOf, allOf, not)
- ✅ Custom properties and extensions
- ✅ Artisan commands for schema management
- ✅ Comprehensive validation with detailed errors
- ✅ Nullable field support
- ✅ Format validation (email, URL, UUID, dates)
- ✅ Array and object validation
- ✅ Configuration-driven behavior

## License

This package is open-source software licensed under the MIT license.

## Example Schemas

### Create `schemas/ExampleUserSchema.php`

```php
<?php

/**
 * Example User Schema
 * 
 * Demonstrates various JSON Schema features and validation rules.
 */

use Masum\JsonSchema\Schema;

return Schema::object()
    ->property('id', Schema::string()->format('uuid')->description('Unique user identifier'))
    ->property('name', Schema::string()->minLength(1)->maxLength(255)->description('User full name'))
    ->property('email', Schema::string()->format('email')->description('User email address'))
    ->property('age', Schema::number()->integer()->minimum(0)->maximum(150)->nullable()->description('User age'))
    ->property('roles', Schema::array()->items(Schema::string()->enum(['admin', 'user', 'editor']))->minItems(1)->description('User roles'))
    ->property('metadata', Schema::object()->additionalProperties(true)->description('Additional user metadata'))
    ->property('created_at', Schema::string()->format('date-time')->description('Creation timestamp'))
    ->required(['id', 'name', 'email', 'created_at'])
    ->custom('$schema', 'http://json-schema.org/draft-07/schema#')
    ->custom('title', 'User Schema')
    ->custom('description', 'Schema for validating user data in the application')
    ->comment('This schema is used for user data validation across the application');
```

## Examples with API Resources

This section demonstrates how to integrate the `laravel-json-schema` package with Laravel API Resources for robust API development and validation, as found in the `examples/` directory.

### Structure

-   `examples/app/Http/Resources/`: Contains various Laravel API Resource classes (`UserResource`, `UserCollection`, `ProfileResource`, `PostResource`, `RoleResource`, `CategoryResource`, `CommentResource`). These resources showcase how to define JSON Schemas directly within the resource classes using `static::schema()` methods, handle relationships, conditional attributes, and include metadata.
-   `examples/app/Http/Controllers/`: Includes an example `UserController` demonstrating how to use the resources for listing, storing, showing, updating, and deleting users. It also shows how to validate incoming request data against a defined JSON Schema.
-   `examples/app/Models/`: Contains a minimal `User.php` model with placeholder relationships and methods to support the examples in the resources and controller.
-   `examples/api-responses/`: Provides example JSON response payloads for both single resources and resource collections, illustrating how the integrated schemas and metadata appear in the API output.

### Key Concepts Demonstrated

-   **Schema Definition within Resources**: How to define a JSON Schema directly within your `JsonResource` classes using a static `schema()` method.
-   **Resource Validation**: Using `Masum\JsonSchema\Schema::validate()` to validate incoming request data against a resource's defined schema in your controllers.
-   **Resource Collections**: Utilizing `ResourceCollection` for consistent pagination and metadata handling.
-   **Conditional Attributes**: Employing `$this->when()` and `$this->whenLoaded()` for efficient data loading and conditional attribute inclusion.
-   **Relationships**: Handling nested resources and collections for related models.
-   **API Metadata**: Adding schema definitions, versioning, and other useful metadata to API responses via the `with()` method.
-   **Artisan Commands**: Although not explicitly shown in code here, the `make:resource` command is a prerequisite for creating these files.

To run these examples, you would typically set up a Laravel application and integrate the `laravel-json-schema` package. You would also need to define routes that point to the `UserController` methods.

## Final Testing Suite

### Create `tests/IntegrationTest.php`

```php
<?php

namespace Masum\JsonSchema\Tests;

use Masum\JsonSchema\Schema;
use Masum\JsonSchema\Helpers;

class IntegrationTest extends TestCase
{
    public function test_can_use_in_laravel_validation(): void
    {
        $schema = Schema::object()
            ->property('email', Schema::string()->format('email'))
            ->required(['email']);

        $validator = \Validator::make(
            ['data' => ['email' => 'test@example.com']],
            ['data' => [Schema::rule($schema)]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_can_convert_schema_to_laravel_rules(): void
    {
        $schema = Schema::object()
            ->property('email', Schema::string()->format('email'))
            ->property('age', Schema::number()->minimum(18))
            ->required(['email']);

        $rules = Helpers::schemaToRules($schema);

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('age', $rules);
        $this->assertContains('email', $rules['email']);
    }

    public function test_artisan_commands_are_registered(): void
    {
        $this->assertTrue(\Artisan::has('make:schema'));
        $this->assertTrue(\Artisan::has('schema:validate'));
    }
}
```

### Run Final Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Test code coverage (if configured)
./vendor/bin/phpunit --coverage-html coverage
```