<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Masum\JsonSchema\Schema;

class PostResource extends JsonResource
{
    /**
     * JSON Schema for Post
     */
    public static function schema()
    {
        return Schema::object()
            ->property('id', Schema::string()->format('uuid'))
            ->property('title', Schema::string()->minLength(1)->maxLength(255))
            ->property('content', Schema::string()->minLength(1))
            ->property('status', Schema::string()->enum(['draft', 'published', 'archived']))
            ->property('author', UserResource::schema()->nullable())
            ->property('tags', Schema::array()->items(Schema::string()))
            ->property('created_at', Schema::string()->format('date-time'))
            ->property('updated_at', Schema::string()->format('date-time'))
            ->required(['id', 'title', 'content', 'status', 'created_at']);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->when($request->is('api/posts'), function () {
                return str($this->content)->limit(150);
            }),
            'status' => $this->status,
            'published_at' => $this->when($this->published_at, function () {
                return $this->published_at->toISOString();
            }),
            
            // Relationships
            'author' => new UserResource($this->whenLoaded('author')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->pluck('name');
            }),
            
            // Metadata
            'reading_time' => $this->reading_time,
            'word_count' => $this->word_count,
            
            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Links
            'links' => [
                'self' => route('posts.show', $this->slug),
                'author' => route('users.show', $this->author_id),
                'comments' => route('posts.comments.index', $this->slug),
            ],
        ];
    }

    /**
     * Add additional metadata to the resource response.
     */
    public function with(Request $request): array
    {
        $includes = [];
        
        if ($this->relationLoaded('author')) {
            $includes['author'] = UserResource::schema()->toArray();
        }
        
        if ($this->relationLoaded('categories')) {
            $includes['categories'] = CategoryResource::schema()->toArray();
        }

        return [
            'meta' => [
                'schema' => self::schema()->toArray(),
                'includes' => $includes,
                'version' => '1.0',
            ],
        ];
    }
}