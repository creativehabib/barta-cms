<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt', 'body',
        'type', 'status', 'format', 'is_premium', 'is_featured', 'is_breaking',
        'allow_comments', 'source', 'source_url', 'video_url', 'views',
        'meta_title', 'meta_description', 'published_at',
    ];

    public array $translatable = ['title', 'excerpt', 'body', 'meta_title', 'meta_description'];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
        'allow_comments' => 'boolean',
        'views' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Relations that are always eager-loaded.
     *
     * Every listing card (the reusable `partials.card` view) renders the
     * article's category badge and author byline, so these two relations are
     * needed on virtually every front-end query. Declaring them here means no
     * controller, widget, or related-posts query can trip Laravel's strict
     * "lazy loading disabled" guard (see AppServiceProvider::boot). Both are
     * BelongsTo (a single extra row each) and neither back-references Post,
     * so there is no risk of a recursive or heavy eager-load. Aggregate
     * queries such as count() do not hydrate models, so they are unaffected.
     */
    protected $with = ['author', 'category'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function (Post $model) {
                $locale = config('barta.default_locale', 'en');
                $base = $model->getTranslation('title', $locale, false)
                    ?: ($model->getTranslation('title', 'en', false)
                    ?: $model->getTranslation('title', 'bn', false));

                return Str::slug((string) $base) !== ''
                    ? $base
                    : 'post-'.Str::lower(Str::random(8));
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    // ---------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('status', 'approved')->whereNull('parent_id')->latest();
    }

    // ---------------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------------
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('type', 'post')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopePages(Builder $query): Builder
    {
        return $query->where('type', 'page');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true);
    }

    public function scopePremium(Builder $query): Builder
    {
        return $query->where('is_premium', true);
    }

    // ---------------------------------------------------------------------
    // Media
    // ---------------------------------------------------------------------
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach ((array) config('barta.images.conversions', []) as $name => $size) {
            $this->addMediaConversion($name)
                ->width((int) ($size['width'] ?? 800))
                ->height((int) ($size['height'] ?? 600))
                ->sharpen(8)
                ->nonQueued();
        }
    }

    public function coverUrl(string $conversion = 'medium'): ?string
    {
        $url = $this->getFirstMediaUrl('featured', $conversion);

        return $url !== '' ? $url : null;
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------
    public function url(): string
    {
        return app('barta.permalink')->urlFor($this);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function readingTime(): int
    {
        return reading_time($this->getTranslation('body', app()->getLocale(), false));
    }

    public function incrementViews(): void
    {
        $this->newQuery()->whereKey($this->getKey())->increment('views');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
