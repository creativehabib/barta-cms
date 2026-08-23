<?php

namespace App\Http\Controllers;

use App\Models\Post;

/**
 * Renders a standalone static page (type = "page"), e.g. About / Contact /
 * Privacy. Only published pages are reachable.
 */
class PageController extends Controller
{
    public function show(Post $page)
    {
        abort_unless($page->type === 'page' && $page->status === 'published', 404);

        app('barta.seo')->forPost($page);

        return app('barta.theme')->view('page', [
            'page' => $page,
        ]);
    }
}
