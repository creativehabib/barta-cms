# Reading Progress — Barta CMS plugin

A small **reference plugin** that shows how to extend Barta CMS without editing core
or theme files. Enable it from **Admin → Plugins**, or by inserting a row in the
`plugins` table with `is_active = 1` and slug `reading-progress`.

What it does:

- Draws a **reading-progress bar** across the top of every front-end page.
- Adds a **“back to top”** floating button that appears after you scroll down.
- Writes a **log line** whenever a reader posts a comment (a back-end example).

## How a plugin is structured

```
plugins/
└── ReadingProgress/
    ├── plugin.json                        ← manifest (discovered automatically)
    └── ReadingProgressServiceProvider.php ← booted when the plugin is active
```

`plugin.json` is the manifest. The important key is `provider`, the fully-qualified
class name of a Laravel service provider:

```json
{
    "name": "Reading Progress",
    "slug": "reading-progress",
    "version": "1.0.0",
    "provider": "Plugins\\ReadingProgress\\ReadingProgressServiceProvider"
}
```

The `Plugins\` namespace is mapped to the `plugins/` directory in `composer.json`
(PSR-4), so `Plugins\ReadingProgress\ReadingProgressServiceProvider` resolves to
`plugins/ReadingProgress/ReadingProgressServiceProvider.php`. After adding a new
plugin folder, run `composer dump-autoload` so the class is discoverable.

When the plugin is active, `PluginManager::boot()` registers that provider, and its
`boot()` method wires up hooks.

## The hook system

Resolve the hook manager anywhere with `app('barta.hooks')` (or type-hint
`App\Services\Plugin\HookManager`).

**Actions** — fire-and-forget side effects:

```php
$hooks->addAction('theme.footer', function () {
    echo '<!-- injected before </body> -->';
});
```

**Filters** — transform a value through a chain:

```php
$hooks->addFilter('reading_progress.color', fn ($color) => '#0a7d2b');
$color = $hooks->applyFilters('reading_progress.color', '#c81420'); // '#0a7d2b'
```

### Hooks fired by the core / default theme

| Hook | Type | When | Arguments |
|------|------|------|-----------|
| `theme.head` | action | inside `<head>` of the theme layout | — |
| `theme.footer` | action | just before `</body>` | — |
| `comment.created` | action | after a reader submits a comment | `Comment $comment` |
| `reading_progress.color` | filter | this plugin, to pick the bar colour | `string $hexColor` |

From a Blade template you can fire an action with the `@doAction` directive:

```blade
@doAction('theme.footer')
```

## Writing your own plugin

1. Create `plugins/MyPlugin/plugin.json` with a `provider` class.
2. Create the provider under the `Plugins\MyPlugin` namespace.
3. In its `boot()`, call `app('barta.hooks')->addAction(...)` / `addFilter(...)`.
4. Run `composer dump-autoload`, then activate the plugin from the admin.

That's it — no core files are touched.
