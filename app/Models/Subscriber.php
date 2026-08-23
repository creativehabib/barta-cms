<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subscriber extends Model
{
    use Notifiable;

    protected $fillable = ['email', 'name', 'locale', 'status', 'token', 'verified_at'];

    protected $casts = ['verified_at' => 'datetime'];

    public function scopeSubscribed($query)
    {
        return $query->where('status', 'subscribed');
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
