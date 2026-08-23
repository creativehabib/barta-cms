<?php

namespace App\Http\Controllers;

use App\Models\Category;

/**
 * Category archive — lists the published posts filed under a category, along
 * with any active sub-categories for further drill-down.
 */
class CategoryController extends Controller
{
    public function show(Category $category)
    {
        abort_unless($category->is_active, 404);

        app('barta.seo')->forCategory($category);

        $posts = $category->posts()->published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);

        $children = $category->children()->active()->get();

        return app('barta.theme')->view('category', [
            'category' => $category,
            'posts' => $posts,
            'children' => $children,
        ]);
    }
}
