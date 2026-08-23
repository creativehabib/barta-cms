<?php

namespace App\Mail;

use App\Models\Newsletter;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

/**
 * A single newsletter issue rendered for one recipient. The recipient is passed
 * in so the footer can carry a personal, one-click unsubscribe link (and the
 * List-Unsubscribe header mail clients look for).
 */
class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public Subscriber $subscriber,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter->subject ?: setting('site_name', config('app.name')),
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<'.route('newsletter.unsubscribe', $this->subscriber->token).'>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.newsletter',
            with: [
                'subject' => $this->newsletter->subject,
                'body' => $this->newsletter->content,
                'unsubscribeUrl' => route('newsletter.unsubscribe', $this->subscriber->token),
                'siteName' => setting('site_name', config('app.name')),
                'locale' => $this->subscriber->locale ?? 'bn',
            ],
        );
    }
}
