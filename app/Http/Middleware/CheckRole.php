<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: middleware('role:head_teacher') or middleware('role:teacher,head_teacher')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles)) {
            abort(403, 'Unauthorized. You do not have the required role.');
        }

        if (! $user->is_active) {
            abort(403, 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
