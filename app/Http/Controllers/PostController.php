<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['category', 'user', 'comments.user'])->withCount('comments')->latest()->paginate(10);
        $categories = Category::all();
        return Inertia::render('posts', ['posts' => $posts, 'categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = auth()->id();
        $posts = Post::with(['category', 'user', 'comments.user'])->where('user_id', $userId)->withCount('comments')->latest()->paginate(10);
        $categories = Category::all();
        return Inertia::render('myposts', ['categories' => $categories, 'posts' => $posts]);
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
            'published_at' => 'nullable|date',
        ]);
        if ($request->filled('published_at')) {
            $validated['published_at'] = Carbon::parse($request->published_at)->format('Y-m-d H:i:s');
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
        //
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
    public function update(Request $request, string $id)
    {
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
        $post = Post::findOrFail($id);
        $post->update($validated);
        return redirect()->route('posts.create')->with('Success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
