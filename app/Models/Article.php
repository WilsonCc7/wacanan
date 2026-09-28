<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $body
 * @property string $category
 * @property string|null $cover_image
 * @property string|null $author_name
 * @property int $likes_count
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property int|null $user_id
 */
class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    public const CATEGORIES = ['Design', 'Technology', 'Business', 'Culture', 'Music'];

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'category',
        'cover_image',
        'author_name',
        'likes_count',
        'published_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'likes_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // ponytail: no per-user auth yet, so likes are tracked in session only.
        static::creating(function (self $article) {
            if (! $article->slug) {
                $slug = Str::slug($article->title);
                $article->slug = static::where('slug', $slug)->exists()
                    ? $slug.'-'.Str::random(6)
                    : $slug;
            }
        });
    }

    /**
     * Articles that are visible to the public.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Resolves a stored cover path to a usable URL: seeded rows keep a full
     * remote URL, uploads store a path relative to the public disk.
     */
    public function coverUrl(): string
    {
        if (! $this->cover_image) {
            return '';
        }

        return str_starts_with($this->cover_image, 'http')
            ? $this->cover_image
            : \Illuminate\Support\Facades\Storage::disk('public')->url($this->cover_image);
    }
}
