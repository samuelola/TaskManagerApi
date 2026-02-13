<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthController extends Controller
{
    /**
     * Register a new user and auto-assign role
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // First user = admin, else regular user
        $user->assignRole(User::count() === 1 ? 'admin' : 'user');

        $token = auth()->login($user);

        return $this->tokenResponse($token, 201);
    }

    /**
     * Login user and return token
     */
    public function login(Request $request)
    {
        if (!$token = auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        return $this->tokenResponse($token);
    }

    /**
     * Logout current device (invalidate current token)
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);

        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized. Token is invalid.'
            ], 401);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired. Please login again.'
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalid. Please login.'
            ], 401);
        }
    }

    /**
     * Logout all devices (invalidate all tokens by incrementing token_version)
     */
    public function logoutAll()
    {
        $user = auth()->user();

        // Increment token_version to invalidate all old tokens
        $user->increment('token_version');

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices'
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        try {
            $newToken = auth()->refresh();
            return $this->tokenResponse($newToken);

        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized. Token is invalid.'
            ], 401);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired. Please login again.'
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token cannot be refreshed. Please login.'
            ], 401);
        }
    }

    /**
     * Return token response with role info
     */
    protected function tokenResponse($token, $status = 200)
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'token' => $token,
            'type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'role' => $user->getRoleNames()
        ], $status);
    }
}
