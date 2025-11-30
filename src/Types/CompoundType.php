<?php

namespace Masum\JsonSchema\Types;

class CompoundType extends Type
{
    public function anyOf(Type ...$types): static
    {
        $this->rules['anyOf'] = array_map(fn($type) => $type->toArray(), $types);
        return $this;
    }

    public function oneOf(Type ...$types): static
    {
        $this->rules['oneOf'] = array_map(fn($type) => $type->toArray(), $types);
        return $this;
    }

    public function allOf(Type ...$types): static
    {
        $this->rules['allOf'] = array_map(fn($type) => $type->toArray(), $types);
        return $this;
    }

    public function not(Type $type): static
    {
        $this->rules['not'] = $type->toArray();
        return $this;
    }

    public function toArray(): array
    {
        return $this->rules;
    }
}