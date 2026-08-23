<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdSlot extends Model
{
    protected $fillable = ['key', 'name', 'width', 'height'];

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }

    public function activeAds(): HasMany
    {
        return $this->hasMany(Ad::class)->active();
    }
}
