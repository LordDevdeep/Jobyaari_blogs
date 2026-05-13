@php
    $publishedAt = $blog?->published_at ? $blog->published_at->format('Y-m-d\TH:i') : old('published_at', now()->format('Y-m-d\TH:i'));
@endphp

<div class="form-group">
    <label for="title">Title <span style="color:var(--color-danger)">*</span></label>
    <input type="text" id="title" name="title" class="form-control"
           value="{{ old('title', $blog?->title) }}" required maxlength="255">
    @error('title') <div class="form-error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="short_description">Short Description <span style="color:var(--color-danger)">*</span></label>
    <textarea id="short_description" name="short_description" class="form-control" rows="3"
              maxlength="300" required>{{ old('short_description', $blog?->short_description) }}</textarea>
    <div class="form-hint"><span id="shortCount">0</span> / 300 characters</div>
    @error('short_description') <div class="form-error">{{ $message }}</div> @enderror
</div>

<div class="form-row">
    <div class="form-group">
        <label for="category_id">Category <span style="color:var(--color-danger)">*</span></label>
        <select id="category_id" name="category_id" class="form-control" required>
            <option value="">— Select category —</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ (int) old('category_id', $blog?->category_id) === $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label for="published_at">Publish Date</label>
        <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="{{ $publishedAt }}">
        @error('published_at') <div class="form-error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label for="image">Featured Image <span class="form-hint" style="font-weight:400">(JPG/PNG/WEBP, max 2MB)</span></label>
    <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
    @error('image') <div class="form-error">{{ $message }}</div> @enderror

    <div class="image-preview" id="imagePreview" style="{{ $blog?->image ? '' : 'display:none' }}">
        @if ($blog?->image)
            <img src="{{ $blog->image_url }}" alt="Current image">
        @else
            <img src="" alt="Preview">
        @endif
    </div>
</div>

<div class="form-group">
    <label for="content">Content <span style="color:var(--color-danger)">*</span></label>
    <textarea id="content" name="content" class="form-control">{{ old('content', $blog?->content) }}</textarea>
    @error('content') <div class="form-error">{{ $message }}</div> @enderror
</div>
