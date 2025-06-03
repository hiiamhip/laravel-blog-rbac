<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with(['category', 'comments'])
            ->withCount('comments')
            ->whereNotNull('published_at')
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->when($request->filled('category') && $request->category !== 'all', function ($query) use ($request) {
                $query->where('category_id', (int) $request->category);
            })
            ->when($request->sort === 'most_commented',
                function ($query) {
                    $query->orderBy('comments_count', 'desc');
                },
                function ($query) {
                    $query->latest();
                }
        );
        $posts = $query->paginate(10)->through(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title ?? '',
                'content' => $post->content ?? '',
                'category' => [
                    'id' => $post->category?->id ?? 0,
                    'name' => $post->category?->name ?? '',
                ],
                'comments_count' => $post->comments_count,
                'created_at' => $post->created_at?->toIsoString() ?? '',
            ];
        });
        $categories = Category::all()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        });
        return Inertia::render('user/posts', [
            'posts' => $posts,
            'categories' => $categories,
            'filter' => array_merge([
                'search' => '',
                'category' => 'all',
                'sort' => 'latest'
            ], $request->only(['search', 'category', 'sort']))
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = auth()->id();
        $posts = Post::with(['category', 'user', 'comments.user'])->where('user_id', $userId)->withCount('comments')->latest()->paginate(10);
        $categories = Category::all();
        return Inertia::render('user/myposts', ['categories' => $categories, 'posts' => $posts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);
        $user = auth()->user();
        if ($user->role === 'admin') {
            $validated['published_at'] = Carbon::now();
        }
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($request['title']);
        Post::create($validated);
        return redirect()->route('posts.create')->with('Success', 'Post added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::with(['category', 'user', 'comments.user'])->withCount('comments')->findOrFail($id);
        return Inertia::render('user/posts/show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title ?? '',
                'content' => $post->content ?? '',
                'category' => [
                    'id' => $post->category?->id ?? 0,
                    'name' => $post->category?->name ?? '',
                ],
                'user' => [
                    'id' => $post->user?->id ?? 0,
                    'name' => $post->user?->name ?? '',
                ],
                'comments' => $post->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content ?? '',
                        'user' => [
                            'id' => $comment->user?->id ?? 0,
                            'name' => $comment->user?->name ?? '',
                        ],
                        'created_at' => $comment->created_at?->toIsoString() ?? '',
                    ];
                }),
                'comments_count' => $post->comments_count,
                'created_at' => $post->created_at?->toIsoString() ?? '',
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
        ]);
        if ($request->filled('published_at')) {
            $validated['published_at'] = Carbon::parse($request->published_at)->format('Y-m-d H:i:s');
        }
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($request['title']);
        $post->update($validated);
        return redirect()->route('posts.create')->with('Success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        $post->delete();
        return redirect()->route('posts.create')->with('Success', 'Post deleted successfully');
    }
}
