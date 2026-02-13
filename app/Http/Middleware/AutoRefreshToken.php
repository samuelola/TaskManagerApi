<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AutoRefreshToken
{
    public function handle($request, Closure $next)
    {
        try {
            // Try authenticate normally
            $user = JWTAuth::parseToken()->authenticate();

        } catch (TokenExpiredException $e) {

            // Token expired → try refresh
            try {
                $newToken = JWTAuth::refresh(JWTAuth::getToken());

                // Attach new token to response header
                $response = $next($request);
                return $response->header('Authorization', 'Bearer ' . $newToken);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Session expired, please login again.'
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
