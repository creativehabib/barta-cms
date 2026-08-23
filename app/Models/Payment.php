<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'subscription_id', 'plan_id', 'gateway', 'reference',
        'transaction_id', 'status', 'amount', 'currency', 'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function markSuccessful(?string $transactionId = null, array $payload = []): void
    {
        $this->update([
            'status' => 'success',
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'payload' => array_merge((array) $this->payload, $payload),
        ]);
    }

    public function markFailed(array $payload = []): void
    {
        $this->update([
            'status' => 'failed',
            'payload' => array_merge((array) $this->payload, $payload),
        ]);
    }
}
