<?php

namespace App\Services;

use App\Models\Post;

/**
 * Builds and resolves post permalinks based on the active structure
 * (settings key `permalink_structure`, falling back to config('barta.permalink')).
 *
 * Because a post slug is globally unique, incoming requests are always
 * resolved by their final {slug} segment regardless of the prefix, which
 * lets the site switch permalink structures without breaking resolution.
 */
class PermalinkService
{
    public function structureKey(): string
    {
        return (string) setting('permalink_structure', config('barta.permalink', 'date'));
    }

    public function structure(): string
    {
        $key = $this->structureKey();

        return config("barta.permalinks.$key", config('barta.permalinks.default'));
    }

    public function pathFor(Post $post): string
    {
        $date = $post->published_at ?? $post->created_at ?? now();

        $path = strtr($this->structure(), [
            '{year}' => $date->format('Y'),
            '{month}' => $date->format('m'),
            '{day}' => $date->format('d'),
            '{slug}' => $post->slug,
            '{category}' => optional($post->category)->slug ?? 'news',
        ]);

        return trim($path, '/');
    }

    public function urlFor(Post $post): string
    {
        return url('/'.$this->pathFor($post));
    }

    /** Find a published post (or page) by its slug, or fail with 404. */
    public function resolveOrFail(string $slug): Post
    {
        return Post::query()
            ->where('slug', $slug)
            ->where(function ($q) {
                $q->published()->orWhere('type', 'page');
            })
            ->firstOrFail();
    }
}
