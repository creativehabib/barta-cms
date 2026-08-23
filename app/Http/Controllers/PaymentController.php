<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Drives the premium checkout flow:
 *   checkout() → create a pending payment and hand off to the gateway,
 *   return()   → the customer's browser comes back from the hosted page,
 *   ipn()      → the gateway's server-to-server confirmation.
 *
 * All finalization (verification + subscription provisioning) lives in
 * PaymentManager; this controller only translates HTTP ↔ that service and
 * decides where to send the reader afterwards.
 */
class PaymentController extends Controller
{
    public function __construct(protected PaymentManager $payments)
    {
    }

    /** Start a subscription purchase for the chosen plan + gateway. */
    public function checkout(Request $request, Plan $plan)
    {
        abort_unless($plan->is_active, 404);

        $available = array_keys($this->payments->available());
        $validated = $request->validate([
            'gateway' => ['required', 'string', 'in:'.implode(',', $available)],
        ]);

        // Already subscribed? Nothing to buy.
        if ($request->user()->isSubscribed()) {
            return redirect()->route('account')->with('status', __('You already have an active subscription.'));
        }

        try {
            $redirectUrl = $this->payments->checkout($request->user(), $plan, $validated['gateway']);
        } catch (Throwable $e) {
            Log::error('Checkout failed', ['plan' => $plan->slug, 'gateway' => $validated['gateway'], 'error' => $e->getMessage()]);

            return redirect()->route('plans')->with('error', __('Could not start the payment. Please try again or choose another method.'));
        }

        return redirect()->away($redirectUrl);
    }

    /**
     * Browser return from the gateway. Not behind auth middleware: gateways
     * post back cross-site, so the session cookie (SameSite=Lax) may be absent —
     * we re-resolve everything from the payment reference instead.
     */
    public function return(Request $request, string $gateway, string $payment)
    {
        try {
            $result = $this->payments->handleCallback($gateway, $payment, $request->all());
        } catch (Throwable $e) {
            Log::error('Payment return failed', ['ref' => $payment, 'error' => $e->getMessage()]);

            return redirect()->route('plans')->with('error', __('We could not verify your payment. If you were charged, please contact support.'));
        }

        $target = $this->redirectTargetFor($request, $result->user_id);

        if ($result->status === 'success') {
            return redirect()->to($target)->with('status', __('Payment successful — your subscription is now active.'));
        }

        if ($result->status === 'canceled') {
            return redirect()->route('plans')->with('error', __('Payment was canceled.'));
        }

        return redirect()->route('plans')->with('error', __('Payment did not complete. Please try again.'));
    }

    /** Server-to-server IPN. CSRF-exempt (see bootstrap/app.php). */
    public function ipn(Request $request, string $gateway, string $payment)
    {
        try {
            $result = $this->payments->handleCallback($gateway, $payment, $request->all());
        } catch (Throwable $e) {
            Log::error('Payment IPN failed', ['ref' => $payment, 'error' => $e->getMessage()]);

            return response()->json(['status' => 'error'], 200); // 200 so the gateway stops retrying a known-bad ref
        }

        return response()->json(['status' => $result->status]);
    }

    /**
     * Prefer the account page for the paying user; if the session was lost on a
     * cross-site return, fall back to the account route (login will catch them)
     * or the home page for guests.
     */
    protected function redirectTargetFor(Request $request, ?int $payingUserId): string
    {
        if ($request->user() || $payingUserId) {
            return route('account');
        }

        return route('home');
    }
}
