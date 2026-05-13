@extends('layouts.public')

@section('title', $blog->title . ' · JobYaari Blogs')
@section('description', \Illuminate\Support\Str::limit($blog->short_description, 160))

@push('og')
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $blog->title }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($blog->short_description, 200) }}">
    <meta property="og:image" content="{{ $blog->image_url }}">
    <meta property="og:url" content="{{ route('blog.show', $blog->slug) }}">
    <meta name="twitter:card" content="summary_large_image">
@endpush

@section('content')
    <article class="jy-detail">
        <nav class="jy-breadcrumb">
            <a href="{{ route('home') }}">Home</a> &rsaquo;
            <a href="{{ route('home', ['category' => $blog->category->slug]) }}">{{ $blog->category->name }}</a> &rsaquo;
            <span>{{ \Illuminate\Support\Str::limit($blog->title, 50) }}</span>
        </nav>

        <img class="jy-detail-image" src="{{ $blog->image_url }}" alt="{{ $blog->title }}">

        <span class="jy-badge" style="background: {{ $blog->category->color }};">{{ $blog->category->name }}</span>
        <h1 style="margin-top:12px;">{{ $blog->title }}</h1>

        <div class="jy-detail-meta">
            <span><i class="fa-regular fa-calendar"></i> {{ $blog->published_at->format('d M Y') }}</span>
            <span><i class="fa-regular fa-eye"></i> {{ number_format($blog->views) }} views</span>
            <span><i class="fa-regular fa-clock"></i> {{ $blog->reading_time }} min read</span>
        </div>

        <div class="jy-content">
            {!! clean($blog->content) !!}
        </div>

        <div class="jy-share">
            <span style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--color-text-muted);font-weight:500;">
                <i class="fa-solid fa-share-nodes"></i> Share:
            </span>
            <a class="jy-share-btn whatsapp" target="_blank" rel="noopener"
               href="https://wa.me/?text={{ urlencode($blog->title . ' — ' . route('blog.show', $blog->slug)) }}">
                <i class="fa-brands fa-whatsapp"></i> WhatsApp
            </a>
            <a class="jy-share-btn twitter" target="_blank" rel="noopener"
               href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode(route('blog.show', $blog->slug)) }}">
                <i class="fa-brands fa-x-twitter"></i> Twitter
            </a>
            <a class="jy-share-btn linkedin" target="_blank" rel="noopener"
               href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $blog->slug)) }}">
                <i class="fa-brands fa-linkedin-in"></i> LinkedIn
            </a>
            <button type="button" class="jy-share-btn" id="copyLink" data-url="{{ route('blog.show', $blog->slug) }}">
                <i class="fa-solid fa-link"></i> <span class="label">Copy Link</span>
            </button>
        </div>

        @if ($related->isNotEmpty())
            <section class="jy-related">
                <h2>Related in {{ $blog->category->name }}</h2>
                <div class="jy-related-grid">
                    @foreach ($related as $r)
                        @include('public.partials.blog-card', ['blog' => $r])
                    @endforeach
                </div>
            </section>
        @endif
    </article>
@endsection

@push('scripts')
<script>
document.getElementById('copyLink')?.addEventListener('click', async function () {
    const label = this.querySelector('.label');
    try {
        await navigator.clipboard.writeText(this.dataset.url);
        const prev = label.textContent;
        label.textContent = 'Copied!';
        setTimeout(() => label.textContent = prev, 1500);
    } catch (e) {
        prompt('Copy this link:', this.dataset.url);
    }
});
</script>
@endpush
