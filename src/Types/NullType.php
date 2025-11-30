<?php

namespace Masum\JsonSchema\Types;

class NullType extends Type
{
    public function __construct()
    {
        $this->rules['type'] = 'null';
    }
}