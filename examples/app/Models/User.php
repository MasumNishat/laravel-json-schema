<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Example relationships (you would define these in a real application)
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Dummy method for example purposes
    public function isAdmin(): bool
    {
        return Str::contains($this->email, 'admin');
    }

    // Dummy attributes for example purposes
    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return $parts[1] ?? '';
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }
}

// Minimal placeholder for Profile, Role, Post classes if needed by examples
class Profile extends \Illuminate\Database\Eloquent\Model { public $timestamps = false; protected $fillable = ['bio', 'website', 'location', 'birth_date', 'avatar_url', 'social_links']; }
class Role extends \Illuminate\Database\Eloquent\Model { public $timestamps = false; protected $fillable = ['name']; }
class Post extends \Illuminate\Database\Eloquent\Model { public $timestamps = false; protected $fillable = ['title', 'content', 'slug', 'status', 'published_at', 'author_id']; public function author() { return $this->belongsTo(User::class); } public function categories() { return $this->belongsToMany(Category::class); } public function comments() { return $this->hasMany(Comment::class); } public function tags() { return $this->belongsToMany(Tag::class); } public function scopePublished($query) { return $query->where('status', 'published'); } }
class Category extends \Illuminate\Database\Eloquent\Model { public $timestamps = false; protected $fillable = ['name']; }
class Comment extends \Illuminate\Database\Eloquent\Model { public $timestamps = false; protected $fillable = ['content', 'post_id', 'user_id']; }
class Tag extends \Illuminate\Database\Eloquent\Model { public $timestamps = false; protected $fillable = ['name']; }
