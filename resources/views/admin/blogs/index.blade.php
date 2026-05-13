@extends('layouts.admin')

@section('title', 'Blogs')

@section('content')
    <h1>Blogs</h1>
    <p class="page-subtitle">Manage all blog posts.</p>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <form method="GET" action="{{ route('admin.blogs.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by title..." class="form-control" style="width:260px;">
                <select name="sort" class="form-control" style="width:auto;">
                    <option value="newest"  {{ $sort === 'newest'  ? 'selected' : '' }}>Newest</option>
                    <option value="oldest"  {{ $sort === 'oldest'  ? 'selected' : '' }}>Oldest</option>
                    <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Most Viewed</option>
                    <option value="title"   {{ $sort === 'title'   ? 'selected' : '' }}>Title (A–Z)</option>
                </select>
                <button type="submit" class="jy-btn secondary jy-btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
            </form>
            <a href="{{ route('admin.blogs.create') }}" class="jy-btn"><i class="fa-solid fa-plus"></i> New Blog</a>
        </div>

        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="adminBlogsTbody">
                    @forelse ($blogs as $blog)
                        <tr data-id="{{ $blog->id }}">
                            <td>
                                <img class="thumb" src="{{ $blog->image_url }}" alt="" loading="lazy">
                            </td>
                            <td>
                                <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" style="color:var(--color-text);font-weight:500;">
                                    {{ \Illuminate\Support\Str::limit($blog->title, 60) }}
                                </a>
                            </td>
                            <td>
                                <span class="jy-badge" style="background: {{ $blog->category->color }}">{{ $blog->category->name }}</span>
                            </td>
                            <td>{{ number_format($blog->views) }}</td>
                            <td>{{ $blog->published_at->format('d M Y') }}</td>
                            <td style="text-align:right;">
                                <div class="actions" style="justify-content:flex-end;">
                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="jy-btn secondary jy-btn-sm">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button type="button" class="jy-btn danger jy-btn-sm js-delete" data-id="{{ $blog->id }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--color-text-muted);padding:48px;">No blogs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;">
            {{ $blogs->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.js-delete', function () {
            const id = $(this).data('id');
            const row = $(`tr[data-id="${id}"]`);
            Swal.fire({
                title: 'Delete this blog?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it',
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/admin/blogs/${id}`,
                    method: 'DELETE',
                    success: function () {
                        row.addClass('removing');
                        setTimeout(() => row.remove(), 350);
                        Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false });
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not delete blog.' });
                    }
                });
            });
        });
    </script>
@endpush
