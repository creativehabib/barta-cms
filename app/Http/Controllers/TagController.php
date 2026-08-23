<?php

namespace App\Http\Controllers;

use App\Models\Tag;

/**
 * Tag archive — lists the published posts associated with a tag.
 */
class TagController extends Controller
{
    public function show(Tag $tag)
    {
        app('barta.seo')
            ->title($tag->getTranslation('name', app()->getLocale(), false))
            ->description(__('Articles tagged :tag', ['tag' => $tag->getTranslation('name', app()->getLocale(), false)]));

        $posts = $tag->posts()->published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);

        return app('barta.theme')->view('tag', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }
}
