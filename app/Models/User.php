<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'bio',
        'locale',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** The user's current, non-expired active subscription (if any). */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest('id')
            ->first();
    }

    public function isSubscribed(): bool
    {
        return (bool) $this->activeSubscription();
    }

    /** Whether the user may access the admin panel. */
    public function isStaff(): bool
    {
        return $this->hasAnyRole(config('barta.staff_roles', []));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function avatarUrl(): string
    {
        return $this->getFirstMediaUrl('avatar')
            ?: 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'?d=mp&s=200';
    }

    public function scopeAuthors($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->whereIn('name', ['super-admin', 'admin', 'editor', 'author', 'contributor']));
    }
}
