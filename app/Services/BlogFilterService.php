<?php

namespace App\Services;

use App\Models\Blog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class BlogFilterService
{
    public const PER_PAGE = 9;

    /** @param array<string,mixed> $filters */
    public function apply(array $filters): LengthAwarePaginator
    {
        $category = $filters['category'] ?? 'all';
        $dateRange = $filters['date_range'] ?? 'all';
        $sort = $filters['sort'] ?? 'newest';
        $search = trim((string)($filters['search'] ?? ''));
        $perPage = (int)($filters['per_page'] ?? self::PER_PAGE);

        $query = Blog::query()
            ->with('category')
            ->where('published_at', '<=', now());

        if ($category && $category !== 'all') {
            $query->whereHas('category', fn (Builder $q) => $q->where('slug', $category));
        }

        $this->applyDateRange($query, $dateRange);
        $this->applySearch($query, $search);
        $this->applySort($query, $sort);

        return $query->paginate($perPage)->withQueryString();
    }

    private function applyDateRange(Builder $query, string $range): void
    {
        $now = Carbon::now();
        match ($range) {
            'today' => $query->whereDate('published_at', $now->toDateString()),
            'week'  => $query->where('published_at', '>=', $now->copy()->startOfWeek()),
            'month' => $query->where('published_at', '>=', $now->copy()->startOfMonth()),
            'year'  => $query->where('published_at', '>=', $now->copy()->startOfYear()),
            default => null,
        };
    }

    private function applySearch(Builder $query, string $search): void
    {
        if ($search === '') {
            return;
        }

        // Fulltext requires >= 3 chars; below that fall back to LIKE.
        if (mb_strlen($search) >= 3 && config('database.default') === 'mysql') {
            $term = addslashes($search);
            $query->whereRaw(
                'MATCH(title, short_description) AGAINST (? IN BOOLEAN MODE)',
                [$term . '*']
            );
        } else {
            $like = '%' . $search . '%';
            $query->where(function (Builder $q) use ($like) {
                $q->where('title', 'like', $like)
                  ->orWhere('short_description', 'like', $like);
            });
        }
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest'  => $query->orderBy('published_at', 'asc'),
            'popular' => $query->orderBy('views', 'desc')->orderBy('published_at', 'desc'),
            default   => $query->orderBy('published_at', 'desc'),
        };
    }
}
