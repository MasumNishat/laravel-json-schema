<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Masum\JsonSchema\Schema;

class ProfileResource extends JsonResource
{
    public static function schema()
    {
        return Schema::object()
            ->property('id', Schema::string()->format('uuid'))
            ->property('user_id', Schema::string()->format('uuid'))
            ->property('bio', Schema::string()->maxLength(1000)->nullable())
            ->property('website', Schema::string()->format('uri')->nullable())
            ->property('location', Schema::string()->maxLength(100)->nullable())
            ->property('birth_date', Schema::string()->format('date')->nullable())
            ->property('avatar_url', Schema::string()->format('uri')->nullable())
            ->required(['id', 'user_id']);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'bio' => $this->bio,
            'website' => $this->website,
            'location' => $this->location,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'avatar_url' => $this->avatar_url,
            'social_links' => $this->social_links ?? [],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}