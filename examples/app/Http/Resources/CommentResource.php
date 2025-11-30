<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Masum\JsonSchema\Schema;

class CommentResource extends JsonResource
{
    public static function schema()
    {
        return Schema::object()
            ->property('id', Schema::string()->format('uuid'))
            ->property('post_id', Schema::string()->format('uuid'))
            ->property('user_id', Schema::string()->format('uuid'))
            ->property('content', Schema::string())
            ->required(['id', 'post_id', 'user_id', 'content']);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'content' => $this->content,
        ];
    }
}