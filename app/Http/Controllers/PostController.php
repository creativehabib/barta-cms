<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * Resolves the permalink catch-all (/{path}) to a single article.
 *
 * Post slugs are globally unique, so whatever prefix the active permalink
 * structure adds (date, category, etc.) we resolve by the final {slug}
 * segment. Premium articles are gated: non-subscribers see a locked preview
 * rather than the full body, and locked views are not counted.
 */
class PostController extends Controller
{
    public function show(Request $request, string $path)
    {
        $post = app('barta.permalink')->resolvePathOrFail($path);

        // Static pages have their own dedicated /page/{slug} route.
        abort_if($post->type === 'page', 404);

        $user = $request->user();
        $locked = $post->is_premium
            && ! (optional($user)->isSubscribed() || optional($user)->isStaff());

        if (! $locked) {
            $post->incrementViews();
        }

        app('barta.seo')->forPost($post);

        $post->load([
            'author',
            'category',
            'tags',
            'approvedComments.user',
            'approvedComments.replies' => fn ($q) => $q->where('status', 'approved')->oldest(),
            'approvedComments.replies.user',
        ]);

        $related = Post::published()
            ->whereKeyNot($post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->take(4)
            ->get();

        return app('barta.theme')->view('single', [
            'post' => $post,
            'related' => $related,
            'locked' => $locked,
        ]);
    }
}
