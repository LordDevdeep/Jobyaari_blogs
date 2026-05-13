<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\Category;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(private ImageUploadService $images)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'newest');

        $query = Blog::with('category');
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where('title', 'like', $like);
        }
        match ($sort) {
            'oldest'  => $query->orderBy('published_at', 'asc'),
            'popular' => $query->orderByDesc('views'),
            'title'   => $query->orderBy('title'),
            default   => $query->orderByDesc('published_at'),
        };

        $blogs = $query->paginate(15)->withQueryString();

        return view('admin.blogs.index', compact('blogs', 'search', 'sort'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['content'] = clean($data['content']);
        $data['slug'] = Blog::generateUniqueSlug($data['title']);
        $data['published_at'] = $data['published_at'] ?? now();

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->storeBlogImage($request->file('image'), $data['slug']);
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully.');
    }

    public function edit(int $id): View
    {
        $blog = Blog::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(UpdateBlogRequest $request, int $id): RedirectResponse
    {
        $blog = Blog::findOrFail($id);
        $data = $request->validated();
        $data['content'] = clean($data['content']);

        if ($data['title'] !== $blog->title) {
            $data['slug'] = Blog::generateUniqueSlug($data['title'], $blog->id);
        }

        if ($request->hasFile('image')) {
            $this->images->deleteBlogImage($blog->image);
            $data['image'] = $this->images->storeBlogImage(
                $request->file('image'),
                $data['slug'] ?? $blog->slug
            );
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $blog = Blog::findOrFail($id);
        $this->images->deleteBlogImage($blog->image);
        $blog->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted.');
    }

    public function uploadInlineImage(Request $request): JsonResponse
    {
        $request->validate([
            'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $filename = $this->images->storeBlogImage(
            $request->file('upload'),
            'inline-' . Str::random(6)
        );

        return response()->json([
            'url' => asset('uploads/blogs/' . $filename),
        ]);
    }
}
