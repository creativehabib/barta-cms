<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Plan extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'currency',
        'interval', 'interval_count', 'features', 'is_active', 'position',
    ];

    public array $translatable = ['name', 'description'];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('position');
    }

    /** Number of days the plan grants, or null for lifetime. */
    public function durationDays(): ?int
    {
        return match ($this->interval) {
            'lifetime' => null,
            'year' => 365 * $this->interval_count,
            'week' => 7 * $this->interval_count,
            'day' => $this->interval_count,
            default => 30 * $this->interval_count,
        };
    }
}
