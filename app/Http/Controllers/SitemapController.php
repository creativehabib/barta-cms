<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;

/**
 * Generates /sitemap.xml on the fly. Kept dependency-free (hand-built XML)
 * so the site works even before optional packages are installed. Covers the
 * home page, published posts, active categories and published static pages.
 */
class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        $urls[] = ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'hourly'];

        Post::published()->latest('published_at')->limit(5000)->get()
            ->each(function (Post $post) use (&$urls) {
                $urls[] = [
                    'loc' => $post->url(),
                    'lastmod' => optional($post->updated_at)->toAtomString(),
                    'priority' => '0.8',
                    'changefreq' => 'daily',
                ];
            });

        Category::active()->get()->each(function (Category $category) use (&$urls) {
            $urls[] = ['loc' => $category->url(), 'priority' => '0.6', 'changefreq' => 'daily'];
        });

        Post::pages()->where('status', 'published')->get()->each(function (Post $page) use (&$urls) {
            $urls[] = ['loc' => url('/page/'.$page->slug), 'priority' => '0.4', 'changefreq' => 'monthly'];
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e($url['loc']).'</loc>'."\n";
            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>'.e($url['lastmod']).'</lastmod>'."\n";
            }
            if (! empty($url['changefreq'])) {
                $xml .= '    <changefreq>'.e($url['changefreq']).'</changefreq>'."\n";
            }
            $xml .= '    <priority>'.e($url['priority']).'</priority>'."\n";
            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
