<?php

namespace App\Services\Payment\Contracts;

use App\Models\Payment;
use App\Services\Payment\PaymentResult;

interface PaymentGateway
{
    /** Machine key, e.g. "sslcommerz" or "bkash". */
    public function key(): string;

    /** Human label shown on the checkout screen. */
    public function label(): string;

    /**
     * Begin a payment and return the URL the browser should be redirected to
     * (a hosted gateway page).
     */
    public function checkout(Payment $payment): string;

    /**
     * Verify the gateway's return / IPN request for the given payment and
     * report the final state.
     *
     * @param  array<string, mixed>  $params
     */
    public function verify(Payment $payment, array $params): PaymentResult;
}
