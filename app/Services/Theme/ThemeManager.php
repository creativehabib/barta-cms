<?php

namespace App\Services\Theme;

use App\Models\Theme;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

/**
 * WordPress-style theme engine. Themes live in /themes/{slug} and expose a
 * theme.json manifest plus a /views directory of Blade templates. The active
 * theme's views are registered under the `theme::` namespace so front-end
 * controllers render `theme::home`, `theme::single`, etc.
 */
class ThemeManager
{
    protected ?string $active = null;

    public function basePath(): string
    {
        return rtrim((string) config('barta.themes_path', base_path('themes')), '/');
    }

    public function path(?string $theme = null): string
    {
        return $this->basePath().'/'.($theme ?? $this->active());
    }

    public function active(): string
    {
        if ($this->active !== null) {
            return $this->active;
        }

        return $this->active = (string) (setting('active_theme') ?: config('barta.active_theme', 'barta'));
    }

    /** Register the active theme's view namespace. Called from the provider. */
    public function boot(): void
    {
        $views = $this->path().'/views';

        if (is_dir($views)) {
            View::addNamespace('theme', $views);
        }
    }

    public function view(string $view, array $data = [])
    {
        return view('theme::'.$view, $data);
    }

    public function exists(string $view): bool
    {
        return View::exists('theme::'.$view);
    }

    public function asset(string $path): string
    {
        return url('/themes/'.$this->active().'/assets/'.ltrim($path, '/'));
    }

    /** Discover every installed theme by scanning for theme.json manifests. */
    public function all(): array
    {
        $base = $this->basePath();
        if (! is_dir($base)) {
            return [];
        }

        $themes = [];
        foreach (File::directories($base) as $dir) {
            $manifest = $dir.'/theme.json';
            if (is_file($manifest)) {
                $data = json_decode(File::get($manifest), true) ?: [];
                $data['slug'] = $data['slug'] ?? basename($dir);
                $themes[$data['slug']] = $data;
            }
        }

        return $themes;
    }

    public function activate(string $slug): void
    {
        app('barta.settings')->set('active_theme', $slug);

        Theme::query()->update(['is_active' => false]);

        $meta = $this->all()[$slug] ?? ['name' => $slug];
        Theme::updateOrCreate(['slug' => $slug], [
            'name' => $meta['name'] ?? $slug,
            'version' => $meta['version'] ?? null,
            'author' => $meta['author'] ?? null,
            'description' => $meta['description'] ?? null,
            'is_active' => true,
        ]);

        $this->active = $slug;
        $this->boot();
    }
}
