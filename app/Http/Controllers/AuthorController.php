<?php

namespace App\Http\Controllers;

use App\Models\User;

/**
 * Author profile page — the writer's bio plus a paginated feed of their
 * published articles. Inactive accounts 404.
 */
class AuthorController extends Controller
{
    public function show(User $user)
    {
        abort_unless($user->is_active, 404);

        app('barta.seo')
            ->title($user->name)
            ->description($user->bio ?: __(':name on :site', ['name' => $user->name, 'site' => setting('site_name', config('app.name'))]));

        $posts = $user->posts()->published()
            ->with('category')
            ->latest('published_at')
            ->paginate(12);

        return app('barta.theme')->view('author', [
            'author' => $user,
            'posts' => $posts,
        ]);
    }
}
