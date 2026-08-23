<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    use HasFactory;
    use HasSlug;
    use HasTranslations;

    protected $fillable = ['name', 'slug'];

    public array $translatable = ['name'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function (Tag $model) {
                $locale = config('barta.default_locale', 'en');
                $base = $model->getTranslation('name', $locale, false)
                    ?: ($model->getTranslation('name', 'en', false)
                    ?: $model->getTranslation('name', 'bn', false));

                return Str::slug((string) $base) !== ''
                    ? $base
                    : 'tag-'.Str::lower(Str::random(6));
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function url(): string
    {
        return url('/tag/'.$this->slug);
    }
}
