<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;

/**
 * The front page. Assembles the editorial blocks a news homepage needs —
 * a lead/featured strip, a breaking-news ticker, the latest feed, the most
 * read list, and a set of per-category section rails.
 */
class HomeController extends Controller
{
    public function __invoke()
    {
        return $this->index();
    }

    public function index()
    {
        app('barta.seo')->forHome();

        $featured = Post::published()->featured()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->take(5)
            ->get();

        $breaking = Post::published()->breaking()
            ->latest('published_at')
            ->take(6)
            ->get();

        $latest = Post::published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);

        $popular = Post::published()
            ->with('category')
            ->orderByDesc('views')
            ->take(6)
            ->get();

        // Per-category rails. We fetch each category's posts in its own query
        // to avoid the "LIMIT applies across all parents" eager-load pitfall.
        $sections = Category::active()->parents()
            ->orderBy('position')
            ->take(8)
            ->get()
            ->map(function (Category $category) {
                $category->setRelation(
                    'latestPosts',
                    $category->posts()->published()->with('author')
                        ->latest('published_at')->take(4)->get()
                );

                return $category;
            })
            ->filter(fn (Category $c) => $c->latestPosts->isNotEmpty())
            ->values();

        return app('barta.theme')->view('home', compact(
            'featured',
            'breaking',
            'latest',
            'popular',
            'sections'
        ));
    }
}
