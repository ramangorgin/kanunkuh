<?php

/**
 * Post model for blog content and SEO metadata.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Represents a blog post with publishing rules and SEO helpers.
 */
class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_alt',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'canonical_url',
        'status',
        'published_at',
        'is_indexable',
        'is_followable',
        'view_count',
        'reading_time',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_indexable' => 'boolean',
        'is_followable' => 'boolean',
    ];

    /**
     * Configure model events for slugging and SEO defaults.
     */
    protected static function booted()
    {
        static::saving(function (Post $post) {
            $post->slug = static::generateUniqueSlug($post->slug ?: $post->title, $post->id);

            // Prevent publishing if slug is not unique
            $slugExists = static::withTrashed()
                ->where('slug', $post->slug)
                ->when($post->id, fn($query) => $query->where('id', '!=', $post->id))
                ->exists();

            if ($post->status === 'published' && $slugExists) {
                throw ValidationException::withMessages([
                    'slug' => __('The slug must be unique before publishing.'),
                ]);
            }

            $post->reading_time = $post->calculateReadingTime($post->content);

            // SEO fallbacks to keep meta tags populated
            $post->seo_title = $post->seo_title ?: $post->title;
            $post->seo_description = $post->seo_description ?: $post->generateSeoDescription();
            $post->canonical_url = $post->canonical_url ?: $post->generateCanonicalUrl();
        });
    }

    /**
     * Get the author of the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get categories assigned to the post.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Scope query to published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Generate a unique slug for the post.
     */
    protected static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $counter = 1;

        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug ?: Str::random(8);
    }

    /**
     * Estimate reading time in minutes based on content length.
     */
    protected function calculateReadingTime(?string $content): ?int
    {
        if (!$content) {
            return null;
        }

        $wordCount = str_word_count(strip_tags($content));

        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * Build a fallback SEO description from excerpt or content.
     */
    protected function generateSeoDescription(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $this->content))), 155, '');
    }

    /**
     * Build a canonical URL for the post.
     */
    protected function generateCanonicalUrl(): string
    {
        $baseUrl = rtrim(config('app.url'), '/');

        return $baseUrl.'/blog/'.$this->slug;
    }
}
