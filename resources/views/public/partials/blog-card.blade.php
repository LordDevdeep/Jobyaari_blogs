<article class="jy-card">
    <a href="{{ route('blog.show', $blog->slug) }}" class="jy-card-image">
        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" loading="lazy">
    </a>
    <div class="jy-card-body">
        <span class="jy-badge" style="background: {{ $blog->category->color }};">{{ $blog->category->name }}</span>
        <h3 class="jy-card-title">
            <a href="{{ route('blog.show', $blog->slug) }}" style="color:inherit;">{{ $blog->title }}</a>
        </h3>
        <p class="jy-card-desc">{{ \Illuminate\Support\Str::limit($blog->short_description, 120) }}</p>
        <a href="{{ route('blog.show', $blog->slug) }}" class="jy-read-more">Read More <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i></a>
        <div class="jy-card-meta">
            <span><i class="fa-regular fa-calendar"></i> {{ $blog->published_at->format('d M Y') }}</span>
            <span><i class="fa-regular fa-eye"></i> {{ number_format($blog->views) }}</span>
        </div>
    </div>
</article>
