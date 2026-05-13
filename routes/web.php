<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Public\BlogController as PublicBlogController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [PublicBlogController::class, 'index'])->name('home');
Route::get('/blog/{slug}', [PublicBlogController::class, 'show'])->name('blog.show');
Route::get('/api/blogs/filter', [PublicBlogController::class, 'filter'])->name('api.blogs.filter');

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/blogs', [AdminBlogController::class, 'index'])->name('blogs.index');
        Route::get('/blogs/create', [AdminBlogController::class, 'create'])->name('blogs.create');
        Route::post('/blogs', [AdminBlogController::class, 'store'])->name('blogs.store');
        Route::get('/blogs/{id}/edit', [AdminBlogController::class, 'edit'])->name('blogs.edit');
        Route::put('/blogs/{id}', [AdminBlogController::class, 'update'])->name('blogs.update');
        Route::delete('/blogs/{id}', [AdminBlogController::class, 'destroy'])->name('blogs.destroy');
        Route::post('/blogs/upload-image', [AdminBlogController::class, 'uploadInlineImage'])->name('blogs.upload-image');
    });
});
