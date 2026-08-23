<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Switches the active UI locale. The choice is persisted to the session (read
 * back by the SetLocale middleware) and, for signed-in readers, saved to their
 * profile so it follows them across devices.
 */
class LanguageController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (in_array($locale, barta_locales(), true)) {
            Session::put('locale', $locale);
            app()->setLocale($locale);

            if ($user = $request->user()) {
                $user->forceFill(['locale' => $locale])->save();
            }
        }

        return redirect()->to($this->safeBackUrl($request));
    }

    /** Only redirect to a same-host referrer; otherwise fall back home. */
    protected function safeBackUrl(Request $request): string
    {
        $back = (string) $request->headers->get('referer', '');

        if ($back !== '' && str_starts_with($back, $request->getSchemeAndHttpHost())) {
            return $back;
        }

        return url('/');
    }
}
