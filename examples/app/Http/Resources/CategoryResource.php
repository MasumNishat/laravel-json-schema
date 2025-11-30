<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Masum\JsonSchema\Schema;

class CategoryResource extends JsonResource
{
    public static function schema()
    {
        return Schema::object()
            ->property('id', Schema::string()->format('uuid'))
            ->property('name', Schema::string())
            ->required(['id', 'name']);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}