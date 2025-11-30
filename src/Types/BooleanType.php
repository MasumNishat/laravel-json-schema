<?php

namespace Masum\JsonSchema\Types;

class BooleanType extends Type
{
    public function __construct()
    {
        $this->rules['type'] = 'boolean';
    }

    public function const(bool $value): static
    {
        $this->rules['const'] = $value;
        return $this;
    }
}