<?php

namespace App\Http\Controllers;

/**
 * Serves static assets (CSS, JS, images) that live inside a theme directory
 * at /themes/{theme}/assets/**. Because themes live outside the public root,
 * this controller streams the file with long-lived cache headers while
 * guarding against directory-traversal escapes.
 */
class ThemeAssetController extends Controller
{
    public function __invoke(string $theme, string $path)
    {
        $base = app('barta.theme')->basePath().'/'.$theme.'/assets';
        $realBase = realpath($base);

        abort_if($realBase === false, 404);

        $full = realpath($realBase.'/'.$path);

        // The resolved path must stay within the theme's assets directory.
        abort_if($full === false || ! str_starts_with($full, $realBase), 404);
        abort_unless(is_file($full), 404);

        return response()->file($full, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
