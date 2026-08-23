<?php

namespace App\Observers;

use App\Jobs\SendBreakingNewsAlert;
use App\Models\Post;

/**
 * Watches posts for the moment they become "published + breaking" and fires a
 * one-time e-mail blast to subscribers.
 *
 * Safety rails (all must pass before anything is dispatched):
 *   1. Never during console/artisan runs — this keeps seeders & migrations quiet.
 *   2. Only when the "notify_breaking_news" setting is explicitly enabled
 *      (defaults to false), so the feature is strictly opt-in.
 *   3. Only on the actual transition into the alerting state, so re-saving or
 *      editing an already-published breaking post does not re-blast subscribers.
 */
class PostObserver
{
    public function created(Post $post): void
    {
        if ($this->shouldAlert($post, true)) {
            SendBreakingNewsAlert::dispatch($post);
        }
    }

    public function updated(Post $post): void
    {
        if ($this->shouldAlert($post, false)) {
            SendBreakingNewsAlert::dispatch($post);
        }
    }

    protected function shouldAlert(Post $post, bool $isNew): bool
    {
        if ($this->app_runningInConsole()) {
            return false;
        }

        if (! setting('notify_breaking_news', false)) {
            return false;
        }

        if ($post->type !== 'post' || ! $post->is_breaking || ! $post->isPublished()) {
            return false;
        }

        // New rows always count as a transition; existing rows only when one of
        // the state-defining columns actually changed on this save.
        return $isNew || $post->wasChanged(['status', 'is_breaking', 'published_at']);
    }

    /** Wrapper kept tiny so it can be stubbed in tests if needed. */
    protected function app_runningInConsole(): bool
    {
        return app()->runningInConsole();
    }
}
