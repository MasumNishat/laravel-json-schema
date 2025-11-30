# Laravel JSON Schema Examples with API Resources

This directory contains examples demonstrating how to integrate the `laravel-json-schema` package with Laravel API Resources for robust API development and validation.

## Structure

-   `app/Http/Resources/`: Contains various Laravel API Resource classes (`UserResource`, `UserCollection`, `ProfileResource`, `PostResource`, `RoleResource`, `CategoryResource`, `CommentResource`). These resources showcase how to define JSON Schemas directly within the resource classes using `static::schema()` methods, handle relationships, conditional attributes, and include metadata.
-   `app/Http/Controllers/`: Includes an example `UserController` demonstrating how to use the resources for listing, storing, showing, updating, and deleting users. It also shows how to validate incoming request data against a defined JSON Schema.
-   `app/Models/`: Contains a minimal `User.php` model with placeholder relationships and methods to support the examples in the resources and controller.
-   `api-responses/`: Provides example JSON response payloads for both single resources and resource collections, illustrating how the integrated schemas and metadata appear in the API output.

## Key Concepts Demonstrated

-   **Schema Definition within Resources**: How to define a JSON Schema directly within your `JsonResource` classes using a static `schema()` method.
-   **Resource Validation**: Using `Masum\JsonSchema\Schema::validate()` to validate incoming request data against a resource's defined schema in your controllers.
-   **Resource Collections**: Utilizing `ResourceCollection` for consistent pagination and metadata handling.
-   **Conditional Attributes**: Employing `$this->when()` and `$this->whenLoaded()` for efficient data loading and conditional attribute inclusion.
-   **Relationships**: Handling nested resources and collections for related models.
-   **API Metadata**: Adding schema definitions, versioning, and other useful metadata to API responses via the `with()` method.
-   **Artisan Commands**: Although not explicitly shown in code here, the `make:resource` command is a prerequisite for creating these files.

To run these examples, you would typically set up a Laravel application and integrate the `laravel-json-schema` package. You would also need to define routes that point to the `UserController` methods.
