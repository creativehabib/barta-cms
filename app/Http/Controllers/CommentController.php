<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * Handles reader comment submissions on a post. Guests must supply a name and
 * e-mail; authenticated readers inherit theirs. New comments land in the
 * `pending` queue unless the `comments_auto_approve` setting is enabled.
 */
class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        if (! $post->allow_comments) {
            return back()->with('comment_status', __('Comments are closed for this article.'));
        }

        $rules = [
            'body' => ['required', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ];

        if (! $request->user()) {
            $rules['author_name'] = ['required', 'string', 'max:255'];
            $rules['author_email'] = ['required', 'email', 'max:255'];
        }

        $data = $request->validate($rules);

        $autoApprove = (bool) setting('comments_auto_approve', false);

        $comment = $post->comments()->create([
            'user_id' => $request->user()?->id,
            'parent_id' => $data['parent_id'] ?? null,
            'author_name' => $request->user()?->name ?? ($data['author_name'] ?? null),
            'author_email' => $request->user()?->email ?? ($data['author_email'] ?? null),
            'body' => $data['body'],
            'status' => $autoApprove ? 'approved' : 'pending',
            'ip_address' => $request->ip(),
        ]);

        app('barta.hooks')->doAction('comment.created', $comment);

        return back()->with('comment_status', $autoApprove
            ? __('Your comment has been posted.')
            : __('Thanks! Your comment is awaiting moderation.'))
            ->withFragment('comments');
    }
}
