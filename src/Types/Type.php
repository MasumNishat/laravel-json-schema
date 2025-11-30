<?php

namespace Masum\JsonSchema\Types;

abstract class Type
{
    protected array $rules = [];
    protected bool $isNullable = false;

    public function toArray(): array
    {
        $rules = $this->rules;

        if ($this->isNullable) {
            if (isset($rules['type'])) {
                $rules['type'] = [$rules['type'], 'null'];
            }
        }

        return $rules;
    }

    public function toJson(int $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES): string
    {
        return json_encode($this->toArray(), $flags);
    }

    public function nullable(): static
    {
        $this->isNullable = true;
        return $this;
    }

    public function description(string $description): static
    {
        $this->rules['description'] = $description;
        return $this;
    }

    public function title(string $title): static
    {
        $this->rules['title'] = $title;
        return $this;
    }

    public function default($value): static
    {
        $this->rules['default'] = $value;
        return $this;
    }

    public function examples(array $examples): static
    {
        $this->rules['examples'] = $examples;
        return $this;
    }

    public function comment(string $comment): static
    {
        $this->rules['$comment'] = $comment;
        return $this;
    }
}