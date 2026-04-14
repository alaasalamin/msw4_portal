<?php

namespace App\Http\Middleware;

use App\Models\UserTypePermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTypePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $type = $user->type ?? null;

        if (!$type || !UserTypePermission::check($type, $permission)) {
            abort(403, "Your account type does not have permission to perform this action.");
        }

        return $next($request);
    }
}
