<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * A tiny, cached key/value settings store backed by the `settings` table.
 * Resilient before the database/migrations exist (returns defaults).
 */
class SettingsRepository
{
    protected ?array $items = null;

    protected string $cacheKey = 'barta.settings';

    public function all(): array
    {
        if ($this->items !== null) {
            return $this->items;
        }

        try {
            if (! Schema::hasTable('settings')) {
                return $this->items = [];
            }

            $this->items = Cache::rememberForever($this->cacheKey, function () {
                return Setting::all()
                    ->mapWithKeys(fn (Setting $s) => [$s->key => $s->typedValue()])
                    ->all();
            });
        } catch (\Throwable $e) {
            $this->items = [];
        }

        return $this->items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }

    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $stored = match (true) {
            is_array($value) => json_encode($value),
            is_bool($value) => $value ? '1' : '0',
            default => (string) $value,
        };

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $type, 'group' => $group],
        );

        $this->flush();
    }

    /** Persist many settings at once: ['key' => value, ...]. */
    public function setMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            $type = match (true) {
                is_bool($value) => 'bool',
                is_int($value) => 'int',
                is_array($value) => 'json',
                default => 'string',
            };
            $this->set($key, $value, $type, $group);
        }
    }

    public function flush(): void
    {
        $this->items = null;
        Cache::forget($this->cacheKey);
    }
}
