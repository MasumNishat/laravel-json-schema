<?php

namespace Masum\JsonSchema\Types;

class ObjectType extends Type
{
    protected array $properties = [];
    protected array $required = [];

    public function __construct()
    {
        $this->rules['type'] = 'object';
        $this->rules['properties'] = [];
    }

    public function property(string $name, Type $type): static
    {
        $this->properties[$name] = $type;
        $this->rules['properties'][$name] = $type->toArray();
        return $this;
    }

    public function required(array $properties): static
    {
        $this->required = array_merge($this->required, $properties);
        $this->rules['required'] = array_values(array_unique($this->required));
        return $this;
    }

    public function additionalProperties(bool $allowed): static
    {
        $this->rules['additionalProperties'] = $allowed;
        return $this;
    }

    public function minProperties(int $min): static
    {
        $this->rules['minProperties'] = $min;
        return $this;
    }

    public function maxProperties(int $max): static
    {
        $this->rules['maxProperties'] = $max;
        return $this;
    }

    public function custom(string $key, $value): static
    {
        $this->rules[$key] = $value;
        return $this;
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        // Ensure properties is always an object in JSON
        if (empty($array['properties'])) {
            $array['properties'] = new \stdClass();
        }

        return $array;
    }
}