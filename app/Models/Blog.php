<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'content',
        'image',
        'category_id',
        'views',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($words / 200));
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return 'https://via.placeholder.com/800x450/e2e8f0/64748b?text=JobYaari';
        }
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        return asset('uploads/blogs/' . ltrim($this->image, '/'));
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;
        while (
            self::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
