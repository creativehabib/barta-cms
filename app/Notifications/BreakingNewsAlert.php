<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * "Breaking news" e-mail alert for a single published, breaking post. Sent to
 * subscribed readers (fanned out by the SendBreakingNewsAlert job). The recipient
 * drives the language, and the footer carries a personal unsubscribe link.
 */
class BreakingNewsAlert extends Notification
{
    use Queueable;

    public function __construct(public Post $post)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loc = $notifiable->locale ?? 'bn';
        $bn = $loc === 'bn';
        $site = setting('site_name', config('app.name'));

        $title = $this->post->getTranslation('title', $loc, false)
            ?: $this->post->getTranslation('title', 'bn', false);
        $excerpt = $this->post->getTranslation('excerpt', $loc, false)
            ?: excerpt($this->post->getTranslation('body', $loc, false), 40);

        $unsubscribeUrl = route('newsletter.unsubscribe', $notifiable->token);

        $message = (new MailMessage())
            ->subject(($bn ? '🔴 ব্রেকিং: ' : '🔴 Breaking: ').$title)
            ->greeting($bn ? 'ব্রেকিং নিউজ' : 'Breaking news')
            ->line($title);

        if ($excerpt) {
            $message->line($excerpt);
        }

        $message->action($bn ? 'সম্পূর্ণ খবর পড়ুন' : 'Read the full story', $this->post->url())
            ->salutation($site);

        $message->line(($bn ? 'সাবস্ক্রিপশন বাতিল করতে: ' : 'To unsubscribe: ').$unsubscribeUrl);
        $message->withSymfonyMessage(function ($email) use ($unsubscribeUrl) {
            $email->getHeaders()->addTextHeader('List-Unsubscribe', '<'.$unsubscribeUrl.'>');
        });

        return $message;
    }
}
