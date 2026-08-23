<?php

namespace App\Services\Plugin;

/**
 * A lightweight WordPress-style hook system: `actions` fire side effects and
 * `filters` transform a value through a chain of callbacks. Plugins register
 * callbacks; the core and themes trigger them.
 *
 *   Hook::addFilter('post.body', fn ($html, $post) => $html.' …');
 *   $html = Hook::applyFilters('post.body', $post->body, $post);
 *
 *   Hook::addAction('post.published', fn ($post) => Log::info($post->id));
 *   Hook::doAction('post.published', $post);
 */
class HookManager
{
    /** @var array<string, array<int, array<int, callable>>> */
    protected array $actions = [];

    /** @var array<string, array<int, array<int, callable>>> */
    protected array $filters = [];

    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][$priority][] = $callback;
    }

    public function doAction(string $hook, mixed ...$args): void
    {
        foreach ($this->sorted($this->actions[$hook] ?? []) as $callback) {
            $callback(...$args);
        }
    }

    public function addFilter(string $hook, callable $callback, int $priority = 10): void
    {
        $this->filters[$hook][$priority][] = $callback;
    }

    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        foreach ($this->sorted($this->filters[$hook] ?? []) as $callback) {
            $value = $callback($value, ...$args);
        }

        return $value;
    }

    public function hasFilter(string $hook): bool
    {
        return ! empty($this->filters[$hook]);
    }

    public function hasAction(string $hook): bool
    {
        return ! empty($this->actions[$hook]);
    }

    /** Flatten a priority-keyed map into an ordered list of callbacks. */
    protected function sorted(array $byPriority): array
    {
        ksort($byPriority);

        return array_merge(...array_values($byPriority)) ?: [];
    }
}
