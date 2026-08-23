<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protects premium routes: staff always pass; otherwise an active subscription
 * is required. Unauthenticated users go to login, others to the plans page.
 */
class EnsureSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if ($user->isStaff() || $user->isSubscribed()) {
            return $next($request);
        }

        return redirect()->route('plans')->with('status', __('This content requires an active subscription.'));
    }
}
