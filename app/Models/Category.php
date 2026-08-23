<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory;
    use HasSlug;
    use HasTranslations;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'description', 'color', 'icon',
        'position', 'is_active', 'show_in_menu', 'meta_title', 'meta_description',
    ];

    public array $translatable = ['name', 'description', 'meta_title', 'meta_description'];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function (Category $model) {
                $locale = config('barta.default_locale', 'en');
                $base = $model->getTranslation('name', $locale, false)
                    ?: ($model->getTranslation('name', 'en', false)
                    ?: $model->getTranslation('name', 'bn', false));

                return Str::slug((string) $base) !== ''
                    ? $base
                    : 'category-'.Str::lower(Str::random(6));
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function url(): string
    {
        return url('/category/'.$this->slug);
    }
}
