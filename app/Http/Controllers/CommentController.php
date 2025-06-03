<?php

namespace App\Http\Controllers;

use App\Models\Comment as ModelsComment;
use App\Models\Post;
use Dom\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);
        return back()->with('Success', 'Comment added successfully');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = ModelsComment::findOrFail($id);
        Gate::authorize('delete', $comment);
        $comment->delete();
        return back()->with('Success', 'Comment deleted successfully');
    }
}
