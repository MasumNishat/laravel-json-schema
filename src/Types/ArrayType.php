<?php

namespace Masum\JsonSchema\Types;

class ArrayType extends Type
{
    public function __construct()
    {
        $this->rules['type'] = 'array';
    }

    public function items(Type $type): static
    {
        $this->rules['items'] = $type->toArray();
        return $this;
    }

    public function minItems(int $min): static
    {
        $this->rules['minItems'] = $min;
        return $this;
    }

    public function maxItems(int $max): static
    {
        $this->rules['maxItems'] = $max;
        return $this;
    }

    public function uniqueItems(bool $unique = true): static
    {
        $this->rules['uniqueItems'] = $unique;
        return $this;
    }

    public function contains(Type $type): static
    {
        $this->rules['contains'] = $type->toArray();
        return $this;
    }
}