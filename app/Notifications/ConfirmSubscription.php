<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Double opt-in confirmation e-mail. Sent to a freshly-created Subscriber (still
 * in the "pending" state) with a link that verifies their address. Queued so a
 * slow mailer never blocks the subscribe request.
 */
class ConfirmSubscription extends Notification
{
    use Queueable;

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bn = ($notifiable->locale ?? 'bn') === 'bn';
        $site = setting('site_name', config('app.name'));

        $confirmUrl = route('newsletter.verify', $notifiable->token);
        $unsubscribeUrl = route('newsletter.unsubscribe', $notifiable->token);

        $message = (new MailMessage())
            ->subject($bn ? $site.' — সাবস্ক্রিপশন নিশ্চিত করুন' : 'Confirm your subscription to '.$site);

        if ($bn) {
            $message->greeting('স্বাগতম!')
                ->line($site.' নিউজলেটারে সাবস্ক্রাইব করার জন্য ধন্যবাদ।')
                ->line('আপনার সাবস্ক্রিপশন চূড়ান্ত করতে নিচের বাটনে ক্লিক করুন।')
                ->action('সাবস্ক্রিপশন নিশ্চিত করুন', $confirmUrl)
                ->line('আপনি যদি এটি অনুরোধ না করে থাকেন, এই ইমেইলটি উপেক্ষা করুন।')
                ->salutation('ধন্যবাদান্তে, '.$site);
        } else {
            $message->greeting('Welcome!')
                ->line('Thanks for subscribing to the '.$site.' newsletter.')
                ->line('Please confirm your subscription by clicking the button below.')
                ->action('Confirm subscription', $confirmUrl)
                ->line('If you did not request this, you can safely ignore this email.')
                ->salutation('Regards, '.$site);
        }

        // A plain-text unsubscribe link + the List-Unsubscribe header (good sender hygiene).
        $message->line(($bn ? 'সাবস্ক্রিপশন বাতিল করতে: ' : 'To unsubscribe: ').$unsubscribeUrl);
        $message->withSymfonyMessage(function ($email) use ($unsubscribeUrl) {
            $email->getHeaders()->addTextHeader('List-Unsubscribe', '<'.$unsubscribeUrl.'>');
        });

        return $message;
    }
}
