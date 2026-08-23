<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Double opt-in newsletter subscriptions. A subscriber is created in the
 * `pending` state with a random token and e-mailed a confirmation link; when
 * no mailer is configured the address is confirmed immediately so the flow
 * still works out of the box in local/demo installs.
 */
class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $subscriber = Subscriber::firstOrNew(['email' => $data['email']]);

        if ($subscriber->exists && $subscriber->status === 'subscribed') {
            return back()->with('newsletter_status', __('You are already subscribed.'));
        }

        $subscriber->fill([
            'name' => $data['name'] ?? $subscriber->name,
            'locale' => app()->getLocale(),
            'status' => 'pending',
            'token' => $subscriber->token ?: Str::random(40),
        ])->save();

        if (class_exists(\App\Notifications\ConfirmSubscription::class)) {
            $subscriber->notify(new \App\Notifications\ConfirmSubscription());

            return back()->with('newsletter_status', __('Almost there! Check your inbox to confirm your subscription.'));
        }

        // No notification class / mailer — confirm right away.
        $subscriber->update(['status' => 'subscribed', 'verified_at' => now()]);

        return back()->with('newsletter_status', __('Thanks for subscribing!'));
    }

    public function verify(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['status' => 'subscribed', 'verified_at' => now()]);

        return redirect()->route('home')->with('newsletter_status', __('Your subscription is confirmed. Welcome aboard!'));
    }

    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['status' => 'unsubscribed']);

        return redirect()->route('home')->with('newsletter_status', __('You have been unsubscribed.'));
    }
}
