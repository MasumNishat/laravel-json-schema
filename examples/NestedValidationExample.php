<?php
// examples/NestedValidationExample.php

use Masum\JsonSchema\Schema;
use Masum\JsonSchema\Helpers;

// Complex nested schema for e-commerce application
$ecommerceSchema = Schema::object()
    ->property('order', Schema::object()
        ->property('id', Schema::string()->format('uuid'))
        ->property('status', Schema::string()->enum(['pending', 'confirmed', 'shipped', 'delivered', 'cancelled']))
        ->property('total_amount', Schema::number()->minimum(0))
        ->property('currency', Schema::string()->enum(['USD', 'EUR', 'GBP']))
        ->property('created_at', Schema::string()->format('date-time'))
        ->required(['id', 'status', 'total_amount', 'currency', 'created_at'])
    )
    ->property('customer', Schema::object()
        ->property('id', Schema::string()->format('uuid'))
        ->property('email', Schema::string()->format('email'))
        ->property('name', Schema::object()
            ->property('first', Schema::string()->minLength(1)->maxLength(50))
            ->property('last', Schema::string()->minLength(1)->maxLength(50))
            ->property('middle', Schema::string()->maxLength(50)->nullable())
            ->required(['first', 'last'])
        )
        ->property('address', Schema::object()
            ->property('shipping', Schema::object()
                ->property('street', Schema::string()->minLength(1))
                ->property('city', Schema::string()->minLength(1))
                ->property('state', Schema::string()->minLength(2)->maxLength(2))
                ->property('zip_code', Schema::string()->pattern('^[0-9]{5}(-[0-9]{4})?$'))
                ->property('country', Schema::string()->enum(['US', 'CA']))
                ->required(['street', 'city', 'state', 'zip_code', 'country'])
            )
            ->property('billing', Schema::object()
                ->property('same_as_shipping', Schema::boolean())
                ->property('address', Schema::object()
                    ->property('street', Schema::string()->minLength(1))
                    ->property('city', Schema::string()->minLength(1))
                    ->property('state', Schema::string()->minLength(2)->maxLength(2))
                    ->property('zip_code', Schema::string()->pattern('^[0-9]{5}(-[0-9]{4})?$'))
                    ->property('country', Schema::string()->enum(['US', 'CA']))
                    ->required(['street', 'city', 'state', 'zip_code', 'country'])
                )
                ->required(['same_as_shipping'])
            )
            ->required(['shipping'])
        )
        ->required(['id', 'email', 'name', 'address'])
    )
    ->property('items', Schema::array()
        ->items(Schema::object()
            ->property('product_id', Schema::string()->format('uuid'))
            ->property('sku', Schema::string()->minLength(1))
            ->property('name', Schema::string()->minLength(1))
            ->property('quantity', Schema::number()->integer()->minimum(1))
            ->property('price', Schema::object()
                ->property('unit', Schema::number()->minimum(0))
                ->property('currency', Schema::string()->enum(['USD', 'EUR', 'GBP']))
                ->property('discount', Schema::number()->minimum(0)->maximum(100)->nullable())
                ->required(['unit', 'currency'])
            )
            ->property('variants', Schema::object()
                ->property('color', Schema::string()->nullable())
                ->property('size', Schema::string()->nullable())
                ->property('weight', Schema::number()->minimum(0)->nullable())
                ->additionalProperties(false)
            )
            ->required(['product_id', 'sku', 'name', 'quantity', 'price'])
        )
        ->minItems(1)
        ->maxItems(100)
    )
    ->property('payment', Schema::object()
        ->property('method', Schema::string()->enum(['credit_card', 'paypal', 'bank_transfer']))
        ->property('status', Schema::string()->enum(['pending', 'authorized', 'captured', 'refunded']))
        ->property('details', Schema::compound()
            ->oneOf(
                Schema::object()
                    ->property('method', Schema::string()->const('credit_card'))
                    ->property('last_four', Schema::string()->pattern('^[0-9]{4}$'))
                    ->property('card_type', Schema::string()->enum(['visa', 'mastercard', 'amex']))
                    ->required(['last_four', 'card_type']),
                Schema::object()
                    ->property('method', Schema::string()->const('paypal'))
                    ->property('email', Schema::string()->format('email'))
                    ->property('transaction_id', Schema::string())
                    ->required(['email', 'transaction_id']),
                Schema::object()
                    ->property('method', Schema::string()->const('bank_transfer'))
                    ->property('account_last_four', Schema::string()->pattern('^[0-9]{4}$'))
                    ->property('routing_number', Schema::string()->pattern('^[0-9]{9}$'))
                    ->required(['account_last_four', 'routing_number'])
            )
        )
        ->required(['method', 'status', 'details'])
    )
    ->required(['order', 'customer', 'items', 'payment']);

// Convert to Laravel validation rules
$validationRules = Helpers::createValidationRules($ecommerceSchema);

// The resulting rules would handle all nested structures:
/*
[
    'order.id' => ['string', 'uuid'],
    'order.status' => ['string', 'in:pending,confirmed,shipped,delivered,cancelled'],
    'order.total_amount' => ['numeric', 'min:0'],
    'order.currency' => ['string', 'in:USD,EUR,GBP'],
    'order.created_at' => ['string', 'date'],

    'customer.id' => ['string', 'uuid'],
    'customer.email' => ['string', 'email'],
    'customer.name.first' => ['string', 'min:1', 'max:50'],
    'customer.name.last' => ['string', 'min:1', 'max:50'],
    'customer.name.middle' => ['string', 'max:50', 'nullable'],

    'customer.address.shipping.street' => ['string', 'min:1'],
    'customer.address.shipping.city' => ['string', 'min:1'],
    'customer.address.shipping.state' => ['string', 'min:2', 'max:2'],
    'customer.address.shipping.zip_code' => ['string', 'regex:/^[0-9]{5}(-[0-9]{4})?$/'],
    'customer.address.shipping.country' => ['string', 'in:US,CA'],

    'customer.address.billing.same_as_shipping' => ['boolean'],
    'customer.address.billing.address.street' => ['string', 'min:1'],
    'customer.address.billing.address.city' => ['string', 'min:1'],
    'customer.address.billing.address.state' => ['string', 'min:2', 'max:2'],
    'customer.address.billing.address.zip_code' => ['string', 'regex:/^[0-9]{5}(-[0-9]{4})?$/'],
    'customer.address.billing.address.country' => ['string', 'in:US,CA'],

    'items.*.product_id' => ['string', 'uuid'],
    'items.*.sku' => ['string', 'min:1'],
    'items.*.name' => ['string', 'min:1'],
    'items.*.quantity' => ['numeric', 'min:1'],
    'items.*.price.unit' => ['numeric', 'min:0'],
    'items.*.price.currency' => ['string', 'in:USD,EUR,GBP'],
    'items.*.price.discount' => ['numeric', 'min:0', 'max:100', 'nullable'],
    'items.*.variants.color' => ['string', 'nullable'],
    'items.*.variants.size' => ['string', 'nullable'],
    'items.*.variants.weight' => ['numeric', 'min:0', 'nullable'],

    'payment.method' => ['string', 'in:credit_card,paypal,bank_transfer'],
    'payment.status' => ['string', 'in:pending,authorized,captured,refunded'],
    'payment.details.last_four' => ['required_if:payment.method,credit_card', 'string', 'regex:/^[0-9]{4}$/'],
    'payment.details.card_type' => ['required_if:payment.method,credit_card', 'string', 'in:visa,mastercard,amex'],
    'payment.details.email' => ['required_if:payment.method,paypal', 'string', 'email'],
    'payment.details.transaction_id' => ['required_if:payment.method,paypal', 'string'],
    'payment.details.account_last_four' => ['required_if:payment.method,bank_transfer', 'string', 'regex:/^[0-9]{4}$/'],
    'payment.details.routing_number' => ['required_if:payment.method,bank_transfer', 'string', 'regex:/^[0-9]{9}$/'],
]
*/

// Usage in Laravel controller
class OrderController extends Controller
{
    public function store(Request $request)
    {
        global $ecommerceSchema;
        $rules = Helpers::createValidationRules($ecommerceSchema);

        $validated = $request->validate($rules);

        // Process the validated nested data
        $order = Order::create($validated['order']);
        $customer = Customer::create($validated['customer']);

        foreach ($validated['items'] as $itemData) {
            $order->items()->create($itemData);
        }

        $payment = Payment::create($validated['payment']);

        return response()->json([
            'order_id' => $order->id,
            'status' => 'created'
        ]);
    }
}