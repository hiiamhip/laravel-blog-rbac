<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $users = User::all();

        foreach ($posts as $post) {
            foreach ($users as $user) {
                Comment::create([
                    'content' => 'This is a comment by ' . $user->name,
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
            }
        }
    }
}
