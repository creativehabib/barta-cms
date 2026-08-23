<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Widget extends Model
{
    use HasTranslations;

    protected $fillable = ['area', 'type', 'title', 'settings', 'position', 'is_active'];

    public array $translatable = ['title'];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeArea($query, string $area)
    {
        return $query->where('area', $area)->orderBy('position');
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
