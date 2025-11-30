<?php
// examples/AdvancedCompoundSchemas.php

use Masum\JsonSchema\Schema;

// Scenario 1: Polymorphic data structure (different shapes based on type)
$polymorphicSchema = Schema::object()
    ->property('type', Schema::string()->enum(['book', 'movie', 'music']))
    ->property('data', Schema::compound()
        ->oneOf(
            // Book type
            Schema::object()
                ->property('type', Schema::string()->const('book'))
                ->property('title', Schema::string())
                ->property('author', Schema::string())
                ->property('pages', Schema::number()->integer()->minimum(1))
                ->required(['title', 'author', 'pages']),

            // Movie type
            Schema::object()
                ->property('type', Schema::string()->const('movie'))
                ->property('title', Schema::string())
                ->property('director', Schema::string())
                ->property('duration', Schema::number()->minimum(1))
                ->required(['title', 'director', 'duration']),

            // Music type
            Schema::object()
                ->property('type', Schema::string()->const('music'))
                ->property('title', Schema::string())
                ->property('artist', Schema::string())
                ->property('album', Schema::string())
                ->required(['title', 'artist'])
        )
    )
    ->required(['type', 'data']);

// Scenario 2: Conditional validation based on other fields
$conditionalSchema = Schema::object()
    ->property('payment_method', Schema::string()->enum(['credit_card', 'paypal', 'bank_transfer']))
    ->property('payment_details', Schema::compound()
        ->anyOf(
            // Credit card details (required when payment_method is credit_card)
            Schema::object()
                ->property('payment_method', Schema::string()->const('credit_card'))
                ->property('card_number', Schema::string()->pattern('^[0-9]{16}$'))
                ->property('expiry_date', Schema::string()->pattern('^(0[1-9]|1[0-2])\/[0-9]{2}$'))
                ->property('cvv', Schema::string()->pattern('^[0-9]{3,4}$'))
                ->required(['card_number', 'expiry_date', 'cvv']),

            // PayPal details (required when payment_method is paypal)
            Schema::object()
                ->property('payment_method', Schema::string()->const('paypal'))
                ->property('email', Schema::string()->format('email'))
                ->required(['email']),

            // Bank transfer details (required when payment_method is bank_transfer)
            Schema::object()
                ->property('payment_method', Schema::string()->const('bank_transfer'))
                ->property('account_number', Schema::string()->minLength(5))
                ->property('routing_number', Schema::string()->pattern('^[0-9]{9}$'))
                ->required(['account_number', 'routing_number'])
        )
    )
    ->required(['payment_method', 'payment_details']);

// Scenario 3: Complex business rules with allOf
$businessRuleSchema = Schema::object()
    ->property('user', Schema::compound()
        ->allOf(
            // Basic user validation
            Schema::object()
                ->property('age', Schema::number()->minimum(18))
                ->property('country', Schema::string()->enum(['US', 'CA', 'UK'])),

            // Age-specific rules
            Schema::compound()
                ->anyOf(
                    // Under 21 restrictions
                    Schema::object()
                        ->property('age', Schema::number()->minimum(18)->maximum(20))
                        ->property('restricted_products', Schema::boolean()->const(false)),

                    // 21 and over - no restrictions
                    Schema::object()
                        ->property('age', Schema::number()->minimum(21))
                ),

            // Country-specific rules
            Schema::compound()
                ->anyOf(
                    // US-specific rules
                    Schema::object()
                        ->property('country', Schema::string()->const('US'))
                        ->property('state', Schema::string()->minLength(2)->maxLength(2))
                        ->required(['state']),

                    // Canada-specific rules
                    Schema::object()
                        ->property('country', Schema::string()->const('CA'))
                        ->property('province', Schema::string()->minLength(2))
                        ->required(['province']),

                    // UK-specific rules
                    Schema::object()
                        ->property('country', Schema::string()->const('UK'))
                        ->property('postcode', Schema::string()->pattern('^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$'))
                        ->required(['postcode'])
                )
        )
    );

// Scenario 4: Exclusive options with not
$exclusiveOptionsSchema = Schema::object()
    ->property('plan_type', Schema::string()->enum(['free', 'premium', 'enterprise']))
    ->property('features', Schema::compound()
        ->allOf(
            // Base features for all plans
            Schema::object()
                ->property('basic_support', Schema::boolean()->const(true)),

            // Plan-specific features with exclusions
            Schema::compound()
                ->oneOf(
                    // Free plan restrictions
                    Schema::object()
                        ->property('plan_type', Schema::string()->const('free'))
                        ->property('advanced_analytics', Schema::boolean()->const(false))
                        ->property('custom_domain', Schema::boolean()->const(false))
                        ->property('api_access', Schema::boolean()->const(false)),

                    // Premium plan features
                    Schema::object()
                        ->property('plan_type', Schema::string()->const('premium'))
                        ->property('advanced_analytics', Schema::boolean()->const(true))
                        ->property('custom_domain', Schema::boolean()->const(true))
                        ->property('api_access', Schema::boolean()->const(false)),

                    // Enterprise plan features
                    Schema::object()
                        ->property('plan_type', Schema::string()->const('enterprise'))
                        ->property('advanced_analytics', Schema::boolean()->const(true))
                        ->property('custom_domain', Schema::boolean()->const(true))
                        ->property('api_access', Schema::boolean()->const(true))
                        ->property('dedicated_support', Schema::boolean()->const(true))
                )
        )
    )
    ->required(['plan_type', 'features']);