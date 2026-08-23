<?php

namespace App\Livewire\Front;

use App\Models\Plan;
use App\Services\Payment\PaymentManager;
use Livewire\Component;

/**
 * Public pricing page. Lists the active subscription plans and, for a signed-in
 * reader without an active subscription, offers a gateway choice that posts to
 * the checkout route. Rendered inside the active theme via layouts.theme.
 */
class Plans extends Component
{
    public function mount(): void
    {
        app('barta.seo')
            ->title(__('Subscription plans'))
            ->description(__('Choose a plan and read premium articles on :site.', ['site' => setting('site_name', config('app.name'))]));
    }

    public function render()
    {
        return view('livewire.front.plans', [
            'plans' => Plan::query()->active()->get(),
            'gateways' => app(PaymentManager::class)->available(),
            'currentSubscription' => auth()->user()?->activeSubscription(),
        ])->layout('layouts.theme');
    }
}
