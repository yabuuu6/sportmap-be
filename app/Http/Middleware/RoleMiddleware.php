<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  Role yang diizinkan (misal: 'admin', 'user')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthenticated (token tidak valid atau tidak ada)',
                'data' => null
            ], 401);
        }

        if ($user->role !== $role) {
            return response()->json([
                'status_code' => 403,
                'message' => "Akses ditolak: hanya role '$role' yang diperbolehkan",
                'data' => null
            ], 403);
        }

        return $next($request);
    }
}
