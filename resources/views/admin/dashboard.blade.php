@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <p class="page-subtitle">Snapshot of your content.</p>

    <div class="admin-stats">
        <div class="admin-stat">
            <div class="icon"><i class="fa-solid fa-newspaper"></i></div>
            <div class="label">Total Blogs</div>
            <div class="value">{{ $totalBlogs }}</div>
        </div>
        <div class="admin-stat">
            <div class="icon" style="background:#fef3c7;color:#b45309;"><i class="fa-solid fa-layer-group"></i></div>
            <div class="label">Categories</div>
            <div class="value">{{ $totalCategories }}</div>
        </div>
        <div class="admin-stat">
            <div class="icon" style="background:#fce7f3;color:#9d174d;"><i class="fa-solid fa-fire"></i></div>
            <div class="label">Most Viewed</div>
            <div class="value" style="font-size:16px;line-height:1.3;">{{ $mostViewed?->title ?? '—' }}</div>
            <div class="sub">{{ $mostViewed ? number_format($mostViewed->views) . ' views' : '' }}</div>
        </div>
        <div class="admin-stat">
            <div class="icon" style="background:#dcfce7;color:#15803d;"><i class="fa-solid fa-clock"></i></div>
            <div class="label">Latest Blog</div>
            <div class="value" style="font-size:16px;line-height:1.3;">{{ $latest?->title ?? '—' }}</div>
            <div class="sub">{{ $latest ? $latest->published_at->format('d M Y') : '' }}</div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <h2>Quick Actions</h2>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('admin.blogs.create') }}" class="jy-btn">
                <i class="fa-solid fa-plus"></i> New Blog
            </a>
            <a href="{{ route('admin.blogs.index') }}" class="jy-btn secondary">
                <i class="fa-solid fa-list"></i> Manage Blogs
            </a>
            <a href="{{ route('home') }}" class="jy-btn secondary" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Site
            </a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <h2>Blogs by Category</h2>
        </div>
        <div style="max-width:420px;margin:0 auto;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('categoryChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($byCategory->pluck('name')),
                datasets: [{
                    data: @json($byCategory->pluck('count')),
                    backgroundColor: @json($byCategory->pluck('color')),
                    borderColor: '#fff',
                    borderWidth: 2,
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
                cutout: '60%',
            }
        });
    </script>
@endpush
