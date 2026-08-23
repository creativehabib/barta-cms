<?php

namespace App\Services\Payment\Gateways;

use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGateway;
use App\Services\Payment\PaymentResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * SSLCommerz hosted checkout (v4 API). Works against the sandbox by default;
 * flip SSLCZ_SANDBOX=false for live. Docs: developer.sslcommerz.com.
 */
class SslCommerzGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'sslcommerz';
    }

    public function label(): string
    {
        return 'SSLCommerz (Card / Mobile Banking)';
    }

    public function checkout(Payment $payment): string
    {
        $config = config('services.sslcommerz');
        $user = $payment->user;

        $response = Http::asForm()->post($this->baseUrl().'/gwprocess/v4/api.php', [
            'store_id' => $config['store_id'],
            'store_passwd' => $config['store_password'],
            'total_amount' => (string) $payment->amount,
            'currency' => $payment->currency,
            'tran_id' => $payment->reference,
            'success_url' => route('payment.return', ['gateway' => $this->key(), 'payment' => $payment->reference, 'result' => 'success']),
            'fail_url' => route('payment.return', ['gateway' => $this->key(), 'payment' => $payment->reference, 'result' => 'fail']),
            'cancel_url' => route('payment.return', ['gateway' => $this->key(), 'payment' => $payment->reference, 'result' => 'cancel']),
            'ipn_url' => route('payment.ipn', ['gateway' => $this->key(), 'payment' => $payment->reference]),
            'cus_name' => $user?->name ?? 'Guest',
            'cus_email' => $user?->email ?? 'guest@example.com',
            'cus_phone' => $user?->phone ?? '01700000000',
            'cus_add1' => 'N/A',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'shipping_method' => 'NO',
            'product_name' => $payment->plan?->getTranslation('name', config('barta.default_locale', 'en'), false) ?? 'Subscription',
            'product_category' => 'subscription',
            'product_profile' => 'non-physical-goods',
        ]);

        $data = $response->json();

        if (($data['status'] ?? null) !== 'SUCCESS' || empty($data['GatewayPageURL'])) {
            throw new RuntimeException('SSLCommerz session failed: '.($data['failedreason'] ?? $response->body()));
        }

        $payment->update(['payload' => array_merge((array) $payment->payload, ['init' => $data])]);

        return $data['GatewayPageURL'];
    }

    public function verify(Payment $payment, array $params): PaymentResult
    {
        // The customer may return via fail/cancel URLs.
        $result = $params['result'] ?? null;
        if ($result === 'cancel') {
            return PaymentResult::canceled($params, 'Payment canceled by user.');
        }

        $valId = $params['val_id'] ?? null;
        if (! $valId) {
            return PaymentResult::failed($params, 'Missing validation id from SSLCommerz.');
        }

        $config = config('services.sslcommerz');
        $response = Http::get($this->baseUrl().'/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => $config['store_id'],
            'store_passwd' => $config['store_password'],
            'format' => 'json',
        ]);

        $data = $response->json();
        $status = $data['status'] ?? null;
        $validAmount = (float) ($data['amount'] ?? 0) >= (float) $payment->amount;
        $matchesTran = ($data['tran_id'] ?? null) === $payment->reference;

        if (in_array($status, ['VALID', 'VALIDATED'], true) && $validAmount && $matchesTran) {
            return PaymentResult::success($data['bank_tran_id'] ?? $valId, $data);
        }

        return PaymentResult::failed($data, 'SSLCommerz validation failed.');
    }

    protected function baseUrl(): string
    {
        return (config('services.sslcommerz.sandbox', true))
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }
}
