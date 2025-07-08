<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'status_code' => 201,
                'message' => 'User registered successfully',
                'data' => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login and return JWT token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Login successful',
            'data' => [
                'user' => Auth::guard('api')->user(),
                'token' => $token
            ]
        ]);
    }

    /**
     * Get the authenticated user's data.
     */
    public function me()
    {
        try {
            $user = Auth::guard('api')->user();

            return response()->json([
                'status_code' => 200,
                'message' => 'User retrieved successfully',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user (invalidate token).
     */
    public function logout()
    {
        try {
            Auth::guard('api')->logout();

            return response()->json([
                'status_code' => 200,
                'message' => 'Logged out successfully',
                'data' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
