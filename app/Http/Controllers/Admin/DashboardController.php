<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBlogs = Blog::count();
        $totalCategories = Category::count();
        $mostViewed = Blog::with('category')->orderByDesc('views')->first();
        $latest = Blog::with('category')->orderByDesc('published_at')->first();

        $byCategory = Category::withCount('blogs')->get()
            ->map(fn ($c) => [
                'name' => $c->name,
                'count' => $c->blogs_count,
                'color' => $c->color,
            ]);

        return view('admin.dashboard', compact(
            'totalBlogs',
            'totalCategories',
            'mostViewed',
            'latest',
            'byCategory'
        ));
    }
}
