<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * The signed-in reader's account area: profile summary, current subscription
 * status and recent payment history. Rendered through the active theme so it
 * inherits the site chrome.
 */
class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        app('barta.seo')->title(__('My account'));

        return app('barta.theme')->view('account', [
            'user' => $user,
            'subscription' => $user->activeSubscription(),
            'payments' => $user->payments()->latest()->take(10)->get(),
        ]);
    }
}
