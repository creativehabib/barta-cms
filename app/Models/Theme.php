<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['slug', 'name', 'version', 'author', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
