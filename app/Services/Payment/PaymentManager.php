<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Payment\Contracts\PaymentGateway;
use App\Services\Payment\Gateways\BkashGateway;
use App\Services\Payment\Gateways\SslCommerzGateway;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Front door for the premium/paywall flow. Knows the available gateways,
 * opens a checkout for a chosen plan, and finalizes the payment + subscription
 * when a gateway calls back.
 */
class PaymentManager
{
    /** @var array<string, class-string<PaymentGateway>> */
    protected array $gateways = [
        'sslcommerz' => SslCommerzGateway::class,
        'bkash' => BkashGateway::class,
    ];

    public function gateway(?string $key = null): PaymentGateway
    {
        $key = $key ?: config('barta.payments.default_gateway', 'sslcommerz');

        if (! isset($this->gateways[$key])) {
            throw new InvalidArgumentException("Unknown payment gateway [{$key}].");
        }

        return app($this->gateways[$key]);
    }

    /** Gateways enabled for this install, as [key => label]. */
    public function available(): array
    {
        $enabled = config('barta.payments.gateways', array_keys($this->gateways));

        return collect($this->gateways)
            ->only($enabled)
            ->map(fn ($class, $key) => app($class)->label())
            ->all();
    }

    /**
     * Create a pending payment for a plan and return the gateway redirect URL.
     */
    public function checkout(User $user, Plan $plan, string $gatewayKey): string
    {
        $gateway = $this->gateway($gatewayKey);

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => $gateway->key(),
            'reference' => $this->reference(),
            'status' => 'pending',
            'amount' => $plan->price,
            'currency' => $plan->currency ?: config('barta.payments.currency', 'BDT'),
        ]);

        return $gateway->checkout($payment);
    }

    /**
     * Handle a gateway return/IPN. Verifies, records the outcome and, on
     * success, provisions the subscription. Returns the finalized payment.
     */
    public function handleCallback(string $gatewayKey, string $reference, array $params): Payment
    {
        $gateway = $this->gateway($gatewayKey);
        $payment = Payment::where('reference', $reference)->firstOrFail();

        if ($payment->status === 'success') {
            return $payment; // already processed (e.g. IPN raced the return)
        }

        $result = $gateway->verify($payment, $params);

        if (! $result->successful) {
            $payment->update([
                'status' => $result->status,
                'payload' => array_merge((array) $payment->payload, ['verify' => $result->raw]),
            ]);

            return $payment;
        }

        $payment->markSuccessful($result->transactionId, ['verify' => $result->raw]);
        $subscription = $this->provision($payment);
        $payment->update(['subscription_id' => $subscription->id]);

        return $payment->refresh();
    }

    /** Create/extend the user's subscription from a successful payment. */
    protected function provision(Payment $payment): Subscription
    {
        $plan = $payment->plan;
        $days = $plan?->durationDays();

        $starts = now();
        $existing = Subscription::where('user_id', $payment->user_id)->active()->latest('ends_at')->first();
        if ($existing && $existing->ends_at && $existing->ends_at->isFuture()) {
            $starts = $existing->ends_at; // stack onto remaining time
        }

        return Subscription::create([
            'user_id' => $payment->user_id,
            'plan_id' => $payment->plan_id,
            'gateway' => $payment->gateway,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => is_null($days) ? null : $starts->copy()->addDays($days),
        ]);
    }

    protected function reference(): string
    {
        return 'BARTA-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));
    }
}
