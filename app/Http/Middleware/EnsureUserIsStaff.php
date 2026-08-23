<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate the admin panel: only authenticated users holding a staff role
 * (super-admin, admin, editor, author, contributor) may pass.
 */
class EnsureUserIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isStaff()) {
            abort(403, __('You do not have access to the admin area.'));
        }

        return $next($request);
    }
}
