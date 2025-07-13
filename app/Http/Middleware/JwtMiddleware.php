<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            auth()->setUser($user);
            
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Token expired',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Token invalid',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Token not found or unauthorized',
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }

        return $next($request);
    }

}
