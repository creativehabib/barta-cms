<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * Full-text-ish search across published posts. Matches the query against the
 * translatable title and body JSON columns for every enabled locale so a
 * reader browsing in Bengali still finds English headlines and vice-versa.
 */
class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        app('barta.seo')->title($query !== '' ? $query : __('Search'));

        $builder = Post::published()->with(['author', 'category']);

        if ($query === '') {
            // Return an empty paginator so the view can always call ->links().
            $builder->whereRaw('1 = 0');
        } else {
            $locales = barta_locales();
            $builder->where(function ($q) use ($query, $locales) {
                foreach ($locales as $loc) {
                    $q->orWhere("title->{$loc}", 'like', "%{$query}%")
                        ->orWhere("excerpt->{$loc}", 'like', "%{$query}%")
                        ->orWhere("body->{$loc}", 'like', "%{$query}%");
                }
            });
        }

        $results = $builder->latest('published_at')->paginate(12)->withQueryString();

        return app('barta.theme')->view('search', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
