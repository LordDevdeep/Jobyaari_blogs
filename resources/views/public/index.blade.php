@extends('layouts.public')

@section('title', 'JobYaari Blogs — Latest Government Jobs, Admit Cards, Results')
@section('description', 'Browse latest government job notifications, admit cards, results, answer keys and syllabi for Indian aspirants.')

@section('content')
    <section class="jy-hero">
        <h1>JobYaari Blogs</h1>
        <p>Latest government job notifications, admit cards, results & syllabi — curated for Indian aspirants.</p>
        <div class="jy-search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input id="searchInput" type="search" class="jy-search-input"
                   placeholder="Search for SSC, UPSC, IBPS, Railway..." value="{{ $filters['search'] }}">
        </div>
    </section>

    <section class="jy-filter-bar">
        <div class="container">
            <div class="jy-pills" id="categoryPills">
                <button type="button" class="jy-pill js-category {{ $filters['category'] === 'all' ? 'active' : '' }}" data-slug="all">All</button>
                @foreach ($categories as $cat)
                    <button type="button" class="jy-pill js-category {{ $filters['category'] === $cat->slug ? 'active' : '' }}"
                            data-slug="{{ $cat->slug }}"
                            style="{{ $filters['category'] === $cat->slug ? 'background:'.$cat->color.';border-color:'.$cat->color.';color:#fff;' : '' }}">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
            <div class="jy-dropdowns">
                <select id="dateRange" class="jy-select">
                    <option value="all"   {{ $filters['date_range'] === 'all'   ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $filters['date_range'] === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week"  {{ $filters['date_range'] === 'week'  ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $filters['date_range'] === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year"  {{ $filters['date_range'] === 'year'  ? 'selected' : '' }}>This Year</option>
                </select>
                <select id="sortBy" class="jy-select">
                    <option value="newest"  {{ $filters['sort'] === 'newest'  ? 'selected' : '' }}>Newest</option>
                    <option value="oldest"  {{ $filters['sort'] === 'oldest'  ? 'selected' : '' }}>Oldest</option>
                    <option value="popular" {{ $filters['sort'] === 'popular' ? 'selected' : '' }}>Most Viewed</option>
                </select>
            </div>
        </div>
    </section>

    <section class="jy-grid-section">
        <div class="jy-loader-overlay" id="gridLoader"><div class="jy-spinner"></div></div>

        <div id="resultsMeta" style="color:var(--color-text-muted);font-size:14px;margin-bottom:16px;">
            Showing {{ $blogs->count() }} of {{ $blogs->total() }} {{ \Illuminate\Support\Str::plural('blog', $blogs->total()) }}
        </div>

        <div class="jy-grid" id="blogGrid">
            @forelse ($blogs as $blog)
                @include('public.partials.blog-card', ['blog' => $blog])
            @empty
                <div class="jy-empty" style="grid-column:1/-1;">
                    <i class="fa-regular fa-folder-open"></i>
                    <h3>No blogs yet</h3>
                    <p>Check back soon, or visit the admin to add the first post.</p>
                </div>
            @endforelse
        </div>

        <div id="paginationContainer">
            @include('public.partials.pagination', ['paginator' => $blogs])
        </div>

        <div class="jy-error" id="errorState" style="display:none;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <h3>Couldn't load blogs</h3>
            <p>Please check your connection and try again.</p>
            <button type="button" class="jy-btn secondary" id="retryBtn"><i class="fa-solid fa-rotate-right"></i> Retry</button>
        </div>

        @if ($recentlyViewed->isNotEmpty())
            <div class="jy-recently" style="margin-top:48px;">
                <h3><i class="fa-regular fa-clock"></i> Recently viewed</h3>
                <ul>
                    @foreach ($recentlyViewed as $r)
                        <li><a href="{{ route('blog.show', $r->slug) }}">{{ $r->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
<script>
$(function () {
    const $grid       = $('#blogGrid');
    const $pagination = $('#paginationContainer');
    const $loader     = $('#gridLoader');
    const $meta       = $('#resultsMeta');
    const $error      = $('#errorState');
    const $searchInp  = $('#searchInput');

    const state = {
        category:   @json($filters['category']),
        date_range: @json($filters['date_range']),
        sort:       @json($filters['sort']),
        search:     @json($filters['search']),
        page:       1,
    };

    // Tiny self-rolled debounce (~5 lines).
    function debounce(fn, ms) {
        let t;
        return function (...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), ms);
        };
    }

    function buildQuery() {
        const p = new URLSearchParams();
        if (state.category && state.category !== 'all') p.set('category', state.category);
        if (state.date_range && state.date_range !== 'all') p.set('date_range', state.date_range);
        if (state.sort && state.sort !== 'newest') p.set('sort', state.sort);
        if (state.search) p.set('search', state.search);
        if (state.page > 1) p.set('page', state.page);
        return p.toString();
    }

    function syncUrl() {
        const qs = buildQuery();
        const newUrl = window.location.pathname + (qs ? '?' + qs : '');
        history.pushState(state, '', newUrl);
    }

    function showLoader(show) { $loader.toggleClass('show', show); }

    function fetchBlogs() {
        showLoader(true);
        $error.hide();
        $.ajax({
            url: '{{ route('api.blogs.filter') }}',
            data: state,
            dataType: 'json',
        }).done(function (res) {
            if (!res.html.trim()) {
                $grid.html(`
                    <div class="jy-empty" style="grid-column:1/-1;">
                        <i class="fa-regular fa-face-frown"></i>
                        <h3>No matching blogs</h3>
                        <p>Try a different filter or search term.</p>
                        <button type="button" class="jy-btn" id="clearFilters"><i class="fa-solid fa-broom"></i> Clear filters</button>
                    </div>
                `);
            } else {
                $grid.html(res.html);
            }
            $pagination.html(res.pagination);
            const shown = (res.html.match(/class="jy-card"/g) || []).length;
            $meta.text(`Showing ${shown} of ${res.total} ${res.total === 1 ? 'blog' : 'blogs'}`);
            syncUrl();
        }).fail(function () {
            $grid.empty();
            $pagination.empty();
            $error.show();
        }).always(function () {
            showLoader(false);
        });
    }

    // Category pill click
    $(document).on('click', '.js-category', function () {
        state.category = $(this).data('slug');
        state.page = 1;
        $('.js-category').removeClass('active').removeAttr('style');
        $(this).addClass('active');
        const slug = state.category;
        if (slug !== 'all') {
            const color = @json($categories->pluck('color', 'slug'));
            if (color[slug]) $(this).css({ background: color[slug], borderColor: color[slug], color: '#fff' });
        }
        fetchBlogs();
    });

    $('#dateRange').on('change', function () { state.date_range = this.value; state.page = 1; fetchBlogs(); });
    $('#sortBy').on('change',    function () { state.sort       = this.value; state.page = 1; fetchBlogs(); });

    $searchInp.on('input', debounce(function () {
        state.search = $(this).val().trim();
        state.page = 1;
        fetchBlogs();
    }, 300));

    $(document).on('click', '.js-page', function () {
        const p = parseInt($(this).data('page'), 10);
        if (isNaN(p) || p < 1) return;
        state.page = p;
        fetchBlogs();
        $('html, body').animate({ scrollTop: $('.jy-filter-bar').offset().top - 60 }, 200);
    });

    $(document).on('click', '#clearFilters', function () {
        state.category = 'all';
        state.date_range = 'all';
        state.sort = 'newest';
        state.search = '';
        state.page = 1;
        $searchInp.val('');
        $('#dateRange').val('all');
        $('#sortBy').val('newest');
        $('.js-category').removeClass('active').removeAttr('style');
        $('.js-category[data-slug="all"]').addClass('active');
        fetchBlogs();
    });

    $('#retryBtn').on('click', fetchBlogs);

    window.addEventListener('popstate', function (e) {
        if (e.state) {
            Object.assign(state, e.state);
            $searchInp.val(state.search);
            $('#dateRange').val(state.date_range);
            $('#sortBy').val(state.sort);
            $('.js-category').removeClass('active').removeAttr('style');
            $(`.js-category[data-slug="${state.category}"]`).addClass('active');
            fetchBlogs();
        }
    });
});
</script>
@endpush
