<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'seo_title',
        'seo_description',
    ];

    protected static function booted()
    {
        static::saving(function (Category $category) {
            $category->slug = $category->slug ?: static::generateUniqueSlug($category->name, $category->id);
        });
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    protected static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug ?: Str::random(8);
    }
}
