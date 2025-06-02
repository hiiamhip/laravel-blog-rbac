<?php

use App\Http\Controllers\CategoryController;
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
    });
});

Route::prefix('admin')->name('admin.')->middleware(CheckAdminRole::class)->group(function () {
    Route::resource('categories', CategoryController::class)->except('show');
});

Route::resource('posts', PostController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
