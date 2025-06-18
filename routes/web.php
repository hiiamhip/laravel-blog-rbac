<?php

use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\CheckAdminRole;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return redirect()->route('posts.index');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware(CheckAdminRole::class)->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('posts', AdminPostController::class);
        Route::put('posts/{post}/approve', [AdminPostController::class, 'approve'])->name('posts.approve');
    });

    Route::resource('posts', PostController::class);
    Route::get('my-posts', [PostController::class, 'myPosts'])->name('my-posts');

    Route::resource('posts.comments', CommentController::class)->shallow();
});




require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
