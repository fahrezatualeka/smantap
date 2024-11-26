<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{

    public function handle($request, Closure $next, $role)
    {
        if (auth()->check() && auth()->user()->role !== $role) {
            Log::warning('Akses tidak sesuai role', [
                'user_id' => auth()->id(),
                'role' => auth()->user()->role,
                'expected_role' => $role,
            ]);
            return response(null, 204); // Tidak ada respon
        }
        return $next($request);
    }
    

}