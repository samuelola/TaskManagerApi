<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\JWTExceptionHandler;
use Throwable;

class CheckJWT
{
    public function handle($request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Throwable $e) {
            return JWTExceptionHandler::handle($e);
        }

        return $next($request);
    }
}
