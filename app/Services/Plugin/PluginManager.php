<?php

namespace App\Services\Plugin;

use App\Models\Plugin;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Discovers plugins on disk (each is a folder under /plugins with a plugin.json
 * manifest), tracks their active state in the database, and boots the active
 * ones during the request lifecycle by registering their service providers.
 */
class PluginManager
{
    protected ?array $discovered = null;

    public function basePath(?string $path = null): string
    {
        $base = config('barta.plugins_path', base_path('plugins'));

        return $path ? $base.DIRECTORY_SEPARATOR.ltrim($path, '/\\') : $base;
    }

    /**
     * All plugins present on disk, keyed by slug.
     *
     * @return array<string, array>
     */
    public function all(): array
    {
        if ($this->discovered !== null) {
            return $this->discovered;
        }

        $plugins = [];
        $base = $this->basePath();

        if (File::isDirectory($base)) {
            foreach (File::directories($base) as $dir) {
                $manifest = $dir.'/plugin.json';
                if (! File::exists($manifest)) {
                    continue;
                }

                $data = json_decode(File::get($manifest), true) ?: [];
                $slug = $data['slug'] ?? Str::slug(basename($dir));
                $data['slug'] = $slug;
                $data['name'] = $data['name'] ?? Str::headline(basename($dir));
                $data['version'] = $data['version'] ?? '1.0.0';
                $data['path'] = $dir;
                $data['dir'] = basename($dir);

                $plugins[$slug] = $data;
            }
        }

        return $this->discovered = $plugins;
    }

    public function get(string $slug): ?array
    {
        return $this->all()[$slug] ?? null;
    }

    /** Slugs marked active in the database (falls back to none before migration). */
    public function activeSlugs(): array
    {
        if (! Schema::hasTable('plugins')) {
            return [];
        }

        return Plugin::query()->where('is_active', true)->pluck('slug')->all();
    }

    /** Load and register every active plugin's service provider. */
    public function boot(): void
    {
        $discovered = $this->all();

        foreach ($this->activeSlugs() as $slug) {
            if (isset($discovered[$slug])) {
                $this->load($discovered[$slug]);
            }
        }
    }

    protected function load(array $plugin): void
    {
        $provider = $plugin['provider'] ?? null;

        if ($provider && class_exists($provider)) {
            app()->register($provider);

            return;
        }

        // Fallback: a plain plugin.php bootstrap file.
        $bootstrap = $plugin['path'].'/plugin.php';
        if (File::exists($bootstrap)) {
            require_once $bootstrap;
        }
    }

    /** Reconcile the database rows with what is present on disk. */
    public function sync(): void
    {
        if (! Schema::hasTable('plugins')) {
            return;
        }

        $seen = [];

        foreach ($this->all() as $slug => $plugin) {
            $seen[] = $slug;
            Plugin::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $plugin['name'],
                    'description' => $plugin['description'] ?? null,
                    'version' => $plugin['version'],
                    'author' => $plugin['author'] ?? null,
                ]
            );
        }

        Plugin::query()->whereNotIn('slug', $seen ?: ['__none__'])->delete();
    }

    public function activate(string $slug): void
    {
        Plugin::query()->where('slug', $slug)->update(['is_active' => true]);
    }

    public function deactivate(string $slug): void
    {
        Plugin::query()->where('slug', $slug)->update(['is_active' => false]);
    }

    public function isActive(string $slug): bool
    {
        return in_array($slug, $this->activeSlugs(), true);
    }
}
