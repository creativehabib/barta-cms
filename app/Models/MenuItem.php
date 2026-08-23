<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    use HasTranslations;

    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'type', 'url', 'target_id', 'target', 'position',
    ];

    public array $translatable = ['label'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position')->with('children');
    }

    /** Resolve the final href for this item based on its type. */
    public function resolveUrl(): string
    {
        return match ($this->type) {
            'category' => optional(Category::find($this->target_id))->url() ?? '#',
            'post' => optional(Post::find($this->target_id))?->url() ?? '#',
            default => $this->url ?: '#',
        };
    }
}
