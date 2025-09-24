<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsSuperadmin
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Kalau relasi role ada: $user->role->name
        // Kalau kamu simpan string langsung di kolom role: $user->role
        $roleName = $user->role->name ?? $user->role ?? null;

        if ($user && $roleName === 'Superadmin') {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
