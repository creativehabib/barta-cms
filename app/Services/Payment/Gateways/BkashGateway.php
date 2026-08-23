<?php

namespace App\Services\Payment\Gateways;

use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGateway;
use App\Services\Payment\PaymentResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * bKash Tokenized Checkout (v1.2.0). Grant a token, create a payment (returns a
 * hosted bkashURL), then execute it after the customer returns. Sandbox by
 * default; set BKASH_SANDBOX=false for live.
 */
class BkashGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'bkash';
    }

    public function label(): string
    {
        return 'bKash';
    }

    public function checkout(Payment $payment): string
    {
        $token = $this->grantToken();

        $response = Http::withHeaders($this->authHeaders($token))
            ->acceptJson()
            ->post($this->baseUrl().'/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => (string) ($payment->user_id ?? 'guest'),
                'callbackURL' => route('payment.return', ['gateway' => $this->key(), 'payment' => $payment->reference]),
                'amount' => (string) $payment->amount,
                'currency' => $payment->currency,
                'intent' => 'sale',
                'merchantInvoiceNumber' => $payment->reference,
            ]);

        $data = $response->json();

        if (empty($data['bkashURL']) || empty($data['paymentID'])) {
            throw new RuntimeException('bKash create-payment failed: '.($data['statusMessage'] ?? $response->body()));
        }

        $payment->update([
            'transaction_id' => $data['paymentID'],
            'payload' => array_merge((array) $payment->payload, ['create' => $data]),
        ]);

        return $data['bkashURL'];
    }

    public function verify(Payment $payment, array $params): PaymentResult
    {
        $status = $params['status'] ?? null;
        if ($status && strtolower($status) !== 'success') {
            return PaymentResult::canceled($params, 'bKash payment '.$status.'.');
        }

        $paymentId = $params['paymentID'] ?? $payment->transaction_id;
        if (! $paymentId) {
            return PaymentResult::failed($params, 'Missing bKash paymentID.');
        }

        $token = $this->grantToken();
        $response = Http::withHeaders($this->authHeaders($token))
            ->acceptJson()
            ->post($this->baseUrl().'/tokenized/checkout/execute', ['paymentID' => $paymentId]);

        $data = $response->json();

        if (($data['transactionStatus'] ?? null) === 'Completed'
            && ($data['merchantInvoiceNumber'] ?? null) === $payment->reference) {
            return PaymentResult::success($data['trxID'] ?? $paymentId, $data);
        }

        return PaymentResult::failed($data, $data['statusMessage'] ?? 'bKash execution failed.');
    }

    protected function grantToken(): string
    {
        $config = config('services.bkash');

        $response = Http::withHeaders([
            'username' => $config['username'],
            'password' => $config['password'],
        ])->acceptJson()->post($this->baseUrl().'/tokenized/checkout/token/grant', [
            'app_key' => $config['app_key'],
            'app_secret' => $config['app_secret'],
        ]);

        $token = $response->json('id_token');

        if (! $token) {
            throw new RuntimeException('bKash token grant failed: '.$response->body());
        }

        return $token;
    }

    protected function authHeaders(string $token): array
    {
        return [
            'Authorization' => $token,
            'X-APP-Key' => config('services.bkash.app_key'),
        ];
    }

    protected function baseUrl(): string
    {
        return (config('services.bkash.sandbox', true))
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    }
}
