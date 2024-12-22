<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // Daftar path yang dikecualikan dari autentikasi
        $excludedPaths = [
            'storage/uploads/', // Semua file dalam folder uploads dan subfolder
        ];

        // Periksa apakah path yang diminta termasuk dalam daftar pengecualian
        foreach ($excludedPaths as $path) {
            if ($request->is($path . '*')) {
                return $next($request); // Lewati middleware untuk path ini
            }
        }

        // Periksa role pengguna
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