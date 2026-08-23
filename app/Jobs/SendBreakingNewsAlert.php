<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\Subscriber;
use App\Notifications\BreakingNewsAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Fans a breaking-news alert out to every subscribed reader. Dispatched from the
 * PostObserver when a post transitions into "published + breaking" — but only if
 * the "notify_breaking_news" setting is enabled, so seeding and routine edits
 * never trigger a blast. Subscribers are streamed in chunks; failures are logged
 * per-recipient rather than aborting the run.
 */
class SendBreakingNewsAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $chunkSize = 200;

    public int $timeout = 3600;

    public function __construct(public Post $post)
    {
    }

    public function handle(): void
    {
        // Re-check state at run time: the post may have been unpublished or the
        // "breaking" flag cleared between dispatch and execution.
        if (! $this->post->is_breaking || ! $this->post->isPublished()) {
            return;
        }

        $sent = 0;
        $failed = 0;

        Subscriber::subscribed()
            ->orderBy('id')
            ->chunkById($this->chunkSize, function ($subscribers) use (&$sent, &$failed) {
                foreach ($subscribers as $subscriber) {
                    try {
                        $subscriber->notify(new BreakingNewsAlert($this->post));
                        $sent++;
                    } catch (\Throwable $e) {
                        $failed++;
                        Log::warning('Breaking alert delivery failed', [
                            'post_id' => $this->post->id,
                            'subscriber_id' => $subscriber->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        Log::info('Breaking news alert sent', [
            'post_id' => $this->post->id,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }
}
