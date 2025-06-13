<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
class PostController extends BaseController
{
    use AuthorizesRequests;

    public function __construct() {
        $this->authorizeResource(Post::class, 'post');
    }

    public function index(Request $request)
    {
        $posts = Post::query()
            ->onlyPublished()
            ->withCategory()
            ->withComment()
            ->withCommentCount()
            ->filterSearch($request)
            ->filterCategory($request)
            ->sortByPreference($request)
            ->paginate(10);

        $categories = Category::all();

        $filter = array_merge([
        'search' => '',
        'category' => 'all',
        'sort' => 'latest'
        ], $request->only(['search', 'category', 'sort']));

        return Inertia::render('user/posts', [
            'posts' => $posts,
            'categories' => $categories,
            'filter' => $filter
        ]);
    }


    public function myPosts() {
        $posts = auth()->user()->posts()->withCategory()->withCommentAndCommenter()->withCommentCount()->latest()->paginate(10);
        $categories = Category::all();

        return Inertia::render('user/myposts', ['categories' => $categories, 'posts' => $posts]);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        Post::createWithMeta($request->validated());
        return redirect()->route('posts.create')->with('Success', 'Post added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['category', 'user', 'comments.user'])->loadCount('comments');

        return Inertia::render('user/posts/show', ['post' => $post]);
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
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->updateWithMeta($request->validated());
        return redirect()->route('posts.create')->with('Success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.create')->with('Success', 'Post deleted successfully');
    }
}
