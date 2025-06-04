<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $users = User::all();

        foreach ($categories as $category) {
            foreach ($users as $user) {
                Post::create([
                    'title' => "Post by {$user->name} in {$category->name}",
                    'slug' => Str::slug("Post by {$user->name} in {$category->name}-" . uniqid()),
                    'content' => 'This is a seeded post content.',
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'published_at' => now(),
                ]);
                Post::create([
                    'title' => "Post by {$user->name} in {$category->name}",
                    'slug' => Str::slug("Post by {$user->name} in {$category->name}-" . uniqid()),
                    'content' => 'This is a seeded post content.',
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'published_at' => null,
                ]);
            }
        }
    }
}
