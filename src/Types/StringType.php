<?php

namespace Masum\JsonSchema\Types;

class StringType extends Type
{
    public function __construct()
    {
        $this->rules['type'] = 'string';
    }

    public function minLength(int $min): static
    {
        $this->rules['minLength'] = $min;
        return $this;
    }

    public function maxLength(int $max): static
    {
        $this->rules['maxLength'] = $max;
        return $this;
    }

    public function pattern(string $pattern): static
    {
        $this->rules['pattern'] = $pattern;
        return $this;
    }

    public function format(string $format): static
    {
        $this->rules['format'] = $format;
        return $this;
    }

    public function enum(array $values): static
    {
        $this->rules['enum'] = $values;
        return $this;
    }

    public function const($value): static
    {
        $this->rules['const'] = $value;
        return $this;
    }
}