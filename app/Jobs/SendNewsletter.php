<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Delivers one newsletter issue to every subscribed reader. Runs on the queue so
 * a large audience never blocks a web request; subscribers are streamed in
 * chunks to keep memory flat, and a single bad address is logged rather than
 * aborting the whole run. When finished the campaign is marked "sent".
 */
class SendNewsletter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** How many subscribers to load per chunk. */
    public int $chunkSize = 200;

    /** Give a big send plenty of time. */
    public int $timeout = 3600;

    public function __construct(public Newsletter $newsletter)
    {
    }

    public function handle(): void
    {
        // Guard against a double-send (e.g. the job being retried after completion).
        if ($this->newsletter->status === 'sent') {
            return;
        }

        $sent = 0;
        $failed = 0;

        Subscriber::subscribed()
            ->orderBy('id')
            ->chunkById($this->chunkSize, function ($subscribers) use (&$sent, &$failed) {
                foreach ($subscribers as $subscriber) {
                    try {
                        Mail::to($subscriber->email)->send(
                            new NewsletterMail($this->newsletter, $subscriber)
                        );
                        $sent++;
                    } catch (\Throwable $e) {
                        $failed++;
                        Log::warning('Newsletter delivery failed', [
                            'newsletter_id' => $this->newsletter->id,
                            'subscriber_id' => $subscriber->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        $this->newsletter->update([
            'status' => 'sent',
            'recipients' => $sent,
            'sent_at' => now(),
        ]);

        Log::info('Newsletter sent', [
            'newsletter_id' => $this->newsletter->id,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }

    /** If the whole job blows up, don't leave the campaign stuck in "sending". */
    public function failed(\Throwable $exception): void
    {
        $this->newsletter->update(['status' => 'failed']);

        Log::error('Newsletter job failed', [
            'newsletter_id' => $this->newsletter->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
