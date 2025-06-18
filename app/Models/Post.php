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

    public function setTitleAttribute($value) {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getPublishedAtAttribute() {
        return $this->attributes['published_at'] ? Carbon::parse($this->attributes['published_at']) : null;
    }

    public function setPublished() {
        $this->attributes['published_at'] = Carbon::now();
    }

    public function publishIfAdmin() {
        if (auth()->user()->role === 'admin') {
            $this->setPublished();
        }
    }
}
