<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');

        $roleName = $user->role->name ?? $user->role ?? null;
        if ($roleName !== 'Admin') {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
