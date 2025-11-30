<?php

namespace Masum\JsonSchema\Types;

class NumberType extends Type
{
    public function __construct()
    {
        $this->rules['type'] = 'number';
    }

    public function integer(): static
    {
        $this->rules['type'] = 'integer';
        return $this;
    }

    public function minimum($min): static
    {
        $this->rules['minimum'] = $min;
        return $this;
    }

    public function maximum($max): static
    {
        $this->rules['maximum'] = $max;
        return $this;
    }

    public function exclusiveMinimum($min): static
    {
        $this->rules['exclusiveMinimum'] = $min;
        return $this;
    }

    public function exclusiveMaximum($max): static
    {
        $this->rules['exclusiveMaximum'] = $max;
        return $this;
    }

    public function multipleOf($value): static
    {
        $this->rules['multipleOf'] = $value;
        return $this;
    }
}