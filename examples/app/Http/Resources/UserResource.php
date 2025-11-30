<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Masum\JsonSchema\Schema;

class UserResource extends JsonResource
{
    /**
     * Define the JSON Schema for this resource
     */
    public static function schema()
    {
        return Schema::object()
            ->property('id', Schema::string()->format('uuid'))
            ->property('name', Schema::string()->minLength(1)->maxLength(255))
            ->property('email', Schema::string()->format('email'))
            ->property('created_at', Schema::string()->format('date-time'))
            ->property('updated_at', Schema::string()->format('date-time'))
            ->required(['id', 'name', 'email', 'created_at']);
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Conditional attributes
            'is_admin' => $this->when($request->user()?->isAdmin(), $this->is_admin),
            
            // Computed attributes
            'full_name' => $this->first_name . ' ' . $this->last_name,
            
            // Links
            'links' => [
                'self' => route('users.show', $this->id),
                'profile' => route('users.profile', $this->id),
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'schema' => self::schema()->toArray(),
                'author' => 'Your API',
            ],
        ];
    }
}