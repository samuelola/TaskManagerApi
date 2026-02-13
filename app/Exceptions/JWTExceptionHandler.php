<?php

namespace App\Exceptions;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class JWTExceptionHandler
{
    public static function handle(\Throwable $exception)
    {
        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired. Please login again.'
            ], 401);
        }

        if ($exception instanceof TokenInvalidException) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalid. Please login.'
            ], 401);
        }

        if ($exception instanceof TokenBlacklistedException) {
            return response()->json([
                'success' => false,
                'message' => 'Token has been blacklisted. Please login again.'
            ], 401);
        }

        return null; // Not a JWT exception, let the global handler handle it
    }
}
