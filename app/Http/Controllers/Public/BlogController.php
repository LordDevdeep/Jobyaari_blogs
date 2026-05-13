<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Services\BlogFilterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(private BlogFilterService $filterService)
    {
    }

    public function index(Request $request): View
    {
        $categories = Category::orderBy('id')->get();
        $blogs = $this->filterService->apply($request->only(['category', 'date_range', 'sort', 'search', 'page']));

        $recentlyViewedIds = collect(session('recently_viewed', []))->take(5)->all();
        $recentlyViewed = $recentlyViewedIds
            ? Blog::with('category')->whereIn('id', $recentlyViewedIds)->get()
                ->sortBy(fn ($b) => array_search($b->id, $recentlyViewedIds))
                ->values()
            : collect();

        $filters = [
            'category'   => $request->query('category', 'all'),
            'date_range' => $request->query('date_range', 'all'),
            'sort'       => $request->query('sort', 'newest'),
            'search'     => $request->query('search', ''),
        ];

        return view('public.index', compact('blogs', 'categories', 'filters', 'recentlyViewed'));
    }

    public function show(string $slug, Request $request): View
    {
        $blog = Blog::with('category')->where('slug', $slug)->firstOrFail();

        $viewed = session('viewed_blogs', []);
        if (! in_array($blog->id, $viewed, true)) {
            DB::table('blogs')->where('id', $blog->id)->increment('views');
            $viewed[] = $blog->id;
            session(['viewed_blogs' => $viewed]);
            $blog->views++;
        }

        $recent = session('recently_viewed', []);
        $recent = array_values(array_unique(array_merge([$blog->id], array_diff($recent, [$blog->id]))));
        $recent = array_slice($recent, 0, 5);
        session(['recently_viewed' => $recent]);

        $related = Blog::with('category')
            ->where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('public.show', compact('blog', 'related'));
    }

    public function filter(Request $request): JsonResponse
    {
        $paginator = $this->filterService->apply($request->only(['category', 'date_range', 'sort', 'search', 'page']));

        $html = '';
        foreach ($paginator->items() as $blog) {
            $html .= view('public.partials.blog-card', ['blog' => $blog])->render();
        }

        $pagination = view('public.partials.pagination', ['paginator' => $paginator])->render();

        return response()->json([
            'html'       => $html,
            'pagination' => $pagination,
            'total'      => $paginator->total(),
            'page'       => $paginator->currentPage(),
            'has_more'   => $paginator->hasMorePages(),
        ]);
    }
}
