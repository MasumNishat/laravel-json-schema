<?php
// examples/CustomKeywordsExample.php

use Masum\JsonSchema\Schema;

// Example 1: Database-specific custom properties
$schemaWithDbKeywords = Schema::object()
    ->property('id', Schema::string()->format('uuid'))
    ->property('name', Schema::string())
    ->custom('x-database-table', 'products')
    ->custom('x-database-primary-key', 'id')
    ->custom('x-database-indexes', ['name', 'created_at'])
    ->custom('x-database-relations', [
        'category_id' => 'categories.id',
        'user_id' => 'users.id'
    ]);

// Example 2: API documentation custom properties
$schemaWithApiDocs = Schema::object()
    ->property('title', Schema::string())
    ->property('content', Schema::string())
    ->custom('x-swagger-example', [
        'title' => 'Sample Post',
        'content' => 'This is a sample blog post content.'
    ])
    ->custom('x-openapi-summary', 'Create a new blog post')
    ->custom('x-openapi-description', 'Creates a new blog post with the provided title and content')
    ->custom('x-openapi-tags', ['blog', 'posts']);

// Example 3: Business logic custom properties
$schemaWithBusinessLogic = Schema::object()
    ->property('order_total', Schema::number()->minimum(0))
    ->property('currency', Schema::string()->enum(['USD', 'EUR', 'GBP']))
    ->custom('x-business-rule', 'order_total > 0')
    ->custom('x-permissions', ['admin', 'manager'])
    ->custom('x-audit-fields', ['order_total', 'currency'])
    ->custom('x-calculated-fields', [
        'tax_amount' => 'order_total * 0.1',
        'total_amount' => 'order_total + tax_amount'
    ]);

// Example 4: UI/UX custom properties
$schemaWithUIProperties = Schema::object()
    ->property('first_name', Schema::string())
    ->property('last_name', Schema::string())
    ->property('email', Schema::string()->format('email'))
    ->custom('x-ui-order', ['first_name', 'last_name', 'email'])
    ->custom('x-ui-widgets', [
        'first_name' => 'text',
        'last_name' => 'text',
        'email' => 'email'
    ])
    ->custom('x-ui-labels', [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email Address'
    ])
    ->custom('x-ui-placeholders', [
        'first_name' => 'Enter your first name',
        'last_name' => 'Enter your last name'
    ]);