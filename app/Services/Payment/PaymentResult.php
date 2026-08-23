<?php

namespace App\Services\Payment;

/**
 * Immutable outcome of verifying a gateway callback.
 */
class PaymentResult
{
    public function __construct(
        public readonly bool $successful,
        public readonly string $status,          // success | failed | canceled | pending
        public readonly ?string $transactionId = null,
        public readonly array $raw = [],
        public readonly ?string $message = null,
    ) {
    }

    public static function success(?string $transactionId, array $raw = [], ?string $message = null): self
    {
        return new self(true, 'success', $transactionId, $raw, $message);
    }

    public static function failed(array $raw = [], ?string $message = null): self
    {
        return new self(false, 'failed', null, $raw, $message);
    }

    public static function canceled(array $raw = [], ?string $message = null): self
    {
        return new self(false, 'canceled', null, $raw, $message);
    }
}
