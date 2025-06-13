<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'user_id',
        'slug',
        'published_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeWithCategory($query) {
        return $query->with('category');
    }

    public function scopeWithComment($query) {
        return $query->with('comments');
    }

    public function scopeWithCommentCount($query) {
        return $query->withCount('comments');
    }

    public function scopeOnlyPublished($query) {
        return $query->whereNotNull('published_at');
    }

    public function scopeFilterSearch($query, $request)
    {
        return $query->when($request->filled('search'), function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        });
    }

    public function scopeFilterCategory($query, $request)
    {
        return $query->when($request->filled('category') && $request->category !== 'all', function ($query) use ($request) {
            $query->where('category_id', (int) $request->category);
        });
    }

    public function scopeWithAuthor($query) {
        return $query->with('user');
    }

    public function scopeWithCommentAndCommenter($query) {
        return $query->with('comments.user');
    }


    public function scopeSortByPreference($query, $request)
    {
        return $query->when(
            $request->sort === 'most_commented',
            fn($query) => $query->orderBy('comments_count', 'desc'),
            fn($query) => $query->latest()
        );
    }

    public static function createWithMeta(array $data) {
        $post = new self($data);

        $post->slug = Str::slug($data['title']);
        if (auth()->user()->role === 'admin' && $post->published_at === null) {
            $post->published_at = now();
        }

        $post->user()->associate(auth()->user());

        $post->save();

        return $post;
    }

    public function updateWithMeta(array $data) {
        if (array_key_exists('title', $data)) {
            $this->slug = Str::slug($data['title']);
        }

        $this->fill($data);
        return $this->save();
    }
}
